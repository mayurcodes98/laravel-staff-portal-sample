<?php

declare(strict_types=1);

namespace App\Actions\Documents;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DownloadDocumentAction
{
    /**
     * Stream the stored file back to the caller.
     * Authorization must be performed by the controller before calling this.
     */
    public function execute(Document $document): StreamedResponse
    {
        return Storage::disk('local')->download(
            $document->file_path,
            basename($document->file_path),
        );
    }
}
