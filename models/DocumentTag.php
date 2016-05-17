<?php
namespace models;

use \framework\BaseModel;


class DocumentTag extends BaseModel {
    protected $primaryKey = ['document_id', 'tag_id'];

    protected $fields = [
        'document_id' => 'integer',
        'tag_id' => 'integer',
    ];

    public function __construct() {
        parent::__construct();
    }
}
