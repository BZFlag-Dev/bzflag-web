<?php
include_once("db.php");
include_once("common.php");
include_once("checkToken.php");

function ErrorPage( $error )
{
	PageHeader();
	echo "<h2>Error</h2>
		" . $error;
	PageFooter();
}

function RedirectList( $note = FALSE)
{
	if (!$note)
		PageRedirect($_SERVER['SCRIPT_NAME'],0);
	else
	{
		PageHeader();
		echo "<div id=\"Note\">".$note. "</div>";
		echo "<a href=\"".$_SERVER['SCRIPT_NAME']."\">Return to Key List</a>";	
		PageFooter();
	}
}

function NewKey()
{
	$key = rand();
	$key .= rand();
	
	$query = "SELECT ID FROM authkeys where key_string='" . $key."'";
	$result = mysql_query($query);
	
	if (!$result)
		return $key;

	$i = 0;
	while (TRUE)
	{
		$key = rand();
		$key .= rand();
		$query = "SELECT ID FROM authkeys where key_string=" . $key."'";
		$result = mysql_query($query);
		if (!$result)
			return $key;
			
		$i += 1;
		if ($i > 10)
		{
			break;
		}
	}
		
	return $key;
}

function Listing ( $note = FALSE )
{
	if (!isset($_SESSION['BZID']) || $_SESSION['BZID'] == -1 )
	{
		ErrorPage("Invalid ID");
		return;
	}
	PageHeader();
	if ($note)
		echo "<div id=\"Note\">".$note. "</div>";
	
	$bzid = Sanitize($_SESSION['BZID']);
	$results = GetQueryResultsArray(SQLGet("SELECT ID FROM authkeys WHERE owner='" . $bzid . "'"));
	if (!$results || !sizeof($results))
		echo "<div id=\"NoKeys\">
				No Keys
			</div>";
	else
	{
		echo "<div id=\"KeyList\">";
		echo "<div id=\"KeyListTitle\">Keys</div>
		<table border=\"0\" id=\"KeyListTable\">
		<tr id=\"KeyListHeader\">
		<td> <div id=\"KeyHostHeader\">Host</div></td> 
		<td> <div id=\"KeyKeyHeader\">Key</div></td> 
		<td> <div>&nbsp;</div></td> 
		</tr>";
		foreach ( $results as $result )
		{
			$id = $result['ID'];
			
			$host = GetDBFieldForID($id,"authkeys","host");
			$key = GetDBFieldForID($id,"authkeys","key_string");
			
			echo "<tr class=\"KeyItem\">
			<td> <div class=\"KeyHost\">".$host."</div></td>
			<td> <div class=\"KeyKey\">".$key."</div></td>
			<td> <div class=\"KeyDelete\"><a href=\"".$_SERVER['SCRIPT_NAME']."?action=removekey&id=".$id."\">Delete</a></div></td>
			</tr>";
		}
		echo "</table>
		</div>";
	}
	
	echo "<div id=\"KeyAdd\">
			New Key
			<div id=\"KeyAddNotes\">To generate a new key, simply enter your servers's host address below.
			This is ether the domain name or the IP address of the server.
			The generated key will only be valid for BZFS instances running on that host.</div>
			<form action=\"".$_SERVER['SCRIPT_NAME']."\" method=\"GET\">
			<input type=\"hidden\" name=\"action\" value=\"addkey\">
			Server Host Address<input type=\"text\" name=\"host\">
			<input type=\"submit\" value=\"Generate Key\">
			</form>
			</div>";
	
	echo "</div>";
	PageFooter();
}

function AddKey()
{
	if (!isset($_REQUEST['host']) )
	{
		ErrorPage("Invalid Entry");
		return;
	}
	
	$bzid = Sanitize($_SESSION['BZID']);
	$host = Sanitize($_REQUEST['host']);
	$key = NewKey();
	$now =  date ("Y-m-d H:i:s");
	
	$query = "INSERT INTO authkeys (key_string, owner, host, edit_date) VALUES ('".$key."', '".$bzid."', '".$host."', '".$now."')";
	
	if (!SQLSet($query))
		RedirectList("Key Failure");
	else
		RedirectList();
}

function RemoveKey()
{
	if (!isset($_REQUEST['id']) )
	{
		ErrorPage("Invalid Entry");
		return;
	}
	
	$bzid = Sanitize($_SESSION['BZID']);
	$id = Sanitize($_REQUEST['id']);
	
	$host = GetDBFieldForID($id,"authkeys","host");
	$key = GetDBFieldForID($id,"authkeys","key_string");	
	$keyOwner = GetDBFieldForID($id,"authkeys","owner");
	
	if ($keyOwner != $bzid)
	{
		ErrorPage("Invalid ID");
		return;
	}
	
	PageHeader();
	echo "<div id=\"DeleteConfirm\">You are about to remove Key:" . $key . " for Host:" . $host . "
	Are you sure?</div>
	<div id=\"DeleteConfirmLink\">
	<a href=\"".$_SERVER['SCRIPT_NAME']."?action=removekeyconfirm&id=". $id."\">Yes, Delete the Key</a>
	</div>";	
	
	PageFooter();
}

function RemoveKeyConfirm()
{
	if (!isset($_REQUEST['id']) )
	{
		ErrorPage("Invalid Entry");
		return;
	}
	
	$bzid = Sanitize($_SESSION['BZID']);
	$id = Sanitize($_REQUEST['id']);
	
	$keyOwner = GetDBFieldForID($id,"authkeys","owner");
	
	if ($keyOwner != $bzid)
	{
		ErrorPage("Invalid ID");
		return;
	}
	
	$query = "DELETE FROM authkeys WHERE ID=" . $id;
	
	if (!SQLSet($query))
		RedirectList("Delete Failure");
	else
		RedirectList();
}

function Login()
{
	if (!isset($_REQUEST['token']) || !isset($_REQUEST['user']) )
	{
		ErrorPage("Invalid Entry");
		return;
	}
	
	$token = $_REQUEST['token'];
	$user = $_REQUEST['user'];
	
	$checkResults = validate_token($token, $user,  array(), FALSE);
	if (!isset($checkResults['bzid']))
	{
		ErrorPage("Invalid Login");
		return;
	}
	$_SESSION['BZID'] = $checkResults['bzid'];
	PageRedirect($_SERVER['SCRIPT_NAME'],0);
}

function Logout()
{
	$_SESSION['BZID'] = -1;
	Header();
	echo "<h2>Loged Out</h2>
		<a href=\"" . $_SERVER['SCRIPT_NAME']."\">Login</a>";
	Footer();
}

session_start();
ConnectToDB();

if (isset($_REQUEST['action']) && (!isset($_SESSION['BZID']) || $_SESSION['BZID'] == -1 ))
	Login();
else if (!isset($_SESSION['BZID']) || $_SESSION['BZID'] == -1 )
{
	$url = "http://my.bzflag.org/weblogin.php?action=weblogin&url=";
	$returnURL = "http://" . $_SERVER['SERVER_NAME'] .  $_SERVER['SCRIPT_NAME'] . "?action=login&token=%TOKEN%&user=%USERNAME%";
	
	$url .= urlencode($returnURL);
	PageRedirect($url,0);
}
else
{
	if (!isset($_REQUEST['action']))
		Listing();
	else
	{
		$action = $_REQUEST['action'];
		if ($action == "addkey")
			AddKey();
		else if ($action == "removekey")
			RemoveKey();
		else if ($action == "removekeyconfirm")
			RemoveKeyConfirm();
		else if ($action == "logout")
			Logout();
		else
			Listing();
	}
}
?>