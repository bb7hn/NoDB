<?php
//SET MAXIMUM CONNECTION LIMIT TO THE 2000 SECS. (ABOUT 30 MINS.)
set_time_limit(2000);
class NoDB{
    //DEFINE DATABASE VARIABLES
    protected $dbName;
    protected $dbPath;
    //DEFINE VALID COLUMN TYPES
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
    //DEFINE ERROR MODE VARIABLE
    protected $throwException = true;
    //DEFINE EXECUTION TIME VARIABLE TO STORE QUERY EXECUTION TIMES
    protected $executionTime;
    //DEFINE CONSTRUCTOR OF CLASS
    function __construct($dbInfo) {
        $this->executionTime = 0;
        return $this->prepareDB($dbInfo);
    }
    //DEFINE PROTECTED FUNCTIONS
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
        //if password is not defined or empty set it empty
        if(!isset($dbInfo["password"]) || empty($dbInfo["password"])){
            $dbInfo["password"] = '';
        }
        //if host is not defined or empty set it localhost
        if(!isset($dbInfo["host"]) || empty($dbInfo["name"])){
            $dbInfo['host'] = 'localhost';
        }
        //if error mode false set throwException false. With that way functions won't throw exceptions. 
        if(isset($dbInfo["errormode"]) && $dbInfo["errormode"] === false){
            $this->throwException = false;  
        }
        // if does not exists, create the database folder
        $this->dbName = $dbName =  $dbInfo['name'];
        if(!is_dir($dbName)){
            mkdir($dbName);
        }
        //get realPath of database
        $this->dbPath = realpath($dbName);
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
    //DEFINE PUBLIC FUNCTIONS
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
    public function getExecTime(){
        return $this->executionTime;
    }
    public function getExec(){
        return "Command/s completed in $this->executionTime seconds";
    }
    //DEFINE DESTRUCTOR OF CLASS
    function __destruct()
    {
        //return "Command/s completed in $this->executionTime seconds";
    }
}
//HERE IS AN EXAMPLE THROW SYNTAX.
/* throw new Exception("Database name can not be empty (name)"); */