<?php

namespace App\Http\Controllers\Systems;

use App\Exports\Users\ImportFormat\Format;
use App\Mail\ActiveUser;
use App\Models\Clients\Client;
use App\Models\Fieldteches\Fieldtech;
use App\Models\Owners\Owner;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Masters\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Middleware\AuthRoles;
use App\Imports\Users\Import;
use App\Libraries\ExportExcel;
use App\Libraries\FileUpload;
use App\SystemModels\Auth;
use App\Libraries\Query;
use App\SystemModels\Auth\Role;
use App\SystemModels\Auth\User as AuthUser;
use App\SystemModels\Globals\Upload;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class User extends Controller
{
    public function index(Request $request)
    {
        $user   = $request->user();
        $view   = 'systems.users.main';

        if ($user->vendors && count($user->vendors)) {
            $vendors = $user->vendors;
        } else $vendors = Vendor::orderBy('name')->get();

        $params = [
            'user' => $user,
            'roles' => Auth\Role::where('id', '>=', $user->role_id)->get(),
            'activities' => Activity::all(),
            'owners' => Owner::all(),
            'clients' => $user->client_id ? Client::where('id', $user->client_id)->get() : Client::all(),
            'vendors' => $vendors,
        ];
        return view($view, $params);
    }

    public function data(Request $request, $counter = true)
    {
        $user   = $request->user();

        $search = ['id', 'name', 'username', 'last_ip', 'email', 'phone'];
        $query  = Auth\User::with('fieldtech', 'vendors');

        $query->where('role_id', '>=', $user->role_id);

        if ($user->client_id) $query->where('client_id', '>=', $user->client_id);
        if ($user->vendor_id) $query->where('vendor_id', '>=', $user->vendor_id);
        if ($user->vendors && count($user->vendors)) {
            $query->whereIn('vendor_id', $user->vendors->pluck('id')->toArray());
        }


        if ($request->has('role') && !empty($request->get('role') && $request->get('role') != 'null' && $request->get('role') !== 'null')) {
            $query->where('role_id', $request->get('role'));
        }

        if ($request->has('client') && !empty($request->get('client') && $request->get('client') != 'null' && $request->get('client') !== 'null')) {
            $query->where('client_id', $request->get('client'));
        }

        if ($request->has('vendor') && !empty($request->get('vendor') && $request->get('vendor') != 'null' && $request->get('vendor') !== 'null')) {
            $query->where('vendor_id', $request->get('vendor'));
        }

        if ($request->has('activities') && !empty($request->get('activities') && $request->get('activities') != 'null' && $request->get('activities') !== 'null')) {
            $query->where('activities', $request->get('activities'));
        }

        if ($request->has('owners') && !empty($request->get('owners') && $request->get('owners') != 'null' && $request->get('owners') !== 'null')) {
            $query->where('owners', $request->get('owners'));
        }

        if ($request->trash < 1) {
            $query->withTrashed();
        } elseif ($request->trash > 1) {
            $query->onlyTrashed();
        }


        $query->orderBy('role_id', 'asc');

        return Query::open($query, $search, $counter);
    }

    public function dataFieldtech(Request $request)
    {
        $query = Fieldtech::where('vendor_id', $request->vendor);
        return Query::open($query, ['nik', 'name'], false);
    }

    public function push(Request $request, $id = null)
    {
        DB::beginTransaction();
        try {
            $input = [
                'role_id' => $request->input('role_id'),
                'username' => $request->input('username'),
                'email' => $request->input('email') ?: null,
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'description' => $request->input('description'),
                'role_id' => $request->input('role_id'),
                'vendor_id' => ($val = $request->input('vendor_id')) ? $val : null,
                'client_id' => ($val = $request->input('client_id')) ? $val : null,
                'fieldtech_id' => ($val = $request->input('fieldtech_id')) ? $val : null,
                'owners' => null,
                'activities' => null,
            ];

            if (($val = $request->input('owners')) && count($val) && $val[0]) {
                $val = array_map(function ($value) {
                    return intval($value);
                }, $val);
                $input['owners'] = $val;
            }
            if (($val = $request->input('activities')) && count($val) && $val[0]) {
                $val = array_map(function ($value) {
                    return intval($value);
                }, $val);
                $input['activities'] = $val;
            }

            if ($id) {
                if (!$input['email'] && Auth\User::where('email', $input['email'])->where('id', '<>', $id)->withTrashed()->first()) {
                    return ['success' => false, 'message' => 'Email Duplicate'];
                } else if (Auth\User::where('username', $input['username'])->where('id', '<>', $id)->withTrashed()->first()) {
                    return ['success' => false, 'message' => 'Username Duplicate'];
                }
                if ($password = $request->password) $input['password'] = Hash::make($password);
                $user = Auth\User::find($id);
                $user->update($input);
            } else {
                if ($input['email'] && Auth\User::where('email', $input['email'])->withTrashed()->first()) {
                    return ['success' => false, 'message' => 'Email Duplicate'];
                } else if (Auth\User::where('username', $input['username'])->withTrashed()->first()) {
                    return ['success' => false, 'message' => 'Username Duplicate'];
                }
                if ($password = $request->password) $input['password'] = Hash::make($password);
                else return ['success' => false, 'message' => 'Password Is Null'];
                $user = Auth\User::create($input);
            }

            if ($vendors = json_decode($request->input('vendors'))) {
                if (count($vendors)) {
                    $user->vendors()->sync($vendors);
                }
            }


            if ($password) {
                //Mail::to($user->email)->send(new ActiveUser($user, $password));
            }


            DB::commit();
            return ['success' => true, 'message' => 'Success...'];
        } catch (Exception $error) {
            DB::rollback();
            return ['success' => false, 'message' => '500 ' . $error->getMessage()];
        }
    }

    public function setRole(Request $request)
    {
        if ($data = json_decode($request->data)) {
            Auth\User::whereIn('id', $data)->update(['role_id' => $request->role_id]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function setPassword(Request $request)
    {
        if ($data = json_decode($request->data)) {
            Auth\User::whereIn('id', $data)->update(['password' => Hash::make($request->password)]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function restore(Request $request)
    {
        if ($data = json_decode($request->data)) {
            Auth\User::withTrashed()->whereIn('id', $data)->restore();
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function forceDelete(Request $request)
    {
        if ($data = json_decode($request->data)) {
            Auth\User::withTrashed()->whereIn('id', $data)->forcedelete();
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function delete(Request $request)
    {
        $user = $request->user();
        if ($data = json_decode($request->data)) {
            Auth\User::whereIn('id', $data)->update(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id]);
            return ['success' => true, 'message' => 'Success!'];
        }
        return ['success' => false, 'message' => 'No Data!'];
    }

    public function exportExcel(Request $request)
    {
        ini_set('memory_limit', '64048M');
        ini_set('max_execution_time', '300');

        $title = [];

        $title[] = ['User Manager', 'h2'];
        if ($request->input('role') !== null && $request->input('role') !== 'null') {
            $role = Role::find($request->input('role'));
            if ($role) {
                $title[] = ['Role: ' . $role->name, 'h4'];
            }
        }

        if ($request->input('client') !== null && $request->input('client') !== 'null') {
            $client = Client::find($request->input('client'));
            if ($client) {
                $title[] = ['Client: ' . $client->name, 'h4'];
            }
        }

        if ($request->input('vendor') !== null && $request->input('vendor') !== 'null') {
            $vendor = Vendor::find($request->input('vendor'));
            if ($vendor) {
                $title[] = ['Area: ' . $vendor->name, 'h4'];
            }
        }
        // if ($request->input('vendor' !== null && $request->input('vendor') !== 'null')) {
        //     $vendor = Vendor::find($request->input('vendor'));
        //     if ($vendor) {
        //         $title[] = ['Area: ' . $vendor->name, 'h4'];
        //     }
        // }
        // if ($request->input('vendor' !== null && $request->input('vendor') !== 'null')) {
        //     $vendor = Vendor::find($request->input('vendor'));
        //     if ($vendor) {
        //         $title[] = ['Area: ' . $vendor->name, 'h4'];
        //     }
        // }

        // dd($title, $request->all());


        $data = $this->data($request, false);

        $columns = [
            [
                'text' => 'ROLE',
                'dataIndex' => 'role',
                'width' => 150,
                'renderer' => function ($e) {
                    return $e ? $e->name : '-';
                }
            ],
            ['text' => 'USERNAME', 'dataIndex' => 'username', 'width' => 200],
            ['text' => 'NAME', 'dataIndex' => 'name', 'width' => 200],
            [
                'text' => 'AREA',
                'dataIndex' => 'vendor',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e ? $e->name : '-';
                }
            ],
            [
                'text' => 'MULTI AREA',
                'dataIndex' => 'vendors',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e  ? $e->pluck('name')->implode(', ') : '-';
                }
            ],
            [
                'text' => 'FIELDTECH',
                'dataIndex' => 'fieldtech',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e ? $e->name : '-';
                }
            ],
            ['text' => 'ACTIVITIES', 'dataIndex' => 'activities', 'width' => 200],
            ['text' => 'OWNER', 'dataIndex' => 'owners', 'width' => 200],
            [
                'text' => 'CLIENT',
                'dataIndex' => 'client',
                'width' => 200,
                'renderer' => function ($e) {
                    return $e ? $e->name : '-';
                }
            ],
            ['text' => 'EMAIL', 'dataIndex' => 'email', 'width' => 200],
            ['text' => 'PHONE', 'dataIndex' => 'phone', 'width' => 200],
            ['text' => 'DESCRIPTION', 'dataIndex' => 'description', 'width' => 200],
        ];

        $params = array(
            'title' => $title,
            'columns' => $columns,
            'data' => $data,
            'filename' => 'Users' . '-' . date('YmdHi'),
            'footer' => [config('app.name') . ' (' . date('d F Y H:i:s') . ')'],
        );

        return ExportExcel::export($params);
    }

    public function importFormat(Request $request)
    {
        $filename = 'user_format.xlsx';
        return Excel::download(new Format(), $filename);
    }

    public function importData(Request $request)
    {

        if ($upload = FileUpload::upload('file', 'user-import')) {
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
}
