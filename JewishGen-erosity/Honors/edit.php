<?php

include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\blockscript\\detector.php';

include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\databases\\cureetc.php';

include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\databases\\bootstrap.php';

include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\databases\\msbootstrap.php';

$jgid = require_userinfo();

if (($jgid == 832770) or ($jgid == 326798) or ($jgid == 1477)) {
    $action = $_POST['action'];
    $p_id = str_replace("'", "''", $_POST['id']);
    if ($p_id != '') {
        if ($action == 'edit') {
            $p_type = str_replace("'", "''", $_POST['type']);
            $p_honoree = str_replace("'", "''", $_POST['honoree']);
            $p_reason = str_replace("'", "''", $_POST['reason']);
            $p_from = str_replace("'", "''", $_POST['fromName']);
            //   ini_set('mssql.charset', 'UTF-8');
            $conn = sqlsrv_connect($MSserverName, $optionsGenerosity);
            if (!$conn) {
                echo "Connection to Generosity could not be established.\n";

                exit(print_r(sqlsrv_errors(), true));
            }
            $query = "UPDATE honors 
	     SET Honoree ='" . $p_honoree . "',
	     ReasonOccasion2 ='" . $p_reason . "',
	     type = '" . $p_type . "',
	    _from = '" . $p_from . "' 
            WHERE id =" . $p_id;
            $stmt = sqlsrv_query($conn, $query);
            if ($stmt === false) {
                exit(print_r(sqlsrv_errors(), true));
            }

            sqlsrv_free_stmt($stmt);
            sqlsrv_close($conn);

        // $row = sqlsrv_fetch_array($stmt);
        // $h = $row["Honoree"];
        // echo $h;
        } elseif ($action == 'delete') {
            $conn = sqlsrv_connect($MSserverName, $optionsGenerosity);
            if (!$conn) {
                echo "Connection to Generosity could not be established.\n";

                exit(print_r(sqlsrv_errors(), true));
            }
            $query = 'UPDATE honors SET deleted = 1 WHERE id=' . $p_id;
            $stmt = sqlsrv_query($conn, $query);
            if ($stmt === false) {
                exit(print_r(sqlsrv_errors(), true));
            }
            sqlsrv_free_stmt($stmt);
            sqlsrv_close($conn);
        }
    }
}
