<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\View\View;

final class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-users');
    }

    public function index(): View
    {
        $users = User::with('departments')->orderBy('name')->paginate(20);

        return view('admin.users.index', ['users' => UserResource::collection($users)]);
    }
}
