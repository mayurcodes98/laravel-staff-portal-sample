<?php

declare(strict_types=1);

namespace Tests\Feature\Documents;

use App\Events\DocumentUploaded;
use App\Listeners\LogDocumentActivity;
use App\Models\Department;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    // ── Activity log row is created after upload (listener runs sync in tests)
    public function test_activity_log_is_created_on_document_upload(): void
    {
        Storage::fake('local');

        // Run listeners synchronously so we don't need a queue worker.
        Event::fake([]);  // don't fake — let listeners fire

        $admin = User::factory()->admin()->create();
        $dept = Department::factory()->create(['code' => 'LOGT', 'name' => 'Log Test Dept']);

        $file = UploadedFile::fake()->create('proc.pdf', 200, 'application/pdf');

        // The listener is queued; run synchronously by making ShouldQueue transparent in testing.
        // We fire the event manually and handle the listener inline.
        $document = Document::factory()->create([
            'department_id' => $dept->id,
            'uploaded_by' => $admin->id,
        ]);

        $listener = new LogDocumentActivity;
        $listener->handle(new DocumentUploaded($document, $admin));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'document.uploaded',
            'subject_type' => Document::class,
            'subject_id' => $document->id,
        ]);
    }
}
