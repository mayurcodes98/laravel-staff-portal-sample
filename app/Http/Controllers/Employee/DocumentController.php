<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employee;

use App\Actions\Documents\DownloadDocumentAction;
use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Document::class);

        $documents = Document::with(['department'])
            ->visibleTo($request->user())
            ->latest()
            ->paginate(20);

        return view('employee.documents.index', compact('documents'));
    }

    public function download(Request $request, Document $document, DownloadDocumentAction $action): StreamedResponse
    {
        $this->authorize('view', $document);

        return $action->execute($document);
    }
}
