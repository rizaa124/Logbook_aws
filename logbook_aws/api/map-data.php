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

$caseValid = buildCaseExpr('valid');

$sql = "SELECT id_station, name_station, nama_kota, tipe_station,
    long_station, latt_station,
    ($caseValid) AS overall_valid
FROM station_flags $W";

$result = $conn->query($sql);
if (!$result) {
    echo json_encode(['error' => $conn->error, 'markers' => []]);
    exit;
}

$markers = [];
while ($row = $result->fetch_assoc()) {
    $lat = floatval($row['latt_station']);
    $lng = floatval($row['long_station']);
    if ($lat == 0 && $lng == 0) continue;

    $v = round(floatval($row['overall_valid']), 1);
    if ($v >= 80)     $color = 'green';
    elseif ($v >= 50) $color = 'orange';
    else              $color = 'red';

    $tipe_raw = strtolower($row['tipe_station']);
    $paramInfo = ['arg'=>'RR only','asrs'=>'SR only','aaws'=>'11 params','aws'=>'13 params'][$tipe_raw] ?? '?';

    $markers[] = [
        'id'         => $row['id_station'],
        'name'       => $row['name_station'],
        'kota'       => $row['nama_kota'],
        'tipe'       => strtoupper($row['tipe_station']),
        'lat'        => $lat,
        'lng'        => $lng,
        'valid_pct'  => $v,
        'color'      => $color,
        'param_info' => $paramInfo,
    ];
}

echo json_encode(['markers' => $markers]);
$conn->close();
