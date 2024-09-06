<?php

namespace App\Imports\Vendors;

use App\Models\Vendors\Vendor;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $startLine = 3;
        $totalRow = 0;
        $totalSuccess = 0;
        $totalError = 0;
        $log = [];

        for ($i = $startLine; $i < count($rows); $i++) {

            if ($name = $rows[$i][0]) {
                $error = null;
                $uid = $this->user->id;

                $color = !empty($rows[$i][2]) ? $rows[$i][2] : $this->generateDarkColor();

                Log::info("Generated color: " . $color);

                $data = (object) [
                    'name' => $name,
                    'alias' => $rows[$i][1],
                    'color' => $color,
                    'address' => $rows[$i][3],
                    'phone' => $rows[$i][4],
                    'email' => $rows[$i][5],
                    'description' => $rows[$i][6],
                    'created_by' => $uid,
                    'updated_by' => $uid,
                ];

                if (!$data->name) {
                    $error = "Vendor Name Not Found";
                } else {
                    DB::beginTransaction();
                    try {
                        $vendor = Vendor::create((array) $data);

                        if ($vendor) {
                            DB::commit();
                            $totalSuccess++;
                        } else {
                            DB::rollback();
                            $error = "Failed to create vendor.";
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

    public function generateDarkColor()
    {
        $r = rand(0, 50);
        $g = rand(0, 50);
        $b = rand(0, 50);

        return sprintf("%02x%02x%02x", $r, $g, $b);
    }
}
