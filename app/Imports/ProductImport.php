<?php

namespace App\Imports;

use App\Exceptions\ExcelImportValidationException;
use App\Models\Category;
use App\Models\User;
use App\Models\Workflow;
use App\Services\ProductService;
use App\Support\ProductValidationRules;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProductImport implements ToCollection, WithHeadingRow
{
    private const SPECIFICATION_COLUMNS = [
        'weight' => 'Weight',
        'length' => 'Length',
        'width' => 'Width',
        'height' => 'Height',
        'size' => 'Size',
    ];

    private const REQUIRED_COLUMNS = [
        'product_code',
        'product_name',
        'name',
        'brand',
        'model',
        'country_of_origin',
        'category',
        'workflow',
    ];

    private const OPTIONAL_COLUMNS = [
        'website_url',
        'unit',
        'description',
        'description_en',
        'online_date',
        'weight',
        'length',
        'width',
        'height',
        'size',
    ];

    private array $seenProductCodes = [];

    private int $importedCount = 0;

    public function __construct(
        private readonly User $user,
        private readonly ProductService $productService,
    ) {}

    public function collection(Collection $rows): void
    {
        $preparedRows = $rows
            ->map(fn ($row, $index) => [
                'row_number' => $index + 2,
                'raw' => $this->normalizeRawRow($row->toArray()),
            ])
            ->reject(fn ($row) => $this->isEmptyRow($row['raw']))
            ->values();

        $this->validateHeadings($preparedRows);

        $validatedRows = [];
        $rowErrors = [];

        foreach ($preparedRows as $preparedRow) {
            $normalizedData = $this->normalizeForProductCreate($preparedRow['raw']);
            $errors = $this->validateRow($normalizedData);

            if (! empty($errors)) {
                $rowErrors[] = [
                    'row' => $preparedRow['row_number'],
                    'errors' => $errors,
                ];

                continue;
            }

            $this->seenProductCodes[] = $normalizedData['product_code'];
            $validatedRows[] = $normalizedData;
        }

        if (! empty($rowErrors)) {
            throw new ExcelImportValidationException($rowErrors);
        }

        foreach ($validatedRows as $validatedRow) {
            $this->productService->create($validatedRow, $this->user);
            $this->importedCount++;
        }
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }

    private function validateHeadings(Collection $preparedRows): void
    {
        if ($preparedRows->isEmpty()) {
            throw new ExcelImportValidationException([
                [
                    'row' => 1,
                    'errors' => [
                        'file' => ['The Excel file does not have any product rows.'],
                    ],
                ],
            ]);
        }

        $headings = collect($preparedRows->first()['raw'])->keys();
        $allowedColumns = collect(self::REQUIRED_COLUMNS)->merge(self::OPTIONAL_COLUMNS);
        $missingColumns = collect(self::REQUIRED_COLUMNS)->diff($headings)->values();
        $unknownColumns = $headings->diff($allowedColumns)->values();

        if ($missingColumns->isEmpty() && $unknownColumns->isEmpty()) {
            return;
        }

        $errors = [];

        if ($missingColumns->isNotEmpty()) {
            $errors['missing_columns'] = [
                'Missing required columns: '.$missingColumns->implode(', '),
            ];
        }

        if ($unknownColumns->isNotEmpty()) {
            $errors['unknown_columns'] = [
                'Unknown columns: '.$unknownColumns->implode(', '),
            ];
        }

        throw new ExcelImportValidationException([
            [
                'row' => 1,
                'errors' => $errors,
            ],
        ]);
    }

    private function validateRow(array $row): array
    {
        $validator = Validator::make(
            $row,
            ProductValidationRules::create($row, ['require_images' => false])
        );

        $validator->after(function ($validator) use ($row) {
            if (in_array($row['product_code'] ?? null, $this->seenProductCodes, true)) {
                $validator->errors()->add(
                    'product_code',
                    'The product code is duplicated in this Excel file.'
                );
            }
        });

        return $validator->fails()
            ? $validator->errors()->toArray()
            : [];
    }

    private function normalizeRawRow(array $row): array
    {
        return collect($row)
            ->mapWithKeys(function ($value, $key) {
                $key = Str::snake(Str::lower(Str::squish((string) $key)));

                if ($key === 'online_date' && filled($value)) {
                    $value = $this->normalizeDateValue($value);
                }

                $value = is_string($value)
                    ? Str::squish($value)
                    : $value;

                return [
                    $key => blank($value) ? null : $value,
                ];
            })
            ->filter(fn ($value, $key) => filled($key))
            ->all();
    }

    private function normalizeForProductCreate(array $row): array
    {
        $categoryId = $this->categoryId($row['category'] ?? null);
        $workflowId = $this->workflowId($row['workflow'] ?? null);

        return [
            ...$row,
            'category_id' => $categoryId,
            'workflow_id' => $workflowId,
            'status_id' => 1,
            'specifications' => $this->normalizeSpecifications($row),
        ];
    }

    private function normalizeSpecifications(array $row): array
    {
        return collect(self::SPECIFICATION_COLUMNS)
            ->map(fn ($name, $column) => [
                'name' => $name,
                'value' => $row[$column] ?? null,
            ])
            ->filter(fn ($specification) => filled($specification['value']))
            ->values()
            ->all();
    }

    private function categoryId(mixed $value): ?int
    {
        if (! filled($value)) {
            return null;
        }

        return Category::query()
            ->when(
                is_numeric($value),
                fn ($query) => $query->whereKey($value),
                fn ($query) => $query->where('name', $value)
            )
            ->value('id');
    }

    private function workflowId(mixed $value): ?int
    {
        if (! filled($value)) {
            return null;
        }

        $slug = Str::slug((string) $value);

        return Workflow::query()
            ->when(
                is_numeric($value),
                fn ($query) => $query->whereKey($value),
                fn ($query) => $query
                    ->where('slug', $value)
                    ->orWhere('slug', $slug)
                    ->orWhere('name', $value)
            )
            ->value('id');
    }

    private function normalizeDateValue(mixed $value): string
    {
        try {
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value))
                    ->startOfMonth()
                    ->toDateString();
            }

            return Carbon::parse($value)
                ->startOfMonth()
                ->toDateString();
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)
            ->filter(fn ($value) => filled($value))
            ->isEmpty();
    }
}
