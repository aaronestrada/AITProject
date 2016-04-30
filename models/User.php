<?php
namespace models;
use framework\BaseEncryption;
use \framework\BaseModel;
use framework\BaseQuery;

class User extends BaseModel {
    protected $primaryKey = ['id'];

    protected $fields = [
        'id' => 'integer',
        'email' => 'string',
        'password' => 'string',
        'password_iv' => 'string',
        'firstname' => 'string',
        'lastname' => 'string',
        'birthdate' => 'date',
        'status' => 'integer',
        'created_at' => 'date',
        'modified_at' => 'date',
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Set password value to user.  Password will be encrypted and stored with the
     * respective initialization vector.
     *
     * @param $password Password value
     */
    public function setPassword($password) {
        $encryption = new BaseEncryption();
        $encrypteData = $encryption->encrypt($password);

        $this->password = $encrypteData['encryptedData'];
        $this->password_iv = $encrypteData['initializationVector'];
    }

    /**
     * Validate a password user
     * @param $password Password to be checked
     * @return bool Whether the password is equal or not
     */
    public function checkPassword($password) {
        $encryption = new BaseEncryption();
        $passwordValue = $encryption->decrypt($this->password, $this->password_iv);

        return $passwordValue == $password ? true : false;
    }


    /**
     * Get list of all document IDs inside the user's shopping cart
     * @return array List of document IDs
     */
    public function getDocumentCartItems() {
        $documentItems = [];
        if($this->id != null) {
            //Create new query: SELECT * FROM user_document_cart WHERE user_id = <user_id>
            $userDocumentCartQuery = new BaseQuery();
            $userDocumentCartQuery->select()
                ->andWhere(['user_id' => $this->id]);

            $objUserDocumentCart = new UserDocumentCart();
            $documentCartList = $objUserDocumentCart->queryAllFromObject($userDocumentCartQuery);

            if ($documentCartList != null) {
                foreach ($documentCartList as $documentCartItem)
                    array_push($documentItems, $documentCartItem->document_id);
            }
        }
        return $documentItems;
    }

    /**
     * Get all the documents associated to a user
     * @return array|null
     */
    public function getDocumentsInCart() {
        if($this->id != null) {
            $documentItems = $this->getDocumentCartItems();

            if(count($documentItems) > 0) {
                //Create new query: SELECT * FROM document WHERE id IN [<document_list>]
                $documentQuery = new BaseQuery();
                $documentQuery->select()
                    ->andInWhere('id', $documentItems);

                $objDocument = new Document();
                $documentList = $objDocument->queryAllFromObject($documentQuery);

                if ($documentList != null)
                    return $documentList;
            }
        }
        return [];
    }

    /**
     * Get list of all document associated to a purchase
     */
    public function getPurchasedDocumentItems() {
        $documentItems = [];
        if($this->id != null) {
            /**
             * Create new query:
             * SELECT document_id FROM purchase_document pr_doc
             * JOIN purchase purch ON purch.id = pr_doc.purchase_id
             * WHERE purch.user_id = <user_id>
             */
            $purchaseQuery = new BaseQuery('pr_doc');

            $purchaseQuery->select(['document_id'])
                ->join('purchase', 'purch', ['purch.id = pr_doc.purchase_id'])
                ->andWhere(['purch.user_id' => $this->id]);
            
            $objPurchaseDocument = new PurchaseDocument();
            $purchaseDocumentList = $objPurchaseDocument->queryAllFromObject($purchaseQuery);
            
            if($purchaseDocumentList != null)
                foreach($purchaseDocumentList as $purchaseDocumentItem) 
                    array_push($documentItems, $purchaseDocumentItem->document_id);
        }
        return $documentItems;
    }
}