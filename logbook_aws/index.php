<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>LOGBOOK AWS — Dashboard QC</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
<style>
:root {
    --bg: #f0f2f5;
    --surface: #ffffff;
    --surface-2: #f7f8fa;
    --border: #e4e7ec;
    --text-primary: #111827;
    --text-secondary: #6b7280;
    --text-muted: #9ca3af;
    --accent: #1a1a2e;
    --accent-2: #16213e;
    --green: #10b981;
    --green-bg: #d1fae5;
    --orange: #f59e0b;
    --orange-bg: #fef3c7;
    --red: #ef4444;
    --red-bg: #fee2e2;
    --blue: #3b82f6;
    --blue-bg: #dbeafe;
    --sidebar-w: 240px;
    --nav-h: 60px;
    --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
    --shadow-hover: 0 4px 16px rgba(0,0,0,.10);
    --radius: 12px;
}
[data-theme="dark"] {
    --bg: #0f1117;
    --surface: #1a1d27;
    --surface-2: #22263a;
    --border: #2d3148;
    --text-primary: #f1f5f9;
    --text-secondary: #94a3b8;
    --text-muted: #64748b;
    --accent: #e2e8f0;
    --accent-2: #cbd5e1;
    --green-bg: rgba(16,185,129,.12);
    --orange-bg: rgba(245,158,11,.12);
    --red-bg: rgba(239,68,68,.12);
    --blue-bg: rgba(59,130,246,.12);
    --shadow: 0 1px 3px rgba(0,0,0,.3), 0 4px 12px rgba(0,0,0,.2);
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text-primary);
    transition: background .3s, color .3s;
    overflow-x: hidden;
}

/* NAVBAR */
.topnav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
    height: var(--nav-h);
    background: #111827;
    display: flex; align-items: center;
    padding: 0 24px;
    gap: 16px;
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.topnav .brand {
    font-size: 16px; font-weight: 700; color: #fff;
    letter-spacing: .5px; white-space: nowrap;
    display: flex; align-items: center; gap: 8px;
}
.topnav .brand .dot { width: 8px; height: 8px; border-radius: 50%; background: #10b981; }
.nav-links { display: flex; gap: 4px; margin-left: 24px; }
.nav-links a {
    color: rgba(255,255,255,.65); font-size: 13.5px; font-weight: 500;
    padding: 6px 14px; border-radius: 8px; text-decoration: none;
    transition: all .2s;
}
.nav-links a:hover, .nav-links a.active { background: rgba(255,255,255,.1); color: #fff; }
.nav-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }
.date-range-badge {
    display: flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.07); color: rgba(255,255,255,.8);
    font-size: 12px; font-weight: 500; padding: 5px 12px; border-radius: 8px;
    border: 1px solid rgba(255,255,255,.1);
}
.date-range-badge i { color: #60a5fa; font-size: 13px; }
.refresh-badge {
    display: flex; align-items: center; gap: 6px;
    background: rgba(16,185,129,.15); color: #10b981;
    font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 20px;
}
.refresh-badge .blink { width: 6px; height: 6px; border-radius: 50%; background: #10b981; animation: blink 1.5s infinite; }
@keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: .2; } }
.dark-toggle {
    background: rgba(255,255,255,.08); border: none; color: #fff;
    width: 36px; height: 36px; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: 16px;
    transition: background .2s;
}
.dark-toggle:hover { background: rgba(255,255,255,.15); }

/* MAIN LAYOUT */
.main-wrap { padding-top: var(--nav-h); min-height: 100vh; }
.content-area { padding: 28px 28px 48px; max-width: 1440px; margin: 0 auto; }

