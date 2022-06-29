<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card mb-3 widget-content">
            <div class="widget-content-outer">
                <a href="{{ route('site') }}" style="text-decoration: none; color: #495057">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading">Total Sites</div>
                            <div class="widget-subheading">
                                Total Active {{ $totalSiteActive }},
                                Total Inactive {{ $totalSite - $totalSiteActive }}
                            </div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-success">{{ $totalSite }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card mb-3 widget-content">
            <div class="widget-content-outer">
                <a href="{{ route('client') }}" style="text-decoration: none; color: #495057">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading">Total Client</div>
                            <div class="widget-subheading">All Client</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-warning">{{ $totalClient }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card mb-3 widget-content">
            <div class="widget-content-outer">
                <a href="{{ route('vendor') }}" style="text-decoration: none; color: #495057">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading">Total Area</div>
                            <div class="widget-subheading">All Area</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-danger">{{ $totalVendor }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card mb-3 widget-content">
            <div class="widget-content-outer">
                <a href="{{ route('fieldtech') }}" style="text-decoration: none; color: #495057">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="widget-heading">Total Fieldtech</div>
                            <div class="widget-subheading">All Fieldtech Area</div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-dark">{{ $totalFieldtech }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
{{--    <div class="col-md-6 col-xl-3">--}}
{{--        <div class="card mb-3 widget-content">--}}
{{--            <div class="widget-content-outer">--}}
{{--                <div class="widget-content-wrapper">--}}
{{--                    <div class="widget-content-left">--}}
{{--                        <div class="widget-heading">Install Part</div>--}}
{{--                        <div class="widget-subheading">Total Install Part</div>--}}
{{--                    </div>--}}
{{--                    <div class="widget-content-right">--}}
{{--                        <div class="widget-numbers text-info">{{ $part->install }}</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="col-md-6 col-xl-3">--}}
{{--        <div class="card mb-3 widget-content">--}}
{{--            <div class="widget-content-outer">--}}
{{--                <div class="widget-content-wrapper">--}}
{{--                    <div class="widget-content-left">--}}
{{--                        <div class="widget-heading">Dismantle Part</div>--}}
{{--                        <div class="widget-subheading">Total Dismantle Part</div>--}}
{{--                    </div>--}}
{{--                    <div class="widget-content-right">--}}
{{--                        <div class="widget-numbers text-success">{{ $part->remove }}</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>
