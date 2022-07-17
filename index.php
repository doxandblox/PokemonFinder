<?php
//Test commit
require_once "./vendor/autoload.php";

global $DBA;

$DBA = new \Tina4\DataSQLite3("data.db");

$config = new \Tina4\Config();
\Tina4\Initialize();
echo new \Tina4\Tina4Php($config);

?> 
