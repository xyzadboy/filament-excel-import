<?php

namespace EightyNine\ExcelImport;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImportAction extends Action
{
    use Concerns\HasUploadForm,
        Concerns\HasFormActionHooks,
        Concerns\HasCustomCollectionMethod,
        Concerns\CanCustomiseActionSetup;

    protected string $importClass = DefaultImport::class;

    protected array $importClassAttributes = [];

    public function use(string $class = null, ...$attributes): static
    {
        $this->importClass = $class ?: DefaultImport::class;
        $this->importClassAttributes = $attributes;

        return $this;
    }

    public static function getDefaultName(): ?string
    {
        return 'import';
    }

    public function action(Closure | string | null $action): static
    {
        if ($action !== 'importData') {
            throw new \Exception('You\'re unable to override the action for this plugin');
        }

        $this->action = $this->importData();

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-arrow-down-tray')
            ->color('warning')
            ->form(fn () => $this->getDefaultForm())
            ->modalIcon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->modalWidth('md')
            ->modalAlignment('center')
            ->modalHeading(fn ($livewire) => __('Import Excel'))
            ->modalDescription(__('Import data into database from excel file'))
            ->modalFooterActionsAlignment('right')
            ->closeModalByClickingAway(false)
            ->action('importData');
    }

    private function importData(): Closure
    {
        return function (array $data, $livewire): bool {
            if (is_callable($this->beforeImportClosure)) {
                call_user_func($this->beforeImportClosure, $data, $livewire, $this);
            }
            $importObject = new $this->importClass(
                method_exists($livewire, 'getModel') ? $livewire->getModel() : null,
                $this->importClassAttributes,
                $this->additionalData
            );

            if(method_exists($importObject, 'setAdditionalData')) {
                $importObject->setAdditionalData($this->additionalData);
            }

            if(method_exists($importObject, 'setCollectionMethod')) {
                $importObject->setCollectionMethod($this->collectionMethod);
            }

            Excel::import($importObject, $data['upload']);

            if (is_callable($this->afterImportClosure)) {
                call_user_func($this->afterImportClosure, $data, $livewire);
            }
            return true;
        };
    }
}
