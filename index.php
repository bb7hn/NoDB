<?php
require_once("NoDB.Class.php");
$dbInfo = [
    "host"      => "localhost",
    "name"      => "test",
    "password"  => "12345678",
    "errorMode" => true
];
$db = new NoDB($dbInfo);

$executionMessage = $db->getExec();

var_dump($executionMessage);
?>