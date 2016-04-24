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
     * Get all the documents associated to a user
     * @return array|null
     */
    public function getDocumentsInCart() {
        if($this->id != null) {
            $userDocumentCartQuery = new BaseQuery();
            $userDocumentCartQuery->select()
                ->andWhere(['user_id' => $this->id]);

            $objUserDocumentCart = new UserDocumentCart();
            $documentCartList = $objUserDocumentCart->queryAllFromObject($userDocumentCartQuery);

            if($documentCartList != null) {
                $documentItems = [];
                foreach($documentCartList as $documentCartItem)
                    array_push($documentItems, $documentCartItem->document_id);

                $documentQuery = new BaseQuery();
                $documentQuery->select()->
                    andInWhere('id', $documentItems);

                $objDocument = new Document();
                $documentList = $objDocument->queryAllFromObject($documentQuery);

                if($documentList != null)
                    return $documentList;
            }
        }
        return [];
    }
}