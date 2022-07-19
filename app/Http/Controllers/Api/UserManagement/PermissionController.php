<?php

namespace App\Http\Controllers\Api\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
{
    public $permissionDenied = 403;
    public $notFound = 404;
    public $badRequest = 400;
    public $storeStatus = 201;
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    public function index()
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        //Get all permissions
        $data['permissions'] = Permission::paginate(10);
        $data['status'] = 200;
        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $this->validate($request, [
            'name' => 'required|string|max:40',
        ]);

        $name = $request['name'];
        $permission = new Permission();
        $permission->name = $name;
        $roles = $request['roles'];
        $permission->save();

        //If one or more role is selected
        if (!empty($request['roles'])) {
            foreach ($roles as $role) {
                //Match input role to db record
                $r = Role::where('id', '=', $role)->firstOrFail();
                $permission = Permission::where('name', '=', $name)->first();
                $r->givePermissionTo($permission);
            }
        }
        $data['Stored Permission'] = $request->name;
        $data['status'] = 201;
        return response()->json($data, $this->storeStatus);
    }

    public function edit($id)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $data['permission'] = Permission::findOrFail($id);
        $data['status'] = 200;
        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $permission = Permission::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:40',
        ]);
        $input = $request->all();
        $permission->fill($input)->save();
        $data['updated_name'] = $request->name;
        $data['status'] = 200;
        return response()->json($data, $this->successStatus);
    }

    public function destroy($id)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $permission = Permission::findOrFail($id);
        //Make it impossible to delete this specific permission
        if ($permission->name == "Administer roles & permissions") {
            $data['status'] = 200;
            return response()->json($data, $this->successStatus);
        }
        $permission->delete();
        $data['status'] = 200;
        return response()->json($data, $this->successStatus);
    }
}
