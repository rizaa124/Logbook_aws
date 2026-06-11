<?php
// =============================================================
// DEFINISI PARAMETER PER TIPE STATION — single source of truth
// =============================================================

// Semua kolom beserta label tampilan
define('PARAM_LABELS', [
    'rr'         => 'Rainfall (RR)',
    'pp_air'     => 'Air Pressure (PP)',
    'rh_avg'     => 'Humidity (RH)',
    'sr_avg'     => 'Solar Rad. Avg',
    'sr_max'     => 'Solar Rad. Max',
    'wd_avg'     => 'Wind Dir. (WD)',
    'ws_avg'     => 'Wind Speed Avg',
    'ws_max'     => 'Wind Speed Max',
    'tt_air_avg' => 'Temp Air Avg',
    'tt_air_min' => 'Temp Air Min',
    'tt_air_max' => 'Temp Air Max',
    'tt_sea'     => 'Temp Sea (TT)',
    'wl_pan'     => 'Pan Water Level',
]);

// Parameter yang dipakai per tipe station
define('TIPE_PARAMS', [
    'arg'  => ['rr'],
    'aws'  => ['rr','pp_air','rh_avg','sr_avg','sr_max','wd_avg','ws_avg','ws_max','tt_air_avg','tt_air_min','tt_air_max','tt_sea','wl_pan'],
    'aaws' => ['rr','pp_air','rh_avg','sr_avg','sr_max','wd_avg','ws_avg','ws_max','tt_air_avg','tt_air_min','tt_air_max'],
    'asrs' => ['sr_avg','sr_max'],
]);

// Helper: bangun SQL ekspresi AVG untuk satu suffix (_valid/_invalid/_missing)
function buildAvgExpr(string $tipe, string $suffix): string {
    $tipe = strtolower($tipe);
    $params = TIPE_PARAMS[$tipe] ?? TIPE_PARAMS['aws'];
    $cols   = array_map(fn($p) => "{$p}_{$suffix}", $params);
    $n      = count($cols);
    return '(' . implode('+', $cols) . ")/{$n}.0";
}

// Helper: bangun CASE WHEN untuk semua tipe sekaligus (dipakai di query aggregate)
function buildCaseExpr(string $suffix): string {
    $cases = [];
    foreach (TIPE_PARAMS as $tipe => $params) {
        $cols = array_map(fn($p) => "{$p}_{$suffix}", $params);
        $n    = count($cols);
        $expr = '(' . implode('+', $cols) . ")/{$n}.0";
        $cases[] = "WHEN LOWER(tipe_station)='{$tipe}' THEN {$expr}";
    }
    // fallback ke aws jika tipe tidak dikenal
    $fallback = buildAvgExpr('aws', $suffix);
    return 'CASE ' . implode(' ', $cases) . " ELSE {$fallback} END";
}

// Helper: daftar tipe yang dikenal
function knownTipes(): array {
    return array_keys(TIPE_PARAMS);
}
