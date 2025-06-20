<?php

namespace App\Imports\Sites;

use App\Models\Clients\Client;
use App\Models\Masters\Master;
use App\Models\Services\Service;
use App\Models\Sites\Site;
use App\Models\Vendors\Vendor;
use App\Models\WorkOrders\Action;
use App\Models\WorkOrders\WorkOrder;
use App\Models\WorkOrders\WorkOrderOngoing;
use DateTime;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportSheet implements ToCollection, WithChunkReading
{
    public $logs = [];
    private $user = null;
    private $activity = null;

    public function __construct($user, $activity)
    {
        $this->user = $user;
        $this->activity = $activity;
    }

    public function collection(Collection $rows)
    {
        $startLine = 4;
        $totalRow = 0;
        $totalSuccess = 0;
        $totalError = 0;
        $log = [];

        $existingSites = Site::whereIn('link_id', $rows->pluck(0)->unique())->pluck('link_id')->toArray();

        $existingVendors = Vendor::whereIn('id', $rows->pluck(1)->unique())->pluck('id')->toArray();
        $existingClients = Client::whereIn('id', $rows->pluck(3)->unique())->pluck('id')->toArray();
        $existingServices = Service::whereIn('id', $rows->pluck(5)->unique())->pluck('id')->toArray();

        for ($i = $startLine; $i < count($rows); $i++) {
            if ($link_id = $rows[$i][0]) {
                $error = null;
                $uid = $this->user->id;
                $data = (object) [
                    'link_id' => $link_id,
                    'vendor_id' => $rows[$i][1],
                    'client_id' => $rows[$i][3],
                    'service_id' => $rows[$i][5],
                    'active_date' => $rows[$i][7] ? Date::excelToDateTimeObject($rows[$i][7])->format('Y-m-d') : date('Y-m-d'),
                    'name' => $rows[$i][8],
                    'pic' => $rows[$i][9],
                    'pic_phone' => $rows[$i][10],
                    'pic_email' => $rows[$i][11],
                    'province' => $rows[$i][12],
                    'city' => $rows[$i][13],
                    'district' => $rows[$i][14],
                    'ward' => $rows[$i][15],
                    'postal_code' => $rows[$i][16],
                    'address' => $rows[$i][17],
                    'description' => $rows[$i][18],
                    'lat' => $rows[$i][19],
                    'long' => $rows[$i][20],
                    'is_active' => 1,
                    'created_by' => $uid,
                    'updated_by' => $uid,
                ];

                if (in_array($link_id, $existingSites)) $error = "Duplicate Link ID ($link_id)";
                else if (!in_array($data->vendor_id, $existingVendors)) $error = "Undefined Vendor ($data->vendor_id)";
                else if (!in_array($data->client_id, $existingClients)) $error = "Undefined Client ($data->client_id)";
                else if (!in_array($data->service_id, $existingServices)) $error = "Undefined Service ($data->service_id)";
                else if (!$data->name) $error = "Name Not Found";
                else {
                    DB::beginTransaction();
                    try {
                        $site = Site::create((array) $data);
                        $ticketNumber = $rows[$i][21];
                        $ticketDescription = $rows[$i][22];
                        $fieldtech = $rows[$i][23];
                        $slot = $rows[$i][25];

                        if ($this->activity) {
                            $ticket = $this->createTicket($site, $ticketNumber, $ticketDescription, $fieldtech, $slot);
                        }

                        if ($ticket->success) {
                            DB::commit();
                            $totalSuccess++;
                        } else {
                            $error = $ticket->message;
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

    private function createTicket($site, $ticketNumber, $ticketDescription, $fieldtech, $slot)
    {
        try {
            $status = ($this->activity->id == 5) ? 5110 : 1110;

            $input = [
                'site_id' => $site->id,
                'activity_id' => $this->activity->id,
                'vendor_id' => $site->vendor_id,
                'client_id' => $site->client_id,
                'fieldtech_id' => $fieldtech,
                'service_id' => $site->service_id,
                'no_wo' => $ticketNumber,
                'description' => $ticketDescription,
                'start_date' => $site->active_date,
                'slot_id' => $slot,
                'last_action' => null,
                'created_by' => $this->user->id,
                'updated_by' => $this->user->id,
            ];

            $wo = WorkOrder::create($input);

            $action = Action::create([
                'wo_id' => $wo->id,
                'status_id' => $status,
                'note' => '-',
                'created_by' => $this->user->id,
                'updated_by' => $this->user->id,
            ]);

            $wo->update(['last_action' => $action->id]);

            WorkOrderOngoing::create(array_merge($input, [
                'last_action' => $action->id,
                'wo_id' => $wo->id,
                'id' => $wo->id,
                'is_hold' => 0,
                'close_date' => null,
            ]));

            return (object)['success' => true, 'message' => 'Success...'];
        } catch (QueryException $e) {
            return (object)['success' => false, 'message' =>  $e->getMessage()];
        }
    }

    private function getTeam($date, $vendor)
    {
        $sql = "SELECT A.id, MAX(B.slot_id) slot, SUM(IF(B.id, 1, 0)) count
                FROM po_m_fieldtech A LEFT JOIN po_wo B ON A.id = B.fieldtech_id AND B.deleted_at IS NULL AND B.start_date = '$date'
                WHERE A.vendor_id = '$vendor' GROUP BY A.id HAVING count < 2 ORDER BY count, slot LIMIT 1";
        $data = DB::select(DB::raw($sql));
        if (count($data)) {
            $data = $data[0];
            $id = $data->id;
            $slot = $data->slot;
            if ($slot == 1) $slot = 2;
            else if ($slot == 2) $slot = 1;
            else $slot = 1;

            return (object) ['fieldtech' => $id, 'slot' => $slot];
        }
        return null;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
