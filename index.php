<?php
	/*
	My Daily Todo
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

	function check_login_from_file( $username, $password )
	{
		$userfile = 'users/' . $username . '.txt';

		// No user file = Not registered user
		if(! file_exists( $userfile ) )
		{
			$_SESSION['errorMsg'] = 'User ' . $username . ' is not found.';
			return false;
		}

		// Read whole user file
		$data = file( $userfile, FILE_IGNORE_NEW_LINES );

		// Try to search what is correct password
		foreach( $data as $row )
		{
			// Values must have key=value -style in user file.
			$tmp = explode( '=', $row );

			// If we have corrupted user file this can happen.
			if(!isset( $tmp[0] ) || !isset( $tmp[1] ) )
				continue;

			// Key is password, good. Check the value.
			if( strtolower( $tmp[0] ) == 'password' )
			{
				// Correct username found!
				if( $tmp[1] == sha1( $password ) )
				{
					$_SESSION['username'] = $username;
					return true;
				}

				$_SESSION['errorMsg'] = 'Invalid password.';
				return false;
			}
		}
	}

	function check_post_values()
	{
		if( isset( $_POST['username'] ) 
			&& isset( $_POST['password'] ) )
		{
			check_login_from_file( $_POST['username'], 
				$_POST['password'] );
		}
	}

	function show_tasks( $day )
	{
		$todo_file = 'users/' . $_SESSION['username'] . '/'
			. $day . '.txt';

		// Check if there is daily file
		if(! file_exists( $todo_file ) )
			return false;

		$data = file( $todo_file, FILE_IGNORE_NEW_LINES );

		echo '<div id="tasks">';
		echo '<table>';

		$i = 0;
		foreach( $data as $row )
		{
			$i++;

			// Index 0 is task name, index 1 is task status
			$tmp = explode( '|', $row );

			echo '<tr>';
			echo '<td width="90%">' . $tmp[0] . '</td>';
			echo '<td><a href="modify_status.php?id=' . $i 
				. '">' . $tmp[1] . '</a></td>';
			echo '</tr>';
		}

		echo '</table>';
		echo '</div>';

		return true;
	}

	function show_own_page( $day )
	{
		// Convert date to finnish format
		$tmp = explode( '-', $day );
		$tmp = $tmp[2] . '.' . $tmp[1] . '.' . $tmp[0];

		echo '<div id="ownpage">';
		echo '<h2>Tasks for day ' . $tmp . '</h1>';

		// If there is no tasks to given date
		if(! show_tasks( $day ) )
			echo 'No tasks for this day.<br><br>';

		echo '<hr>';

		if( $day == date( 'Y-m-d' ) )
			echo '<a href="edit.php">Modify tasks</a>';

		echo '<a href="logout.php">Logout</a>';
		echo '</div>';
	}

	function show_login()
	{
		echo '<div id="login">';
		echo '<h2>Login</h2>';
		echo '<form action="index.php" method="post">';
		show_error_msg();
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
		echo '<td colspan="2">';
		echo '<input type="submit" value="Login"> or ';
		echo '<a href="register.php">Register</a>';
		echo '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td colspan="2"></td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';
		echo '</div>';
	}

	function user_logged()
	{
		if( isset( $_SESSION['username'] ) )
			return true;

		return false;
	}

	function main()
	{
		// Set day to show
		if( isset( $_GET['day'] ) )
			$day = $_GET['day'];
		else
			$day = date( 'Y-m-d' );

		create_html_start();

		// Check if there is POST-data
		check_post_values();

		// Show login or user own page
		if( user_logged() )
			show_own_page( $day );
		else
			show_login();

		create_html_end();
	}


	main();
?>
