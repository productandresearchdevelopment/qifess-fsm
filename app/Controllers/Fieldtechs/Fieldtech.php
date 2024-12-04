<?php

namespace App\Controllers\Fieldtechs;

use App\Exports\Fieldtechs\ImportFormat\Format;
use App\Imports\FieldTechs\Import;
use App\Http\Controllers\Controller;
use App\Libraries\ExportExcel;
use App\Libraries\FileUpload;
use App\Models\WorkOrders\Masters as Master;
use App\Libraries\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\SystemModels\Auth;
use App\Models\Fieldteches\Fieldtech as Mod;
use App\Models\Vendors\Vendor;
use Illuminate\Http\Request;
use App\SystemModels\Globals\Upload;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class Fieldtech extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->vendors && count($user->vendors)) $vendors = $user->vendors;
        else $vendors = Vendor::orderBy('name')->get();


        $params = [
            'user' => $user,
            'vendors' => $vendors,
            'activity' => Master\Activity::all(),
            'service' => Master\Service::all(),
            'title' => 'Fieldtech Data'
        ];
        return view('fieldtechs.main', $params);
    }

    public function data(Request $request, $counter = true)
    {
        $user = $request->user();
        $search = ['nik', 'name'];
        $query = Mod::with(['users', 'files']);
        $query->withCount(['workorders']);
        if ($user->vendor_id) $query = $query->where('vendor_id', $user->vendor_id);
        if (count($user->vendors)) {
            $query->whereIn('vendor_id', $user->vendors->pluck('id')->toArray());
        }

        $filter = $request->input('filter-vendor');

        if ($filter !== 'null' && $filter !== null) {
            $query->where('vendor_id', $filter);
        }

        if (!$request->trash) $query->withTrashed();
        if ($request->trash == 2) $query->onlyTrashed();

        return Query::open($query, $search, $counter);
    }

    public function showdetail(Request $request, $id)
    {
        $fieldtech = Mod::with(['vendor', 'users'])
            ->withCount('workorders')
            ->find($id);

        if (!$fieldtech) {
            return response()->json([
                'message' => 'Fieldtech not found',
            ], 404);
        }

        $data = [
            'id' => $fieldtech->id,
            'name' => $fieldtech->name,
            'vendor_name' => $fieldtech->vendor,
            'users' => $fieldtech->users,
            'workorders_count' => $fieldtech->workorders_count,
        ];

        return response()->json($data);
    }

    public function push(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            $photo = FileUpload::upload('photo', 'fieldtech');

            $input = [
                'nik' => $request->input('nik'),
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'email' => $request->input('email'),
                'vendor_id' => $request->input('vendor_id'),
                'vendor_name' => $request->input('vendor_name'),
            ];

            if ($id) {
                $data = Mod::find($id);
                $data->update($input);
            } else {
                $data = Mod::create($input);
                $id = $data->id;
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
            ['TEAM', 'h2']
        ];

        if ($request->input('trash') !== null && $request->input('trash') !== 'null') {
            if ($request->input('trash') == 1) {
                $title[] = ['DATA : Active', 'h5'];
            } elseif ($request->input('trash') == 2) {
                $title[] = ['DATA : Deleted', 'h5'];
            }
        } else {
            $title[] = ['DATA : All ( Active + Deleted )', 'h5'];
        }

        $filterVendor = $request->input('filter-vendor');

        if ($filterVendor !== 'null' && $filterVendor !== null) {
            $vendor = Vendor::find($filterVendor);
            if ($vendor) {
                $title[] = ['AREA : ' . $vendor->name, 'h5'];
            }
        } else {
            $title[] = ['AREA : All', 'h5'];
        }

        $data = $this->data($request, false);

        $columns = [
            [
                'text' => 'AREA',
                'dataIndex' => 'vendor',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e ? $e->name : '-';
                }
            ],
            [
                'text' => 'NIK',
                'dataIndex' => 'nik',
                'align' => 'center',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e ? $e : '-';
                }
            ],
            [
                'text' => 'NAME',
                'dataIndex' => 'name',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e ? $e : '-';
                }
            ],
            [
                'text' => 'User',
                'dataIndex' => 'users',
                'width' => 200,
                'renderer' => function ($e) {
                    if ($e && count($e)) {
                        $result = [];
                        foreach ($e as $value) {
                            $result[] = $value->name;
                        }
                        return implode(' ', $result);
                    }
                    return '-';
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
                'text' => 'FIELDTECH',
                'columns' => [
                    ['text' => 'SATU', 'dataIndex' => 'fieldtech1', 'width' => 200, 'renderer' => function ($e) {
                        return $e ? $e : '-';
                    }],
                    ['text' => 'DUA', 'dataIndex' => 'fieldtech2', 'width' => 200, 'renderer' => function ($e) {
                        return $e ? $e : '-';
                    }],
                ]
            ],
            [
                'text' => 'VENDOR NAME',
                'dataIndex' => 'vendor_name',
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
        ];

        $params = [
            'title' => $title,
            'columns' => $columns,
            'data' => $data,
            'filename' => 'Team' . '-' . date('YmdHi'),
            'footer' => [config('app.name') . ' (' . date('d F Y H:i:s') . ')'],
        ];

        return ExportExcel::export($params);
    }

    public function importFormat(Request $request)
    {
        $filename = 'team_format.xlsx';
        return Excel::download(new Format(), $filename);
    }

    public function importData(Request $request)
    {
        if ($upload = FileUpload::upload('file', 'team-import')) {
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
