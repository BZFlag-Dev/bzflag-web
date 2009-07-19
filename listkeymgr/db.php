<?php  
	include_once("conf/config.php");
	
	function Sanitize ( $value )
	{
		return mysql_real_escape_string(addslashes($value));	
	}
	
	function Unsanitize ( $value )
	{
		return stripslashes($value);	
	}
	
	function ConnectToDB ()
	{
		global $CONFIG_DATABASE_SERVER;
		global $CONFIG_DATABASE_DATABSE;
		global $CONFIG_DATABASE_USER;
		global $CONFIG_DATABASE_PASS;
		
		$db = mysql_pconnect($CONFIG_DATABASE_SERVER,$CONFIG_DATABASE_USER,$CONFIG_DATABASE_PASS);
		if (!$db)
		{
			return FALSE;
		}
		else
			$result = mysql_select_db($CONFIG_DATABASE_DATABSE);
		
		return $db;
	}
	
	function SQLError ( $query )
	{
		echo "SQL ERROR: " . mysql_error() . "<br>";
		echo "SQL ERROR Query: " . $query . "<br>";
		return FALSE;
	}
	
	function SQLGet ( $query )
	{
		$result = mysql_query($query);
		if (!$result && $result != 0 && mysql_num_rows($result) > 0)
			return SQLError($query);
			
		return $result;
	}
	
	function SQLSet ( $query )
	{
		$result = mysql_query($query);
		if (!$result)
			return SQLError($query);
			
		return TRUE;
	}
	
	function GetQueryResults ( $result, $field )
	{
		if (!$result)
			return FALSE;
			
		$list = array(); 
		$count = mysql_num_rows($result);
		for ($i = 0; $i < $count; $i += 1)
		{
			$row = mysql_fetch_array($result);
			$list[] = Unsanitize($row[$field]);
		}
		
		return $list;
	}
	
	function GetQueryResultsArray ( $result  )
	{
		if (!$result)
			return FALSE;
			
		$list = array(); 
		$count = mysql_num_rows($result);
		for ($i = 0; $i < $count; $i += 1)
		{
			$row = mysql_fetch_array($result);
			$rowList = array();
			foreach ($row as $key => $value)
			{
				$rowList[$key] = Unsanitize($value);
			}
			$list[] = $rowList;
		}
		
		return $list;
	}
	
	function GetDBFieldForKey ( $keyName, $key, $db, $field )
	{
		$query = "SELECT " . $field . " FROM ". $db ." WHERE " . $keyName . "='" .$key . "'";		
		$results = GetQueryResults(SQLGet($query),$field );
		
		if (!$results)
			return FALSE;
		return Unsanitize($results[0]);
	}
	
	function GetDBFieldForID ( $id, $db, $field )
	{
		$query = "SELECT " . $field . " FROM ". $db ." WHERE ID=" . $id;		
		$results = GetQueryResults(SQLGet($query),$field );
		
		if (!$results)
			return FALSE;
		return Unsanitize($results[0]);
	}
	
	function SetDBFieldForKey ( $keyName, $key, $db, $field, $value )
	{
		$query = "UPDATE " . $db ." SET " . $field . "='" .$value."' WHERE " . $keyName ."='" .$key. "'";
		return SQLSet($query); 
	}
	
	function SetDBFieldForID ( $id, $db, $field, $value )
	{
		return SetDBFieldForKey("ID", $id, $db, $field, $value);
	}

	
?>