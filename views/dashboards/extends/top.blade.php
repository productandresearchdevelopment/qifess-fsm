<style>
    .widget-heading{font-size: 17px}
    .widget-content-wrapper td{font-size: 12px; padding: 5px 0px}
    .total-top .box{display: inline-block; color: #fff; font-size: 10px; padding: 1px 5px; border-radius: 3px; margin-left: 5px}
</style>

<div class="row">
    <div class="col-md-6 col-xl-6">
        <div class="card mb-3 widget-content bg-midnight-bloom">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">All Workorders</div>
                    <div class='widget-subheading'>ON-GOING ({{ $totalOngoingTicket }} TICKET) + ARCHIVE: ({{ $totalAllTicket - $totalOngoingTicket }} TICKET)</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{ $totalAllTicket }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-6">
        <div class="card mb-3 widget-content bg-grow-early">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">Ongoing Workorders</div>
                    <div class="widget-subheading">Total Ticket</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{ $totalOngoingTicket }}</span></div>
                </div>
            </div>
        </div>
    </div>
    {{--
    <div class="col-md-6 col-xl-3">
        <div class="card mb-3 widget-content bg-grow-early">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">Ongoing</div>
                    <div class="widget-subheading">Total Ticket Ongoing</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{ $totalTicketOngoing }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card mb-3 widget-content bg-premium-dark">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">Ongoing Installation</div>
                    <div class="widget-subheading">Total Ongoing Installation</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-warning"><span>{{ $totalTicketOngoingInstall }}</span></div>
                </div>
            </div>
        </div>
    </div>
    --}}
</div>

<div class="row total-top">
    <div class="col-md-6 col-xl-6">
        <div class="card mb-3 widget-content">
            <div class="widget-content-outer">
                <a href="{{ route('site') }}" style="text-decoration: none; color: #495057">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading" style="margin-bottom: 10px">Total All Workorder (By Activities)</div>
                            <div>
                                <table cellspacing="0" cellpadding="0">
                                    @foreach($allTicket AS $r)
                                        <tr>
                                            <td width="220">
                                                <span style="color: #333">{{ $r->name }}</span>
                                                <div class="box" style="background-color: #{{ $r->color }};">{{ $r->alias }}</div>
                                            </td>
                                            <td width="5">:</td>
                                            <td width="90" align="right"><b>{{ $r->count }}</b> Ticket</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-6">
        <div class="card mb-3 widget-content">
            <div class="widget-content-outer">
                <a href="{{ route('site') }}" style="text-decoration: none; color: #495057">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading" style="margin-bottom: 10px">Total Ongoing Workorder (By Activities)</div>
                            <div>
                                <table cellspacing="0" cellpadding="0">
                                    @foreach($ongoingTicket AS $r)
                                        <tr>
                                            <td width="220">
                                                <span style="color: #333">{{ $r->name }}</span>
                                                <div class="box" style="background-color: #{{ $r->color }};">{{ $r->alias }}</div>
                                            </td>
                                            <td width="5">:</td>
                                            <td width="90" align="right"><b>{{ $r->count }}</b> Ticket</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
