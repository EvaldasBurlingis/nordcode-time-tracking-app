<?php

namespace Modules\Task\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'comment' => 'required|string|max:255',
            'date' => 'required|date',
            'time_spent' => 'required|integer|min:1|max:1440'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
