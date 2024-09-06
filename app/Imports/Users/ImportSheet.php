<?php

namespace App\Imports\Users;

use App\Models\Clients\Client;
use App\Models\Fieldteches\Fieldtech;
use App\Models\Vendors\Vendor;
use App\SystemModels\Auth\Role;
use App\SystemModels\Auth\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ImportSheet implements ToCollection, WithChunkReading
{
    public $logs = [];
    private $user = null;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection(Collection $rows)
    {
        $startLine = 4;
        $totalRow = 0;
        $totalSuccess = 0;
        $totalError = 0;
        $log = [];

        for ($i = $startLine; $i < count($rows); $i++) {
            if ($role_id = $rows[$i][0]) {
                $error = null;
                $data = (object) [
                    'role_id' => $role_id,
                    'username' => $rows[$i][2],
                    'name' => $rows[$i][3],
                    'vendor_id' => $rows[$i][4],
                    'fieldtech_id' => $rows[$i][6],
                    'activities' => $rows[$i][8] ?? null,
                    'owners' => $rows[$i][9] ?? null,
                    'client_id' => $rows[$i][10],
                    'email' => $rows[$i][12],
                    'password' => Hash::make($rows[$i][13]),
                    'token_fcm' => null,
                    'token_api' => null,
                    'token_api_expired_at' => null,
                    'phone' => $rows[$i][14],
                    'photo' => null,
                    'description' => $rows[$i][15],
                    'remember_token' => null,
                    'last_ip' => null,
                    'last_module' => null,
                    'last_url' => null,
                    'last_active' => null,
                ];

                if (!Role::find($role_id)->first()) $error = "Undefined Role ID ($role_id)";
                else if (!Vendor::find($data->vendor_id)) $error = "Undefined Area ($data->vendor_id)";
                else if (!Client::find($data->client_id)) $error = "Undefined Client ($data->client_id)";
                else if (!Fieldtech::find($data->fieldtech_id)) $error = "Undefined Fieldtech ($data->fieldtech_id)";
                else {
                    DB::beginTransaction();
                    try {
                        $user = User::create((array) $data);

                        if ($user) {
                            DB::commit();
                            $totalSuccess++;
                        } else {
                            $error = "Error on Create User";
                            DB::rollback();
                        }
                    } catch (QueryException $e) {
                        DB::rollback();
                        $error = $e->getMessage();
                    }
                }


                if ($error) {
                    $log[] = [
                        'row' => ($i + 1),
                        'success' => false,
                        'message' => $error
                    ];
                    $totalError++;
                }

                $totalRow++;
            }
        }

        $this->logs = [
            'totalRow' => $totalRow,
            'totalSuccess' => $totalSuccess,
            'totalError' => $totalError,
            'errorLog' => $log
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
