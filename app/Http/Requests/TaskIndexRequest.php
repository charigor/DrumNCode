<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskIndexRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'priorityFrom' => 'integer|min:0',
            'priorityTo' => 'integer|max:5',
            'title' => 'string|min:3',
            'sortBy' => Rule::in(['priority','created_at','completed_at']),
            'sortOrder' => Rule::in(['asc','desc']),
        ];
    }
}
