<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportSubscriptionRequest extends FormRequest
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
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'csv_file.required' => 'Berkas CSV wajib diunggah.',
            'csv_file.file' => 'Berkas yang diunggah tidak valid.',
            'csv_file.mimes' => 'Format berkas harus berupa .csv atau .txt.',
            'csv_file.max' => 'Ukuran berkas CSV maksimal adalah 2MB.',
        ];
    }
}
