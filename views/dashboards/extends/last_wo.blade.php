<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-header">Work Order (Top Last Updated)</div>
            <div class="table-responsive">
                <table class="align-middle mb-0 table table-borderless table-striped table-hover" style="font-size: 14px">
                    <thead>
                    <tr>
                        <th class="text-center" width="80">Activity</th>
                        <th class="text-left" width="200">ID</th>
                        <th>Site</th>
                        <th width="200">Client</th>
                        <th width="100">Area</th>
                        <th width="150">Fieldtech</th>
                        <th class="text-center">Status</th>

                    </tr>
                    </thead>
                    <tbody>

                    @foreach($wo AS $row)

                    <tr>
                        <td class="text-center">
                            <div class="badge" style="background-color: #{{$row->activity->color}}; color: #fff">{{ $row->activity->alias }}</div>
                        </td>
                        <td class="text-left text-muted">#{{ $row->no_wo }}</td>
                        <td class="text-left">
                            {{ $row->site ? $row->site->name : ($row->removeSite ? $row->removeSite->name : '') }}
                        </td>
                        <td class="text-left">
                            {{ $row->site ? $row->site->client->name : ($row->removeSite ? $row->removeSite->client->name : '') }}
                        </td>
                        <td class="text-left">{{ $row->vendor ? $row->vendor->alias : '' }}</td>
                        <td class="text-left">{{ $row->fieldtech ? $row->fieldtech->name : '' }}</td>
                        <td class="text-center">
                            <div class="badge" style="background-color: #{{$row->lastAction->status->color}}; color: #fff">{{ $row->lastAction->status->alias }}</div>
                        </td>

                    </tr>

                    @endforeach

                    </tbody>
                </table>
            </div>
            <div class="d-block text-center card-footer">
                <a href="{{ route('wo') }}">
                    <button class="btn-wide btn btn-success">View All</button>
                </a>
            </div>
        </div>
    </div>
</div>
