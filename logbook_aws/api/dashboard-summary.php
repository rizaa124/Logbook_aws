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

$caseValid   = buildCaseExpr('valid');
$caseInvalid = buildCaseExpr('invalid');
$caseMissing = buildCaseExpr('missing');

// Untuk range > 1 hari: station bisa duplikat per tanggal.
// Maka:
// - Total/Active/Problematic dihitung per DISTINCT id_station
// - Valid/Invalid/Missing dihitung sebagai AVG per station (rata-rata antar tanggal), lalu di-AVG lagi antar station.

// 1. Total distinct station
$total = intval($conn->query(
    "SELECT COUNT(*) as c FROM (SELECT id_station FROM station_flags $W GROUP BY id_station) s"
)->fetch_assoc()['c']);

// 2. Active distinct station (indikator umum: rr_valid > 0 pada station untuk range tsb)
$Wactive = $where
    ? 'WHERE ' . implode(' AND ', $where) . ' AND rr_valid > 0'
    : 'WHERE rr_valid > 0';
$active = intval($conn->query(
    "SELECT COUNT(*) as c FROM (SELECT id_station FROM station_flags $Wactive GROUP BY id_station) s"
)->fetch_assoc()['c']);

// 3. Problematic distinct station (avg_valid per station < 50)
$prob = intval($conn->query(
    "SELECT COUNT(*) as c FROM (\n        SELECT id_station, AVG(($caseValid)) AS avg_valid\n        FROM station_flags $W\n        GROUP BY id_station\n    ) s WHERE s.avg_valid < 50"
)->fetch_assoc()['c']);

// 4. Avg per tipe: avg_valid/invalid/missing = rata-rata per station (antar tanggal), lalu rata-rata antar station
$avgRow = $conn->query(
    "SELECT\n        AVG(st.avg_valid)   AS avg_valid,\n        AVG(st.avg_invalid) AS avg_invalid,\n        AVG(st.avg_missing) AS avg_missing\n     FROM (\n        SELECT\n            id_station,\n            AVG($caseValid)   AS avg_valid,\n            AVG($caseInvalid) AS avg_invalid,\n            AVG($caseMissing) AS avg_missing\n        FROM station_flags $W\n        GROUP BY id_station\n     ) st"
)->fetch_assoc();


// 5. Range tanggal data di DB (untuk info)
$rangeRow = $conn->query("SELECT MIN(tanggal) as tgl_min, MAX(tanggal) as tgl_max FROM station_flags $W")->fetch_assoc();

echo json_encode([
    'total_aws'   => intval($total),
    'active_aws'  => intval($active),
    'problem_aws' => intval($prob),
    'avg_valid'   => round(floatval($avgRow['avg_valid']),   2),
    'avg_invalid' => round(floatval($avgRow['avg_invalid']), 2),
    'avg_missing' => round(floatval($avgRow['avg_missing']), 2),
    'date_min'    => $rangeRow['tgl_min'] ?? '',
    'date_max'    => $rangeRow['tgl_max'] ?? '',
]);
$conn->close();
