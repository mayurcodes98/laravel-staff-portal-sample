<?php

declare(strict_types=1);

namespace App\Actions\Documents;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

final class DeleteDocumentAction
{
    /**
     * Remove the database record, then the stored file.
     * The DB row is deleted first: if the Storage call fails, the record
     * is already gone and no orphaned DB entry remains. A dangling file
     * can be cleaned up by a storage-audit command; a record pointing at a
     * missing file causes silent errors at download time.
     * Authorization must be performed by the caller before invoking this action.
     */
    public function execute(Document $document): void
    {
        $path = $document->file_path;

        $document->delete();

        Storage::disk('local')->delete($path);
    }
}
