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

		<table style="width: 90%; margin-right: auto; margin-left: auto; border-spacing: 15px;">
			<tr>
				<td class="banner"><?php include( "links.php" ); ?></td>
			</tr>
			<?php if( $auth->isAdmin() ) { ?>
			<tr>
				<td class="banner"><?php include( "adminlinks.php" ); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td class="mainframe">
