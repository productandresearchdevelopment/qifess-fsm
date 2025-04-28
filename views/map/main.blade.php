@include('headers.head')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="{{ asset('js/leaflet.markercluster/dist/MarkerCluster.css') }}" />
<link rel="stylesheet" href="{{ asset('js/leaflet.markercluster/dist/MarkerCluster.Default.css') }}" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="{{ asset('js/leaflet.markercluster/dist/leaflet.markercluster-src.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<style>
  body,
  html {
    margin: 0;
    padding: 0;
  }

  #map {
    width: 100%;
    height: 100vh;
  }

  .legend,
  .summary,
  #siteSelectContainer {
    position: absolute;
    background: white;
    padding: 10px;
    border-radius: 5px;
    font-size: 14px;
    z-index: 9999;
  }

  .legend {
    bottom: 10px;
    left: 10px;
  }

  .summary {
    top: 10px;
    left: 10px;
    max-height: 160px;
    max-width: 880px;
    overflow-y: auto;
    overflow-x: auto;
  }

  .summary-table {
    border-collapse: collapse;
    font-size: 12px;
    width: 100%;
    min-width: 700px;
    /* Tetapkan lebar minimum tabel */
  }

  .summary-table th,
  .summary-table td {
    border: 1px solid #ddd;
    padding: 5px;
    white-space: nowrap;
    /* Hindari line break pada konten */
  }

  .summary-table th {
    background-color: #f2f2f2;
  }

  .summary-header {
    font-weight: bold;
    font-size: 14px;
    margin-bottom: 5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    left: 0;
    background: white;
    padding-bottom: 5px;
  }

  .export-btn {
    background-color: #f2f2f2;
    /* color: white; */
    border: none;
    padding: 6px 16px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
  }

  .export-btn:hover {
    background-color: #dedede;
  }

  .summary th {
    padding: 6px 10px;
  }

  #siteSelectContainer {
    top: 10px;
    right: 10px;
    width: 280px;
  }

  #siteSelectContainer label {
    font-weight: bold
  }

  #vendorSelect,
  #siteSelect,
  #fieldtechSelect,
  #activitySelect,
  #vendorNameInput,
  #startDate,
  #endDate {
    width: 100%;
    margin-bottom: 5px;
    padding: 5px;
    font-size: 14px;
    box-sizing: border-box;
  }

  .select2-dropdown.select2-dropdown--below {
    z-index: 9999;
  }

  .circle-marker {
    width: 15px;
    height: 15px;
    border-radius: 50%;
    border: 2px solid white;
  }

  .table-ticket {
    min-width: 100px;
    width: 100px;
    text-align: center;
    word-wrap: break-word;
  }

  .leaflet-popup-content {
    min-width: 800px;
    max-width: 1000px;
    overflow-x: auto;
  }

  .workorder-container table {
    width: 100%;
    border-collapse: collapse;
  }

  .workorder-container th,
  .workorder-container td {
    border: 1px solid #ddd;
    padding: 5px;
    text-align: center;
    font-size: 12px;
  }

  .workorder-container th {
    background-color: #f2f2f2;
  }

  .date-range-container {
    display: flex;
    gap: 5px;
  }

  .date-range-container>div {
    flex: 1;
  }

  .date-range-container input[type="date"] {
    width: 100%;
    font-size: 14px;
  }
</style>


