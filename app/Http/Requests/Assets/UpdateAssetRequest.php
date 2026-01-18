<?php

namespace App\Http\Requests\Assets;

use App\Enums\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['sometimes', 'integer', 'exists:products,id'],
            'location_id' => ['sometimes', 'integer', 'exists:locations,id'],
            'asset_tag' => ['required', 'string', 'max:50', Rule::unique('assets', 'asset_tag')->ignore($this->asset)],
            'serial_number' => ['nullable', 'string', 'max:255', Rule::unique('assets', 'serial_number')->ignore($this->asset)],
            'status' => ['required', Rule::enum(AssetStatus::class)],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:today'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
