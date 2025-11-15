<?php

namespace App\Observers;

use App\Models\FixedItemInstance;
use App\Services\FixedItemInstanceService;
use Illuminate\Validation\ValidationException;

class FixedItemInstanceObserver
{
    public function saving(FixedItemInstance $instance): void
    {
        $instance->loadMissing('item');
        app(FixedItemInstanceService::class)->validate($instance);
    }

    public function deleting(FixedItemInstance $instance): void
    {
        if ($instance->status === 'borrowed') {
            throw ValidationException::withMessages([
                'instance' => 'Tidak bisa menghapus: instance ini sedang dipinjam.',
            ]);
        }
    }
}
