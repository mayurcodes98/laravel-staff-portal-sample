<?php

declare(strict_types=1);

namespace App\Actions\Documents;

use App\Events\DocumentUploaded;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class UploadDocumentAction
{
    /**
     * Store the file, persist the record, and fire the uploaded event.
     * The file is stored before the DB write. If the DB write fails, the
     * catch block deletes the orphaned file to keep storage consistent.
     */
    public function execute(User $uploader, UploadedFile $file, string $title, int $departmentId): Document
    {
        $path = $file->store('documents', 'local');

        try {
            $document = Document::create([
                'title' => $title,
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'department_id' => $departmentId,
                'uploaded_by' => $uploader->id,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        DocumentUploaded::dispatch($document, $uploader);

        return $document;
    }
}
