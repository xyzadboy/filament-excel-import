<?php

namespace EightyNine\ExcelImport;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;

use function Pest\Laravel\call;

class ExcelImportAction extends Action
{
    protected string $importClass = DefaultImport::class;

    protected array $importClassAttributes = [];

    protected ?string $disk = null;

    protected string | Closure $visibility = 'public';

    protected ?Closure $beforeImportClosure = null;

    protected ?Closure $afterImportClosure = null;

    protected array $acceptedFileTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];

    protected bool $storeFiles = false;

    public function use(string $class = null, ...$attributes): static
    {
        $this->importClass = $class ?: DefaultImport::class;
        $this->importClassAttributes = $attributes;

        return $this;
    }

    protected function getDisk()
    {
        return $this->disk ?: config('filesystems.default');
    }

    public function visibility(string | Closure | null $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function acceptedFileTypes(array $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function storeFiles(bool $storeFiles): static
    {
        $this->storeFiles = $storeFiles;

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

    public function beforeImport(Closure $closure): static
    {
        $this->beforeImportClosure = $closure;

        return $this;
    }

    public function afterImport(Closure $closure): static
    {
        $this->afterImportClosure = $closure;

        return $this;
    }

    protected function getDefaultForm(): array
    {
        return [
            FileUpload::make('upload')
                ->acceptedFileTypes($this->acceptedFileTypes)
                ->label(function ($livewire) {
                    if (! method_exists($livewire, 'getTable')) {
                        return __('Excel Data');
                    }

                    return str($livewire->getTable()->getPluralModelLabel())->title() . ' ' . __('Excel Data');
                })
                ->default(1)
                ->storeFiles($this->storeFiles)
                ->disk($this->getDisk())
                ->visibility($this->visibility)
                ->columns()
                ->required(),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-arrow-down-tray')
            ->color('warning')
            ->form($this->getDefaultForm())
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

    /**
     * Import data function.
     *
     * @param  array  $data The data to import.
     * @param $livewire The Livewire instance.
     * @return bool Returns true if the import was successful, false otherwise.
     */
    private function importData(): Closure
    {
        return function (array $data, $livewire): bool {
            if(is_callable($this->beforeImportClosure)){
                call_user_func($this->beforeImportClosure, $data, $livewire);
            }
            $importObject = new $this->importClass(
                method_exists($livewire, 'getModel') ? $livewire->getModel() : null,
                ...$this->importClassAttributes
            );
            Excel::import($importObject, $data['upload']);
            if(is_callable($this->afterImportClosure)){
                call_user_func($this->afterImportClosure, $data, $livewire);
            }
            return true;
        };
    }
}
