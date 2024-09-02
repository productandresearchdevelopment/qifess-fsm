<?php
namespace App\Libraries;

class Routes {
    public static function list() {
    	$result = [];
        $routes = app()->routes->getRoutes();
        foreach ($routes AS $value) {
        	for($i=0; $i < count($value->middleware()); $i++){
        		if($value->middleware()[$i] == 'roles' || $value->middleware()[$i] == 'mobileRoles') {
        			if($value->getName()){
						$data = (object) array(
						    'url'  	 => $value->uri,
						    'prefix' => $value->getPrefix(),
						    'id' 	 => $value->getName(),
						    'method' => strtolower($value->methods[0]),
						    'action' => $value->getActionMethod(),
						);
						array_push($result, $data);
					}
        		}
        	}
        }
        return $result;
    }
}
