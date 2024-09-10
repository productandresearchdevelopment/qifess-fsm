<?php

namespace App\Controllers\Vendors;

use App\Exports\Vendors\ImportFormat\Format;
use App\Imports\Vendors\Import;
use App\Libraries\ExportExcel;
use App\Http\Controllers\Controller;
use App\Libraries\FileUpload;
use App\SystemModels\Globals\Upload;
use App\Libraries\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Vendors\Vendor as Mod;
use Illuminate\Http\Request;
use App\Models as Model;
use App\Models\WorkOrders\Masters as Master;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Sites\Site;
use App\Models\Clients\Client;
use Illuminate\Support\Facades\App;


class Vendor extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $params = [
            'user' => $user,
            'title' => 'Area'
        ];
        return view('vendors.main', $params);
    }

    public function data(Request $request, $counter = true)
    {
        $search = ['name', 'address'];
        $query = Mod::with(['files']);
        $query->withCount(['workorders']);
        $query->withCount(['fieldteches']);

        if (!$request->trash) $query->withTrashed();
        if ($request->trash == 2) $query->onlyTrashed();

        return Query::open($query, $search, $counter);
    }

    public function push(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            $input = [
                'name' => $request->input('name'),
                'alias' => $request->input('alias'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'email' => $request->input('email'),
                'description' => $request->input('description'),
            ];

            if ($id) {
                $data = Mod::find($id);
                $data->update($input);
            } else {
                $data = Mod::create($input);
                $id = $data->id;
            }

            if ($files = $request->input('attachment')) {
                $data->files()->detach();
                $files = json_decode($files);
                foreach ($files as $file) {
                    if (is_object($file)) $data->files()->attach($file->id);
                    else if ($fid = FileUpload::push($file, 'vendor-attachment')) {
                        $data->files()->attach($fid);
                    }
                }
            }

            DB::commit();
            return ['success' => true, 'message' => 'Success...'];
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
            ['AREA', 'h2']
        ];

        $data = $this->data($request, false);

        $columns = [
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
                'text' => 'COLOR',
                'dataIndex' => 'color',
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
                'text' => 'PHONE',
                'dataIndex' => 'phone',
                'align' => 'center',
                'width' => 180,
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
            'filename' => 'Area' . '-' . date('YmdHi'),
            'footer' => [config('app.name') . ' (' . date('d F Y H:i:s') . ')'],
        ];

        return ExportExcel::export($params);
    }

    public function importFormat(Request $request)
    {
        $filename = 'area_format.xlsx';
        return Excel::download(new Format(), $filename);
    }

    public function importData(Request $request)
    {
        if ($activity = $request->input('activity_id')) {
            if (!Master\Activity::find($activity)) {
                return ['success' => false, 'message' => 'Undefined Activity Ticket'];
            }
        }

        if ($upload = FileUpload::upload('file', 'vendor-import')) {
            $user = $request->user();
            $file = Upload::find($upload);
            $fileexcel = Storage::disk('public_uploads')->path($file->filename);
            $importExcel = new Import($user, $activity);
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
