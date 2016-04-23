<?php

namespace framework;

/**
 * Class BaseQuery
 * Construct a SELECT query to obtain values of a model.
 *
 * @package framework
 */
class BaseQuery {
    private $queryColumns = '';
    private $tableName = '';
    private $whereConditions = [];
    private $inWhereConditions = [];
    private $orderConditions = [];
    private $limitValue = null;
    private $offsetValue = null;
    private $parameters = [];
    private $joinConditions = [];
    private $tablePseudonim = '';

    public function __construct($tablePseudonim = '') {
        $this->tablePseudonim = $tablePseudonim;
    }

    /**
     * Set the columns to retrieve in the select query
     * @param array $columns List of columns.  If empty, retrieve all columns
     * @return $this This object
     */
    public function select($columns = []) {
        $this->queryColumns = ((count($columns) > 0) ? $columns : '');
        return $this;
    }

    /**
     * Set name of table
     * @param $tableName Table name
     */
    public function setTable($tableName) {
        $this->tableName = $tableName;
    }

    /**
     * Add a joining table to the query
     * @param $joinType Type of join (left, join, inner = '')
     * @param $joinTable Table to join
     * @param $joinPseudonim Pseudonim for joined table
     * @param array $columnsJoin List of columns to join with new table
     * @return $this This object
     */
    private function setJoin($joinType, $joinTable, $joinPseudonim, $columnsJoin = []) {
        array_push($this->joinConditions, [
            'type' => $joinType,
            'table' => $joinTable,
            'pseudonim' => $joinPseudonim,
            'columns' => $columnsJoin
        ]);
        return $this;
    }

    /**
     * Add a joining table to the query usint LEFT JOIN
     * @param $joinTable Table to join
     * @param $joinPseudonim Pseudonim for joined table
     * @param array $columnsJoin List of columns to join with new table
     * @return $this This object
     */
    public function joinLeft($joinTable, $joinPseudonim, $columnsJoin = []) {
        return $this->setJoin('LEFT', $joinTable, $joinPseudonim, $columnsJoin);
    }

    /**
     * Add a joining table to the query using RIGHT JOIN
     * @param $joinTable Table to join
     * @param $joinPseudonim Pseudonim for joined table
     * @param array $columnsJoin List of columns to join with new table
     * @return $this This object
     */
    public function joinRight($joinTable, $joinPseudonim, $columnsJoin = []) {
        return $this->setJoin('RIGHT', $joinTable, $joinPseudonim, $columnsJoin);
    }

    /**
     * Add a joining table to the query using INNER JOIN     
     * @param $joinTable Table to join
     * @param $joinPseudonim Pseudonim for joined table
     * @param array $columnsJoin List of columns to join with new table
     * @return $this This object
     */
    public function join($joinTable, $joinPseudonim, $columnsJoin = []) {
        return $this->setJoin('', $joinTable, $joinPseudonim, $columnsJoin);
    }

    /**
     * Include an IN where condition in the object
     * @param $fieldName Name of the field to include as IN
     * @param $itemList List of items to check in the IN condition
     * @param string $operator Type of preposition (AND or OR)
     * @return $this This object
     */
    private function setInWhere($fieldName, $itemList, $operator = 'AND') {
        array_push($this->inWhereConditions, [
            'field' => $fieldName,
            'list' => $itemList,
            'operator' => $operator
        ]);
        return $this;
    }

    /**
     * Add an IN where condition using the AND preposition
     * @param $fieldName Name of the field to include as IN
     * @param $itemList List of items to check in the IN condition
     * @return BaseQuery This object
     */
    public function andInWhere($fieldName, $itemList) {
        return $this->setInWhere($fieldName, $itemList, 'AND');
    }

    /**
     * Add an IN where condition using the OR preposition
     * @param $fieldName Name of the field to include as IN
     * @param $itemList List of items to check in the IN condition
     * @return BaseQuery This object
     */
    public function orInWhere($fieldName, $itemList) {
        return $this->setInWhere($fieldName, $itemList, 'OR');
    }

