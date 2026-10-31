<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

final class DocumentPolicy
{
    /** Admins can list all; employees see the index but scoped by the model scope. */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Employee must share a department with the document. */
    public function view(User $user, Document $document): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->departments()->where('departments.id', $document->department_id)->exists();
    }

    /** Only admins may upload. */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Only admins may delete. */
    public function delete(User $user, Document $document): bool
    {
        return $user->isAdmin();
    }
}
