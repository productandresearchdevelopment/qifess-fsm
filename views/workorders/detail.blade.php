@include('headers.head')
@require('details.detail')

<style>
    * {scrollbar-width: thin; scrollbar-color: #EEE transparent;}
    ::-webkit-scrollbar {width: 5px; height: 4px;}
    ::-webkit-scrollbar-track {background: transparent;}
    ::-webkit-scrollbar-thumb {background-color: #EEE;}

    .x-body, .x-form-textarea, .x-form-text {font-family: Quicksand,helvetica,tahoma,verdana,sans-serif; font-size:13px;}
</style>

<div id="main-container"></div>

<script>
    var activities = @json($activities);
    var services = @json($services);
    var owners = @json($owners);
    var clients = @json($clients);
    var vendors = @json($vendors);
    var slots = @json($slots);
    var statusAction = @json($status);

    let detailWo = new DetailWo('#main-container');
    detailWo.load(@json($data));
</script>