<div id="siteSelectContainer">
  <label for="activitySelect">Select Activity:</label>
  <select id="activitySelect" class="form-control select2">
    <option value="all">All Activities</option>
    @foreach ($activities as $activity)
      <option value="{{ $activity['id'] }}">{{ $activity['alias'] }}</option>
    @endforeach
  </select>

  <label>Date Range :</label>
  <div class="date-range-container">
    <div>
      <label for="startDate">Start</label>
      <input type="date" id="startDate">
    </div>
    <div>
      <label for="endDate">End</label>
      <input type="date" id="endDate">
    </div>
  </div>

  <label for="vendorSelect">Select Area:</label>
  <select id="vendorSelect">
    <option value="all">All Area</option>
    @foreach ($vendors as $vendor)
      <option value="{{ $vendor['id'] }}">{{ $vendor['name'] }}</option>
    @endforeach
  </select>

  <label for="siteSelect">Select Site:</label>
  <select id="siteSelect">
    <option value="all">All Sites</option>
  </select>

  <label for="fieldtechSelect">Select Fieldtech:</label>
  <select id="fieldtechSelect" class="form-control select2">
    <option value="all">All Fieldtech</option>
  </select>

  <label for="vendorNameInput">Vendor:</label>
  <input type="text" id="vendorNameInput" placeholder="Search vendor ...">
</div>

<div class="summary" id="summaryContainer"></div>
<div id="map" style="width: 100%; height: 100vh;"></div>

