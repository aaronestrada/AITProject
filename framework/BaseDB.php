<?php
namespace framework;
use PDO;

class BaseDB extends \PDO {
    public function __construct() {
        try {
            $db_params = require('../config/db.php');
            if(!empty($db_params['servername']) && !empty($db_params['driver']) && !empty($db_params['database'])) {
                $connectionString =
                    $db_params['driver'] .
                    ':host=' . $db_params['servername'] .
                    ($db_params['port'] !== '' ? ';port=' . $db_params['port'] : '') .
                    ';dbname=' . $db_params['database'] .
                    ';charset=' . ($db_params['charset'] != '' ? $db_params['charset'] : 'utf8mb4');

                parent::__construct($connectionString, $db_params['username'], $db_params['password']);
                $this->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch(\PDOException $ex) {
            echo('Connection failed: ' . $ex->getMessage());
        }
    }
}