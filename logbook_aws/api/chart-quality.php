<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../config/database.php';
require_once '../config/params.php';

$conn = getConnection();

$tanggal_from = isset($_GET['tanggal_from']) ? $conn->real_escape_string($_GET['tanggal_from']) : '';
$tanggal_to   = isset($_GET['tanggal_to'])   ? $conn->real_escape_string($_GET['tanggal_to'])   : '';
$kota         = isset($_GET['kota'])         ? $conn->real_escape_string($_GET['kota'])         : '';
$tipe         = isset($_GET['tipe'])         ? $conn->real_escape_string($_GET['tipe'])         : '';

$where = [];
if ($tanggal_from) $where[] = "tanggal >= '$tanggal_from'";
if ($tanggal_to)   $where[] = "tanggal <= '$tanggal_to'";
if ($kota)         $where[] = "nama_kota = '$kota'";
if ($tipe)         $where[] = "LOWER(tipe_station) = '" . strtolower($tipe) . "'";
$W = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Tentukan parameter mana yang ditampilkan di chart:
// Jika filter tipe spesifik → pakai param tipe itu saja
// Jika semua tipe → gabungan unik semua tipe yang ada di data
$tipeRows = $conn->query("SELECT DISTINCT LOWER(tipe_station) as t FROM station_flags $W")->fetch_all(MYSQLI_ASSOC);
$tipeList = array_column($tipeRows, 't');

if (count($tipeList) === 1) {
    // Satu tipe saja
    $singleTipe = $tipeList[0];
    $usedParams = TIPE_PARAMS[$singleTipe] ?? TIPE_PARAMS['aws'];
    $isSingleTipe = true;
} else {
    // Gabungan: union semua param dari semua tipe yang ada
    $usedParams = [];
    foreach ($tipeList as $t) {
        $usedParams = array_unique(array_merge($usedParams, TIPE_PARAMS[$t] ?? TIPE_PARAMS['aws']));
    }
    // Urutkan sesuai urutan PARAM_LABELS
    $usedParams = array_values(array_intersect(array_keys(PARAM_LABELS), $usedParams));
    $isSingleTipe = false;
}

$allLabels  = PARAM_LABELS;
$labels = $valid_data = $invalid_data = $missing_data = [];

foreach ($usedParams as $key) {
    $label = $allLabels[$key] ?? $key;

    if ($isSingleTipe) {
        // Query langsung, semua baris sudah tipe yang sama
        $sql = "SELECT AVG({$key}_valid) as v, AVG({$key}_invalid) as i, AVG({$key}_missing) as m
                FROM station_flags $W";
    } else {
        // Mixed tipe: untuk setiap param, hanya ambil tipe yang punya param tsb
        $relevantTipes = [];
        foreach (TIPE_PARAMS as $t => $params) {
            if (in_array($key, $params)) $relevantTipes[] = "'$t'";
        }
        $tipeIn   = implode(',', $relevantTipes);
        $extraCond = $W
            ? "$W AND LOWER(tipe_station) IN ($tipeIn)"
            : "WHERE LOWER(tipe_station) IN ($tipeIn)";
        $sql = "SELECT AVG({$key}_valid) as v, AVG({$key}_invalid) as i, AVG({$key}_missing) as m
                FROM station_flags $extraCond";
    }

    $row = $conn->query($sql)->fetch_assoc();
    $labels[]       = $label;
    $valid_data[]   = round(floatval($row['v']), 2);
    $invalid_data[] = round(floatval($row['i']), 2);
    $missing_data[] = round(floatval($row['m']), 2);
}

echo json_encode([
    'labels'  => $labels,
    'valid'   => $valid_data,
    'invalid' => $invalid_data,
    'missing' => $missing_data,
]);
$conn->close();
