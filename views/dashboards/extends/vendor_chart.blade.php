<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Rank Area (SLA)
                </div>
            </div>

            <div class="card-body">
                <div id="chart-vendor"></div>
            </div>
        </div>
    </div>
</div>

<script>
    FusionCharts.ready(function() {
        var salesAnlysisChart = new FusionCharts({
            type: 'mscombi2d',
            renderAt: 'chart-vendor',
            width: '100%',
            height: '400',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "xAxisname": "Area",
                    "yAxisName": "Project Ticket",
                    "divlineColor": "#eee",
                    "toolTipColor": "#ffffff",
                    "toolTipBorderThickness": "0",
                    "toolTipBgColor": "#000000",
                    "toolTipBgAlpha": "70",
                    "toolTipBorderRadius": "5",
                    "toolTipPadding": "10",
                    "theme": "ocean"
                },
                "categories": [{
                    "category": [
                        @foreach($vendors AS $row)
                        {
                            "label": "{{ $row->name }}",
                            "plotToolText": "Store location: $label <br> Sales (YTD): $dataValue <br> $displayValue"
                        },
                        @endforeach
                    ]
                }],
                "dataset": [
                    {
                        "seriesName": "Total WO",
                        "showValues": "1",
                        "data": [
                            @foreach($vendors AS $row)
                            {
                                "value": "{{ $row->total }}",
                                "tooltext": "{{ $row->name }}<br>Total WO: {{ $row->total }}<br>On Target: {{ $row->ontarget }}<br>Not Achieved: {{ $row->total - $row->ontarget }}"
                            },
                            @endforeach
                        ]
                    },
                    {
                        "seriesName": "On Target",
                        "renderAs": "line",
                        "data": [
                            @foreach($vendors AS $row)
                            {
                                "value": "{{ $row->ontarget }}",
                                "tooltext": "{{ $row->name }}<br>Total WO: {{ $row->total }}<br>On Target: {{ $row->ontarget }}<br>Not Achieved: {{ $row->total - $row->ontarget }}"
                            },
                            @endforeach
                        ]
                    },
                    {
                        "seriesName": "Not Achieved",
                        "renderAs": "area",
                        "data": [
                            @foreach($vendors AS $row)
                            {
                                "value": "{{ $row->total - $row->ontarget }}",
                                "tooltext": "{{ $row->name }}<br>Total WO: {{ $row->total }}<br>On Target: {{ $row->ontarget }}<br>Not Achieved: {{ $row->total - $row->ontarget }}"
                            },
                            @endforeach
                        ]
                    }
                ]
            }
        }).render();
    });

</script>
