<?php
class NoDb {
    private $validTypes = [
        "int",
        "varchar",
        "bool",
        "text",
        "auto_increment",
        "unique_id"
    ];
    function createTable($tableName,$fields){
        if(!is_object($fields)){
            return "Fields are invalid. Only objects are acceptable.";
        }
        $tablePath = __DIR__."\\$tableName";
        if(is_dir($tablePath)){
            return "Table '$tableName' already exists";
        }
        mkdir($tablePath);
        foreach($fields as $name=>$type){
            $type = strval($type);
            if(!in_array($type,$this->validTypes)){
                rmdir($tableName);
                return"invalid type '$type' for $name";
            }
        }
        return 'true';
    }
}