<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DocumentUploaded;
use App\Models\ActivityLog;
use App\Models\Document;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Queued listener: writing the activity log row is fire-and-forget
 * and must not delay the HTTP response.
 *
 * Laravel auto-discovers this listener from the handle(DocumentUploaded $event)
 * type-hint, so no EventServiceProvider mapping is required.
 */
final class LogDocumentActivity implements ShouldQueue
{
    public function handle(DocumentUploaded $event): void
    {
        ActivityLog::create([
            'user_id' => $event->uploader->id,
            'action' => 'document.uploaded',
            'subject_type' => Document::class,
            'subject_id' => $event->document->id,
            'meta' => [
                'title' => $event->document->title,
                'department_id' => $event->document->department_id,
            ],
        ]);
    }
}
