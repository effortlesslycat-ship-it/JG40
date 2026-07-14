<?php

// include_once("\\\jewishgen6\JEWISHGEN\wwwroot\blockscript\detector.php");

// include_once '../databases/cureetc.php';

include_once '../databases/bootstrap.php';

include_once '../databases/msbootstrap.php';

include '_bootstrap.php';


$con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);

$sqlcommand = 'update blindcontact ';
$sqlcommand .= 'set message = \'\' ';
$sqlcommand .= 'where `datetime` < date_sub(now(), interval 45 day) ';
$sqlcommand .= 'and not message = \'\' ';

if (!mysqli_query($con, $sqlcommand)) {
	mail("gsandler@jewishgen.org", "JGFFContact Purge Error", mysqli_error($con));
	$headers = 'From: JGFF-Comms@jewishgen.org' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
} else {
	echo "ok" . PHP_EOL;
}

mysqli_close($con);
