<?php
namespace models;

use framework\BaseModel;

class UserDocumentCart extends BaseModel{
    protected $primaryKey = ['document_id', 'user_id'];

    protected $fields = [
        'document_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function __construct() {
        parent::__construct();
    }
}