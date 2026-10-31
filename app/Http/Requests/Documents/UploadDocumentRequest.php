<?php

declare(strict_types=1);

namespace App\Http\Requests\Documents;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

final class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Document::class) ?? false;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        $maxKilobytes = (int) (config('portal.document_max_size_bytes') / 1024);

        return [
            'title' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'file' => [
                'required',
                'file',
                'mimes:pdf,docx,xlsx',
                "max:{$maxKilobytes}",
            ],
        ];
    }
}
