<a href=".">Home</a>&nbsp;<img src="template/img/diamond.gif">&nbsp;
<a href="">Search Groups</a>&nbsp;<img src="template/img/diamond.gif">&nbsp;
<a href="help.php">Help</a>&nbsp;<img src="template/img/diamond.gif">&nbsp;
<?php
	if( $auth->isLoggedIn() ) {
		?>
<a href="">Report an Issue</a>&nbsp;<img src="template/img/diamond.gif">&nbsp;
<a href="login.php?action=logout">Logout</a>
<?php
	} else {
		?>
<a href="login.php">Log In</a>
<?php
	}
?>