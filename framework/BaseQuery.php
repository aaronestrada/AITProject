<?php

namespace framework;

class BaseQuery {
    private $query = '';
    private $where = [];
    
    public function __construct() {}
    
    public function select($columns = [], $tableName) {
        $this->query = 'SELECT ' . ((count($columns) > 0) ? implode(',', $columns) : '*') . ' FROM ' . $tableName;
        return $this;
    }
    
    public function andWhere($column, $value) {
        $this->where[$column] = $value;
    }
}