<?php

namespace App\Http\Controllers\Api\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Session\Session;
use Validator;
use App\User;

class RoleController extends Controller
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
        $data['roles'] = Role::with('permissions')->paginate(10);//Get all roles
        return response()->json($data, $this->successStatus);
    }

    public function store(Request $request)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|max:10',
            'permissions' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors()], 401);
        }
        $role_create = Role::create(['name' => $request->name, 'guard_name' => 'api']);
        $permissions = $request['permissions'];
        //Looping thru selected permissions
        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail();
            //Fetch the newly created role and assign permission
            $role = Role::where('name', '=', $request->name)->first();
            $role->givePermissionTo($p);
        }
        $data['role_name'] = $role->name;
        return response()->json($data, $this->storeStatus);
    }

    public function edit($id)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        $data['role'] = $role;
        $data['permissions'] = $permissions;
        return response()->json($data, $this->successStatus);
    }

    public function update(Request $request, $id)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $role = Role::findOrFail($id);//Get role with the given id
        //Validate name and permission fields
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:10|unique:roles,name,' . $id,
            'permissions' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors()], 401);
        }
        $input = $request->except(['permissions']);
        $permissions = $request['permissions'];
        $role->fill($input)->save();
        $p_all = Permission::all();//Get all permissions
        foreach ($p_all as $p) {
            $role->revokePermissionTo($p); //Remove all permissions associated with role
        }
        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form //permission in db
            $role->givePermissionTo($p);  //Assign permission to role
        }
        $data['Update Role name'] = $role->name;
        return response()->json($data, $this->successStatus);
    }

    public function destroy($id)
    {
        $check_role = Auth()->user()->getRoleNames();
        $check_role = $check_role[0];
        if ($check_role != 'admin') {
            return response()->json('failed', $this->permissionDenied);
        }
        $role = Role::findOrFail($id);
        $role->delete();
        $data['Deleted Role name'] = $role->name;
        return response()->json($data, $this->successStatus);
    }

}