    /**
     * Add a WHERE condition (private use for andWhere and orWhere functions
     * @param $operator Type of operator to add as condition (AND, OR)
     * @param $conditionItems List of condition
     * @param string $comparisonOperator Operator to use as comparison (LIKE, =, <>, >, <)
     * @param string $insideComparison Internal comparison (AND or OR)
     * @return $this This object
     */
    private function setWhereCondition($operator, $conditionItems, $comparisonOperator = '=', $insideComparison = 'AND') {
        if(is_array($conditionItems)) {
            array_push($this->whereConditions, [
                'operator' => $operator,
                'comparison' => $comparisonOperator,
                'conditions' => $conditionItems,
                'inside_comparison' => $insideComparison
            ]);
        }
        return $this;
    }

    /**
     * Add a WHERE (AND) condition
     * @param $conditionItems List of conditions ['param1' => 'param1val', 'param2' => 'param2val'...]
     * @param string $comparisonOperator Operator to use as comparison (LIKE, =, <>, >, <)
     * @param string $insideComparison Internal comparison (AND or OR)
     * @return $this This object
     */
    public function andWhere($conditionItems, $comparisonOperator = '=', $insideComparison = 'AND') {
        return $this->setWhereCondition('AND', $conditionItems, $comparisonOperator, $insideComparison);
    }

    /**
     * Add a WHERE (OR condition
     * @param $conditionItems List of conditions ['param1' => 'param1val', 'param2' => 'param2val'...]
     * @param string $comparisonOperator Operator to use as comparison (LIKE, =, <>, >, <)
     * @param string $insideComparison Internal comparison (AND or OR)
     * @return $this This object
     */
    public function orWhere($conditionItems, $comparisonOperator = '=', $insideComparison = 'AND') {
        return $this->setWhereCondition('OR', $conditionItems, $comparisonOperator, $insideComparison);
    }

    /**
     * Set order conditions
     * @param $orderConditions ['val1' => 'ASC', 'val2' => 'DESC' ...]
     * @return $this This object
     */
    public function order($orderConditions) {
        $this->orderConditions = $orderConditions;
        return $this;
    }

    /**
     * Set limit to the query
     * @param $limitValue Limit value
     * @return $this This object
     */
    public function limit($limitValue) {
        $this->limitValue = $limitValue;
        return $this;
    }

    /**
     * Set offset to the query
     * @param $offsetValue Offset value
     * @return $this This object
     */
    public function offset($offsetValue) {
        $this->offsetValue = $offsetValue;
        return $this;
    }

    /**
     * Remove points to bind parameters
     * @param $paramName Parameter name
     * @return mixed
     */
    private function getBindParameterName($paramName) {
        return str_replace('.', '', $paramName);
    }
    /**
     * Set parameters values from the WHERE clauses
     * @param $id key of the parameter
     * @param $value value of the parameter
     */
    private function setParameter($id, $value) {
        $this->parameters[$this->getBindParameterName($id)] = $value;
    }

