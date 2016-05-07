<?php

namespace models;

use framework\BaseModel;
use framework\BaseQuery;

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

    /**
     * Obtain the list of documents from a purchase item
     * @return array|null List of documents
     */
    public function getPurchasedDocuments() {
        if($this->id != null) {
            $documentPurchaseQuery = new BaseQuery();
            $documentPurchaseQuery->select()
                ->andWhere(['purchase_id' => $this->id])
                ->order(['document_id' => 'ASC']);

            $objPurchaseDocument = new PurchaseDocument();
            return $objPurchaseDocument->queryAllFromObject($documentPurchaseQuery);
        }
        return [];
    }
    
}