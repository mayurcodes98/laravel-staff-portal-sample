<?php

declare(strict_types=1);

namespace Tests\Feature\Authorization;

use App\Models\Department;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    // ── 1. Employee cannot reach admin document management ───────────────────
    public function test_employee_cannot_access_admin_document_index(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->get('/admin/documents')
            ->assertForbidden();
    }

    // ── 2. Employee cannot access admin user list ─────────────────────────────
    public function test_employee_cannot_access_admin_user_index(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->get('/admin/users')
            ->assertForbidden();
    }

    // ── 3. Employee cannot download a document from a different department ───
    public function test_employee_cannot_download_document_from_another_department(): void
    {
        $deptA = Department::factory()->create(['code' => 'DEPA', 'name' => 'Dept A']);
        $deptB = Department::factory()->create(['code' => 'DEPB', 'name' => 'Dept B']);

        $employee = User::factory()->employee()->create();
        $employee->departments()->attach($deptA);

        $admin = User::factory()->admin()->create();
        $document = Document::factory()->create([
            'department_id' => $deptB->id,
            'uploaded_by' => $admin->id,
        ]);

        $this->actingAs($employee)
            ->get("/my/documents/{$document->id}/download")
            ->assertForbidden();
    }

    // ── 4. Admin can access admin routes ──────────────────────────────────────
    public function test_admin_can_access_admin_document_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/documents')
            ->assertOk();
    }

    // ── 5. Employee can download a document from their own department ─────────
    public function test_employee_can_download_document_from_own_department(): void
    {
        $dept = Department::factory()->create(['code' => 'MYDT', 'name' => 'My Dept']);
        $employee = User::factory()->employee()->create();
        $employee->departments()->attach($dept);

        $admin = User::factory()->admin()->create();
        $document = Document::factory()->create([
            'department_id' => $dept->id,
            'uploaded_by' => $admin->id,
            'file_path' => 'documents/sample.pdf',
        ]);

        // Use Storage fake so the download doesn't fail on missing file
        Storage::fake('local');
        Storage::disk('local')->put('documents/sample.pdf', 'content');

        $this->actingAs($employee)
            ->get("/my/documents/{$document->id}/download")
            ->assertOk();
    }
}
