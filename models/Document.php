<?php
namespace models;
use \framework\BaseModel;

class Document extends BaseModel {
    protected $primaryKey = 'id';

    protected $fields = [
        'id',
        'author_id',
        'status',
        'name',
        'description',
        'price',
        'created_at',
        'modified_at',
        'published_at',
        'filename'
    ];

    public function __construct() {
        parent::__construct();
    }

    public function getAllDocuments() {
        return $this->fetchAll();
    }
}