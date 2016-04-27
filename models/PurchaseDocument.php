<?php

namespace models;

use framework\BaseModel;

class PurchaseDocument extends BaseModel{
    protected $primaryKey = ['purchase_id', 'document_id'];

    protected $fields = [
        'purchase_id' => 'integer',
        'document_id' => 'integer',
        'price' => 'float',
    ];

    public function __construct() {
        parent::__construct();
    }

}