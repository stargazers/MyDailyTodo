<?php

	/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	session_start();
	require 'general_functions.php';

	function check_post_values()
	{
		if(! isset( $_POST['username'] ) )
			return false;

		// Are there two different passwords?
		if( isset( $_POST['password'] ) 
			&& isset( $_POST['password_again'] ) )
		{
			if( $_POST['password'] != $_POST['password_again'] )
			{
				$_SESSION['errorMsg'] = 'Passwords did not match.';
				return;
			}
		}

		// Is username empty?
		if( isset( $_POST['username'] ) && empty( $_POST['username'] ) )
		{
			$_SESSION['errorMsg'] = 'Username is not set.';
			return;
		}

		// If users exists
		if( file_exists( 'users/' . $_POST['username'] . '.txt' ) )
		{
			$_SESSION['errorMsg'] = 'Username is already used.';
			return;
		}

		$fh = @fopen( 'users/' . $_POST['username'] . '.txt', 'w' );
		
		if(! $fh )
		{
			$_SESSION['errorMsg'] = 'Permission denied.';
			return;
		}

		fwrite( $fh, 'password=' . sha1( $_POST['password'] ) . "\n" );
		fclose( $fh );

		$_SESSION['errorMsg'] = 'User is now registered!';
		header( 'Location: index.php' );
	}

	function create_register_form()
	{
		echo '<div id="register">';
		echo '<h2>Register</h2>';
		echo '<form action="register.php" method="post">';
		echo '<table>';
		echo '<tr>';
		echo '<td>Username</td>';
		echo '<td><input type="text" name="username"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>Password</td>';
		echo '<td><input type="password" name="password"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>Password again</td>';
		echo '<td><input type="password" name="password_again"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td colspan="2"><input type="submit" value="Register"></td>';
		echo '</tr>';
		echo '</table>';
		echo '<br />';
		echo '<a href="index.php">Back to main page</a>';
		echo '</form>';
		echo '</div>';
	}

	function main()
	{
		check_post_values();
		create_html_start();
		show_error_msg();
		create_register_form();
		create_html_end();
	}

	main();
?>
