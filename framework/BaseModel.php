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
        if (in_array($field, array_keys($this->fields)))
            $this->values[$field] = $value;
    }

    /**
     * Magic method to get field values of a model
     * @param $field Name of the field
     * @return mixed|null
     */
    public function __get($field) {
        if (isset($this->values[$field]))
            return $this->values[$field];
        return null;
    }

    /**
     * Mapping between field types and PDO types
     * @param $fieldType Field type
     * @return int PDO mapped type
     */
    private function getPDOType($fieldType) {
        $fieldPDOType = \PDO::PARAM_STR;
        switch ($fieldType) {
            case 'integer':
                $fieldPDOType = \PDO::PARAM_INT;
                break;
            case 'boolean':
                $fieldPDOType = \PDO::PARAM_BOOL;
                break;
        }

        return $fieldPDOType;
    }

    /**
     * Fetch one object of the table based on the primary key value.
     * @param $id ID of the primary key to be fetched.  Can be a single value or a list of keys.
     *            If a set of values is passed as parameter but the table has set a single key,
     *            it will ignore the rest of keys
     * @return BaseModel Value of the database stored in a BaseModel object
     */
    public function fetchOne($id) {
        $primaryKeyConstraint = [];

        //verify if list is array to set keys
        if (is_array($id)) {
            foreach ($id as $primaryKey => $fetchValue)
                if (in_array($primaryKey, $this->primaryKey))
                    $primaryKeyConstraint[$primaryKey] = $fetchValue;
        } else {
            //fetch object is a single value, set to the last primary key found in list
            $primaryKeyName = '';
            foreach ($this->primaryKey as $primaryKeyItem)
                $primaryKeyName = $primaryKeyItem;

            $primaryKeyConstraint[$primaryKeyName] = $id;
        }

        //Step 1: prepare query
        if (count($primaryKeyConstraint) > 0) {
            $wherePrimaryKey = [];
            foreach (array_keys($primaryKeyConstraint) as $primaryKey)
                array_push($wherePrimaryKey, $primaryKey . ' = :' . $primaryKey);

            //construct query
            $query = $this->prepare('SELECT * FROM ' . $this->tableName . ' WHERE ' . implode(' AND ', $wherePrimaryKey));

            //prepare key values
            foreach ($primaryKeyConstraint as $primaryKey => $primaryKeyValue)
                $query->bindValue(':' . $primaryKey, $primaryKeyValue, $this->getPDOType(isset($this->fields[$primaryKey]) ? $this->fields[$primaryKey] : ''));

            try {
                $query->execute();
                $queryValueList = $query->fetch(\PDO::FETCH_ASSOC);

                //if retrieved, obtain values and store them in a new BaseModel object
                if ((is_array($queryValueList)) && (count($queryValueList) > 0)) {
                    $className = get_class($this);
                    $baseObject = new $className;

                    //store field values in class object
                    foreach (array_keys($this->fields) as $fieldItem)
                        $baseObject->$fieldItem = $queryValueList[$fieldItem];

                    return $baseObject;
                }
                return null;
            } catch (\PDOException $e) {
                \framework\BaseError::throwMessage(404, 'Error on retrieving data: ' . $e->getMessage());
            }
        }
        return null;
    }
    
    /**
     * Construct a SELECT query and store retrieved values as list of ORM objects  
     * @param $queryText Query to be retrieved
     * @param array $queryParams List of parameters to be set on prepare statement
     * @param bool $first Obtain only first element
     * @return array|null List of ORM objects
     */
    private function fetchAllFromQuery($queryText, $queryParams = [], $first = false) {
        try {
            //Step 1: prepare query
            $query = $this->prepare($queryText);

            foreach($queryParams as $queryKey => $queryValue) 
                $query->bindValue(':' . $queryKey, $queryValue, $this->getPDOType(isset($this->fields[$queryKey]) ? $this->fields[$queryKey] : ''));

            $query->execute();
            $queryValueList = $query->fetchAll(\PDO::FETCH_ASSOC);

            $fetchObjects = [];
            foreach ($queryValueList as $queryValueItem) {
                //Generate new instances of BaseModel object
                $className = get_class($this);
                $baseObject = new $className;

                //Set fields for new BaseModel object
                foreach (array_keys($this->fields) as $fieldItem)
                    $baseObject->$fieldItem = $queryValueItem[$fieldItem];

                if($first === true)
                    return $baseObject;

                array_push($fetchObjects, $baseObject);
            }

            return count($fetchObjects) > 0 ? $fetchObjects : null;
        } catch (\PDOException $e) {
            \framework\BaseError::throwMessage(404, 'Error on retrieving data: ' . $e->getMessage());
        }
    }
    
    
    /**
     * Fetch all the objects of a table as BaseModel mapped objects
     * @return array List of BaseModel objects
     */
    public function fetchAll() {
        return $this->fetchAllFromQuery('SELECT * FROM ' . $this->tableName);
    }

    /**
     * Insert stored values of a BaseModel mapped object
     * Use the fields and the values to form the insert query
     */
    public function insert() {
        $fieldValues = [];
        $paramItems = [];

        //verifies that field values are not empty prior to create the insert statement
        foreach ($this->fields as $fieldItem => $fieldType)
            if (($this->$fieldItem != '') && ($this->$fieldItem != null)) {
                $fieldValues[$fieldItem] = $fieldType;
                array_push($paramItems, ':' . $fieldItem);
            }

        if (count($fieldValues) > 0) {
            try {
                $queryString = 'INSERT INTO ' . $this->tableName . '(' . implode(',', array_keys($fieldValues)) . ') VALUES (' . implode(',', $paramItems) . ')';
                $query = $this->prepare($queryString);

                //bind each of the values stored in the model
                foreach ($fieldValues as $fieldItem => $fieldType)
                    $query->bindValue(':' . $fieldItem, $this->$fieldItem, $this->getPDOType($fieldType));

                /**
                 * Execute the insertion and store ID value in the primary key field,
                 * so it is still possible to use the object to make any updates
                 */
                if ($query->execute())
                    $this->{$this->primaryKey} = $this->lastInsertId();
            } catch (\PDOException $e) {
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
            $primaryKeyConstraints = [];
            foreach ($this->primaryKey as $primaryKeyItem)
                array_push($primaryKeyConstraints, $primaryKeyItem . ' = :' . $primaryKeyItem);

            //Verifies that primary key value is not empty to make the update
            if (count($primaryKeyConstraints) > 0) {
                $fieldSet = [];

                //set fields to be set as parameters of update operation
                foreach (array_keys($this->fields) as $fieldItem)
                    array_push($fieldSet, $fieldItem . ' = :' . $fieldItem);

                //create update string
                $queryString = 'UPDATE ' . $this->tableName
                    . ' SET ' . implode(',', $fieldSet)
                    . ' WHERE ' . implode(' AND ', $primaryKeyConstraints);

                $query = $this->prepare($queryString);
                foreach ($this->fields as $fieldItem => $fieldType)
                    $query->bindValue(':' . $fieldItem, $this->$fieldItem, $this->getPDOType($fieldType));

                if ($query->execute())
                    return true;
            }

            return false;
        } catch (\PDOException $e) {
            \framework\BaseError::throwMessage(404, 'Error on updating data: ' . $e->getMessage());
        }
    }

    /**
     * Construct a query based on the settings of a BaseQuery object and retrieve all the data
     * @param BaseQuery $queryObject Object with SELECT settings
     * @return array|null List of retrieved objects
     */
    public function queryAllFromObject(BaseQuery $queryObject) {
        //set table name to query object
        $queryObject->setTable($this->tableName);
        
        $queryText = $queryObject->constructQuery();
        $queryParams = $queryObject->getParameters();
        return $this->fetchAllFromQuery($queryText, $queryParams);
    }

    /**
     * Construct a query based on the settings of a BaseQuery object and retrieve one object
     * @param BaseQuery $queryObject Object with SELECT settings
     * @return BaseModel|null Retrieved object
     */
    public function queryOneFromObject(BaseQuery $queryObject) {
        //set table name to query object
        $queryObject->setTable($this->tableName);

        $queryText = $queryObject->constructQuery();
        $queryParams = $queryObject->getParameters();
        return $this->fetchAllFromQuery($queryText, $queryParams, true);
    }

    /**
     * Deletes a BaseModel mapped object
     * Makes use of the primary key
     */
    public function delete() {
        try {
            //Verifies that primary key value is not empty to make the update
            if ($this->{$this->primaryKey} != '') {
                //create update string
                $queryString = 'DELETE FROM  ' . $this->tableName . ' WHERE ' . $this->primaryKey . ' = :' . $this->primaryKey;

                $query = $this->prepare($queryString);
                $query->bindValue(':' . $this->primaryKey, $this->{$this->primaryKey});
                if ($query->execute()) {
                    //unset values
                    foreach (array_keys($this->fields) as $fieldItem)
                        unset($this->$fieldItem);

                    return true;
                }
            }

            return false;
        } catch (\PDOException $e) {
            \framework\BaseError::throwMessage(404, 'Error on deleting data: ' . $e->getMessage());
        }
    }
}