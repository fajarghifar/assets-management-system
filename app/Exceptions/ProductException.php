<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Support\Facades\Log;

class ProductException extends Exception
{
    public static function createFailed(string $message, ?Throwable $previous = null): self
    {
        if ($previous instanceof \Illuminate\Database\UniqueConstraintViolationException) {
            return new self("Product code is already registered. Please use another code.", 422, $previous);
        }

        Log::error("Product Creation Failed: $message", ['exception' => $previous]);
        return new self("Failed to create product: System error occurred.", 500, $previous);
    }

    public static function updateFailed(string $id, string $message, ?Throwable $previous = null): self
    {
        if ($previous instanceof \Illuminate\Database\UniqueConstraintViolationException) {
            return new self("Product code is already registered. Please use another code.", 422, $previous);
        }

        Log::error("Product Update Failed (ID: $id): $message", ['exception' => $previous]);
        return new self("Failed to update product: System error occurred.", 500, $previous);
    }

    public static function deletionFailed(string $id, string $message, ?Throwable $previous = null): self
    {
        if ($previous && str_contains($previous->getMessage(), 'Constraint violation')) {
            return new self("Failed to delete: This product is currently in use.", 409, $previous);
        }

        Log::error("Product Deletion Failed (ID: $id): $message", ['exception' => $previous]);
        return new self("Failed to delete product: System error occurred.", 500, $previous);
    }

    public static function inUse(string $message = 'Product is currently in use and cannot be deleted.'): self
    {
        return new self($message, 409);
    }
}
