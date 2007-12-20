<?php
	global $auth;
?>

<html>
	<head>
		<title>
                        BZFlag Global Group Manager<?php if( $page_title ) echo " - ".$page_title;?>
                </title>
                <link href="template/styles.css" rel="stylesheet" type="text/css">
        </head>
        <body>
		<p style="text-align: center">
			<img src="template/img/title.png">
		</p>

		<table class="mainframe">
			<tr>
				<td class="banner"><?php include( "links.inc" ); ?></td>
			</tr>
			<?php if( $auth->isAdmin() ) { ?>
			<tr>
				<td class="banner"><?php include( "adminlinks.inc" ); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td>