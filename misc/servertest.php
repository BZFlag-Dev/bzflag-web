<?php  
	
	$port = $_REQUEST['port'];
	if (!$port)
		$port = 5154;
		
	$address = $_SERVER['REMOTE_ADDR'];
	$host = $_REQUEST['address'];
	
	if ($host)
	{
		$hostAddress = gethostbyname($host);
		if ($hostAddress == '0.0.0.0')
			echo "Host address did not resolve\r\n";
		if ($hostAddress != $address)
			echo "Host address does not match request\r\n";
	}
	
	$somethingFailed = false;
	
	// test via TCP
	$socket = @fsockopen ($address, $port, $socketerrno, $socketerrstring, 5);
	if (!$socket || $socketerrno != 0 )
	{
		echo "TCP Connection Fialed" . $socketerrstring . "\r\n";	
		$somethingFailed = true;
	}
	else
		fwrite($socket,"1234");
	@fclose($socket);
	
	// test via UDP
	$socket = @fsockopen ("udp://".$address, $port, $socketerrno, $socketerrstring, 5);
	if (!$socket || $socketerrno != 0 )
	{
		echo "UDP Connection Fialed" . $socketerrstring . "\r\n";	
		$somethingFailed = true;
	}
	@fclose($socket);

	if(!$somethingFailed)
		echo "OK on " . $address . ":" . $port;
?>