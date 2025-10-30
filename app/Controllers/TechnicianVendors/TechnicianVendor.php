<?php

namespace App\Controllers\TechnicianVendors;

use App\Http\Controllers\Controller;
use App\Libraries\Query;
use App\Models\ListVendor\ListVendor;
use Illuminate\Database\QueryException;
use App\Models\TechnicianVendors\TechnicianVendor as Mod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TechnicianVendor extends Controller
{
    public function index(Request $request)
    {

        $user = $request->user();

        $params = [
            'user' => $user,
            'listVendors' => ListVendor::all(),
            'technicianVendors' => Mod::all(),
            'title' => 'Technician Vendors',
        ];
        return view('technician_vendor.main', $params);
    }

    public function data(Request $request, $counter = true)
    {
        $user = $request->user();
        $search = [
            'name',
        ];
        $query = Mod::with(['listvendors']);

        if ($request->trash < 1) {
            $query->withTrashed();
        } elseif ($request->trash > 1) {
            $query->onlyTrashed();
        }

        if ($search = $request->input("query")) {
            $query->where(function ($query) use ($search) {
                $query->orwhere("name", "LIKE", "%$search%");
                $query->orwhere("description", "LIKE", "%$search%");
            });
        }
        return Query::open($query, null, $counter);
    }

    public function push(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            $input = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'listvendor_id' => $request->input('listvendor_id'),
            ];

            if ($id) Mod::find($id)->update($input);
            else Mod::create($input);

            DB::commit();
            return ['success' => true, 'message' => 'Success...'];
        } catch (QueryException $error) {
            DB::rollback();
            return ['success' => false, 'message' => '500 ' . $error->getMessage()];
        }
    }
    public function delete(Request $request)
    {
        $user = $request->user();
        if ($data = json_decode($request->data)) {
            Mod::whereIn('id', $data)->update(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function restore(Request $request)
    {
        if ($data = json_decode($request->data)) {
            Mod::withTrashed()->whereIn('id', $data)->restore();
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function forceDelete(Request $request)
    {
        try {
            if ($data = json_decode($request->data)) {
                Mod::withTrashed()->whereIn('id', $data)->forcedelete();
                return response()->json(['success' => true, 'message' => 'Success!']);
            }
            return response()->json(['success' => false, 'message' => 'No Data!']);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json(['success' => false, 'message' => 'Cannot delete records because there are related entries. Please remove or reassign the related data first.'], 400);
            }
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the records.'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred.'], 500);
        }
    }
}
