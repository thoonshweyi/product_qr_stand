<?php

namespace App\Exceptions;

use Exception;

class ExcelImportValidationException extends Exception
{
    public function __construct(
        private readonly array $errors,
        private readonly int $rowNumber = 1,
        private readonly int $totalCount = 0
    ) {
        parent::__construct('Excel import validation failed.');
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function rowNumber(): int
    {
        return $this->rowNumber;
    }

    public function totalCount(): int
    {
        return $this->totalCount;
    }
}
