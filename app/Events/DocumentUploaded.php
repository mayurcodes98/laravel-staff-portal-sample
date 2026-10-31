<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

final class DocumentUploaded
{
    use Dispatchable;

    public function __construct(
        public readonly Document $document,
        public readonly User $uploader,
    ) {}
}
