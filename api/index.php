<?php
// this is where all files connect

// set globals
$environment        = 'development';
if($environment === 'development'){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// set common settings, timezone, and dbsettings etc
date_default_timezone_set('Africa/Dar_es_salaam');
$current_time       = date('Y-m-d H:i:s');
$host               = 'localhost';
$database           = 'hl_database';
$username           = 'root';
$password           = 'akamimi94@ubuntu';
$dsn                = 'mysql:host='.$host.';dbname='.$database;

// require files
require_once('data/Database.php');
require_once('data/Model.php');
require_once('Controller.php');
require_once('helpers.php');

// start your database connection
$database   = new Database($dsn, $username, $password);
$connection = $database->getConnection();




