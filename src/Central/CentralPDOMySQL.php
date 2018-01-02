<?php
/**
 * @Filename: CentralPDOMySQL.php
 * Location: /Central
 * Function: Class containing various PDO MySQL helper functions
 * @Creator: Stefan Harvey (SAH)
 * Changes:
 *  20130101 SAH Created
 *  20140709 SAH Changed constructor (_construct) to allow an instance to be created without the need to connect to a database, and thus make the 4 parameter
 * 				 no longer mandatory.
 *  20160531 SAH Changed constructor (_construct) to correct the use of the $e->getErrorDetails and use $e->getCode and $e->getMessage instead. Also changed 
 *               recordError() to use $_SERVER['DOCUMENT_ROOT'] at the front of the file being opened, as it wasn't working with it on some servers
 *  20170323 SAH Change constructor to check for a valid host, before trying to make a connection, added option to define message image shown to the 
 *				 user when an error occurs, and make sure the DOCUMENT_ROOT is removed from the front of the error path before appending, so that it
 *               doesn't appear twice.
 *  20171010 SAH Changed escapeString function to be public static, and added check for the first letter of the error path is a slash, if not it will add one
 *  20171211 SAH Added functionality to continue after an error has been recorded in the log file rather than showing an error to the screen
 *  20171219 SAH BUG: Changed $errorpath to $errorPath on line 68. It was causing as issue in codeigniiter build
 *  20180102 SAH MOD: Changed connection failure error message to include host and database
 */
 
/*
  // In order to use this file as a codeigniter model the file name must be 'centralpdomysql.php' in lowercase and you must use 
  class CentralPDO extends CI_Model
  // instead of
  class CentralPDOMySQL
 */
namespace Central;

class CentralPDOMySQL
{

    public $link          = '';
    public $result        = '';
    public $errorPath     = 'mysql_log.txt';
	public $errorMessage  = 'Unfortunately a server error has occurred. Please try again later.';
	public $errorContinue = 'stop';
	public $active		  = true;

    //***************************************************************************************************************************************
    /**
     * __construct:	Class constructor method run on object instantionation and connects to MySQL database with the passed in parameters
     * @param 	string	$host: The host of the databse to connect to
     * @param 	string	$username: The username to connect to the database with
     * @param 	string	$password: the password to connect to the database with, can be blank e.g. working on a local machine rather than a server
     * @param	string	$database: The name the database being connected to
	 * @param	string	$errorPath: Override the default log file
	 * @param	string	$errorMessage: Override the default error message show to the user on error
	 * @param	string	$errorContinue: If 'continue', with database error, the script will continue rather than die with the error message 
     * @return	none
     */
    public function __construct($host = '', $username = '', $password = '', $database = '', $errorPath = '', $errorMessage='', $errorContinue='stop') 
	{

		if ($errorPath!=''):
			$this->setErrorFile($errorPath);
		endif;	
		
		if ($errorMessage!=''):
			$this->setErrorMessage($errorMessage);
		endif;	
		
		$this->errorContinue = $errorContinue; // If set to 'continue', the error will be recorded in the log file, but the script will continue after error
		
		$this->errorPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->errorPath);
		if (substr($this->errorPath, 0, 1)!='/'):
			$this->errorPath = '/'.$this->errorPath;
		endif;
		
		$this->errorPath = $_SERVER['DOCUMENT_ROOT'].$this->errorPath;
				
		if (($host != '') && ($username != '') && ($database != '')): // Not testing password because the password may be blank.
            
			if (!filter_var($host, FILTER_VALIDATE_IP) === false) 
			{
    			// The host is an ip address so no problem
			} 
			else 
			{
    			// The host is a hostname, so we need to check it exists
				$ipaddress = gethostbyname($host);
									
				if (!filter_var($ipaddress, FILTER_VALIDATE_IP) === false) 
				{
					// We have correctly found the ipaddress for this domain, 
				}
				else
				{
					// No ipaddress can be extracted so the hostname doesn't exist
					$message = 'Error: '.$host.' is not a valid domain or ipaddress';
                	$this->recordError($message);					
				}
			}
	
