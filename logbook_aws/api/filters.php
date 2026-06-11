<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../config/database.php';
require_once '../config/params.php';

$conn = getConnection();

$cities = [];
$res = $conn->query("SELECT DISTINCT nama_kota FROM station_flags WHERE nama_kota IS NOT NULL AND nama_kota != '' ORDER BY nama_kota");
if ($res) while ($r = $res->fetch_assoc()) $cities[] = $r['nama_kota'];

$types = [];
$res2 = $conn->query("SELECT DISTINCT LOWER(tipe_station) as t FROM station_flags WHERE tipe_station IS NOT NULL ORDER BY t");
if ($res2) while ($r = $res2->fetch_assoc()) $types[] = strtoupper($r['t']);

// Range tanggal tersedia
$rangeRow = $conn->query("SELECT MIN(tanggal) as tgl_min, MAX(tanggal) as tgl_max FROM station_flags")->fetch_assoc();

echo json_encode([
    'cities'   => $cities,
    'types'    => $types,
    'date_min' => $rangeRow['tgl_min'] ?? '',
    'date_max' => $rangeRow['tgl_max'] ?? '',
]);
$conn->close();
