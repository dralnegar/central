<?php

/**
 * @Filename: CentralPDOMySQL.php
 * Location: /_includes/_central
 * Function: Class containing various MySQL helper functions
 * @Creator: Stefan Harvey (SAH)
 * Changes:
 *  20130101 SAH Created
 *  20140709 SAH Changed constructor (_construct) to allow an instance to be created without the need to connect to a database, and thus make the 4 parameter
 * 				 no longer mandatory.
 *  20160531 SAH Changed constructor (_construct) to correct the use of the $e->getErrorDetails and use $e->getCode and $e->getMessage instead. Also changed 
 *               recordError() to use $_SERVER['DOCUMENT_ROOT'] at the front of the file being opened, as it wasn't working with it on some servers
 */
/*
  // In order to use this file as a codeigniter model the file name must be 'centralpdomysql.php' in lowercase and you must use 
  class CentralPDO extends CI_Model
  // instead of
  class CentralPDOMySQL
 */
namespace Central;

class CentralPDOMySQL {

    public $link = "";
    public $result = "";
    public $errorPath = '/_includes/_central/mysql_log.txt';
	

    //***************************************************************************************************************************************
    /**
     * __construct	:	Class constructor method run on object instantionation and connects to MySQL database with the passed in parameters
     * @param 	string	$host		the host of the databse to connect to
     * @param 	string	$username	the username to connect to the database with
     * @param 	string	$password	the password to connect to the database with, can be blank e.g. working on a local machine rather than a server
     * @param		string	$database	the name the database being connected to
     * @return none
     */
    public function __construct($host = "", $username = "", $password = "", $database = "") {

		if (($host != "") && ($username != "") && ($database != "")) { // Not testing password because the password may be blank.
            try {
                
				$this->link = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $username, $password);
            } catch (PDOException $e) {
                $code    = $e->getCode();
				$message = $e->getMessage();
                $message = "Error: " . $code . ': ' . $message . " - Query: " . $query;
                $this->recordError($message);
            }
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // $this->link->setAttribute(PDO::ATTR_EMULATE_PREPARED, false);
			$this->errorPath = $_SERVER['DOCUMENT_ROOT'].$this->errorPath;
        }
    }

    //***************************************************************************************************************************************
    //*************************************************************************************************************************************** 
    /**
     * setErrorFile: Set the path to where the database error log file can be found
     * @param string $errorPath File path to where to record errors
     * @return false
     */
    public function setErrorFile($errorPath) {
        $this->errorPath = $errorPath;
    }

    //***************************************************************************************************************************************
    //*************************************************************************************************************************************** 
    /**
     * recordError: Record in a log file when an error occures
     * @param string $errorString error message to record
     * @return null
     */
    private function recordError($errorString) {
        
		$this->errorPath = str_replace('/', DIRECTORY_SEPARATOR, $this->errorPath);
		
		if (!file_exists($_SERVER['DOCUMENT_ROOT'].$this->errorPath)):
			$action = 'w+';
		else:
			$action = 'a+';
		endif;
		
		$fh = fopen($_SERVER['DOCUMENT_ROOT'].$this->errorPath, $action);
        fwrite($fh, date("d/m/Y H:i:s", time()) . " - " . $errorString . "\n");
        fclose($fh);
        die("Unfortunately a server error has occurred. Please try again later.");
    }

    //***************************************************************************************************************************************
    /**
     * query	:	Run a MySQL query using object conectiom
     * @param	string	$query	sql query to run
     * @return none
     */
    public function query($query) {
        // echo "<span style=\"color:black\">QUERY:".$query."<br/></span>";
        try {
		    $this->result = $this->link->query($query);
        } catch (PDOException $e) {
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            $code    = $e->getCode();
			$message = $e->getMessage();
            $message = "Error: " . $code . ': ' . $message . " - Query: " . $query;
            $this->recordError($message);
        }
        return $this->result;
    }

    //***************************************************************************************************************************************

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * insertId	:	Return the auto incremented number from object connection
     * @param		none
     * @return	int		$id	last inserted id
     */
    public function insertId() {
        try {
            $id = $this->link->lastInsertId();
        } catch (PDOException $e) {
			$code    = $e->getCode();
			$message = $e->getMessage();
            $message = "Error: " . $code . ': ' . $message . " - Query: " . $query;
            $this->ecordError($message);
        }
        return $id;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * fetchAssoc	:	Return associated array from object result param
     * @param		none
     * @return 	array 	$return	results of query
     */
    public function fetchAssoc() {
        $return = $this->result->fetch(PDO::FETCH_ASSOC);
        return $return;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************	
    /**
     * freeResult	:	Free the resource of the object current result
     * @param		none
     * @return 	none
     */
    public function freeResult() {
        try {
            $this->result->closeCursor();
        } catch (PDOException $e) {
            $code    = $e->getCode();
			$message = $e->getMessage();
            $message = "Error: " . $code . ': ' . $message . " - Query: " . $query;
            $this->recordError($message);
        }
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************		
    /**
     * numRows	:	Return the number of rows in the object current result
     * @param		none
     * @return	$return	int	number of rows
     */
    public function numRows() {
        $return = $this->result->rowCount();
        return $return;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * affectedRows	:	Return the number of rows affected by the last query executed on the object database connection
     * @param		none
     * @return	int		$return		count of affected rows
     */
    public function affectedRows() {
        try {
            $return = $this->result->rowCount();
        } catch (PDOException $e) {
            $details = $e->errorInfo();
            $code    = $e->getCode();
			$message = $e->getMessage();
			$message = "affected Rows: " . $code . ': ' . $message . " - Query: " . $query;
            $this->recordError($message);
        }
        return $return;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * close	:	Close current database connection
     * @param		none
     * @param		none
     */
    public function close() {
        try {
            $this->link = null;
        } catch (PDOException $e) {
            $code    = $e->getCode();
			$message = $e->getMessage();
			$message = "close: " . $code . ': ' . $message . " - Query: " . $query;
            $this->recordError($message);
        }
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * singleRow	:	Return a single row from the query. This is intented for a query that will return a single record
     * @param		string	$query	sql query to run
     * @return	array	$row	results of query
     */
    public function singleRow($query) {
        $this->query($query);
        $row = $this->fetchAssoc();
        $this->freeResult();
        return $row;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * multiRow	:	Return a multiple rows from the query. This is intented for a query that will return multiple rows
     * @param		string	$query	sql query to run
     * @return	array 	$rows	results of query
     */
    public function multiRow($query) {
        $this->query($query);
        $rows = $this->result->fetchAll(PDO::FETCH_ASSOC);
        $this->freeResult();
        return $rows;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * queryBuilder	:	Create an SQL select statement from passed in parameters
     * @param	string	$table	database table
     * @param	string	$rows	columns to be returned
     * @param	array	$where	setting for the where clause of the select statement
     * @param	string	$order	order the results will be shown
     * @param	array	$join	setting	for a join on the table
     * @param	array	$limit	setting for a join on the table
     * @return	$query	string	constructed select statement 
     */
    public static function queryBuilder($table, $rows = '*', $where = null, $order = null, $join = null, $group = null, $limit = null) {
        $query = 'select ' . $rows . ' from ' . $table;

        if (($join != null) && (count($join) > 0)) {
            foreach ($join as $joint) {
                $query .= strtolower(' ' . $joint['type']) . ' join ' . $joint['table'] . ' on ' . $table . '.' . $joint['origin'] . '=' . $joint['table'] . '.' . $joint['destination'];
            }
        }
        $query .= self::queryWhere($where, "and", true);
        if ($group != null) {
            $group_data = "";
            if (is_array($group)) {
                for ($x = 0; $x < count($group); $x++) {
                    if ($group_data != "")
                        $group_data .= ", ";
                    $group_data .= $group[$x];
                }
            } else
                $group_data = $group;
            $query .= ' group by ' . $group_data;
        }

        if ($order != null)
            $query .= ' order by ' . $order;

        if ($limit != null)
            $query .= ' limit ' . $limit;
        return $query;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * insertQuery	:	Create an SQL insert query
     * @param	string	$table	database table
     * @param	array	$values	field settings
     * @return string	$query	constructed insert statement 
     */
    public static function insertQuery($table, $values) {
        $query = 'insert into ' . $table . " set " . self::queryValues($values);
        return $query;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************#
    /**
     * updateQuery	:	Create an SQL update query
     * @param	string 	$table	database table
     * @param	array 	$values	field settings
     * @param	array 	$where	field where clause
     * @return string 	$query	constructed update statement 
     */
    public static function updateQuery($table, $values, $where = null) {
        $query = 'update ' . $table . " set " . self::queryValues($values) . self::queryWhere($where);
        return $query;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * deleteQuery	:	Create an SQL delete query
     * @param	string	$table	database table
     * @param	array	$where	field where clause
     * @return string	$query	constructed delete statement 
     */
    public static function deleteQuery($table, $where = null) {
        $query = 'delete from ' . $table . self::queryWhere($where);
        return $query;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * queryValues	:	Process values array into a string for use in an sql statement
     * @param	string	$values	database table
     * @return string	$output	settings clause for an sql statement
     */
    public static function queryValues($values) {
        $output = "";
        foreach ($values as $key => $value) {
            if ($output != "")
                $output .= ", ";
            if ($key == "full")
                $output .= $value;
            else if (in_array($value, array("now()")))
                $output .= str_replace("alt_", "", $key) . " = " . $value;
            else
                $output .= str_replace("alt_", "", $key) . " = '" . $value . "'";
        }
        return $output;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * queryWhere	:	Return a string containing the where clause for an sql statement
     * @param		array	$separator		database fields 
     * @param		string	$where			operator to seperate each field assignment
     * @param		string	$include_where	add 'where' to start of return string
     * @return	string	$where_data		full constructed where clause for the sql statement 
     */
    public static function queryWhere($where = null, $separator = "and", $include_where = true) {
        $where_data = '';
        $whereCount = count($where);
        $whereIsArray = is_array($where);
        if (($where != null) && ($whereCount > 0) && ($whereIsArray)) {
            $array_keys = array_keys($where);
            $last_key = end($array_keys);
            foreach ($where as $key => $value) {
                $isArrayValue = is_array($value);
                if ($isArrayValue) {
                    $sql_value = "'" . $value['value'] . "'";
                    $sql_operator = trim($value['operator']);
                } else {
                    $sql_value = "'" . $value . "'";
                    $sql_operator = '=';
                }

                if (in_array($value, array('null', 'not null'))) {
                    $sql_value = $value;
                    $sql_operator = 'is';
                }

                if (substr($key, 0, 4) == 'full') {
                    $where_data .= $value;
                } else {
                    $stringReplace = str_replace('alt_', '', $key);
                    $trim = trim($sql_operator);
                    $tmp = $stringReplace . ' ' . $trim . ' ' . $sql_value;

                    $where_data .= $tmp;
                }

                if ($key != $last_key) {
                    $where_data .= ' ' . trim($separator) . ' ';
                }
            }
            if ($include_where == true) {
                $where_data = ' where ' . $where_data;
            }
        }
        return $where_data;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    /**
     * escapeString	:	Escape the a string so it can be safely used in mysql queries
     * @param		string	$string	
     * @return	string	$return	escaped value
     */
    function escapeString($string) {
       	$withoutSlashes = stripslashes($string);
		$withSlashes    = addslashes($withoutSlashes);
		$return         = $withSlashes;
        return $return;
    }
    //***************************************************************************************************************************************
}

?>