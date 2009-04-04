<?php  
	
	$port = $_REQUEST['port'];
	if (!$port)
		$port = 5154;
		
	if (!$_REQUEST['address'])
		$address = $_SERVER['REMOTE_ADDR'];
	else
	{
		$address = gethostbyname($_REQUEST['address']);
		if ($address == '0.0.0.0')
		{
			$responce += "address did not resolve\n";
			$address = $_SERVER['REMOTE_ADDR'];
		}
	}
	
	$somethingFailed = false;
	
	// test via TCP
	$socket = @fsockopen ($address, $port, $socketerrno, $socketerrstring, 5);
	if (!$socket || $socketerrno != 0 )
	{
		echo "TCP Connection Fialed" . $socketerrstring . "\n";	
		$somethingFailed = true;
	}
	else
		fwrite($socket,"1234");
	@fclose($socket);
	
	// test via UDP
	$socket = @fsockopen ("udp://".$address, $port, $socketerrno, $socketerrstring, 5);
	if (!$socket || $socketerrno != 0 )
	{
		echo "UDP Connection Fialed" . $socketerrstring . "\n";	
		$somethingFailed = true;
	}
	@fclose($socket);

	if(!$somethingFailed)
		echo "OK on " . $address . ":" . $port;
?>