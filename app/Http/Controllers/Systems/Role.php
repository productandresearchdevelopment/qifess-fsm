<?php

namespace App\Http\Controllers\Systems;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SystemModels\Auth\Module;
use App\SystemModels\Projects\Clients;
use App\SystemModels\Auth\Role AS Roles;

class Role extends Controller
{
    public function index(Request $request){
    	$user   = $request->user();
        $params = ['user' => $user];
        return view('systems.roles.main', $params);
    }

    public function dataRoles(Request $request){
        $user = $request->user();
        $data = Roles::where('id', '>=', $user->role->id)->get();
        return response()->json($data);
    }

    public function dataModules(Request $request, $role){
        $user = $request->user();
        $data = $this->treeModules($role);
        return response()->json($data);
    }

    public function input($req){
        $result = (object) [];

        $result->name        = $req['name'];
        $result->alias       = $req['alias'];
        $result->color       = $req['color'];
        $result->description = $req['description'];

        $valid = $this->valid($result);

        if($valid->fails()) return (object) ['success' => false, 'message' => $valid->errors()->first()];
        else return (object) ['success' => true, 'data' => $result];
    }

    public function valid($input){
        $check = (object) [];

        $check->name        = 'required|string|max:50';
        $check->alias       = 'required|string|max:6';
        $check->color       = 'required|string|max:6';
        $check->description = 'nullable|string|max:255';

        return Validator::make((array) $input, (array) $check);
    }

    public function create(Request $request){
        $input = $this->input($request->all());
        if($input->success) {
            Roles::create((array) $input->data);
            return response()->json(['success' => true, 'message' => 'Success!!!']);
        }
        return response()->json($input);
    }

    public function update(Request $request, $id){
        $input = $this->input($request->all());
        if($input->success) {
            Roles::find($id)->update((array) $input->data);
            return response()->json(['success' => true, 'message' => 'Success!!!']);
        }
        return response()->json($input);
    }

    public function delete(Request $request, $id){
        Roles::find($id)->delete();
        return response()->json(['success' => true, 'message' => 'Success!!!']);
    }

    public function setAuth(Request $request, $role){
        $request->all();
        $module = $request['module'];
        $auth   = $request['auth'];
        if($role && $module){
            $data = Roles::find($role);
            $data->modules()->detach([$module]);
            if($auth) $data->modules()->attach([$module]);
            return response()->json(['success' => true, 'message' => 'Success...']);
        }
        return response()->json(['success' => false, 'message' => 'Module Not Found']);
    }

    public function setHome(Request $request, $role){
        $request->all();
        $module = $request['module'];
        if($role && $module){
            Roles::find($role)->update(['home' => $module]);
            return response()->json(['success' => true, 'message' => 'Success...']);
        }

        return response()->json(['success' => false, 'message' => 'Module Not Found']);
    }


    private function treeModules($role, $parent = null){
        $result = Module::where('parent', $parent);
        $result = $result->orderBy('sort')->get();
        $role   = Roles::find($role);
        foreach ($result AS $row) {
            $row->children = $this->treeModules($role->id, $row->id);
            $row->leaf     = (count($row->children)) ? false : true;
            $row->checked  = $row->hasRole($role->id);
            $row->icon     = asset('images/icons/'.$row->type->icon.'.png');
            $row->home     = ($role->home == $row->id) ? true : false;
        }
        return $result;
    }


}
