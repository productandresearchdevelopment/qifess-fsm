<?php

namespace App\Controllers\Clients;

use App\Exports\Clients\ImportFormat\Format;
use App\Imports\Clients\Import;
use App\Http\Controllers\Controller;
use App\Libraries\ExportExcel;
use App\Libraries\FileUpload;
use App\Libraries\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Clients\Client as Mod;
use Illuminate\Http\Request;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters as Master;
use App\SystemModels\Globals\Upload;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class Client extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $params = [
            'user' => $user,
            'activities' => Master\Activity::all(),
            'services' => Master\Service::all(),
            'vendors' => Vendor::all(),
            'title' => 'Clients Data'
        ];
        return view('clients.main', $params);
    }

    public function data(Request $request, $counter = true)
    {
        $user = $request->user();
        $search = ['id', 'name', 'address', 'alias', 'customer_id'];
        $query = Mod::query();
        $query->withCount(['workorders']);
        $query->withCount(['sites']);
        if ($user->client_id) $query = $query->where('id', $user->client_id);

        if (!$request->trash) $query->withTrashed();
        if ($request->trash == 2) $query->onlyTrashed();

        return Query::open($query, $search, $counter);
    }

    public function dataPublic(Request $request)
    {
        $search = ['name', 'alias', 'customer_id'];
        $query = Mod::query();
        return Query::open($query, $search, false, 20);
    }

    public function get(Request $request, $id = null)
    {
        return Mod::find($id);
    }

    public function push(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            if (!$request->input('customer_id'))  return ['success' => false, 'message' => 'customer_id Is Null'];
            else if (!$request->input('name'))  return ['success' => false, 'message' => 'name Is Null'];
            else if (!$request->input('alias'))  return ['success' => false, 'message' => 'alias Is Null'];

            $input = [
                'customer_id' => $request->input('customer_id'),
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'alias' => $request->input('alias'),
                'email' => $request->input('email'),
                'description' => $request->input('description'),
            ];

            if ($id) {
                $data = Mod::find($id);
                $data->update($input);
            } else $data = Mod::create($input);

            DB::commit();
            return ['success' => true, 'message' => 'Success...', 'data' => $data];
        } catch (QueryException $error) {
            DB::rollback();
            return ['success' => false, 'message' => '500 ' . $error->getMessage()];
        }
    }

    public function exportExcel(Request $request)
    {
        ini_set('memory_limit', '64048M');
        ini_set('max_execution_time', '300');

        $title = [
            ['CLIENT', 'h2']
        ];

        $data = $this->data($request, false);

        $columns = [
            ['text' => 'CUSTOMER ID', 'dataIndex' => 'customer_id', 'width' => 200, 'align' => 'center'],
            [
                'text' => 'NAME',
                'dataIndex' => 'name',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e ? $e : '-';
                }
            ],
            [
                'text' => 'ALIAS',
                'dataIndex' => 'alias',
                'width' => 120,
                'renderer' => function ($e) {
                    return $e ? $e : '-';
                }
            ],
            [
                'text' => 'ADDRESS',
                'dataIndex' => 'address',
                'width' => 400,
                'renderer' => function ($e) {
                    return $e ? $e : '-';
                }
            ],
            [
                'text' => 'EMAIL',
                'dataIndex' => 'email',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e ? $e : '-';
                }
            ],
            [
                'text' => 'PHONE',
                'dataIndex' => 'phone',
                'align' => 'center',
                'width' => 180,
                'renderer' => function ($e) {
                    return $e ? $e : '-';
                }
            ],
            [
                'text' => 'DESCRIPTION',
                'dataIndex' => 'description',
                'width' => 400,
                'renderer' => function ($e) {
                    return $e ? $e : '-';
                }
            ],
        ];

        $params = [
            'title' => $title,
            'columns' => $columns,
            'data' => $data,
            'filename' => 'Client' . '-' . date('YmdHi'),
            'footer' => [config('app.name') . ' (' . date('d F Y H:i:s') . ')'],
        ];

        return ExportExcel::export($params);
    }

    public function importFormat(Request $request)
    {
        $filename = 'client_format.xlsx';
        return Excel::download(new Format(), $filename);
    }

    public function importData(Request $request)
    {
        if ($upload = FileUpload::upload('file', 'client-import')) {
            $user = $request->user();
            $file = Upload::find($upload);
            $fileexcel = Storage::disk('public_uploads')->path($file->filename);
            $importExcel = new Import($user);
            Excel::import($importExcel, $fileexcel);
            unlink($fileexcel);
            Upload::where('id', $upload)->delete();

            return ['success' => true, 'message' => $importExcel->logs()];
        }
        return ['success' => false, 'message' => 'The data you uploaded was not found'];
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

    public function delete(Request $request)
    {
        $user = $request->user();
        if ($data = json_decode($request->data)) {
            Mod::whereIn('id', $data)->update(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }
}
