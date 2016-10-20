<?php
/**
 * @Filename: CentralMySQL.php
 * Location: /_includes/_central
 * Function: Class containing various MySQL helper functions
 * @Creator: Stefan Harvey (SAH)
 * Changes:
 *  20130101 SAH Created
 *  20141125 SAH Corrected insertId() function definition by removing static
 */
 
 /*
// In order to use this file as a codeigniter model the file name must be 'centralmysql.php' in lowercase and you must use 
class Centralmysql extends CI_Model 
// instead of 
class CentralMySQL 
*/

namespace Central;

class CentralMySQL
{
  public $link = "";
  public $result = "";
	
  //***************************************************************************************************************************************
  /**
   * Class constructor method run on object instantionation and connects to MySQL database with the passed in parameters
   * @param string the host of the databse to connect to
   * @param string the username to connect to the database with
   * @param string the password to connect to the database with
   * @param string the name the database being connected to
   * @return none
   */
  public function __construct($host="", $username="", $password="", $database="")
  {
    // parent::__construct();
	
	if (($host!="") && ($username!="") && ($password!="") && ($database!=""))
	{
		try
		{
			if($this->link = mysql_connect($host, $username, $password))
			{
				// Link has successfully been made
				if (mysql_select_db($database, $this->link))
				{
					// Assigned to database corectly
				}
				else throw new Exception("db select: ".mysql_errno().': '.mysql_error());
			}
			else throw new Exception("connection: ".mysql_errno().': '.mysql_error());
		}
		catch (Exception $e)
		{
			self::recordError($e->getMessage());
		}
	}
  }
  //***************************************************************************************************************************************
	
   
  //*************************************************************************************************************************************** 
  /**
   * Record in a log file when an error occures
   * @param string error message to record
   * @return int sucess of wrting to the log file
   */ 
  private static function recordError($error_string)
  {
		$fh = fopen($_SERVER['DOCUMENT_ROOT'].'/_includes/_central/mysql_log.txt', 'a+');
		fwrite($fh,  date("d/m/Y H:i:s", time())." - ".$error_string."\n"); 
		fclose($fh); 
		die("Unfortunately a server error has occurred. Please try again later.");
  }
  //***************************************************************************************************************************************
  
  //***************************************************************************************************************************************
  /**
   * Run a MySQL query using object conectiom
   * @param string sql query to run
   * @return none
   */
  public function query($query)
  {
    // echo "<span style=\"color:black\">QUERY:".$query."<br/></span>";
	try
	{
		if ($this->result = mysql_query($query, $this->link))
		{
			// The query has been sucessful
		}
		else throw new Exception("query: ".mysql_errno().': '.mysql_error()." - Query: ".$query);
	}
    catch (Exception $e)
	{
		self::recordError($e->getMessage());
	}
  }
  //***************************************************************************************************************************************
	
  //***************************************************************************************************************************************
  /**
   * Return the auto incremented number from object connection
   * @return int last inserted id
   */
  public function insertId()
  {
    try
	{
		if ($id = mysql_insert_id($this->link))
		{
			// The query has been sucessful
		}
		else throw new Exception("insertID: ".mysql_errno().': '.mysql_error());
	}
    catch (Exception $e)
	{
		self::recordError($e->getMessage());
	}
	return $id;	
  }
  //***************************************************************************************************************************************
	
  //***************************************************************************************************************************************
  /**
   * Return associated array from object result param
   * @return array results of query
   */
  public function fetchAssoc()
  {	
 	$return = mysql_fetch_assoc($this->result);
	return $return;	
  }
  //***************************************************************************************************************************************
	
  //***************************************************************************************************************************************	
  /**
   * Free the resource of the object current result
   * @return array results of query
   */
  public function freeResult()
  {	
	try
	{
		if (mysql_free_result($this->result))
		{
			// Freeing the result was succesful
		}
		else throw new Exception("freeResult: ".mysql_errno().': '.mysql_error());
	}
    catch (Exception $e)
	{
		self::recordError($e->getMessage());
	}	
  }
  //***************************************************************************************************************************************
	
  //***************************************************************************************************************************************		
  /**
   * Return the number of rows in the object current result
   * @return int number of rows
   */
  public function numRows()
  {	
	try
	{
		if ($return = mysql_num_rows($this->result))
		{
			// Freeing the result was succesful
		}
		else throw new Exception("numRows: ".mysql_errno().': '.mysql_error());
	}
    catch (Exception $e)
	{
		self::recordError($e->getMessage());
	}
	return $return;	
  }
  //***************************************************************************************************************************************
	
  
  
  //***************************************************************************************************************************************
  /**
   * Return the number of rows affected by the last query executed on the object database connection
   * @return int count of affected rows
   */
  public function affectedRows()
  {
	try
	{
		if ($return = mysql_affected_rows())
		{
			// Freeing the result was succesful
		}
		else throw new Exception("affectedRows: ".mysql_errno().': '.mysql_error());
	}
    catch (Exception $e)
	{
		self::recordError($e->getMessage());
	}
	return $return;	
  }
  //***************************************************************************************************************************************
	
  
  //***************************************************************************************************************************************
  /**
   * Close current database connection
   * @return success of close
   */
  public function close()
  {
	try
	{
		if (mysql_close($this->link))
		{
			// Freeing the result was succesful
		}
		else throw new Exception("close: ".mysql_errno().': '.mysql_error());
	}
    catch (Exception $e)
	{
		self::recordError($e->getMessage());
	}	

  }
  //***************************************************************************************************************************************
  
