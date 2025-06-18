<?php

namespace App\Controllers\Sites;

use App\Exports\Sites\ImportFormat\Format;
use App\Imports\Sites\Import;
use App\Libraries\ExportExcel;
use App\Http\Controllers\Controller;
use App\Libraries\ExportExcelChunk;
use App\Libraries\FileUpload;
use App\Libraries\Query;
use App\SystemModels\Globals\Upload;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Sites\Site as Mod;
use Illuminate\Http\Request;
use App\Models\Clients\Client;
use App\Models\Services\Service;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters as Master;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class Site extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->vendors && count($user->vendors)) $vendors = $user->vendors;
        else $vendors = Vendor::orderBy('name')->get();

        $params = [
            "user" => $user,
            "activities" => Master\Activity::all(),
            "clients" => Client::all(),
            "vendors" => $vendors,
            "services" => Master\Service::all(),
            "status" => Master\Status::with("details.options")->get(),
            "title" => "Sites Data"
        ];
        return view("sites.main", $params);
    }

    public function data(Request $request, $counter = true, $asQuery = false)
    {
        $user = $request->user();
        $query = Mod::query();
        if ($user->client_id) $query->where("client_id", $user->client_id);
        if (count($user->vendors)) {
            $query->whereIn('vendor_id', $user->vendors->pluck('id')->toArray());
        }

        if (($filter = $request->input('filter-client')) !== null && $filter !== 'null') {
            $query->where('client_id', $filter);
        }
        if (($filter = $request->input('filter-area')) !== null && $filter !== 'null') {
            $query->where('vendor_id', $filter);
        }
        if (($filter = $request->input('filter-service')) !== null && $filter !== 'null') {
            $query->where('service_id', $filter);
        }
        if (($filter = $request->input('filter-status')) !== null && $filter !== 'null') {
            $query->where('is_active', ($filter > 1) ? 0 : 1);
        }


        $trash = $request->input("trash");
        if ($trash < 1) $query->withTrashed();
        else if ($trash > 1) $query->onlyTrashed();

        if ($search = $request->input("query")) {
            $query->where(function ($query) use ($search) {
                $query->whereHas("client", function ($query) use ($search) {
                    $query->where("name", "LIKE", "%$search%");
                });
                $query->orwhere("link_id", "LIKE", "%$search%");
                $query->orwhere("name", "LIKE", "%$search%");
                $query->orwhere("address", "LIKE", "%$search%");
            });
        }

        $query->with(["workorders"]);
        $query->withCount(["workorders"]);

        if ($asQuery) {
            return $query;
        }
        return Query::open($query, null, $counter);
    }

    public function dataPublic(Request $request)
    {
        $search = ['name', 'link_id', 'terminal_name'];
        $query = Mod::query();
        return Query::open($query, $search);
    }

    public function get(Request $request, $id = null)
    {
        return Mod::find($id);
    }

    public function push(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            if (!$request->input("name")) return ["success" => false, "message" => "name Is Null"];
            if (!$request->input("client_id")) return ["success" => false, "message" => "client_id Is Null"];

            $input = [
                "name" => $request->input("name"),
                "client_id" => $request->input("client_id"),
                "vendor_id" => $request->input("vendor_id"),
                "link_id" => $request->input("link_id"),
                "pic" => $request->input("pic"),
                "pic_phone" => $request->input("pic_phone"),
                "pic_email" => $request->input("pic_email"),
                "province" => $request->input("province"),
                "city" => $request->input("city"),
                "district" => $request->input("district"),
                "ward" => $request->input("ward"),
                "postal_code" => $request->input("postal_code"),
                "address" => $request->input("address"),
                "lat" => $request->input("lat"),
                "long" => $request->input("long"),
                "description" => $request->input("description"),
                "is_active" => $request->input("active"),
                "active_date" => $request->input("active_date"),
                "terminal_name" => $request->input("terminal_name"),
                "beam" => $request->input("beam"),
                "airmac" => $request->input("airmac"),
                "serial_number" => $request->input("serial_number"),
                "service_id" => $request->input("service_id"),

            ];

            if ($id) {
                $data = Mod::find($id);
                $data->update($input);
            } else $data = Mod::create($input);

            DB::commit();
            return ['success' => true, 'message' => 'Success...', 'data' => $data];
        } catch (QueryException $error) {
            DB::rollback();
            return ["success" => false, "message" => "500 " . $error->getMessage()];
        }
    }

    public function exportExcel(Request $request)
    {
        ini_set('memory_limit', '4096M');
        ini_set('max_execution_time', '600');

        if (ob_get_length()) ob_end_clean();
        ob_start();

        $title = [];

        $title[] = ['Site', 'h2'];
        if ($request->input('trash') !== null && $request->input('trash') !== 'null') {
            if ($request->input('trash') == 1) {
                $title[] = ['DATA : Active', 'h5'];
            } elseif ($request->input('trash') == 2) {
                $title[] = ['DATA : Deleted', 'h5'];
            }
        } else {
            $title[] = ['DATA : All ( Active + Deleted )', 'h5'];
        }


        if ($request->input('filter-client') !== null && $request->input('filter-client') !== 'null') {
            $client = Client::find($request->input('filter-client'));
            if ($client) {
                $title[] = ['CLIENT : ' . $client->name, 'h5'];
            }
        } else {
            $title[] = ['CLIENT : All', 'h5'];
        }

        if ($request->input('filter-service') !== null && $request->input('filter-service') !== 'null') {
            $service = Service::find($request->input('filter-service'));
            if ($service) {
                $title[] = ['SERVICE : ' . $service->name, 'h5'];
            }
        } else {
            $title[] = ['SERVICE : All', 'h5'];
        }
        if ($request->input('filter-status') !== null && $request->input('filter-status') !== 'null') {
            $title[] = ['STATUS : ' . ($request->input('filter-status') == 1 ? 'Active' : 'Inactive'), 'h5'];
        } else {
            $title[] = ['STATUS : All', 'h5'];
        }


        $data = $this->data($request, false);

        $columns = [
            ['text' => 'LINK ID', 'dataIndex' => 'link_id', 'width' => 120, 'align' => 'center'],
            [
                'text' => 'CLIENT',
                'dataIndex' => 'client',
                'width' => 150,
                'renderer' => function ($e) {
                    return $e ? $e->name : '-';
                }
            ],
            [
                'text' => 'SERVICES',
                'dataIndex' => 'service',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e ? $e->name : '-';
                }
            ],
            [
                'text' => 'AREA',
                'dataIndex' => 'vendor',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e ? $e->name : '-';
                }
            ],
            ['text' => 'NAME', 'dataIndex' => 'name', 'width' => 200],
            ['text' => 'PIC NAME', 'dataIndex' => 'name', 'width' => 220],
            ['text' => 'PIC PHONE', 'dataIndex' => 'pic_phone', 'width' => 120],
            ['text' => 'PIC EMAIL', 'dataIndex' => 'pic_email', 'width' => 180],
            [
                'text' => 'STATUS',
                'dataIndex' => 'is_active',
                'width' => 80,
                'align' => 'center',
                'renderer' => function ($e) {
                    return $e ? 'Active' : 'Inactive';
                }
            ],
            ['text' => 'ACTIVE DATE', 'dataIndex' => 'active_date', 'type' => 'date', 'width' => 100, 'align' => 'center'],
            ['text' => 'ADDRESS', 'dataIndex' => 'address', 'width' => 300],
            ['text' => 'DESCRIPTION', 'dataIndex' => 'description', 'width' => 300]
        ];

        $params = array(
            // 'title' => $title,
            'columns' => $columns,
            'data' => $data,
            'filename' => 'Site' . '-' . date('YmdHi'),
            'footer' => [config('app.name') . ' (' . date('d F Y H:i:s') . ')'],
            'chunkSize' => 5000,
        );

        $isChunked = ExportExcelChunk::export($params);

        ob_end_flush();
        flush();

        Log::info('Export Excel Site menggunakan chunk: ' . ($isChunked ? 'YA' : 'TIDAK'));
    }

    public function importFormat(Request $request)
    {
        $filename = 'site_format.xlsx';
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
            Mod::whereIn("id", $data)->update(["deleted_at" => date("Y-m-d H:i:s"), "deleted_by" => $user->id]);
            return ["success" => true, "message" => "Success!"];
        }
        return ["success" => false, "message" => "No Data!"];
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
