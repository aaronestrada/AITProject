<?php
namespace framework;

class BaseModel extends \framework\BaseDB {
    protected $tableName;
    protected $fields = [];
    protected $values = [];

    public function __construct() {
        parent::__construct();
    }

    public function __set($field, $value) {
        $this->values[$field] = $value;
    }

    public function __get($field) {
        if(isset($this->values[$field]))
            return $this->values[$field];
        return null;
    }

    public function fetchAll() {
        $queryValues = $this->query('SELECT * FROM ' . $this->tableName);
        $queryValueList = $queryValues->fetchAll(\PDO::FETCH_ASSOC);

        $fetchObjects = [];
        foreach($queryValueList as $queryValueItem) {
            $baseObject = new self();

            foreach($this->fields as $fieldItem)
                $baseObject->$fieldItem = $queryValueItem[$fieldItem];

            array_push($fetchObjects, $baseObject);
        }

        return $fetchObjects;
    }
}