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

$sql = "SELECT
    id_station, name_station, nama_kota, tipe_station, tanggal,
    ($caseValid)   AS overall_valid,
    ($caseInvalid) AS overall_invalid,
    ($caseMissing) AS overall_missing
FROM station_flags $W ORDER BY tanggal DESC";

$result = $conn->query($sql);
if (!$result) {
    echo json_encode(['error' => $conn->error, 'data' => []]);
    exit;
}

// Label jumlah param per tipe
$paramCount = [];
foreach (TIPE_PARAMS as $t => $params) {
    $paramCount[$t] = count($params) . ' param' . (count($params) > 1 ? 's' : '');
}
$paramCount['arg'] = 'RR only';
$paramCount['asrs'] = 'SR only';

$data = [];
while ($row = $result->fetch_assoc()) {
    $tipe_raw = strtolower($row['tipe_station']);
    $v = round(floatval($row['overall_valid']),   1);
    $i = round(floatval($row['overall_invalid']), 1);
    $m = round(floatval($row['overall_missing']), 1);

    if ($v >= 80)     $status = 'good';
    elseif ($v >= 50) $status = 'warning';
    else              $status = 'critical';

    $data[] = [
        'id_station'   => $row['id_station'],
        'name_station' => $row['name_station'],
        'nama_kota'    => $row['nama_kota'],
        'tipe_station' => strtoupper($row['tipe_station']),
        'tanggal'      => $row['tanggal'],
        'valid_pct'    => $v,
        'invalid_pct'  => $i,
        'missing_pct'  => $m,
        'status'       => $status,
        'param_used'   => $paramCount[$tipe_raw] ?? '?',
    ];
}

echo json_encode(['data' => $data]);
$conn->close();
