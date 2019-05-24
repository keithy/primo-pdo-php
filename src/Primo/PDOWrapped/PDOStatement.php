<?php

// Specifying an alternative PDOStatement class is possible, but it
// Cannot be used with persistent PDO instances.
//
// However we extend the interface provided by the non-wrapped version

namespace Primo\PDOWrapped;

class PDOStatement extends \Primo\PDOSubclassed\PDOStatement {

    protected $PDOStatement;

    /**
     * Sets the PDO logging array instance and prepared statement.
     *
     * @param Pdo           $pdo       The PDO logging class instance.
     * @param \PDOStatement $statement The original prepared statement.
     */
    function __construct( \Primo\PDOWrapped\PDO $pdo, \PDOStatement $PDOStatement) {
        parent::__construct($pdo);
        $this->PDOStatement = $PDOStatement;
    }

    /**
     * Relay all calls.
     *
     * @param string $name      The method name to call.
     * @param array  $arguments The arguments for the call.
     *
     * @return mixed The call results.
     */
    function bindColumn($column, &$param, $type = NULL, $maxlen = NULL, $driverdata = NULL) {
        return $this->PDOStatement->bindColumn($column, $param, $type, $maxlen, $driverdata);
    }

    function bindParam($parameter, &$variable, $data_type = \PDO::PARAM_STR, $maxlen = NULL, $driverdata = NULL) {
        $this->bindings[$parameter] = $variable;
        return $this->PDOStatement->bindParam($parameter, $variable, $data_type, $maxlen, $driverdata);
    }

    function bindValue($parameter, $variable, $data_type = \PDO::PARAM_STR) {
        $this->bindings[$parameter] = $variable;
        return $this->PDOStatement->bindValue($parameter, $variable, $data_type);
    }

    function closeCursor() {
        return $this->PDOStatement->closeCursor();
    }

    function columnCount() {
        return $this->PDOStatement->columnCount();
    }

    function debugDumpParams() {
        return $this->PDOStatement->debugDumpParams();
    }

    function errorCode() {
        return $this->PDOStatement->errorCode();
    }

    function errorInfo() {
        return $this->PDOStatement->errorInfo();
    }

    function fetch($how = null, $class_name = null, $ctor_args = null) {
        if (!isset($how)) return $this->PDOStatement->fetch();
        if (!isset($class_name)) return $this->PDOStatement->fetch($how);
        if (!isset($ctor_args)) return $this->PDOStatement->fetch($how, $class_name);
        return $this->PDOStatement->fetch($how, $class_name, $ctor_args);
    }

//    function fetch($how = NULL, $orientation = NULL, $offset = NULL) {
//        return $this->PDOStatement->fetch($how, $orientation, $offset);
//    }

    function fetchAll($how = null, $class_name = null, $ctor_args = null) {
        if (!isset($how)) return $this->PDOStatement->fetchAll();
        if (!isset($class_name)) return $this->PDOStatement->fetchAll($how);
        if (!isset($ctor_args)) return $this->PDOStatement->fetchAll($how, $class_name);
        return $this->PDOStatement->fetchAll($how, $class_name, $ctor_args);
    }

//    function fetchAll($how = null, $class_name = null, $ctor_args = null) {
//        return $this->PDOStatement->fetchAll($how, $class_name, $ctor_args);
//    }

    function fetchColumn($column_number = 0) {
        return $this->PDOStatement->fetchColumn($column_number);
    }

//    function fetchObject($class_name = NULL, $ctor_args = NULL) {
//        return $this->PDOStatement->fetchObject($class_name, $ctor_args);
//    }

    function fetchObject($class_name = null, $ctor_args = null) {
        if (!isset($class_name)) return $this->PDOStatement->fetchObject();
        if (!isset($ctor_args)) return $this->PDOStatement->fetchObject($class_name);
        return $this->PDOStatement->fetchObject($class_name, $ctor_args);
    }

    function getAttribute($attribute) {
        return $this->PDOStatement->getAttribute($attribute);
    }

    function getColumnMeta($column) {
        return $this->PDOStatement->getColumnMeta($column);
    }

    function nextRowSet() {
        return $this->PDOStatement->nextRowSet();
    }

    function rowCount() {
        return $this->PDOStatement->rowCount();
    }

    function setAttribute($attribute, $value) {
        return $this->PDOStatement->setAttribute($attribute, $value);
    }

    function setFetchMode($mode, $params = NULL) {
        return $this->PDOStatement->setFetchMode($mode, $params);
    }

    function queryString() {
        return $this->PDOStatement->queryString;
    }

    function parentExecute($params) {
        return $this->PDOStatement->execute($params);
    }

}
