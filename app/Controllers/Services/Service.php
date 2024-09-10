<?php

namespace App\Controllers\Services;

use App\Exports\Services\ImportFormat\Format;
use App\Http\Controllers\Controller;
use App\Imports\Services\Import;
use App\Libraries\ExportExcel;
use App\Libraries\FileUpload;
use App\Libraries\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Services\Service as Mod;
use Illuminate\Http\Request;
use App\Models\Sites\Site;
use App\SystemModels\Globals\Upload;
use Illuminate\Support\Facades\App;
use App\Models\WorkOrders\Masters as Master;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class Service extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $params = [
            'user' => $user,
            'services' => Mod::all(),
            'title' => 'Services Data'
        ];
        return view('services.main', $params);
    }

    public function data(Request $request, $counter = true)
    {
        $user = $request->user();
        $search = ['alias', 'name'];
        $query = Mod::query();

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
                'color' => $request->input('color'),
                'alias' => $request->input('alias'),
                'description' => $request->input('description'),
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

    public function exportExcel(Request $request)
    {
        ini_set('memory_limit', '64048M');
        ini_set('max_execution_time', '300');

        $title = [];

        $title[] = ['Service', 'h2'];

        $data = $this->data($request, false);

        $columns = [
            ['text' => 'Name', 'dataIndex' => 'name', 'width' => 300],
            ['text' => 'Description', 'dataIndex' => 'description', 'width' => 300],
        ];

        $params = array(
            'title' => $title,
            'columns' => $columns,
            'data' => $data,
            'filename' => 'Services' . '-' . date('YmdHi'),
            'footer' => [config('app.name') . ' (' . date('d F Y H:i:s') . ')'],
        );

        return ExportExcel::export($params);
    }

    public function importFormat(Request $request)
    {
        $filename = 'service_format.xlsx';
        return Excel::download(new Format(), $filename);
    }

    public function importData(Request $request)
    {
        if ($activity = $request->input('activity_id')) {
            if (!Master\Activity::find($activity)) {
                return ['success' => false, 'message' => 'Undefined Activity Ticket'];
            }
        }

        if ($upload = FileUpload::upload('file', 'site-import')) {
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