			try 
			{
				$this->link = new \PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', 
									   $username, 
									   $password, 
									   array(\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION));
            } 
			catch (\PDOException $e) 
			{
                $code    = $e->getCode();
				$message = $e->getMessage();
                $message = 'Error: ' . $code . ': ' . $message.' (host:'.$host.') (database:'.$database.')';
                $this->recordError($message);
            }
            // $this->link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // $this->link->setAttribute(\PDO::ATTR_EMULATE_PREPARED, false);
    	endif;
    }

    //***************************************************************************************************************************************
	
    //*************************************************************************************************************************************** 
    /**
     * setErrorFile: Set the path to where the database error log file can be found
     * @param	string	$errorPath: File path to where to record errors
     * @return	none
     */
    public function setErrorFile($errorPath) 
	{
        $this->errorPath = $errorPath;
    }
    //***************************************************************************************************************************************
	
	//*************************************************************************************************************************************** 
    /**
     * setErrorMessage: Set the default error message shown to the user when a database error occurs
     * @param	string	$errorPath: File path to where to record errors
     * @return	none
     */
    public function setErrorMessage($errorMessage) 
	{
        $this->errorMessage = $errorMessage;
    }
    //***************************************************************************************************************************************
    
	/*************************************************************************************************************************************** 
    /**
     * recordError: Record in a log file when an error occures
     * @param	string	$errorString: error message to record
     * @return	none
     */
    private function recordError($errorString) 
	{
		$this->errorPath = str_replace('/', DIRECTORY_SEPARATOR, $this->errorPath);
		
		if (!file_exists($this->errorPath)):
			$action = 'w+';
		else:
			$action = 'a+';
		endif;
		
		$fh = fopen($this->errorPath, $action);
        fwrite($fh, date('d/m/Y H:i:s', time()) . ' - ' . $errorString . PHP_EOL);
        fclose($fh);
		
        if ($this->errorContinue=='stop'):
			die($this->errorMessage);
		endif;
		$this->active = false;
    }
	 //***************************************************************************************************************************************

    //***************************************************************************************************************************************
    /**
     * query:	Run a MySQL query using object conectiom
     * @param	string	$query: SQL query to run
     * @return	none
     */
    public function query($query) {
        // echo '<span style="color:black">QUERY:'.$query.'<br/></span>';
        try 
		{
			$this->result = $this->link->query($query);
        } 
		catch (\PDOException $e) 
		{
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            $code    = $e->getCode();
			$message = $e->getMessage();
            $message = 'Error: ' . $code . ': ' . $message . ' - Query: ' . $query;
            $this->recordError($message);
        }
        return $this->result;
    }
    //***************************************************************************************************************************************

    //***************************************************************************************************************************************
    /**
     * insertId:	Return the auto incremented number from object connection
     * @param		none
     * @return	int		$id	last inserted id
     */
    public function insertId() 
	{
        try 
		{
            $id = $this->link->lastInsertId();
        } 
		catch (\PDOException $e) 
		{
			$code    = $e->getCode();
			$message = $e->getMessage();
            $message = 'Error: ' . $code . ': ' . $message;
            $this->ecordError($message);
        }
        return $id;
    }
    //***************************************************************************************************************************************
    
	//***************************************************************************************************************************************
    /**
     * fetchAssoc:	Return associated array from object result param
     * @param		none
     * @return 	array 	$return	results of query
     */
    public function fetchAssoc() 
	{
        $return = $this->result->fetch(\PDO::FETCH_ASSOC);
        return $return;
    }
    //***************************************************************************************************************************************
    
	//***************************************************************************************************************************************	
    /**
     * freeResult:	Free the resource of the object current result
     * @param	none
     * @return 	none
     */
    public function freeResult() 
	{
        try 
		{
            $this->result->closeCursor();
        } 
		catch (\PDOException $e) 
		{
            $code    = $e->getCode();
			$message = $e->getMessage();
            $message = 'Error: ' . $code . ': ' . $message;
            $this->recordError($message);
        }
    }
    //***************************************************************************************************************************************

    //***************************************************************************************************************************************		
    /**
     * numRows:	Return the number of rows in the object current result
     * @param	none
     * @return	int		$return: Number of rows
     */
    public function numRows() 
	{
        $return = $this->result->rowCount();
        return $return;
    }
    //***************************************************************************************************************************************

    //***************************************************************************************************************************************
    /**
     * affectedRows:	Return the number of rows affected by the last query executed on the object database connection
     * @param	none
     * @return	int		$return: Count of affected rows
     */
    public function affectedRows() 
	{
        try 
		{
            $return = $this->result->rowCount();
        } 
		catch (\PDOException $e) 
		{
            $details = $e->errorInfo();
            $code    = $e->getCode();
			$message = $e->getMessage();
			$message = 'affected Rows: ' . $code . ': ' . $message;
            $this->recordError($message);
        }
        return $return;
    }

    //***************************************************************************************************************************************
    
	//***************************************************************************************************************************************
    /**
     * close:	Close current database connection
     * @param	none
     * @return	none
     */
    public function close() 
	{
        try 
		{
            $this->link = null;
        } 
		catch (\PDOException $e) 
		{
            $code    = $e->getCode();
			$message = $e->getMessage();
			$message = 'close: ' . $code . ': ' . $message;
            $this->recordError($message);
        }
    }

    //***************************************************************************************************************************************
	
    //***************************************************************************************************************************************
    /**
     * singleRow:	Return a single row from the query. This is intented for a query that will return a single record
     * @param	string	$query: SQL query to run
     * @return	array	$row: Single row results of query
     */
    public function singleRow($query) 
	{
        $this->query($query);
        $row = $this->fetchAssoc();
        $this->freeResult();
        return $row;
    }
    //***************************************************************************************************************************************

    //***************************************************************************************************************************************
    /**
     * multiRow:	Return a multiple rows from the query. This is intented for a query that will return multiple rows
     * @param	string	$query: SQL query to run
     * @return	array 	$rows: Multiple rows of query results
     */
    public function multiRow($query) {
        $this->query($query);
        $rows = $this->result->fetchAll(\PDO::FETCH_ASSOC);
        $this->freeResult();
        return $rows;
    }
    //***************************************************************************************************************************************

    //***************************************************************************************************************************************
    /**
     * queryBuilder:	Create an SQL select statement from passed in parameters
     * @param	string	$table: Database table
     * @param	string	$rows: Columns to be returned
     * @param	array	$where: Setting for the where clause of the select statement
     * @param	string	$order: Order the results will be shown
     * @param	array	$join: Setting for a join on the table
     * @param	array	$limit: Setting to limit the results of the query
     * @return	string	$query: Constructed select statement 
     */
    public static function queryBuilder($table, $rows = '*', $where = null, $order = null, $join = null, $group = null, $limit = null) {
        $query = 'select ' . $rows . ' from ' . $table;

        if (($join != null) && (count($join) > 0)): 
		
            foreach ($join as $joint):
				$query .= strtolower(' ' . $joint['type']) . ' join ' . $joint['table'] . ' on ' . $table . '.' . $joint['origin'] . '=' . $joint['table'] . '.' . $joint['destination'];
            endforeach;
			
        endif;
        $query .= self::queryWhere($where, 'and', true);
        if ($group != null): 
		
            $group_data = '';
            if (is_array($group)):
			
                for ($x = 0; $x < count($group); $x++):
                    if ($group_data != ''):
                        $group_data .= ', ';
					endif;
                    $group_data .= $group[$x];
                endfor;
            else:
                $group_data = $group;
			endif;
			
            $query .= ' group by ' . $group_data;
        endif;

        if ($order != null):
            $query .= ' order by ' . $order;
		endif;

        if ($limit != null)
            $query .= ' limit ' . $limit;
        return $query;
    }
    //***************************************************************************************************************************************
	
    //***************************************************************************************************************************************
    /**
     * insertQuery:	Create an SQL insert query
     * @param	string	$table:	Database table
     * @param	array	$values: Field settings
     * @return	string	$query:	Constructed insert statement 
     */
    public static function insertQuery($table, $values) 
	{
        $query = 'insert into ' . $table . ' set ' . self::queryValues($values);
        return $query;
    }
    //***************************************************************************************************************************************

    //***************************************************************************************************************************************#
    /**
     * updateQuery:	Create an SQL update query
     * @param	string 	$table:	Database table
     * @param	array 	$values: Field settings
     * @param	array 	$where: Field where clause
     * @return	string 	$query: Constructed update statement 
     */
    public static function updateQuery($table, $values, $where = null) 
	{
        $query = 'update ' . $table . ' set ' . self::queryValues($values) . self::queryWhere($where);
        return $query;
    }
    //***************************************************************************************************************************************
   
    //***************************************************************************************************************************************
    /**
     * deleteQuery:	Create an SQL delete query
     * @param	string	$table	database table
     * @param	array	$where	field where clause
     * @return string	$query	constructed delete statement 
     */
    public static function deleteQuery($table, $where = null) 
	{
        $query = 'delete from ' . $table . self::queryWhere($where);
        return $query;
    }

    //***************************************************************************************************************************************
    
	//***************************************************************************************************************************************
    /**
     * queryValues:	Process values array into a string for use in an sql statement
     * @param	string	$values: Database fields and their values
     * @return	string	$output: field and values constructed into a string
	 */
    public static function queryValues($values) 
	{
        $output = '';
        foreach ($values as $key => $value):
		
            if ($output != ''):
                $output .= ', ';
            endif;
			
			if ($key == 'full'):
                $output .= $value;
            elseif (in_array($value, array('now()'))):
                $output .= str_replace('alt_', '', $key) . ' = ' . $value;
            else:
                $output .= str_replace('alt_', '', $key) . ' = \'' . $value . '\'';
			endif;
        endforeach;
        return $output;
    }

    //***************************************************************************************************************************************
	
    //***************************************************************************************************************************************
    /**
     * queryWhere:	Return a string containing the where clause for an sql statement
     * @param	array	$where: database fields 
     * @param	string	$separator:	operator to seperate each field assignment
     * @param	string	$include_where: Add 'where' to start of return string
     * @return	string	$where_data: Full constructed where clause for the sql statement 
     */
    public static function queryWhere($where = null, $separator = 'and', $include_where = true) {
    
	    $where_data = '';
        $whereCount = count($where);
        $whereIsArray = is_array($where);
        if (($where != null) && ($whereCount > 0) && ($whereIsArray)):
		
            $array_keys = array_keys($where);
            $last_key = end($array_keys);
            foreach ($where as $key => $value): 
			
                $isArrayValue = is_array($value);
                if ($isArrayValue):
				
                    $sql_value = '\'' . $value['value'] . '\'';
                    $sql_operator = trim($value['operator']);
				else:
	                $sql_value = '\'' . $value . '\'';
                    $sql_operator = '=';
                endif;

                if (in_array($value, array('null', 'not null'))):
				
                    $sql_value = $value;
                    $sql_operator = 'is';
                endif;

                if (substr($key, 0, 4) == 'full'):
				
                    $where_data .= $value;
                 
				else:
				
                    $stringReplace = str_replace('alt_', '', $key);
                    $trim = trim($sql_operator);
                    $tmp = $stringReplace . ' ' . $trim . ' ' . $sql_value;

                    $where_data .= $tmp;
                endif;

                if ($key != $last_key):
                    $where_data .= ' ' . trim($separator) . ' ';
                endif;
				
            endforeach;
            if ($include_where == true):
                $where_data = ' where ' . $where_data;
            endif;
        endif;
        return $where_data;
    }

    //***************************************************************************************************************************************
    
	//***************************************************************************************************************************************
    /**
     * escapeString:	Escape the a string so it can be safely used in mysql queries
     * @param	string	$rawString: String to escap	
     * @return	string	$return:	Escaped String
     */
    public static function escapeString($string) 
	{
       	$withoutSlashes = stripslashes($string);
		$withSlashes    = addslashes($withoutSlashes);
		$return         = $withSlashes;
        return $return;
    }
    //***************************************************************************************************************************************
}

?>