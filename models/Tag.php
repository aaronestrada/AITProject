<?php
namespace models;

use framework\BaseModel;
use framework\BaseQuery;

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

    /**
     * Obtain number of documents associated to a tag
     * @return array|int|null
     */
    public function getDocumentCount() {
        $documentCount = 0;
        if ($this->id != null) {
            $objDocumentQuery = new BaseQuery();
            $objDocumentQuery->select()
                ->andWhere(['tag_id' => $this->id])
                ->count();

            $objDocumentTag = new DocumentTag();
            $documentCount = $objDocumentTag->queryAllFromObject($objDocumentQuery);
        }
        return $documentCount;
    }
}