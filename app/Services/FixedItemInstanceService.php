<?php

namespace App\Services;

use App\Models\FixedItemInstance;
use Illuminate\Validation\ValidationException;

class FixedItemInstanceService
{
    public function validate(FixedItemInstance $instance): void
    {
        if ($instance->item->type !== 'fixed') {
            throw ValidationException::withMessages([
                'item_id' => 'Item yang dipilih bukan Barang Tetap.',
            ]);
        }
    }

    public function delete(FixedItemInstance $instance): void
    {
        $instance->delete();
    }

    public function restore(FixedItemInstance $instance): void
    {
        $instance->restore();
    }

    public function forceDelete(FixedItemInstance $instance): void
    {
        $instance->forceDelete();
    }
}
