<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Permissions/PermissionIndex', [
            'permissions' => PermissionResource::collection(Permission::all())
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Permissions/PermissionCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePermissionRequest $request): RedirectResponse
    {
        Permission::create($request->validated());
        return to_route('permissions.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $role = Permission::findById($id);

        return Inertia::render('Admin/Permissions/PermissionEdit', [
            'permission' => new PermissionResource($role)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreatePermissionRequest $request, string $id): RedirectResponse
    {

        $permission = Permission::findById($id);
        $permission->update($request->validated());
        return to_route('permissions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findById($id);
        $permission->delete();
        return back();
    }


    /**
     * Revoke the specified permission from role.
     */
    public function revoke(Role $role, Permission $permission): RedirectResponse
    {
        $role->revokePermissionTo($permission);
        return back();
    }
}
