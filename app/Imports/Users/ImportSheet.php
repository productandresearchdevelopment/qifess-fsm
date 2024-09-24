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

        for ($i = $startLine; $i < count($rows); $i++) {;

            if ($role_id = $rows[$i][0]) {
                $error = null;
                $role = Role::find($role_id);

                if (!$role) {
                    $error = "Undefined Role ID ($role_id)";
                } else {
                    $data = (object) [
                        'role_id' => $role_id,
                        'username' => $rows[$i][2],
                        'name' => $rows[$i][3],
                        'vendor_id' => $role->name === 'AREA LEADER' ? null : ($rows[$i][4] ?? null),
                        'fieldtech_id' => $rows[$i][6] ?? null,
                        'activities' => $rows[$i][8] ?? null,
                        'owners' => $rows[$i][9] ?? null,
                        'client_id' => $rows[$i][10] ?? null,
                        'email' => $rows[$i][12] ?? null,
                        'password' => Hash::make($rows[$i][13]),
                        'token_fcm' => null,
                        'token_api' => null,
                        'token_api_expired_at' => null,
                        'phone' => $rows[$i][14] ?? null,
                        'photo' => null,
                        'description' => $rows[$i][15] ?? null,
                        'remember_token' => null,
                        'last_ip' => null,
                        'last_module' => null,
                        'last_url' => null,
                        'last_active' => null,
                    ];

                    if ($role->name === 'AREA LEADER' && empty($rows[$i][4])) {
                        $error = "AREA must be provided for AREA LEADER role.";
                    } elseif ($role->name === 'FIELDTECH' && (empty($data->vendor_id) || empty($data->fieldtech_id))) {
                        $error = "AREA and TEAM must be provided for FIELDTECH role.";
                    } else {
                        if (!$error) {
                            DB::beginTransaction();
                            try {
                                $user = User::create((array) $data);
                                if ($user) {
                                    if ($role->name === 'AREA LEADER' && !empty($rows[$i][4])) {
                                        $user->vendors()->attach($rows[$i][4]);
                                    }
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
