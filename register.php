<?php

	/*
	Registering page. Part of My Daily Todo -project.
	Copyright (C) 2009 Aleksi Räsänen <aleksi.rasanen@runosydan.net>

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
		if(! isset( $_POST['todo_username'] ) )
			return false;

		// Remove dots and / characters so the user cannot try 
		// to set username to ../../pwnd or similar.
		$_POST['todo_username'] = preg_replace( '/\./', '', $_POST['todo_username'] );
		$_POST['todo_username'] = preg_replace( '/\//', '', $_POST['todo_username'] );

		// Is username empty?
		if( isset( $_POST['todo_username'] ) && empty( $_POST['todo_username'] ) )
		{
			$_SESSION['errorMsg'] = 'Username is not set.';
			return false;
		}

		// If users exists
		if( file_exists( 'users/' . $_POST['todo_username'] . '.txt' ) )
		{
			$_SESSION['errorMsg'] = 'Username is already used.';
			return false;
		}

		// Are there two different passwords?
		if( isset( $_POST['password'] ) 
			&& isset( $_POST['password_again'] ) )
		{
			if( $_POST['password'] != $_POST['password_again'] )
			{
				$_SESSION['errorMsg'] = 'Passwords did not match.';
				return false;
			}
		}

		// Check that the 'users/' directory exist.
		if(! file_exists( 'users/' ) )
		{
			if(! mkdir( 'users/', 0700 ) )
			{
				$_SESSION['errorMsg'] = 'Can\'t create folder users/';
				return false;
			}
		}

		$fh = @fopen( 'users/' . $_POST['todo_username'] . '.txt', 'w' );
		
		if(! $fh )
		{
			$_SESSION['errorMsg'] = 'Permission denied.';
			return false;
		}

		fwrite( $fh, 'password=' . sha1( $_POST['password'] ) . "\n" );
		fclose( $fh );

		$_SESSION['errorMsg'] = 'User ' . $_POST['todo_username'] 
			. ' is now registered!';

		return true;
	}

	function create_register_form()
	{
		echo '<div id="register">';
		echo '<h2>Register</h2>';
		echo '<form action="register.php" method="post">';
		echo '<table>';
		echo '<tr>';
		echo '<td>Username</td>';
		echo '<td><input type="text" name="todo_username"></td>';
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
		create_html_start();

		// If check_post_values returns true, then new
		// user is succesfully created (or at least it should be...)
		// Note that show_error_msg shows message where is
		// message that user is registered succesfully!
		if( check_post_values() )
		{
			show_error_msg();
			echo '<br />';
			echo '<a href="index.php">Back to main page</a>';
		}
		else
		{
			show_error_msg();
			create_register_form();
		}

		create_html_end();
	}

	main();
?>
