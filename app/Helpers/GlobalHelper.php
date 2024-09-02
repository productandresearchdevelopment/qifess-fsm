<?php
function isMobile(){
    $agent = new Jenssegers\Agent\Agent();
    return $agent->isMobile();
}

function hari($date=null){
    if($date){
        $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
        $d = date('N', strtotime($date));
        return $days[$d-1];
    }
    return null;
}

function bulan($date=null){
    if($date){
        $month = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'July', 'September', 'Oktober', 'November', 'Desember'];
        $d = date('n', strtotime($date)) * 1;
        return $month[$n-1];
    }
    return null;
}
