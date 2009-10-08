<?php

	/*
	Settings page. Part of My Daily Todo -project.
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
		if( isset( $_POST['password'] )
			&& isset( $_POST['password_again'] ) )
		{
			// Passwords did not match, do not change it.
			if( $_POST['password'] != $_POST['password_again'] )
				return 1;

			$user_file = 'users/' . $_SESSION['username'] . '.txt';

			$fh = @fopen( $user_file, 'w' );

			if(! $fh )
			{
				echo 'Failed to open user file!';
				return 2;
			}

			fwrite( $fh, 'password=' . sha1( $_POST['password'] ) . "\n" );
			fclose( $fh );

			return 0;
		}

		return 3;
	}

	function create_settings_page()
	{
		echo '<h2>Settings</h2>';

		echo '<form action="settings.php" method="post">';
		echo '<table>';
		echo '<tr>';
		echo '<td>Password</td>';
		echo '<td><input type="password" name="password"></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>Password again</td>';
		echo '<td><input type="password" name="password_again"></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td colspan="2"><input type="submit" value="Change"></td>';
		echo '</tr>';

		echo '</table>';
		echo '</form>';

		echo '<br /><br /><hr />';
		echo '<a href="index.php">Back to main page</a>';
		echo '<br /><br />';
		echo '<a href="logout.php">Logout</a>';
		echo '</div>';
	}

	function main()
	{
		create_html_start();

		echo '<div id="settings">';

		$ret = check_post_values();

		if( $ret == 0 )
			echo 'Password changed!';
		else if( $ret == 1 )
			echo 'Passwords did not match!';

		create_settings_page();
		create_html_end();
	}

	main();
?>
