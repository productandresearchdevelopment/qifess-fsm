<?php

namespace App\Imports\Fieldtechs;

use App\Models\Fieldteches\Fieldtech;
use App\Models\Vendors\Vendor;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

            if ($vendor_id = $rows[$i][0]) {
                $error = null;
                $uid = $this->user->id;

                $data = (object) [
                    'vendor_id' => $vendor_id,
                    'nik' => $rows[$i][2],
                    'name' => $rows[$i][3],
                    'address' => $rows[$i][4],
                    'email' => $rows[$i][5],
                    'photo' => null,
                    'fieldtech1' => $rows[$i][6],
                    'fieldtech2' => $rows[$i][7],
                    'vendor_name' => $rows[$i][8],
                    'phone' => $rows[$i][9],
                    'created_by' => $uid,
                    'updated_by' => $uid,
                ];

                if (!$data->name) {
                    $error = "Team Name Not Found";
                } else if (!Vendor::find($data->vendor_id)) {
                    $error = "Vendor Not Found";
                } else {
                    DB::beginTransaction();
                    try {
                        $team = Fieldtech::create((array) $data);

                        if ($team) {
                            DB::commit();
                            $totalSuccess++;
                        } else {
                            DB::rollback();
                            $error = "Failed to create team.";
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
