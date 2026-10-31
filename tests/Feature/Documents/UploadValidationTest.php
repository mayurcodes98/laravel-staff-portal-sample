<?php

declare(strict_types=1);

namespace Tests\Feature\Documents;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class UploadValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Queue::fake();
    }

    // ── 1. Valid PDF uploads successfully ─────────────────────────────────────
    public function test_admin_can_upload_valid_pdf(): void
    {
        $admin = User::factory()->admin()->create();
        $dept = Department::factory()->create(['code' => 'TST1', 'name' => 'Test Dept']);

        $file = UploadedFile::fake()->create('report.pdf', 500, 'application/pdf');

        $this->actingAs($admin)
            ->post('/admin/documents', [
                'title' => 'Quarterly Report',
                'department_id' => $dept->id,
                'file' => $file,
            ])
            ->assertRedirect(route('admin.documents.index'));
    }

    // ── 2. Executable file is rejected ───────────────────────────────────────
    public function test_executable_file_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $dept = Department::factory()->create(['code' => 'TST2', 'name' => 'Test Dept 2']);

        $file = UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload');

        $this->actingAs($admin)
            ->post('/admin/documents', [
                'title' => 'Bad File',
                'department_id' => $dept->id,
                'file' => $file,
            ])
            ->assertSessionHasErrors('file');
    }

    // ── 3. File exceeding 10 MB is rejected ───────────────────────────────────
    public function test_oversized_file_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $dept = Department::factory()->create(['code' => 'TST3', 'name' => 'Test Dept 3']);

        // 11 MB file
        $file = UploadedFile::fake()->create('huge.pdf', 11 * 1024, 'application/pdf');

        $this->actingAs($admin)
            ->post('/admin/documents', [
                'title' => 'Giant Doc',
                'department_id' => $dept->id,
                'file' => $file,
            ])
            ->assertSessionHasErrors('file');
    }
}