/* FILTER BAR */
.filter-bar {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 16px 20px;
    display: flex; flex-wrap: wrap; gap: 12px; align-items: center;
    margin-bottom: 24px;
    box-shadow: var(--shadow);
}
.filter-bar label { font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
.filter-bar select, .filter-bar input {
    background: var(--surface-2); border: 1px solid var(--border);
    color: var(--text-primary); border-radius: 8px; padding: 6px 12px;
    font-size: 13.5px; font-family: 'DM Sans', sans-serif;
    min-width: 140px; transition: border .2s;
}
.filter-bar select:focus, .filter-bar input:focus { outline: none; border-color: #3b82f6; }
.filter-group { display: flex; align-items: center; gap: 8px; }
.btn-apply {
    background: #111827; color: #fff; border: none;
    border-radius: 8px; padding: 7px 18px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: opacity .2s;
}
.btn-apply:hover { opacity: .85; }
.btn-reset {
    background: var(--surface-2); color: var(--text-secondary); border: 1px solid var(--border);
    border-radius: 8px; padding: 7px 16px; font-size: 13px; font-weight: 500;
    cursor: pointer; transition: background .2s;
}
.btn-reset:hover { background: var(--border); }

/* PAGE HEADER */
.page-header { margin-bottom: 24px; }
.page-header .breadcrumb-hint { font-size: 12px; color: var(--text-muted); font-weight: 500; margin-bottom: 4px; }
.page-header h1 { font-size: 22px; font-weight: 700; color: var(--text-primary); }

/* STAT CARDS */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
@media(max-width:768px) { .stats-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:480px) { .stats-grid { grid-template-columns: 1fr; } }

.stat-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 20px 22px;
    box-shadow: var(--shadow); transition: box-shadow .2s, transform .2s;
    position: relative; overflow: hidden;
}
.stat-card:hover { box-shadow: var(--shadow-hover); transform: translateY(-1px); }
.stat-card .card-label {
    font-size: 11.5px; font-weight: 600; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .6px; margin-bottom: 12px;
    display: flex; align-items: center; gap: 6px;
}
.stat-card .card-value {
    font-size: 36px; font-weight: 700; line-height: 1; color: var(--text-primary);
    font-variant-numeric: tabular-nums;
}
.stat-card .card-value .unit { font-size: 18px; font-weight: 500; color: var(--text-secondary); margin-left: 2px; }
.stat-card .card-icon {
    position: absolute; right: 18px; top: 18px;
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 18px;
}
.stat-card.green .card-icon { background: var(--green-bg); color: var(--green); }
.stat-card.blue  .card-icon { background: var(--blue-bg);  color: var(--blue);  }
.stat-card.red   .card-icon { background: var(--red-bg);   color: var(--red);   }
.stat-card.orange .card-icon { background: var(--orange-bg); color: var(--orange); }
.stat-card .progress-bar-wrap { margin-top: 14px; height: 4px; background: var(--border); border-radius: 2px; overflow: hidden; }
.stat-card .progress-bar-fill { height: 100%; border-radius: 2px; transition: width .8s ease; }
.stat-card.green .progress-bar-fill { background: var(--green); }
.stat-card.orange .progress-bar-fill { background: var(--orange); }
.stat-card.red .progress-bar-fill { background: var(--red); }

/* SKELETON ANIMATION */
.skeleton { animation: skeleton-pulse 1.5s ease-in-out infinite; background: linear-gradient(90deg, var(--border) 25%, var(--surface-2) 50%, var(--border) 75%); background-size: 200% 100%; border-radius: 6px; }
@keyframes skeleton-pulse { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
.skeleton-text { height: 42px; width: 80px; display: inline-block; }
.skeleton-small { height: 14px; width: 60px; display: inline-block; }

/* TWO COL LAYOUT */
.two-col { display: grid; grid-template-columns: 1fr 420px; gap: 20px; margin-bottom: 20px; }
@media(max-width:1100px) { .two-col { grid-template-columns: 1fr; } }

/* CARD GENERIC */
.panel {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden;
}
.panel-header {
    padding: 18px 22px 0; display: flex; align-items: center; gap: 10px;
}
.panel-title { font-size: 14px; font-weight: 700; color: var(--text-primary); }
.panel-sub { font-size: 12px; color: var(--text-muted); margin-left: auto; }
.panel-body { padding: 18px 22px 22px; }

/* CHART AREA */
.chart-wrap { position: relative; height: 360px; }

/* MAP */
#aws-map { height: 400px; border-radius: 0 0 var(--radius) var(--radius); }

/* TABLE SECTION */
.table-section { margin-bottom: 20px; }
.table-toolbar {
    padding: 16px 22px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.export-btn {
    display: flex; align-items: center; gap: 5px;
    background: var(--surface-2); border: 1px solid var(--border);
    color: var(--text-secondary); border-radius: 7px; padding: 6px 12px;
    font-size: 12.5px; font-weight: 600; cursor: pointer; text-decoration: none;
    transition: all .2s;
}
.export-btn:hover { background: #111827; color: #fff; border-color: #111827; }
.export-btn.excel:hover { background: #16a34a; border-color: #16a34a; }
.export-btn.pdf:hover   { background: #dc2626; border-color: #dc2626; }

/* STATUS BADGE */
.badge-status {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 20px;
}
.badge-status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
.badge-good    { background: var(--green-bg);  color: #059669; }
.badge-good::before { background: #059669; }
.badge-warning { background: var(--orange-bg); color: #d97706; }
.badge-warning::before { background: #d97706; }
.badge-critical{ background: var(--red-bg);   color: #dc2626; }
.badge-critical::before { background: #dc2626; }

.badge-type {
    font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 5px;
    font-family: 'JetBrains Mono', monospace; letter-spacing: .3px;
}
.badge-type.aws  { background: var(--blue-bg); color: #2563eb; }
.badge-type.arg  { background: rgba(139,92,246,.12); color: #7c3aed; }
.badge-type.aaws { background: rgba(16,185,129,.12); color: #059669; }
.badge-type.asrs { background: rgba(245,158,11,.15); color: #d97706; }

/* DATATABLES CUSTOM */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    background: var(--surface-2); border: 1px solid var(--border);
    color: var(--text-primary); border-radius: 7px; padding: 5px 10px;
    font-family: 'DM Sans', sans-serif; font-size: 13px;
}
.dataTables_wrapper .dataTables_filter input:focus { outline: none; border-color: var(--blue); }
table.dataTable thead th {
    background: var(--surface-2); color: var(--text-muted);
    font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid var(--border) !important;
    padding: 12px 14px;
}
table.dataTable tbody td {
    font-size: 13.5px; color: var(--text-primary);
    border-bottom: 1px solid var(--border) !important;
    padding: 11px 14px; vertical-align: middle;
}
table.dataTable tbody tr:hover td { background: var(--surface-2); }
table.dataTable { border: none !important; }
.dataTables_wrapper { padding: 18px 22px 22px; }

/* LEGEND */
.chart-legend { display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
.legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; color: var(--text-secondary); }
.legend-dot { width: 10px; height: 10px; border-radius: 2px; }

/* SECTION TITLE */
.section-title { font-size: 13px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.section-title::after { content: ''; flex: 1; height: 1px; background: var(--border); }

/* TOAST */
.toast-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
.toast-item {
    background: #111827; color: #fff; padding: 12px 18px; border-radius: 10px;
    font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px;
    animation: toast-in .3s ease; box-shadow: 0 8px 24px rgba(0,0,0,.3);
}
@keyframes toast-in { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

/* MINI SPARKLINE */
.pct-bar { display: flex; height: 6px; border-radius: 3px; overflow: hidden; gap: 1px; min-width: 80px; }
.pct-bar .seg { height: 100%; }
.pct-bar .seg-v { background: #10b981; }
.pct-bar .seg-i { background: #f59e0b; }
.pct-bar .seg-m { background: #d1d5db; }

/* SUMMARY ROW */
.summary-row { display: flex; gap: 6px; align-items: center; font-size: 11.5px; color: var(--text-muted); margin-top: 8px; font-family: 'JetBrains Mono', monospace; }
.summary-val { color: var(--text-primary); font-weight: 500; }

/* SCROLL FADE */
.fade-in { animation: fadeIn .4s ease forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="topnav">
  <div class="brand">
    <div class="dot"></div>
    LOGBOOK AWS
  </div>
  <div class="nav-links">
    <a href="#" class="active"><i class="bi bi-grid-1x2-fill me-1"></i>Dashboard QC</a>
    <a href="#peta"><i class="bi bi-map me-1"></i>Peta</a>
    <a href="#tabel"><i class="bi bi-table me-1"></i>Logbook</a>
  </div>
  <div class="nav-right">
    <div class="date-range-badge" id="date-range-badge" title="Range tanggal data tersedia">
      <i class="bi bi-calendar-range"></i>
      <span id="nav-date-range">—</span>
    </div>
    <div class="refresh-badge">
      <div class="blink"></div>
      <span id="last-update">Live</span>
    </div>
    <button class="dark-toggle" id="dark-toggle" title="Toggle dark mode">
      <i class="bi bi-moon-stars-fill"></i>
    </button>
  </div>
</nav>

<div class="main-wrap">
<div class="content-area">

  <!-- PAGE HEADER -->
  <div class="page-header fade-in">
    <div class="breadcrumb-hint">Preview &nbsp;/&nbsp; Dashboard</div>
    <h1>Dashboard QC AWS</h1>
  </div>

  <!-- FILTER BAR -->
  <div class="filter-bar fade-in">
    <div class="filter-group">
      <label><i class="bi bi-calendar3"></i> Dari</label>
      <input type="date" id="f-tanggal-from" title="Tanggal mulai"/>
    </div>
    <div class="filter-group">
      <label><i class="bi bi-calendar3"></i> Sampai</label>
      <input type="date" id="f-tanggal-to" title="Tanggal akhir"/>
    </div>
    <div class="filter-group">
      <label><i class="bi bi-building"></i> Kota</label>
      <select id="f-kota"><option value="">Semua Kota</option></select>
    </div>
    <div class="filter-group">
      <label><i class="bi bi-cpu"></i> Tipe</label>
      <select id="f-tipe">
        <option value="">Semua Tipe</option>
        <option value="aws">AWS</option>
        <option value="arg">ARG</option>
        <option value="aaws">AAWS</option>
        <option value="asrs">ASRS</option>
      </select>
    </div>
    <button class="btn-apply" id="btn-apply"><i class="bi bi-funnel me-1"></i>Terapkan</button>
    <button class="btn-reset" id="btn-reset"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</button>
  </div>

  <!-- STAT CARDS -->
  <div class="stats-grid fade-in">
    <div class="stat-card">
      <div class="card-icon" style="background:#f3f4f6;color:#374151"><i class="bi bi-broadcast"></i></div>
      <div class="card-label"><i class="bi bi-circle-fill" style="color:#374151;font-size:7px"></i> Jumlah Peralatan AWS</div>
      <div class="card-value" id="stat-total"><span class="skeleton skeleton-text"></span></div>
    </div>
    <div class="stat-card blue">
      <div class="card-icon"><i class="bi bi-activity"></i></div>
      <div class="card-label"><i class="bi bi-circle-fill" style="color:var(--blue);font-size:7px"></i> AWS Aktif Mengirim Data</div>
      <div class="card-value" id="stat-active"><span class="skeleton skeleton-text"></span></div>
    </div>
    <div class="stat-card red">
      <div class="card-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
      <div class="card-label"><i class="bi bi-circle-fill" style="color:var(--red);font-size:7px"></i> AWS Bermasalah</div>
      <div class="card-value" id="stat-problem"><span class="skeleton skeleton-text"></span></div>
    </div>
    <div class="stat-card green">
      <div class="card-icon"><i class="bi bi-check-circle-fill"></i></div>
      <div class="card-label"><i class="bi bi-circle-fill" style="color:var(--green);font-size:7px"></i> Jumlah Valid Data</div>
      <div class="card-value" id="stat-valid"><span class="skeleton skeleton-text"></span></div>
      <div class="progress-bar-wrap"><div class="progress-bar-fill" id="bar-valid" style="width:0%"></div></div>
    </div>
    <div class="stat-card orange">
      <div class="card-icon"><i class="bi bi-dash-circle-fill"></i></div>
      <div class="card-label"><i class="bi bi-circle-fill" style="color:var(--orange);font-size:7px"></i> Jumlah Invalid Data</div>
      <div class="card-value" id="stat-invalid"><span class="skeleton skeleton-text"></span></div>
      <div class="progress-bar-wrap"><div class="progress-bar-fill" id="bar-invalid" style="width:0%"></div></div>
    </div>
    <div class="stat-card" style="">
      <div class="card-icon" style="background:#f3f4f6;color:#6b7280"><i class="bi bi-slash-circle-fill"></i></div>
      <div class="card-label"><i class="bi bi-circle-fill" style="color:#9ca3af;font-size:7px"></i> Jumlah Missing Data</div>
      <div class="card-value" id="stat-missing"><span class="skeleton skeleton-text"></span></div>
      <div class="progress-bar-wrap"><div class="progress-bar-fill" id="bar-missing" style="width:0%;background:#9ca3af"></div></div>
    </div>
  </div>

  <!-- CHART + MAP ROW -->
  <div class="two-col fade-in">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">Type vs Data Quality</span>
        <span class="panel-sub">Per parameter sensor</span>
      </div>
      <div class="panel-body">
        <div class="chart-legend">
          <div class="legend-item"><div class="legend-dot" style="background:#10b981"></div> Valid</div>
          <div class="legend-item"><div class="legend-dot" style="background:#f59e0b"></div> Invalid</div>
          <div class="legend-item"><div class="legend-dot" style="background:#d1d5db"></div> Missing</div>
        </div>
        <div class="chart-wrap">
          <canvas id="chart-quality"></canvas>
        </div>
      </div>
    </div>
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title"><i class="bi bi-geo-alt-fill me-1" style="color:var(--red)"></i>Peta Lokasi AWS</span>
        <span class="panel-sub" id="map-count">—</span>
      </div>
      <div id="aws-map"></div>
    </div>
  </div>

  <!-- TABLE SECTION -->
  <div class="panel table-section fade-in" id="tabel">
    <div class="table-toolbar">
      <span class="panel-title"><i class="bi bi-table me-2"></i>Data Station</span>
      <div style="margin-left:auto;display:flex;gap:8px;">
        <a href="#" class="export-btn excel" id="btn-excel"><i class="bi bi-file-earmark-excel-fill"></i> Excel</a>
        <a href="#" class="export-btn pdf"   id="btn-pdf">  <i class="bi bi-file-earmark-pdf-fill"></i>  PDF</a>
      </div>
    </div>
    <div class="dataTables_wrapper">
      <table id="station-table" class="table dataTable" style="width:100%">
        <thead>
          <tr>
            <th>ID Station</th>
            <th>Nama Station</th>
            <th>Kota</th>
            <th>Tipe</th>
            <th>Tanggal</th>
            <th>Valid %</th>
            <th>Kualitas</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="table-body"></tbody>
      </table>
    </div>
  </div>

  <!-- LEGEND MAP -->
  <div class="panel fade-in" style="padding:16px 22px;margin-bottom:20px;" id="peta">
    <div style="display:flex;gap:20px;flex-wrap:wrap;align-items:center;">
      <span style="font-size:12.5px;font-weight:600;color:var(--text-muted)">Status Warna Peta:</span>
      <span class="badge-status badge-good">Valid > 80% (Baik)</span>
      <span class="badge-status badge-warning">Valid 50–80% (Perhatian)</span>
      <span class="badge-status badge-critical">Valid < 50% (Kritis)</span>
    </div>
  </div>

</div><!-- /content-area -->
</div><!-- /main-wrap -->

<!-- TOAST AREA -->
<div class="toast-wrap" id="toast-wrap"></div>

<!-- SCRIPTS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.1/jspdf.plugin.autotable.min.js"></script>

<script>
// ============================================================
// STATE
// ============================================================
const state = { tanggal_from: '', tanggal_to: '', kota: '', tipe: '' };
let qualityChart = null;
let leafletMap = null;
let markersLayer = null;
let dtable = null;
let rawTableData = [];

const API = 'api/';

// ============================================================
// UTILS
// ============================================================
function showToast(msg, icon = '✓') {
    const w = document.getElementById('toast-wrap');
    const el = document.createElement('div');
    el.className = 'toast-item';
    el.innerHTML = `<span>${icon}</span><span>${msg}</span>`;
    w.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateY(20px)'; el.style.transition = '.3s'; }, 2500);
    setTimeout(() => el.remove(), 2800);
}

function getFilters() {
    const p = new URLSearchParams();
    if (state.tanggal_from) p.set('tanggal_from', state.tanggal_from);
    if (state.tanggal_to)   p.set('tanggal_to',   state.tanggal_to);
    if (state.kota)         p.set('kota',          state.kota);
    if (state.tipe)         p.set('tipe',          state.tipe.toLowerCase());
    return p.toString();
}

function animCount(el, target, suffix = '') {
    // Clear any skeleton children first
    el.innerHTML = '';
    const isInt = Number.isInteger(target) && suffix === '';
    const start = 0, dur = 800, step = 16;
    let cur = start;
    const inc = (target || 0) / (dur / step);
    if (!target || target === 0) { el.textContent = isInt ? '0' : '0.00' + suffix; return; }
    const timer = setInterval(() => {
        cur = Math.min(cur + inc, target);
        el.textContent = isInt ? Math.round(cur) + suffix : cur.toFixed(2) + suffix;
        if (cur >= target) clearInterval(timer);
    }, step);
}

// ============================================================
// FILTERS
// ============================================================
async function loadFilters() {
    try {
        const res = await fetch(API + 'filters.php');
        const data = await res.json();

        // Populate kota dropdown
        const sel = document.getElementById('f-kota');
        data.cities.forEach(c => {
            const o = document.createElement('option'); o.value = c; o.textContent = c; sel.appendChild(o);
        });

        // Show date range in navbar
        if (data.date_min && data.date_max) {
            const fmt = d => {
                const [y,m,day] = d.split('-');
                return `${day}/${m}/${y}`;
            };
            document.getElementById('nav-date-range').textContent =
                fmt(data.date_min) + ' – ' + fmt(data.date_max);

            // Set default date range inputs to available range
            document.getElementById('f-tanggal-from').value = data.date_min;
            document.getElementById('f-tanggal-to').value   = data.date_max;
            state.tanggal_from = data.date_min;
            state.tanggal_to   = data.date_max;
        }
    } catch(e) { console.error('loadFilters error', e); }
}

document.getElementById('btn-apply').addEventListener('click', () => {
    state.tanggal_from = document.getElementById('f-tanggal-from').value;
    state.tanggal_to   = document.getElementById('f-tanggal-to').value;
    state.kota         = document.getElementById('f-kota').value;
    state.tipe         = document.getElementById('f-tipe').value;
    // Update navbar date range display
    if (state.tanggal_from || state.tanggal_to) {
        const fmt = d => { if(!d) return '?'; const [y,m,day] = d.split('-'); return `${day}/${m}/${y}`; };
        document.getElementById('nav-date-range').textContent =
            fmt(state.tanggal_from) + ' – ' + fmt(state.tanggal_to);
    }
    loadAll();
    showToast('Filter diterapkan');
});

document.getElementById('btn-reset').addEventListener('click', async () => {
    // Reset to full DB range
    try {
        const res = await fetch(API + 'filters.php');
        const data = await res.json();
        state.tanggal_from = data.date_min || '';
        state.tanggal_to   = data.date_max || '';
        document.getElementById('f-tanggal-from').value = state.tanggal_from;
        document.getElementById('f-tanggal-to').value   = state.tanggal_to;
        if (data.date_min && data.date_max) {
            const fmt = d => { const [y,m,day] = d.split('-'); return `${day}/${m}/${y}`; };
            document.getElementById('nav-date-range').textContent =
                fmt(data.date_min) + ' – ' + fmt(data.date_max);
        }
    } catch(e) {}
    state.kota = ''; state.tipe = '';
    document.getElementById('f-kota').value = '';
    document.getElementById('f-tipe').value = '';
    loadAll();
    showToast('Filter direset', '↺');
});

// ============================================================
// DARK MODE
// ============================================================
const darkBtn = document.getElementById('dark-toggle');
darkBtn.addEventListener('click', () => {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    html.setAttribute('data-theme', isDark ? 'light' : 'dark');
    darkBtn.innerHTML = isDark ? '<i class="bi bi-moon-stars-fill"></i>' : '<i class="bi bi-sun-fill"></i>';
    if (qualityChart) updateChartColors();
});

function updateChartColors() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    qualityChart.options.scales.x.grid.color = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.05)';
    qualityChart.options.scales.y.ticks.color = isDark ? '#94a3b8' : '#6b7280';
    qualityChart.update();
}

// ============================================================
// SUMMARY
// ============================================================
async function loadSummary() {
    const res = await fetch(API + 'dashboard-summary.php?' + getFilters());
    const text = await res.text();
    let d;
    try { d = JSON.parse(text); } catch(e) {
        console.error('dashboard-summary parse error:', text);
        showToast('Error API summary: ' + text.substring(0, 80), '⚠');
        return;
    }
    if (d.error) { console.error('API error:', d.error); showToast('DB Error: ' + d.error, '⚠'); return; }

    const el = (id) => document.getElementById(id);
    animCount(el('stat-total'),   d.total_aws);
    animCount(el('stat-active'),  d.active_aws);
    animCount(el('stat-problem'), d.problem_aws);
    animCount(el('stat-valid'),   d.avg_valid,   '%');
    animCount(el('stat-invalid'), d.avg_invalid, '%');
    animCount(el('stat-missing'), d.avg_missing, '%');

    // Update navbar date range from filtered result
    if (d.date_min && d.date_max) {
        const fmt = d => { const [y,m,day] = d.split('-'); return `${day}/${m}/${y}`; };
        document.getElementById('nav-date-range').textContent =
            fmt(d.date_min) + ' – ' + fmt(d.date_max);
    }

    setTimeout(() => {
        el('bar-valid').style.width   = d.avg_valid   + '%';
        el('bar-invalid').style.width = d.avg_invalid + '%';
        el('bar-missing').style.width = d.avg_missing + '%';
    }, 100);
}

// ============================================================
// CHART
// ============================================================
async function loadChart() {
    const res = await fetch(API + 'chart-quality.php?' + getFilters());
    const text = await res.text();
    let d;
    try { d = JSON.parse(text); } catch(e) {
        console.error('chart-quality parse error:', text);
        return;
    }
    if (!d.labels || !d.labels.length) return;

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const ctx = document.getElementById('chart-quality').getContext('2d');

    if (qualityChart) { qualityChart.destroy(); qualityChart = null; }

    qualityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: d.labels,
            datasets: [
                { label: 'Valid',   data: d.valid,   backgroundColor: '#10b981', borderRadius: 2 },
                { label: 'Invalid', data: d.invalid, backgroundColor: '#f59e0b', borderRadius: 2 },
                { label: 'Missing', data: d.missing, backgroundColor: '#d1d5db', borderRadius: 2 },
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.x.toFixed(1)}%`
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    max: 100,
                    grid: { color: isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.05)' },
                    ticks: { color: '#9ca3af', font: { size: 11 }, callback: v => v + '%' }
                },
                y: {
                    stacked: true,
                    grid: { display: false },
                    ticks: { color: isDark ? '#94a3b8' : '#6b7280', font: { size: 11 } }
                }
            }
        }
    });
}

// ============================================================
// MAP
// ============================================================
function initMap() {
    leafletMap = L.map('aws-map', { zoomControl: true }).setView([-2.5, 118], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 18
    }).addTo(leafletMap);
    markersLayer = L.layerGroup().addTo(leafletMap);
}

async function loadMap() {
    const res = await fetch(API + 'map-data.php?' + getFilters());
    const d = await res.json();
    markersLayer.clearLayers();

    const colorMap = { green: '#10b981', orange: '#f59e0b', red: '#ef4444' };
    document.getElementById('map-count').textContent = d.markers.length + ' station';

    d.markers.forEach(m => {
        const color = colorMap[m.color] || '#6b7280';
        const icon = L.divIcon({
            className: '',
            html: `<div style="width:14px;height:14px;background:${color};border:2px solid #fff;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,.3)"></div>`,
            iconSize: [14,14], iconAnchor: [7,7]
        });
        const marker = L.marker([m.lat, m.lng], { icon });
        const popupParamMap = {
            ARG: {bg:'rgba(139,92,246,.12)',c:'#7c3aed',l:'RR only'},
            ASRS:{bg:'rgba(245,158,11,.15)',c:'#d97706',l:'SR only'},
            AAWS:{bg:'rgba(16,185,129,.12)',c:'#059669',l:'11 params'},
            AWS: {bg:'#dbeafe',             c:'#2563eb',l:'13 params'},
        };
        const pm = popupParamMap[m.tipe] || {bg:'#f3f4f6',c:'#374151',l:m.tipe};
        const paramBadge = `<span style="font-size:10px;background:${pm.bg};color:${pm.c};padding:1px 7px;border-radius:10px;font-weight:600;">${pm.l}</span>`;
        marker.bindPopup(`
            <div style="font-family:'DM Sans',sans-serif;min-width:190px;">
                <div style="font-weight:700;font-size:13px;margin-bottom:4px;">${m.name}</div>
                <div style="font-size:12px;color:#6b7280;margin-bottom:8px;">${m.kota}</div>
                <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                    <span style="font-size:11px;background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:10px;font-weight:700;">${m.tipe}</span>
                    ${paramBadge}
                    <span style="font-size:12px;font-weight:700;color:${color}">${m.valid_pct}% valid</span>
                </div>
            </div>
        `);
        markersLayer.addLayer(marker);
    });
}

// ============================================================
// TABLE
// ============================================================
async function loadTable() {
    const res = await fetch(API + 'station-table.php?' + getFilters());
    const d = await res.json();
    rawTableData = d.data;

    const tbody = document.getElementById('table-body');
    tbody.innerHTML = '';

    d.data.forEach(r => {
        const statusLabel = r.status === 'good' ? 'Baik' : r.status === 'warning' ? 'Perhatian' : 'Kritis';
        const tipeClass = r.tipe_station.toLowerCase();
        const paramLabels = { ARG: '', ASRS: '', AAWS: '', AWS: '' };
        const paramColors = {
            ARG:  'rgba(139,92,246,.08);color:#7c3aed',
            ASRS: 'rgba(245,158,11,.1);color:#d97706',
            AAWS: 'rgba(16,185,129,.1);color:#059669',
            AWS:  'rgba(59,130,246,.1);color:#2563eb',
        };
        const paramLabel = paramLabels[r.tipe_station] || '';
        const paramColor = paramColors[r.tipe_station] || 'rgba(0,0,0,.05);color:#666';
        const tipeBadge = `<span class="badge-type ${tipeClass}">${r.tipe_station}</span>`
            + (paramLabel ? ` <span style="font-size:10px;background:${paramColor};padding:1px 6px;border-radius:8px;font-weight:600;margin-left:2px;">${paramLabel}</span>` : '');
        const pctBar = `
            <div style="display:flex;align-items:center;gap:8px;">
                <div class="pct-bar">
                    <div class="seg seg-v" style="width:${r.valid_pct}%"></div>
                    <div class="seg seg-i" style="width:${r.invalid_pct}%"></div>
                    <div class="seg seg-m" style="width:${r.missing_pct}%"></div>
                </div>
                <span style="font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600">${r.valid_pct}%</span>
            </div>`;
        const row = `<tr>
            <td><span style="font-family:'JetBrains Mono',monospace;font-size:12px;">${r.id_station}</span></td>
            <td>${r.name_station}</td>
            <td>${r.nama_kota}</td>
            <td>${tipeBadge}</td>
            <td>${r.tanggal}</td>
            <td>${pctBar}</td>
            <td><span class="badge-status badge-${r.status}">${statusLabel}</span></td>
            <td></td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });

    if (dtable) { dtable.destroy(); dtable = null; }
    dtable = $('#station-table').DataTable({
        pageLength: 10,
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            paginate: { previous: '‹', next: '›' }
        },
        columnDefs: [
            { orderable: false, targets: [5, 7] }
        ]
    });
}

// ============================================================
// EXPORT
// ============================================================
document.getElementById('btn-excel').addEventListener('click', (e) => {
    e.preventDefault();
    if (!rawTableData.length) return showToast('Tidak ada data', '!');
    const ws = XLSX.utils.json_to_sheet(rawTableData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Station Flags');
    XLSX.writeFile(wb, 'logbook_aws.xlsx');
    showToast('Excel berhasil diexport', '📊');
});

document.getElementById('btn-pdf').addEventListener('click', (e) => {
    e.preventDefault();
    if (!rawTableData.length) return showToast('Tidak ada data', '!');
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape' });
    doc.setFontSize(14);
    doc.text('LOGBOOK AWS — Data Station', 14, 15);
    doc.setFontSize(9);
    doc.text('Tanggal cetak: ' + new Date().toLocaleDateString('id-ID'), 14, 22);

    const cols = ['ID Station','Nama Station','Kota','Tipe','Tanggal','Valid%','Invalid%','Status'];
    const rows = rawTableData.map(r => [r.id_station, r.name_station, r.nama_kota, r.tipe_station, r.tanggal, r.valid_pct+'%', r.invalid_pct+'%', r.status]);

    doc.autoTable({ head: [cols], body: rows, startY: 28, styles: { fontSize: 8 }, headStyles: { fillColor: [17,24,39] } });
    doc.save('logbook_aws.pdf');
    showToast('PDF berhasil diexport', '📄');
});

// ============================================================
// LOAD ALL
// ============================================================
async function loadAll() {
    try {
        await Promise.all([loadSummary(), loadChart(), loadMap(), loadTable()]);
        document.getElementById('last-update').textContent = 'Update ' + new Date().toLocaleTimeString('id-ID');
    } catch(err) {
        showToast('Gagal memuat data. Cek koneksi database.', '⚠');
        console.error(err);
    }
}

// ============================================================
// INIT
// ============================================================
document.addEventListener('DOMContentLoaded', async () => {
    await loadFilters();
    initMap();
    await loadAll();

    // Auto refresh every 30 seconds
    setInterval(loadAll, 30000);
});
</script>
</body>
</html>
