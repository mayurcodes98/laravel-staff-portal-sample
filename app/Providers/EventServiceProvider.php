<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\DocumentUploaded;
use App\Listeners\LogDocumentActivity;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /** @var array<class-string,list<class-string>> */
    protected $listen = [
        DocumentUploaded::class => [
            LogDocumentActivity::class,
        ],
    ];
}
