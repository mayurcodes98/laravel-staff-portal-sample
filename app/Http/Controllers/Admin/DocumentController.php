<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Documents\DeleteDocumentAction;
use App\Actions\Documents\UploadDocumentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Documents\UploadDocumentRequest;
use App\Models\Department;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Document::class);

        $documents = Document::with(['department', 'uploader'])
            ->latest()
            ->paginate(20);

        return view('admin.documents.index', compact('documents'));
    }

    public function create(): View
    {
        $this->authorize('create', Document::class);

        $departments = Department::orderBy('name')->get();

        return view('admin.documents.create', compact('departments'));
    }

    public function store(UploadDocumentRequest $request, UploadDocumentAction $action): RedirectResponse
    {
        $document = $action->execute(
            uploader: $request->user(),
            file: $request->file('file'),
            title: $request->validated('title'),
            departmentId: (int) $request->validated('department_id'),
        );

        return redirect()->route('admin.documents.index')
            ->with('success', "Document \"{$document->title}\" uploaded.");
    }

    public function destroy(Document $document, DeleteDocumentAction $action): RedirectResponse
    {
        $this->authorize('delete', $document);

        $action->execute($document);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document deleted.');
    }
}
