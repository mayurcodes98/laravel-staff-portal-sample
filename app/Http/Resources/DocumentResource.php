<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class DocumentResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'department' => $this->whenLoaded('department', fn () => [
                'id' => $this->department->id,
                'name' => $this->department->name,
            ]),
            'uploaded_by' => $this->whenLoaded('uploader', fn () => $this->uploader->name),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
