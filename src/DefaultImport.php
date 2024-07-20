<?php

namespace EightyNine\ExcelImport;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DefaultImport implements ToCollection, WithHeadingRow
{
    public function __construct(
        public string $model,
        public array $attributes = [],
        public array $additionalData = []
    ) {
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $data = $row->toArray();
            $data = array_merge($data, $this->additionalData);
            $this->model::create($data);
        }
    }
}
