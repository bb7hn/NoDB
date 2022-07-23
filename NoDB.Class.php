<?php
set_time_limit(2000);
class NoDB{
    protected $dbName;
    protected $dbPath;
    protected $executionTime;
    protected $columnTypes = [
        "char",
        "varchar",
        "text",
        "date",
        "time",
        "timestamp",
        "int",
        "autoinc"
    ];
    protected $throwException = true;
    function __construct($dbInfo) {
        $this->executionTime = 0;
        return $this->prepareDB($dbInfo);
    }
    
    protected function prepareDB($dbInfo){
        //set array keys to lowerCase
        $dbInfo = array_change_key_case($dbInfo, CASE_LOWER);
        //if name did not set throw exception
        if(!isset($dbInfo["name"])){
            throw new Exception('Setup parameters do not contain database name (name)');
        }
        //if name is empty throw exception
        if(empty($dbInfo["name"])){
            throw new Exception('Database name can not be empty (name)');
        }
        if(!isset($dbInfo["password"]) || empty($dbInfo["password"])){
            $dbInfo["password"] = '';
        }
        if(!isset($dbInfo["host"]) || empty($dbInfo["name"])){
            $dbInfo['host'] = 'localhost';
        }
        if(isset($dbInfo["errormode"]) && $dbInfo["errormode"] === false){
            $this->throwException = false;  
        }
        // if does not exists create the database
        $this->dbName = $dbName =  $dbInfo['name'];
        if(!is_dir($dbName)){
            mkdir($dbName);
        }
        $this->dbPath = realpath($dbName);
    }
    protected function createTable($tableName,$tableColumns){
        $tableFile     = "$this->dbPath\\$tableName.nodb";
        $tableFolder   = "$this->dbPath\\$tableName";
        if(file_exists($tableFile) || is_dir($tableFolder)){
            if($this->throwException){
                throw new Exception("Table ($tableName) already exists!");
            }
            return false;
        }
        
        $columns = [];
        $tempArr = explode(',',$tableColumns);
        $tempArr = array_filter($tempArr);
        $didAutoIncDefined = false;
        foreach($tempArr as $val){
            $temp = ["columnName"=>null,"columnType"=>null];
            $val = trim($val);
            $val = strtolower($val);
            if(empty($val)){
                continue;
            }
            $arr = explode(' ',$val);
            if(count($arr) !=2 && count($arr) !=3){
                if($this->throwException){
                    throw new Exception("there is a syntax error: '$val'");
                }
                continue;
            }
            if(!in_array($arr[1],$this->columnTypes)){
                if($this->throwException){
                    throw new Exception("Unexpected type '$arr[1]' for column '$arr[0]'");
                }
                continue;
            }
            $temp["columnName"] = $arr[0];
            $temp["columnType"] = $arr[1];
            if($arr[1] === "autoinc"){
                if($didAutoIncDefined){
                    if($this->throwException){
                        throw new Exception("'$arr[0]' can not define as 'AutoInc'. Already, there is a column, defined as AutoInc!");
                    }
                    return false;
                }
                $columns[]= ["columnName"=>"primary","lastVal"=>1,"symTo"=>$arr[0]];
                $didAutoIncDefined = true;
            }
            $columns[]=$temp;
        }
        if(!$didAutoIncDefined){
            $columns[]= ["columnName"=>"length","columnType"=>0];
        }
        unset($tempArr);
        $stream = fopen($tableFile,'w');
        fwrite($stream,json_encode($columns));
        fclose($stream);
        mkdir($tableFolder);
        $this->executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        return true;
    }
    protected function insertTable($tableName,$columnNames,$columnValues){
        //trim tableName
        $tableName      = trim($tableName);
        //define table folder and file path 
        $tableFile     = "$this->dbPath\\$tableName.nodb";
        $tableFolder   = "$this->dbPath\\$tableName";
        if(!file_exists($tableFile) || !is_dir($tableFolder)){
            //if error mode on throw exception
            if($this->throwException){
                throw new Exception("Table ($tableName) does not exists!");
            }
            return false;
        }
        $columnNames    = trim($columnNames);
        $columnValues   = trim($columnValues);
        
        $columnNames    = explode(',',$columnNames);
        $columnValues   = explode(',',$columnValues);

        $columnCount    = count($columnNames);
        $valueCount     = count($columnValues); 
        if($columnCount != $valueCount){
            if($this->throwException){
                throw new Exception("Column count ($columnCount) is not equal to value count ($valueCount)!");
            }
            return false;
        }
        //get db file and decode json
        $db = json_decode(file_get_contents($tableFile));
        //create a temporary array for to store information about columns with columName index
        $dbArr = [];
        //start a loop for ever single column and insert into temp array by setting array keys as column names
        foreach($db as $column){
            $dbArr[$column->columnName]=$column;
        }
        //unset $db
        unset($db);
        //point $db to $dbArr
        $db = $dbArr;
        //unset $dbArr
        unset($dbArr);
        $validColumns = array_keys($db);
        $insertArr = [];
        for($i=0;$i<$columnCount;$i+=1){
            $columnNames[$i] = trim($columnNames[$i]);
            if(!in_array($columnNames[$i],$validColumns)){
                if($this->throwException){
                    throw new Exception("Column '$columnNames[$i]' does not exists in table '$tableName'");
                }
                continue;
            }
            
            $insertArr[$columnNames[$i]] = $this->convertVal(trim($columnValues[$i]),$db[$columnNames[$i]]->columnType);
        }
        
        unset($columnNames);
        unset($columnValues);
        $fileName = "0.ndb";
        if(isset($db['primary'])){
            $autoInc = $db['primary'];
            $fileName = $this->calcFileName($autoInc->lastVal);
            $insertArr[$autoInc->symTo] = $autoInc->lastVal;
            $db['primary']->lastVal++;
        }
        elseif($db['length']->columnType>=1000){
            $tempVal = $db['length']->columnType;
            $fileName = intval($tempVal/1000).'.ndb';
        }
        ksort($insertArr);
        $stream = fopen("$tableFolder\\$fileName",'a+');
        fwrite($stream,implode(',',$insertArr).PHP_EOL);
        fclose($stream);
        $stream = fopen("$tableFile",'w');
        fwrite($stream,json_encode($db));
        fclose($stream);
        $this->executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        return true;
    }
    public function convertVal($val,$type){
        $type = strtolower($type);
        $typeOfVal = gettype($val);
        if($type === "int" || $type === "autoinc"){
            $val = trim($val,'"\'');
            try {
                return intval($val);
            } catch (Exception $e) {
                $error = $e->getMessage();
                if($this->throwException){
                    throw new Exception("invalid value '$val' to convert int! $error");
                }
                return 0;
            }
        }
        elseif($type === "char"){
            if( $typeOfVal ==="integer" ){
                return chr($val);
            }
            elseif($typeOfVal === "boolean"){
                return '0';
            }
            else{
                return '';
            }
        }
        elseif($type === "varchar" || $type === "text"){
            return strval($val);
        }
        elseif($type === "date"){
            try{
                return date('Y-m-d',strtotime($val));
            }
            catch(Exception $e){
                $error = $e->getMessage();
                if($this->throwException){
                    throw new Exception("invalid value '$val' to convert date! $error");
                }
                return date('Y-m-d',strtotime("2000-01-01"));
            }
        }
        elseif($type === "time"){
            try{
                return date('h:i:s',strtotime($val));
            }
            catch(Exception $e){
                $error = $e->getMessage();
                if($this->throwException){
                    throw new Exception("invalid value '$val' to convert time! $error");
                }
                return date('h:i:s',strtotime("2000-01-01"));
            }
        }
        elseif($type === "timestamp"){
            try{
                return strtotime($val);
            }
            catch(Exception $e){
                $error = $e->getMessage();
                if($this->throwException){
                    throw new Exception("invalid value '$val' to convert time! $error");
                }
                return strtotime("2000-01-01");
            }
        }
        else{
            if($this->throwException){
                throw new Exception("Invalid dataType $type for convertVal() !");
            }
            return NULL;
        }
    }
    public function query($sql){
        //Define regex pattern to detect sql queries
        $createPattern = "/^create +?table +?([a-z0-9]+)([\n\r\t ]+)?\(([\n\r\t ]+)?(.*)([\n\r\t ]+)?\)([\n\r\t ]+)?(;)?$/is";
        $insertPattern = "/^([\n\r\t ]+)?insert +into +([a-z0-9]+)([\n\r\t ]+)?\((.+)\)([\n\r\t ]+)?values([\n\r\t ]+)?\((.+)\);?([\n\r\t ]+)?$/is";
        $deletePattern = "/^(DELETE|delete) (FROM|from) ([a-zA-z0-9]+)( ([a-zA-z]+))?[;]?$/is";
        //explode sql to commands by ';' and clean nulls
        $commands = explode(';',$sql);
        $unmatches = [];
        
        //start loop for every single command
        foreach($commands as $key=>$command){
            //If command matches with create pattern
            if(preg_match($createPattern,$command,$matches)){
                //get table name from pattern
                $tableName = isset($matches[1])?$matches[1]:false;
                //get column queries from command
                $tableColumns = isset($matches[4])?$matches[4]:false;
                //If tableName and tableColumns are not false
                if($tableName != false && $tableColumns !=false){
                    //call createTable function
                    $this->createTable($tableName,$tableColumns);
                }
                else if($this->throwException){
                    // else if throw exception mode is on throw exception
                    throw new Exception("Syntax error in query: $command)!");
                }
                //else
                //go to next step;
                continue;
            };
            //If command matches with insert pattern
            if(preg_match($insertPattern,$command,$matches)){
                //get table name from pattern
                $tableName = isset($matches[2])?$matches[2]:false;
                //get column queries from command
                $columnNames = isset($matches[4])?$matches[4]:false;
                //get value queries from command
                $columnValues = isset($matches[7])?$matches[7]:false;
                //If tableName, ColumnNames and tableValues are not false
                if($tableName != false && $columnNames !=false && $columnValues!=false){
                    //call createTable function
                    $this->insertTable($tableName,$columnNames,$columnValues);
                }
                else if($this->throwException){
                    // else if throw exception mode is on throw exception
                    throw new Exception("Syntax error in query: $command)!");
                }
                //else
                //go to next step;
                continue;
            }
            array_push($unmatches,$command);
        }
        //var_dump($unmatches);
        $this->executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        return true;
    }
    public function getExecTime(){
        return $this->executionTime;
    }
    public function getExec(){
        return "Command/s completed in $this->executionTime seconds";
    }
    protected function calcFileName($lastVal){
            $limit = 1000;
            if($lastVal%$limit === 0){
                $tempVal = (intval($lastVal/$limit) - 1)*$limit;
                ($lastVal>$limit && $lastVal == 1000)?$startVal = $tempVal:$startVal = $tempVal+1;
                $endVal   = $tempVal + $limit;
                if($endVal - $startVal===1000){
                    echo'1';
                    var_dump($lastVal);
                    exit;
                }
                return("$startVal-$endVal.ndb");
            }
            else{
                $tempVal    = intval($lastVal/$limit)*$limit;
                $startVal   = $tempVal + 1;
                $endVal   = $tempVal + $limit;
                if($endVal - $startVal===1000){
                    echo'2';
                    var_dump($lastVal);
                    exit;
                }
                return("$startVal-$endVal.ndb");
            }
    }
    function __destruct()
    {
        //return "Command/s completed in $this->executionTime seconds";
    }
}
/* throw new Exception('Database name can not be empty (name)'); */