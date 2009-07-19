<?php  
	function Meta()
	{
		echo
		"<meta http-equiv=\"Content-Typ\e\" content=\"text/html; charset=ISO-8859-1\">
		<meta name=\"robots\" content=\"index, follow\">
		<link href=\"css.css\" rel=\"stylesheet\" type=\"text/css\">";
	}
	
	function DocType()
	{
		echo 
		"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\">
		<head>";
	}
	
	function PageHeader( $title = '' )
	{	
		DocType();
		
		echo "<title>" . $title . "</title>";
		Meta();
		
		echo 
			"</head>
			<body background=0xffffff leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
			<div id=\"PageFrame\">
			
			<!-- begin header -->
			<div id=\"Header\">
			</div>			
			<!-- end header -->
			";	
	}
	
	function PageFooter( )
	{			
		echo "
			<!-- begin footer -->
			<div id=\"Footer\">
			</div>			
			<!-- end footer -->
			
			</div> <!-- PageFrame -->
			</body>
			</html>
			";	
	}
	
	function PageRedirect( $url, $wait = 0 )
	{	
		DocType();

		echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"" . $wait . "; url=" . $url . "\">";
		Meta();
		
		echo 
			"</head>
			<body background=0xffffff leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
			<div id=\"Redirect\">
			You are being directed to <a href=\"".$url."\"> ". $url . " </a>
			</div>
			</body>
			</html>
			";	
	}
?>