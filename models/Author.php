<?php
namespace models;

use \framework\BaseModel;

class Author extends BaseModel {
    protected $primaryKey = ['id'];

    protected $fields = [
        'id' => 'integer',
        'name' => 'string',
        'contact' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'created_at' => 'date',
        'modified_at' => 'date'
    ];

    public function __construct() {
        parent::__construct();
    }
}