  //***************************************************************************************************************************************
  /**
   * Return a single row from the query. This is intented for a query that will return a single record
   * @param string sql query to run
   * @return array results of query
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
   * Return a multiple rows from the query. This is intented for a query that will return multiple rows
   * @param string sql query to run
   * @return array results of query
   */	
  public function multiRow($query) 
  {
    $this->query($query);
    $rows = array();
    for($i=0; $i<$this->numRows(); $i++)
    { 
      $rows[] = $this->fetchAssoc();;
    }
    $this->freeResult();
    return $rows;
  }
  //***************************************************************************************************************************************
	
  
 
 
  //***************************************************************************************************************************************
  /**
  * Create an SQL select statement from passed in parameters
  * @param string database table
  * @param string columns to be returned
  * @param array  setting for the where clause of the select statement
  * @param string order the results will be shown
  * @param array  setting for a join on the table
  * @param array  setting for a join on the table
  * @return string constructed select statement 
  */	
  public function queryBuilder($table, $rows = '*', $where = null, $order = null, $join = null, $group = null, $limit = null)
  {
    $query = 'select '.$rows.' from '.$table;
    if (($join != null) && (count($join) > 0))
    {
      foreach ($join as $joint)
      {
        $query .= strtolower(' '.$joint['type']).' join '.$joint['table'].' on '.$table.'.'.$joint['origin'].'='.$joint['table'].'.'.$joint['destination'];
      }
    }
    $query .= self::queryWhere($where, "and", true); 
    if ($group != null)
    {
      $group_data = "";
      if (is_array($group))
      {
        for($x = 0; $x < count($group); $x++)
        {
          if ($group_data != "")
            $group_data .= ", ";
          $group_data .= $group[$x];
        }
      }
      else $group_data = $group;
      $query .= ' group by '.$group_data; 
    }

    if ($order != null)  
      $query .= ' order by '.$order;  

    if($limit != null)
      $query .= ' limit '.$limit;	
    return $query;
  }
  //***************************************************************************************************************************************
	
  //***************************************************************************************************************************************
  /**
  * Create an SQL insert query
  * @param string database table
  * @param array field settings
  * @return string constructed insert statement 
  */
  public static function insertQuery($table, $values)  
  {
    return 'insert into '.$table." set ".self::queryValues($values);	
  }
  //***************************************************************************************************************************************
	
  //***************************************************************************************************************************************#
  /**
  * Create an SQL update query
  * @param string database table
  * @param array field settings
  * @param array field where clause
  * @return string constructed update statement 
  */
  public static function updateQuery($table, $values, $where=null)  
  {
    return 'update '.$table." set ".self::queryValues($values).self::queryWhere($where);	
  }
  //***************************************************************************************************************************************
	
  //***************************************************************************************************************************************
  /**
  * Create an SQL delete query
  * @param string database table
  * @param array field where clause
  * @return string constructed delete statement 
  */
  public static function deleteQuery($table, $where=null)  
  {
    return 'delete from '.$table.self::queryWhere($where);	
  }
  //***************************************************************************************************************************************
	
  //***************************************************************************************************************************************
  /**
  * Process values array into a string for use in an sql statement
  * @param string database table
  * @return string settings clause for an sql statement
  */
  public static function queryValues($values)
  {
    $output = "";
    foreach($values as $key=>$value)
    {
      if ($output!="") $output .= ", ";
      if ($key=="full")
        $output .= $value;      
      else if (in_array($value, array("now()")))
        $output .= str_replace("alt_", "", $key)." = ".$value;
      else $output .= str_replace("alt_", "", $key)." = '".$value."'";      
    }
    return $output;
  }
  //***************************************************************************************************************************************
	
  //***************************************************************************************************************************************
  /**
   * Return a string containing the where clause for an sql statement
   * @param array database fields 
   * @param string operator to seperate each field assignment
   * @param string add 'where' to start of return string
   * @return string where clause for an sql statement 
   */
  public static function queryWhere($where=null, $separator="and", $include_where=true)
  {
    $where_data = "";
    if (($where != null) && (count($where) > 0) && (is_array($where)))  
    {
      $array_keys = array_keys($where);
      $last_key = end($array_keys);
      foreach ($where as $key => $value)
      {
        if (is_array($value))
        {
          $sql_value    = "'".$value["value"]."'";
          $sql_operator = trim($value["operator"]); 
        }
        else
        {
          $sql_value = "'".$value."'";
          $sql_operator = "=";
        }

        if (in_array($value, array("null", "not null")))
        {
          $sql_value    = $value;
          $sql_operator = "is";
        }

        if (substr($key, 0, 4)=="full")
        {
          $where_data .= $value;
        }
        else $where_data .= str_replace("alt_", "", $key)." ".trim($sql_operator)." ".$sql_value;

        if ($key != $last_key)
          $where_data .= " ".trim($separator)." ";	
      }
      if ($include_where==true)
        $where_data = ' where '.$where_data;     
    }
    return $where_data;
  }
  //***************************************************************************************************************************************
  
  //***************************************************************************************************************************************
  /**
   * Escape the a string so it can be safely used in mysql queries
   * @return success of close
   */
  public static function escapeString($string)
  {
	$return = mysql_real_escape_string($string);
	return $return;
  }
  //***************************************************************************************************************************************
  
}

?>