    /**
     * Return list of parameters from WHERE clauses
     * @return array List
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * Final construction of the query based on class settings
     * @return string Query to be executed
     */
    public function constructQuery() {
        if($this->tableName != '') {

            //Step 1: construct SELECT
            $queryStructure = [];

            if($this->queryColumns == '')
                $queryColumns = ($this->tablePseudonim != '' ? $this->tablePseudonim . '.' : '') . '*';
            else
                $queryColumns = implode(', ', $this->queryColumns);

            array_push($queryStructure, 'SELECT ' . $queryColumns . ' FROM ' . $this->tableName . ($this->tablePseudonim != '' ? ' ' . $this->tablePseudonim : ''));

            if(count($this->joinConditions) > 0)
                foreach($this->joinConditions as $joinItem) {
                    $joinTable = $joinItem['table'];
                    $joinPseudonim = $joinItem['pseudonim'];
                    $joinType = $joinItem['type'];

                    array_push($queryStructure, $joinType . ' JOIN ' . $joinTable . ' ' . $joinPseudonim . (count($joinItem['columns']) > 0 ? ' ON ' . implode(' AND ', $joinItem['columns']) : ''));
                }

            //Step 2: construct WHERE
            if(count($this->whereConditions) > 0) {
                $whereList = [];
                $firstCondition = true;

                //Step 2.1: Construct WHERE conditions
                foreach ($this->whereConditions as $conditionBlock) {
                    $conditionOperator = trim(strtoupper($conditionBlock['operator']));
                    $conditionComparison = trim(strtoupper($conditionBlock['comparison']));

                    if((in_array($conditionOperator, ['AND', 'OR'])) && (in_array($conditionComparison, ['=', '<>', '>', '<', 'LIKE', 'IN']))) {
                        $conditionWhere = [];
                        foreach($conditionBlock['conditions'] as $conditionItem => $conditionValue) {
                            array_push($conditionWhere,
                                $conditionItem .
                                ' ' .
                                $conditionComparison .
                                ' :' .
                                $this->getBindParameterName($conditionItem)
                            );

                            if($conditionComparison == 'LIKE')
                                $conditionValue = '%' . $conditionValue . '%';

                            $this->setParameter($conditionItem, $conditionValue);
                        }

                        if($firstCondition) {
                            $conditionOperator = '';
                            $firstCondition = false;
                        }
                        $insideComparison = in_array($conditionBlock['inside_comparison'], ['AND', 'OR']) ? $conditionBlock['inside_comparison'] : 'AND';
                        array_push($whereList, $conditionOperator . '(' . implode(' ' . $insideComparison . ' ', $conditionWhere) . ')');
                    }
                }

                //Step 2.2: Construct IN conditions
                foreach($this->inWhereConditions as $inConditionBlock) {
                    $conditionField = $inConditionBlock['field'];
                    $conditionList = $inConditionBlock['list'];
                    $conditionOperator = trim(strtoupper($inConditionBlock['operator']));

                    if(in_array($conditionOperator, ['AND', 'OR'])) {
                        $counter = 0;
                        if(is_array($conditionList)) {
                            $inWhereList = [];
                            foreach($conditionList as $conditionValue) {
                                array_push($inWhereList, $conditionField . ' = :' . $this->getBindParameterName($conditionField .$counter));
                                $this->setParameter($conditionField .$counter, $conditionValue);
                                $counter++;
                            }

                            if($firstCondition) {
                                $conditionOperator = '';
                                $firstCondition = false;
                            }
                            array_push($whereList, $conditionOperator . '(' . implode(' OR ', $inWhereList) . ')');
                        }
                    }
                }
                
                array_push($queryStructure, 'WHERE ' . implode(' ', $whereList));
            }

            //Step 3: Construct ORDER
            if (is_array($this->orderConditions)) {
                if (count($this->orderConditions) > 0) {
                    $orderConditionList = [];
                    array_push($queryStructure, 'ORDER BY');

                    foreach ($this->orderConditions as $orderItem => $orderValue) {
                        $orderValue = trim(strtoupper($orderValue));

                        if (in_array($orderValue, ['ASC', 'DESC']))
                            array_push($orderConditionList, $orderItem . ' ' . $orderValue);
                    }

                    //push order values into structure
                    array_push($queryStructure, implode(', ', $orderConditionList));
                }
            }

            //Step 4: Construct limit
            if ($this->limitValue != null)
                if (is_numeric($this->limitValue) && $this->limitValue > 0)
                    //push limit value into structure
                    array_push($queryStructure, 'LIMIT ' . $this->limitValue);

            //Step 5: Construct offset
            if ($this->offsetValue != null)
                if (is_numeric($this->offsetValue) && $this->offsetValue > 0)
                    //push offset value into structure
                    array_push($queryStructure, 'OFFSET ' . $this->offsetValue);

            //Step 6: return final construction
            if (count($queryStructure) > 0)
                //return structure glued by space
                return implode(' ', $queryStructure);
        }

        return '';
    }
}