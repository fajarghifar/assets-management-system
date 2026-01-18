<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Support\Facades\Log;

class LoanException extends Exception
{
    public static function createFailed(string $message, ?Throwable $previous = null): self
    {
        Log::error("Loan creation failed: " . $message);
        return new self("Failed to create loan: " . $message, 0, $previous);
    }

    public static function updateFailed(string $message, ?Throwable $previous = null): self
    {
        Log::error("Loan update failed: " . $message);
        return new self("Failed to update loan: " . $message, 0, $previous);
    }

    public static function approveFailed(string $message, ?Throwable $previous = null): self
    {
        Log::error("Loan approval failed: " . $message);
        return new self("Failed to approve loan: " . $message, 0, $previous);
    }

    public static function rejectFailed(string $message, ?Throwable $previous = null): self
    {
        Log::error("Loan rejection failed: " . $message);
        return new self("Failed to reject loan: " . $message, 0, $previous);
    }

    public static function restoreFailed(string $message, ?Throwable $previous = null): self
    {
        Log::error("Loan restoration failed: " . $message);
        return new self("Failed to restore loan: " . $message, 0, $previous);
    }

    public static function deletionFailed(string $message, ?Throwable $previous = null): self
    {
        Log::error("Loan deletion failed: " . $message);
        return new self("Failed to delete loan: " . $message, 0, $previous);
    }

    public static function returnFailed(string $message, ?Throwable $previous = null): self
    {
        Log::error("Loan items return failed: " . $message);
        return new self("Failed to return items: " . $message, 0, $previous);
    }

    public static function insufficientStock(string $productName, int $requested, int $available): self
    {
        return new self("Insufficient stock for {$productName}. Requested: {$requested}, Available: {$available}");
    }

    public static function assetUnavailable(string $assetTag, string $status): self
    {
        return new self("Asset {$assetTag} is not available (Status: {$status}).");
    }
}
