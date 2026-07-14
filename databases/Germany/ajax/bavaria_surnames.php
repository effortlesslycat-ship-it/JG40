<?php

include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\blockscript\\detector.php';

$servername = '192.168.254.215';
$username = 'web_user';
$password = 'webJG!234';

// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
    exit('Connection failed: ' . mysqli_connect_error());
}
$sql = 'select * from jewishgen.JGBS_surnames;';
$result = mysqli_query($conn, $sql) or exit(mysqli_error());
$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    echo '["' . $r['SURNAME'] . '",' . $r['count'] . '],';
    $rows['data'][] = $r;
}
header('Content-type: application/json');
// echo $rows;
// echo json_encode($rows);
mysqli_close($con);
