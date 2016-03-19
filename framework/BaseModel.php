<?php
namespace framework;

class BaseModel extends \framework\BaseDB {
    protected $tableName;

    public function __construct() {
        parent::__construct();
    }

    public function fetchAll() {
        $queryValues = $this->query('SELECT * FROM ' . $this->tableName);
        return($queryValues->fetchAll(\PDO::FETCH_ASSOC));
    }
}