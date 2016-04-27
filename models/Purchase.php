<?php

namespace models;

use framework\BaseModel;

class Purchase extends BaseModel {
    protected $primaryKey = ['id'];

    protected $fields = [
        'id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'date',
    ];

    public function __construct() {
        parent::__construct();
    }
    
}