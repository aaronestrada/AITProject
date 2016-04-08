<?php
namespace models;
use \framework\BaseModel;

class Tag extends BaseModel {
    protected $primaryKey = 'id';

    protected $fields = [
        'id',
        'name',
        'status',
        'created_at'
    ];

    public function __construct() {
        parent::__construct();
    }
}