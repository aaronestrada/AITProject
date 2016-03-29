<?php
namespace models;
use \framework\BaseModel;

class Document extends BaseModel {
    protected $tableName = 'document';

    protected $fields = [
        'id',
        'filename',
        'funder',
        'status',
        'created_at'
    ];

    public function __construct() {
        parent::__construct();
    }

    public function getAllDocuments() {
        return $this->fetchAll();
    }
}