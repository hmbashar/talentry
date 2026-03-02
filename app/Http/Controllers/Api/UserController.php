<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            }))
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $users->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role?->value,
                'role_label' => $u->role?->label(),
                'created_at' => $u->created_at->toDateTimeString(),
            ]),
            'meta' => [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    public function updateRole(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $request->validate(['role' => ['required', 'in:admin,recruiter']]);

        $user->update(['role' => $request->role]);
        $user->refresh();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role?->value,
                'role_label' => $user->role?->label(),
            ],
        ]);
    }
}
