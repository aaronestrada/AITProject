<?php
namespace models;
use \framework\BaseModel;

class Tag extends BaseModel {
    protected $primaryKey = ['id'];

    protected $fields = [
        'id' => 'integer',
        'name' => 'string',
        'status' => 'integer',
        'created_at' => 'date'
    ];

    public function __construct() {
        parent::__construct();
    }
}