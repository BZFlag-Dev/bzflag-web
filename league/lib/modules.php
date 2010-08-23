<?php // $Id: modules.php,v 1.2 2005/03/24 16:32:38 dennismp Exp $

function module_list() {
	$list = array();
	//foreach( glob(SECTION_DIR . '*.php') as $file ) {
	//	$list[] = substr(basename($file),0,-4);
	//}
	// This code above does the same as this one. 
	if($dir=@opendir(SECTION_DIR)){
		while (false !== ($file = readdir($dir))) { 
			if( substr($file,-4) =='.php') {
				$list[] = substr(basename($file),0,-4);
			}
		}
		closedir($dir);
	}

	return $list;
}

function module_load($name) {
	$file = 'section/' . $name . '.php';

	if( file_exists($file) ) {
		require_once($file); 
		return true;
	}
	
	return false;
}

function module_invoke($name,$method) {
	// TODO Support arguments
	$func = 'section_'. $name . '_' . $method;

	if( function_exists($func) ) {
		return call_user_func($func);
	}
	else {
		return null;
	}
}

function module_invoke_all($method) {
	$retval = array();
	foreach( module_list() as $m ) {
		module_load($m);
		$retval[$m] = module_invoke($m,$method);
	}
	return $retval;
}
?>
