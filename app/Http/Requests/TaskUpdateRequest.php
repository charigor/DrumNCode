<?php

namespace App\Http\Requests;

use App\Enums\TaskStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskUpdateRequest extends FormRequest
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
            'priority' => 'integer|min:1|max:5',
            'title' => 'required|string|min:3',
            'description' => 'nullable',
            'parent_id' => 'nullable|integer',
            'status' => Rule::in([TaskStatusEnum::Todo,TaskStatusEnum::Done])
        ];
    }
}
