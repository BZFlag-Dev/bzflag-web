<?php

function graceful_exit( $reason ) {
	require_once( 'template/header.php' );
	echo $reason;
	require_once( 'template/footer.php' );

	exit;
}

?>
