<?php
$queryCount = 0;
for($i=0;$queryCount<100000;$i+=3){
echo "INSERT INTO users(
    username, email,password)
    VALUES 
    ('value$i', 'value".($i+1)."', 'value".($i+2)."');";
    $queryCount++;
}
?>