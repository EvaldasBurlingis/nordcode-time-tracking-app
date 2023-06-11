<?php

namespace Modules\Task\Http\Requests;

use App\Enums\FileTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class DownloadTaskFileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date'],
            'fileType' => ['required', new Enum(FileTypes::class)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
