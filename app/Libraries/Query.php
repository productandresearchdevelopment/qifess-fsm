<?php
namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class Query{
    public static function open($query, $searchfield = null, $withCounter = true, $take=false) {
        $request = app('request');
        $search  = $request->input('query') ? $request->input('query') : ($request->input('q') ? $request->input('q') : null);
        $sort    = $request->input('sort');
        $dir     = $request->input('dir');
        $start   = $request->input('start');
        $limit   = $take ?: $request->input('limit');

        if($request->has('filter-trash')){
            $trash = $request->input('filter-trash');
            if(!$trash) $query->withTrashed();
            else if($trash == 2) $query->onlyTrashed();
        }

        if($searchfield && count($searchfield)){
            if($search){
                $query->where(function($query) use ($search, $searchfield){
                    foreach ($searchfield as $field) {
                        $relation = explode('.', $field);
                        if(count($relation) > 1){
                            $query->orWhereHas(count($relation)>2?$relation[0].'.'.$relation[1]:$relation[0], function($q) use ($search, $relation){
                                $q->where(end($relation), 'LIKE', '%'.$search.'%');
                            });
                        }
                        else{
                            if($field == 'id') {
                                $query->orWhere($field, $search);
                            }
                            else $query->orWhere($field, 'LIKE', '%'.$search.'%');
                        }
                    }
                });
            }
        }

        if($sort && $dir) $query->orderBy($sort, $dir);
        else if($sort) $query->orderBy($sort);

        if($withCounter) {
            $count = $query->count();

            if ($start) $query->skip($start);
            if ($limit) $query->take($limit)->get();

            return ['count' => $count, 'data' => $query->get()];
        }
        else if($take) $query->take($take)->get();

        return $query->get();
    }

    public static function openRaw($sql = null, $searchField=null, $filter=null, $counter=true, $xlimit = null, $xsort=null ){

        $request = app('request');

        $searchValue  = $request->input('query') ? $request->input('query') : ($request->input('q') ? $request->input('q') : null);
        $sort         = $request->input('sort');
        $dir          = $request->input('dir');
        $start        = $request->input('start') ?: 0;
        $limit        = $request->input('limit');

        $sql = preg_replace('/SELECT/', '', $sql, 1);

        $searchResult = '';
        if($searchValue && ($searchField && count($searchField))){
            $value = $searchValue;
            $result = [];
            foreach ($searchField as $field) {
                if($field == 'id') $result[] = "id = '$value'";
                else $result[] = "$field LIKE '%$value%'";
            }
            $searchResult = implode(' OR ', $result);
        }

        $filterResult = '';
        if($filter){
            if(is_array($filter)){
                if(count($filter)) $filterResult = implode(' AND ', $filter);
            }
            else $filterResult = $filter;
        }

        if($filterResult && $searchResult) $sql = "$sql WHERE ($filterResult) AND ($searchResult)";
        else if($searchResult) $sql = "$sql WHERE $searchResult";
        else if($filterResult) $sql = "$sql WHERE $filterResult";

        if($xsort) $sql = "$sql ORDER BY $xsort";
        else if($sort) $sql = "$sql ORDER BY $sort $dir";

        $resultLimit = '';
        if($xlimit) $sql = "$sql LIMIT 0, $xlimit";
        else if($limit) $sql  = "$sql LIMIT $start, $limit";
        $query = DB::select(DB::raw("SELECT SQL_CALC_FOUND_ROWS $sql"));

        if($counter && ($limit || $xlimit)) {
            $count = 0;
            $dbcount = DB::select(DB::raw("SELECT FOUND_ROWS() AS count"));
            if($dbcount && count($dbcount)) $count = $dbcount[0]->count;

            return ['data' => $query, 'count' => $count];
        }

        return $query;
    }


    public static function get($sql = null, $searchField=null, $filter=null, $counter=true, $xlimit = null, $xsort=null ){
        $request = app('request');

        $searchValue  = $request->input('query') ? $request->input('query') : ($request->input('q') ? $request->input('q') : null);
        $sort         = $request->input('sort');
        $dir          = $request->input('dir');
        $start        = $request->input('start') ?: 0;
        $limit        = $request->input('limit');

        $sql = preg_replace('/SELECT/', '', $sql, 1);

        $searchResult = '';
        if($searchValue && ($searchField && count($searchField))){
            $value = $searchValue;
            $result = [];
            foreach ($searchField as $field) {
                if($field == 'id') $result[] = "id = '$value'";
                else $result[] = "$field LIKE '%$value%'";
            }
            $searchResult = implode(' OR ', $result);
        }

        $filterResult = '';
        if($filter){
            if(is_array($filter)){
                if(count($filter)) $filterResult = implode(' AND ', $filter);
            }
            else $filterResult = $filter;
        }

        if($filterResult && $searchResult) $sql = "$sql WHERE ($filterResult) AND ($searchResult)";
        else if($searchResult) $sql = "$sql WHERE $searchResult";
        else if($filterResult) $sql = "$sql WHERE $filterResult";

        if($xsort) $sql = "$sql ORDER BY $xsort";
        else if($sort) $sql = "$sql ORDER BY $sort $dir";

        $resultLimit = '';
        if($xlimit) $sql = "$sql LIMIT 0, $xlimit";
        else if($limit) $sql  = "$sql LIMIT $start, $limit";

        $query = DB::select("SELECT SQL_CALC_FOUND_ROWS $sql");

        if($counter && ($limit || $xlimit)) {
            $count = 0;
            $dbcount = DB::select("SELECT FOUND_ROWS() AS count");
            if($dbcount && count($dbcount)) $count = $dbcount[0]->count;

            return ['data' => $query, 'count' => $count];
        }

        return $query;
    }
}