<script>
  const vendors = @json($vendors);
  const activities = @json($activities);

  let map = L.map('map').setView([-2.5489, 118.0149], 5);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  let markerCluster = L.markerClusterGroup({
    disableClusteringAtZoom: 15
  });
  map.addLayer(markerCluster);

  let sites = [];

  function debounce(func, wait) {
    let timeout;
    return function() {
      const context = this,
        args = arguments;
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        func.apply(context, args);
      }, wait);
    };
  }

  function fetchSiteData() {
    const bounds = map.getBounds();
    const vendorId = $('#vendorSelect').val();
    const siteId = $('#siteSelect').val();
    const fieldtechId = $('#fieldtechSelect').val();
    const vendorName = $('#vendorNameInput').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const activityId = $('#activitySelect').val();

    const params = new URLSearchParams({
      min_lat: bounds.getSouth(),
      max_lat: bounds.getNorth(),
      min_lng: bounds.getWest(),
      max_lng: bounds.getEast(),
      vendor_id: vendorId,
      site_id: siteId,
      fieldtech_id: fieldtechId,
      activity_id: activityId
    });

    if (vendorName) {
      params.append('vendor_name', vendorName);
    }

    if (startDate && endDate) {
      params.append('start_date', startDate);
      params.append('end_date', endDate);
    }

    $('#summaryContainer').html('Loading...');

    const url = `{{ route('map.sites') }}?${params.toString()}`;

    return fetch(url)
      .then(res => res.json())
      .then(data => {
        sites = data;
        markerCluster.clearLayers();

        data.forEach(site => {
          if (!site.latitude || !site.longitude) return;

          let customIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div class="circle-marker" style="background-color: #${site.color_marker};"></div>`,
            iconSize: [15, 15],
            iconAnchor: [7, 7]
          });

          let popupContent = `
            <div class="workorder-container">
              <b>Site Name : ${site.name}
              <br>City : ${site.vendor}
              <br>Team : ${site.fieldtech_name}, Vendor : ${site.vendor_name}
              <br>Lat : ${site.latitude}, Long:  ${site.longitude} </b>
              <br>
              <div id="workorder-container-${site.id}">Loading work orders...</div>
            </div>
          `;

          let marker = L.marker([site.latitude, site.longitude], {
            icon: customIcon
          }).bindPopup(popupContent).on('click', function() {
            getWorkOrders(site.id, function(workOrders) {
              let workOrdersHtml = `<table>
                <thead>
                  <tr>
                    <th>Activity</th>
                    <th>Ticket ID</th>
                    <th>Status</th>
                    <th>Client</th>
                    <th>Site</th>
                    <th>Booking Date</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Duration Completion</th>
                  </tr>
                </thead>
                <tbody>`;

              workOrders.forEach(wo => {

                workOrdersHtml += `<tr>
                  <td>${wo.activity?.name || 'N/A'}</td>
                  <td>${wo.id || 'N/A'}</td>
                  <td>${wo.last_action?.status?.name || 'N/A'}</td>
                  <td>${wo.client?.name || 'N/A'}</td>
                  <td>${wo.site?.name || 'N/A'}</td>
                  <td>${wo.start_date || 'N/A'}</td>
                  <td>${wo.last_action?.lat || 'N/A'}</td>
                  <td>${wo.last_action?.long || 'N/A'}</td>
                  <td>${wo.duration_minutes ? wo.duration_minutes + ' minutes' : 'N/A'}</td>
                </tr>`;
              });

              workOrdersHtml += `</tbody></table>`;
              document.getElementById(`workorder-container-${site.id}`).innerHTML = workOrdersHtml;
            });
          });

          markerCluster.addLayer(marker);
        });

        if (!vendorName || $('#fieldtechSelect option').length <= 1) {
          loadFieldtechOptions();
        }

        updateSiteOptions(data);

        return data;
      })
      .catch(err => {
        console.error('Failed to fetch site data:', err);
      });
  }

  function getWorkOrders(siteId, callback) {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const activityId = $('#activitySelect').val();

    let url = "{{ route('map.workorders', ['site_id' => '__SITE_ID__']) }}".replace('__SITE_ID__', siteId);

    const params = new URLSearchParams();

    if (startDate && endDate) {
      params.append('start_date', startDate);
      params.append('end_date', endDate);
    }

    if (activityId !== 'all') {
      params.append('activity_id', activityId);
    }

    if (params.toString()) {
      url += '?' + params.toString();
    }

    fetch(url)
      .then(res => res.json())
      .then(callback)
      .catch(err => {
        console.error('Failed to fetch work orders:', err);
        callback([]);
      });
  }

  function updateSiteOptions(currentSites) {
    const siteSelect = $('#siteSelect');
    const currentSiteId = siteSelect.val();

    siteSelect.empty().append('<option value="all">All Sites</option>');

    if (currentSites && currentSites.length > 0) {
      currentSites.sort((a, b) => (a.name || '').localeCompare(b.name || ''));

      currentSites.forEach(site => {
        if (site.id) {
          siteSelect.append(`<option value="${site.id}">${site.name || 'Unnamed Site'}</option>`);
        }
      });
    }

    if (currentSiteId !== 'all' && siteSelect.find(`option[value="${currentSiteId}"]`).length) {
      siteSelect.val(currentSiteId);
    } else {
      siteSelect.val('all');
    }

    siteSelect.trigger('change.select2');
  }

  function loadSiteOptions() {
    fetchSiteData();
  }

  function loadFieldtechOptions() {
    const fieldtechMap = {};
    const currentFieldtechId = $('#fieldtechSelect').val() || 'all';

    sites.forEach(site => {
      if (site.fieldtech_id && site.fieldtech_name) {
        fieldtechMap[site.fieldtech_id] = {
          id: site.fieldtech_id,
          name: site.fieldtech_name,
          vendor_name: site.vendor_name
        };
      }
    });

    const fieldtechs = Object.values(fieldtechMap);

    fieldtechs.sort((a, b) => a.name.localeCompare(b.name));

    const fieldtechSelect = $('#fieldtechSelect');

    fieldtechSelect.empty().append('<option value="all">All Fieldtech</option>');

    fieldtechs.forEach(fieldtech => {
      fieldtechSelect.append(
        `<option value="${fieldtech.id}">${fieldtech.name}</option>`);
    });

    if (currentFieldtechId !== 'all' && fieldtechSelect.find(`option[value="${currentFieldtechId}"]`).length) {
      fieldtechSelect.val(currentFieldtechId);
    } else {
      fieldtechSelect.val('all');
    }

    fieldtechSelect.trigger('change.select2');
  }


  function loadSummary() {
    const vendorId = $('#vendorSelect').val();
    const siteId = $('#siteSelect').val();
    const fieldtechId = $('#fieldtechSelect').val();
    const vendorName = $('#vendorNameInput').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const activityId = $('#activitySelect').val();

    let url = "{{ route('map.summary') }}?";
    const params = new URLSearchParams();


    if (activityId !== 'all') params.append('activity_id', activityId);
    if (vendorId !== 'all') params.append('vendor_id', vendorId);
    if (siteId !== 'all') params.append('site_id', siteId);
    if (fieldtechId !== 'all') params.append('fieldtech_id', fieldtechId);
    if (vendorName) params.append('vendor_name', vendorName);
    if (startDate && endDate) {
      params.append('start_date', startDate);
      params.append('end_date', endDate);
    }

    url += params.toString();

    fetch(url)
      .then(res => res.json())
      .then(data => {
        if (!data.length) {
          $('#summaryContainer').html('<b>No data</b>');
          return;
        }

        let html = `
        <div class="summary-header">
          <span>Summary</span>
            <div>
                <button class="export-btn" id="exportSummary">EXPORT</button>
                <button class="export-btn" id="exportRawSummary">EXPORT RAW DATA</button>
            </div>
        </div>
        <div class="summary-table-wrapper">
          <table class="summary-table">
            <thead>
              <tr>
                <th>TEAM</th>
                <th>VENDOR</th>
                <th>AREA</th>
                <th>ACTIVITY TICKET</th>
                <th>TOTAL TICKET</th>
                <th>CLOSE TICKET</th>
                <th>COMPLETE RATE (%)</th>
                <th>DURATION COMPLETION</th>
              </tr>
            </thead>
            <tbody>`;

        data.forEach(item => {
          html += `<tr>
          <td>${item.name}</td>
          <td>${item.vendor_name}</td>
          <td>${item.vendor}</td>
          <td style="text-align: center;">${item.activity}</td>
          <td style="text-align: center;">${item.total_ticket}</td>
          <td style="text-align: center;">${item.closed_ticket ?? 0}</td>
          <td style="text-align: center;">${item.closed_percent} %</td>
          <td style="text-align: center;">${item.duration_minutes} Minutes</td>
        </tr>`;
        });

        html += `</tbody>
        </table>
      </div>`;
        $('#summaryContainer').html(html);

        $('#exportSummary').on('click', function() {
          console.log("Exporting summary...");
          const route = "{{ route('map.export.excel') }}";
          window.open(`${route}?${params.toString()}`, '_blank');
        });

        $('#exportRawSummary').on('click', function() {
          console.log("Exporting summary raw...");
          const route = "{{ route('map.export.raw.excel') }}";
          window.open(`${route}?${params.toString()}`, '_blank');
        });
      })
      .catch(err => {
        console.error("Error loading summary:", err);
        $('#summaryContainer').html('<b>Error loading summary</b>');
      });
  }

  const updateData = debounce(function() {
    fetchSiteData().then(() => {
      loadSummary();
    });
  }, 500);

  $('#activitySelect').on('change', function() {
    updateData();
  });

  $('#siteSelect').on('change', function() {
    const siteId = $(this).val();

    if (siteId === 'all') {
      map.setView([-2.5489, 118.0149], 5);
    } else {
      const selected = sites.find(s => s.id == siteId);
      if (selected && selected.latitude && selected.longitude) {
        map.setView([selected.latitude, selected.longitude], 15);
      }
    }

    updateData();
  });

  $('#vendorSelect').on('change', function() {
    updateData();
  });

  $('#fieldtechSelect').on('change', function() {
    updateData();
  });

  $('#vendorNameInput').on('input', debounce(function() {
    updateData();
  }, 500));

  $('#startDate, #endDate').on('change', function() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();

    if ((startDate && endDate) || (!startDate && !endDate)) {
      updateData();
    }
  });

  map.on('moveend zoomend', function() {
    fetchSiteData();
    loadSummary();
  });

  $(document).ready(function() {
    $('#vendorSelect, #siteSelect, #fieldtechSelect,#activitySelect').select2();
    fetchSiteData().then(() => {
      loadSummary();
    });
  });
</script>
