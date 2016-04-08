<?php
namespace framework;

use models;

class BaseModel extends \framework\BaseDB {
    private $tableName;
    protected $primaryKey;
    protected $fields = [];
    protected $values = [];

    /**
     * BaseModel constructor.
     * All the model classes must have the name "<TableName>Model"
     * For tables with underscore, the camel-case syntax must be respected.  For instance,
     * if a table name is "document_title", the class of the model must be "DocumentTitleModel"
     *
     * The names are used to set the table name to be queried inside the BaseModel, which is an
     * instance of \framework\BaseDB, which at the same time is an instance of PDO.
     */
    public function __construct() {
        parent::__construct();

        //Set table name according to the name of the class
        $tableName = str_replace('models\\', '', get_class($this));

        /**
         * Uncamelize value of table.  For tables with name "DocumentName" for example, this function
         * will convert the table name to "document_name".
         */
        $tableName = strtolower(
            preg_replace(
                ["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
                ["_$1", "_$1_$2"],
                lcfirst($tableName)
            )
        );

        //set table name based of the "uncamelized" value
        $this->tableName = $tableName;
    }

    /**
     * Magic method to set field values in a model
     * @param $field Name of the field
     * @param $value Value to be set
     */
    public function __set($field, $value) {
        //Verify that field has been set in list of fields to store value
        if(in_array($field, $this->fields))
            $this->values[$field] = $value;
    }

    /**
     * Magic method to get field values of a model
     * @param $field Name of the field
     * @return mixed|null
     */
    public function __get($field) {
        if(isset($this->values[$field]))
            return $this->values[$field];
        return null;
    }

    /**
     * Fetch one object of the table based on the primary key value
     * @param $id ID of the primary key to be fetched
     * @return BaseModel Value of the database stored in a BaseModel object
     */
    public function fetchOne($id) {
        if(is_numeric($id)) {
            //Step 1: prepare query
            $query = $this->prepare('SELECT * FROM ' . $this->tableName . ' WHERE ' . $this->primaryKey . ' = :id');
            $query->bindValue(':id', $id, \PDO::PARAM_INT);

            try {
                $query->execute();
                $queryValueList = $query->fetch(\PDO::FETCH_ASSOC);

                //if retrieved, obtain values and store them in a new BaseModel object
                if ((is_array($queryValueList)) && (count($queryValueList) > 0)) {
                    $className = get_class($this);
                    $baseObject = new $className;

                    //store field values in class object
                    foreach ($this->fields as $fieldItem)
                        $baseObject->$fieldItem = $queryValueList[$fieldItem];

                    return $baseObject;
                }
                return null;
            }
            catch (\PDOException $e){
                \framework\BaseError::throwMessage(404, 'Error on retrieving data: ' . $e->getMessage());
            }
        }
    }

    /**
     * Fetch all the objects of a table as BaseModel mapped objects
     * @return array List of BaseModel objects
     */
    public function fetchAll() {
        try {
            //Step 1: prepare query
            $query = $this->prepare('SELECT * FROM ' . $this->tableName);
            $query->execute();
            $queryValueList = $query->fetchAll(\PDO::FETCH_ASSOC);

            $fetchObjects = [];
            foreach ($queryValueList as $queryValueItem) {
                //Generate new instances of BaseModel object
                $className = get_class($this);
                $baseObject = new $className;

                //Set fields for new BaseModel object
                foreach ($this->fields as $fieldItem)
                    $baseObject->$fieldItem = $queryValueItem[$fieldItem];

                array_push($fetchObjects, $baseObject);
            }

            return count($fetchObjects) > 0 ? $fetchObjects : null;
        }
        catch (\PDOException $e){
            \framework\BaseError::throwMessage(404, 'Error on retrieving data: ' . $e->getMessage());
        }
    }

    /**
     * Insert stored values of a BaseModel mapped object
     * Use the fields and the values to form the insert query
     */
    public function insert() {
        $fieldValues = [];
        $paramItems = [];

        //verifies that field values are not empty prior to create the insert statement
        foreach ($this->fields as $fieldItem)
            if(($this->$fieldItem != '') && ($this->$fieldItem != null)) {
                array_push($fieldValues, $fieldItem);
                array_push($paramItems, ':' . $fieldItem);
            }

        if(count($fieldValues) > 0) {
            try {
                $queryString = 'INSERT INTO ' . $this->tableName . '(' . implode(',', $fieldValues) . ') VALUES (' . implode(',', $paramItems) . ')';
                $query = $this->prepare($queryString);

                //bind each of the values stored in the model
                foreach ($fieldValues as $fieldItem)
                    $query->bindValue(':' . $fieldItem, $this->$fieldItem);

                /**
                 * Execute the insertion and store ID value in the primary key field,
                 * so it is still possible to use the object to make any updates
                 */
                if ($query->execute())
                    $this->{$this->primaryKey} = $this->lastInsertId();
            }
            catch (\PDOException $e){
                \framework\BaseError::throwMessage(404, 'Error on inserting data: ' . $e->getMessage());
            }
        }
    }

    /**
     * Updates a BaseModel mapped object into the database
     * @return bool Whether the update operation is successful or not
     */
    public function update() {
        try {
            //Verifies that primary key value is not empty to make the update
            if($this->{$this->primaryKey} != '') {
                $fieldSet = [];

                //set fields to be set as parameters of update operation
                foreach ($this->fields as $fieldItem)
                    array_push($fieldSet, $fieldItem . ' = :' . $fieldItem);

                //create update string
                $queryString = 'UPDATE ' . $this->tableName
                    . ' SET ' . implode(',', $fieldSet)
                    . ' WHERE ' . $this->primaryKey . ' = :' . $this->primaryKey;

                $query = $this->prepare($queryString);
                foreach ($this->fields as $fieldItem)
                    $query->bindValue(':' . $fieldItem, $this->$fieldItem);

                if ($query->execute())
                    return true;
            }

            return false;
        }
        catch (\PDOException $e){
            \framework\BaseError::throwMessage(404, 'Error on updating data: ' . $e->getMessage());
        }
    }

    /**
     * Deletes a BaseModel mapped object
     * Makes use of the primary key
     */
    public function delete() {
        try {
            //Verifies that primary key value is not empty to make the update
            if($this->{$this->primaryKey} != '') {
                //create update string
                $queryString = 'DELETE FROM  ' . $this->tableName . ' WHERE ' . $this->primaryKey . ' = :' . $this->primaryKey;

                $query = $this->prepare($queryString);
                $query->bindValue(':' . $this->primaryKey, $this->{$this->primaryKey});
                if ($query->execute()) {
                    //unset values
                    foreach ($this->fields as $fieldItem)
                        unset($this->$fieldItem);

                    return true;
                }
            }

            return false;
        }
        catch (\PDOException $e){
            \framework\BaseError::throwMessage(404, 'Error on deleting data: ' . $e->getMessage());
        }
    }
}