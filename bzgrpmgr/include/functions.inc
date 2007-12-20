<?php

function graceful_exit( $reason ) {
	require_once( 'template/header.inc' );
	echo $reason;
	require_once( 'template/footer.inc' );

	exit;
}

?>