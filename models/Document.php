<?php
namespace models;
use \framework\BaseModel;

class Document extends BaseModel {
    protected $tableName = 'document';

    public function __construct() {
        parent::__construct();
    }

    public function getAllDocuments() {
        return $this->fetchAll();
    }
}