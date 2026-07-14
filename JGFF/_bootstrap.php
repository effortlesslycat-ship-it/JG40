<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
// error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);

ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

$options_JGFF = [
    'hostname' => SOLR_SERVER_HOSTNAME,
    'login' => SOLR_SERVER_USERNAME,
    'password' => SOLR_SERVER_PASSWORD,
    'port' => SOLR_SERVER_PORT,
    'path' => 'solr/JGFFproto',
];

$options_communities = [
    'hostname' => SOLR_SERVER_HOSTNAME,
    'login' => SOLR_SERVER_USERNAME,
    'password' => SOLR_SERVER_PASSWORD,
    'port' => SOLR_SERVER_PORT,
    'path' => 'solr/communities',
];

define('MYSQL_SERVER_HOSTNAME', 'jewishgen13:3306');
define('MYSQL_SERVER_USERNAME', 'mtobias');
define('MYSQL_SERVER_PASSWORD', 'nt732b#$');
define('MYSQL_SERVER_DATABASE', 'jewishgen');

// use:
// mysqli_connect( MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE );
