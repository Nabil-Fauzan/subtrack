<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|in:IDR,USD,EUR,SGD,GBP',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'next_billing_date' => 'required|date',
            'is_active' => 'nullable',
        ];
    }

    /**
     * Get sanitized data for model persistence.
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $data = $this->validated();
        $data['currency'] = $data['currency'] ?? 'IDR';
        $data['is_active'] = $this->boolean('is_active', true);

        return $data;
    }
}
