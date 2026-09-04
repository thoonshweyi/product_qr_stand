<?php

namespace App\Exceptions;

use Exception;

class ExcelImportValidationException extends Exception
{
    public function __construct(
        private readonly array $errors,
        private readonly int $rowNumber = 1
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
}
