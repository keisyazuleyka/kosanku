<?php

namespace App\Http\Requests\Owner;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Property::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'min:20'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'type' => ['required', Rule::enum(PropertyType::class)],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:80'],
            'village' => ['nullable', 'string', 'max:80'],
            'address' => ['required', 'string', 'max:255'],
            'monthly_price' => ['required', 'integer', 'min:100000'],
            'deposit' => ['required', 'integer', 'min:0'],
            'additional_fees' => ['nullable', 'string'],
            'rules' => ['nullable', 'string'],
            'rooms_total' => ['required', 'integer', 'min:1', 'max:200'],
            'rooms_available' => ['required', 'integer', 'min:0', 'lte:rooms_total'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['integer', 'exists:facilities,id'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'submit_action' => ['required', 'in:draft,pending'],
        ];
    }
}
