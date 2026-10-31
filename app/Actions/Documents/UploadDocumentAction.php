<?php

declare(strict_types=1);

namespace App\Actions\Documents;

use App\Events\DocumentUploaded;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class UploadDocumentAction
{
    /**
     * Store the file, persist the record, and fire the uploaded event.
     * The DB write and file store are kept atomic: on exception the
     * transaction rolls back and the stored file is cleaned up manually.
     */
    public function execute(User $uploader, UploadedFile $file, string $title, int $departmentId): Document
    {
        $path = $file->store('documents', 'local');

        try {
            $document = DB::transaction(function () use ($uploader, $file, $path, $title, $departmentId): Document {
                return Document::create([
                    'title' => $title,
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'department_id' => $departmentId,
                    'uploaded_by' => $uploader->id,
                ]);
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        DocumentUploaded::dispatch($document, $uploader);

        return $document;
    }
}
