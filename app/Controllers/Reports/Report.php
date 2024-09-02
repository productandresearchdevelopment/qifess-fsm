<?php

namespace App\Controllers\Reports;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WorkOrders\WorkOrder AS Wo;

class Report extends Controller
{
    public function woPdf(Request $request, $id = null){
        $user = $request->user();
        $view   = 'reports.wo_pdf';
        $data = Wo::find($id);
        $params = [
            'user' => $user,
            'data' => $data
        ];
        //return view($view, $params);
        $html = view($view, $params);
        $pdf = PDF::loadHTML($html);
        return $pdf->stream("DOCUMENT-".$data->id.".pdf");
    }

    public function bastPdf(Request $request, $id = null){
        $user = $request->user();
        $view   = 'reports.bast_pdf';
        $data = Wo::find($id);

        $params = [
            'user' => $user,
            'data' => $data
        ];
        //return view($view, $params);
        $html = view($view, $params);
        $pdf = PDF::loadHTML($html);
        return $pdf->stream("BAST-".$data->id.".pdf");
    }


}
