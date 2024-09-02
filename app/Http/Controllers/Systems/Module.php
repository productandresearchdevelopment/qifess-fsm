<?php

namespace App\Http\Controllers\Systems;

use Validator;
use Illuminate\Http\Request;
use App\Libraries\Routes;
use App\Http\Controllers\Controller;
use App\SystemModels\Auth\Role;
use App\SystemModels\Auth\ModuleType;
use App\SystemModels\Auth\Module AS Modules;


class Module extends Controller
{
    public function index(Request $request){
    	$user  = $request->user();
        $params = [
        	'types' => ModuleType::all(),
        	'routes' => Routes::list(),
        	'roles' => Role::where('id','>=', $user->role_id)->get(),
        	'user' => $user
        ];
        return view('systems.modules.main', $params);
    }

    public function data(Request $request){
    	$data = Modules::tree();
    	return response()->json($data);
    }

    public function create(Request $request){
    	$input = $this->input($request->all());
    	if($input->success) {
    		$id = Modules::create((array) $input->data)->id;
	    	Modules::setPath($id);
	    	Modules::resorting($input->data->parent);
    		return response()->json(['success' => true, 'message' => 'Success!!!', 'id' => $id]);
    	}
    	return response()->json($input);
    }

    public function update(Request $request, $id){
    	$input = $this->input($request->all());
    	if($input->success) {
	    	Modules::find($id)->update((array) $input->data);
	    	Modules::setPath($id);
	    	Modules::resorting($input->data->parent);
    		return response()->json(['success' => true, 'message' => 'Success!!!', 'id' => $id]);
    	}
    	return response()->json($input);
    }

    public function valid($input, $type){
        $check = (object) [];

        $check->type_id 	= 'required|integer';
        $check->text 		= 'required|string|max:50';
        $check->description = 'nullable|string|max:255';

        if($type->xroute) $check->route = 'required|string|max:255';
        if($type->xurl)   $check->url   = 'required|string|max:255';
        if($type->xauth)  $check->auth  = 'required|string|unique:auth_module,auth,'.$input->auth.',auth';

        return Validator::make((array) $input, (array) $check);
    }

    public function input($input){
        $result = (object) [];

        $type   = ModuleType::find($input['type_id']);

        $result->type_id 		= $type->id;
		$result->text 			= $input['text'];
		$result->parent 		= $input['parent'] ? $input['parent'] : null;
		$result->is_active 		= $input['is_active'];
		$result->is_locked 		= $input['is_locked'];
		$result->description 	= $input['description'];
        $result->icon 			= $type->xicon ? $input['icon'] : null;
		$result->route          = $type->xroute ? $input['route'] : null;
        $result->param          = $type->xroute ? $input['param'] : null;
	 	$result->url 			= $type->xurl ? $input['url'] : null;
		$result->auth 			= $type->xauth ? $input['auth'] : null;
		$result->device 		= $type->xdevice ? $input['device'] : null;

		$valid = $this->valid($result, $type);

        if($valid->fails()) return (object) ['success' => false, 'message' => $valid->errors()->first()];
        else return (object) ['success' => true, 'data' => $result];
    }

    public function delete(Request $request){
        $input = $request->all();
        $data  = json_decode($input['data']);
        Modules::whereIn('id', $data)->forceDelete();
        return response()->json(['success' => true, 'message' => 'Success!!!']);
    }

    public function updateField(Request $request, $field){
        $input = $request->all();
        $value = $input['value'];
        $data  = json_decode($input['data']);

        switch ($field) {
            case 'active': $field = ['is_active'=> $value]; break;
            case 'lock': $field = ['is_locked'=> $value]; break;
            case 'device': $field = ['device'=> $value]; break;
            default: return response()->view('errors.404', [], 404);
        }

        Modules::whereIn('id', $data)->update($field);
        return response()->json(['success' => true, 'message' => 'Success!!!']);
    }

    public function updateRoles(Request $request, $id){
        $input  = $request->all();
        $roles  = json_decode($input['roles']);
        $data   = Modules::find($id);

        $data->roles()->detach();
        $data->roles()->attach($roles);

        return response()->json(['success' => true, 'message' => 'Success!!!']);
     }

    public function move(Request $request, $mode){
        $input  = $request->all();
        $from   = json_decode($input['from']);
        $to     = json_decode($input['to']);

        if($mode == 'append'){
            Modules::find($from->id)->update(['parent' => $to->id, 'sort' => null]);
            Modules::resorting($to->id);
            return response()->json(['success' => true, 'message' => 'Success!!!']);
        }
        else if($mode == 'before' || $mode == 'after'){
            $sort = ($mode == 'after') ? ($to->sort + 1) : $to->sort;
            $data = Modules::where('sort', '>=', $sort)->where(['parent' => $to->parent])->orderBy('sort')->get();
            foreach ($data as $row) {
                $row->update(['sort' => $row->sort + 1]);
            }
            Modules::find($from->id)->update(['sort' => $sort]);
            Modules::resorting($from->parent);
            Modules::setPath($from->id);

            return response()->json(['success' => true, 'message' => 'Success!!!']);
        }

        return response()->view('errors.404', [], 404);
    }
}
