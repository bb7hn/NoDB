<?php
/* $limit = 1000;
$lastVal = 2000;
if($lastVal%$limit === 0){
    $tempVal = (intval($lastVal/$limit) - 1)*$limit;
    
    if($lastVal>$limit && $lastVal == 1000){
        $startVal = $tempVal;
    }
    else{
        $startVal = $tempVal+1;
    }
    $endVal   = $tempVal + $limit;
    echo("$startVal-$endVal.ndb");
}
else{
    $tempVal    = intval($lastVal/$limit)*$limit;
    $startVal   = $tempVal + 1;
    $endVal   = $tempVal + $limit;
    echo("$startVal-$endVal.ndb");
}
exit; */
require_once("NoDB.Class.php");
$dbInfo = [
    "host"      => "localhost",
    "name"      => "test",
    "password"  => "12345678",
    "errorMode" => true
];
$db = new NoDB($dbInfo);
/* $db->query('CREATE TABLE users (
    id AUTOINC,
    username VARCHAR,
    email varchar,
    password TeXt
);'); */
$db->query(file_get_contents('http://nodb.localhost/queryCreator.php'));
$executionMessage = $db->getExec();

var_dump($executionMessage);
?>
<script>
    /* 
        //Find on stackoverflow and solved problem :))
        function waitForCondition(i,r) {
        //console.log("Waiting..." + i + " ms");
        console.log(new Date().getMinutes() >= nextMinute);
        if (new Date().getMinutes() >= nextMinute) {
            r();
        }
        else {
            setTimeout(function() {
                waitForCondition(i,r);
            },i);
        }
    }

    let nextMinute = new Date().getMinutes() + 1 % 59;
    console.log("Waiting for minute " + nextMinute.toString());
    waitForCondition(nextMinute,function() {
        console.log("Condition met :)");
    }); */
</script>