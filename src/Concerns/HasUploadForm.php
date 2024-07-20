<?php

namespace EightyNine\ExcelImport\Concerns;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;

trait HasUploadForm
{
    protected ?array $beforeUploadFieldFormFields = null;

    protected ?array $afterUploadFieldFormFields = null;

    protected ?string $disk = null;

    protected ?Closure $uploadField = null;

    protected string | Closure $visibility = 'public';


    public function uploadField(Closure $closure): static
    {
        $this->uploadField = $closure;
        return $this;
    }

    private function addField(Field $field, bool $isAfterUpload = false)
    {
        if ($isAfterUpload) {
            $this->afterUploadFieldFormFields[] = $field;
        } else {
            $this->beforeUploadFieldFormFields[] = $field;
        }
        return $this;
    }

    public function beforeUploadField(array $fields): static
    {
        foreach ($fields as $field) {
            $this->addField($field, false);
        }
        return $this;
    }

    public function afterUploadField(array $fields): static
    {
        foreach ($fields as $field) {
            $this->addField($field, true);
        }
        return $this;
    }

    protected function getDefaultForm(): array
    {
        if ($this->beforeUploadFieldFormFields) {
            $formFields = $this->beforeUploadFieldFormFields;
        }
        $formFields[] = $this->uploadField ?
            call_user_func($this->uploadField, $this->getUploadField()) :
            $this->getUploadField();

        if ($this->afterUploadFieldFormFields) {
            $formFields = array_merge($formFields, $this->afterUploadFieldFormFields);
        }
        return $formFields;
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }


    public function visibility(string | Closure | null $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    protected function getUploadField()
    {
        return FileUpload::make('upload')
            ->acceptedFileTypes($this->acceptedFileTypes)
            ->label(function ($livewire) {
                if (!method_exists($livewire, 'getTable')) {
                    return __('Excel Data');
                }

                return str($livewire->getTable()->getPluralModelLabel())->title() . ' ' . __('Excel Data');
            })
            ->default(1)
            ->storeFiles($this->storeFiles)
            ->disk(fn () => $this->disk ?: (config('excel-import.upload_disk') ?:
                    config('filesystems.default')))
            ->visibility($this->visibility)
            ->columns()
            ->required();
    }
}
