<?php

// Includes
require_once( 'include/global.inc' );

switch( $_GET['action'] ) {
	case "login":
		if( $auth->logUserIn( $_POST['username'], $_POST['password'] ) ) {
			header( "Location: ." );

			exit;
		} else {
			require_once( 'template/header.inc' );
			?>
Sorry, your login was unsuccessful.
<?php
			require_once( 'template/footer.inc' );

			exit;
		}

		break;
	case "logout":
		$auth->logUserOut();
		header( "Location: ." );

		exit;

		break;
	default:
		require_once( 'template/header.inc' );

		?>

<form method="POST" action="login.php?action=login">
        <table>
                <tr>
                        <td><b>Username:</b></td>
                        <td><input type="text" name="username" value=""></td>
                </tr>
                <tr>
                        <td><b>Password:</b></td>
                        <td><input type="password" name="password" value=""></td>
                </tr>
		<tr>
			<td><b>Stay logged in?</b></td>
			<td>
				<input type="checkbox" name="cookie">&nbsp;

				<select name="cookie_length">
					<option value="60">1 hour</option>
					<option value="720">12 hours</option>
					<option value="1440">1 day</option>
					<option value="10080">1 week</option>
					<option value="0" selected="selected">Forever</option>
				</select>
			</td>
		</tr>
        </table>
        <br>
        <input type="submit" name="submit" value="Log In">
</form>

<?php

require_once( 'template/footer.inc' );
		break;
}

?>