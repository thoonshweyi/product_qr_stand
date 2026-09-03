<?php

namespace App\Exceptions;

use Exception;

class ExcelImportValidationException extends Exception
{
    public function __construct(
        private readonly array $errors,
        private readonly int $rowNumber
    ) {
        parent::__construct('Validation failed at row '.$rowNumber.'.');
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
