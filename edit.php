<?php

	/*
	Todos adding and editing file. Part of My Daily Todo -project.
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

	function create_empty_inputs()
	{
		$max = 4;

		for( $i=1; $i < $max; $i++ )
		{
			echo '<tr>';
			echo '<td>' . $i . '.</td>';
			echo '<td><input type="text" name="todo' . $i . '"></td>';
			echo '</tr>';
		}
	}

	function create_inputs_from_file( $todo_file )
	{
		/*
		$todo_file = 'users/' . $_SESSION['todo_username'] . '/'
			. date( 'Y-m-d' ) . '.txt';
		*/

		$tmp = file( $todo_file );
		$max = 4;

		for( $i=1; $i < $max; $i++ )
		{
			$data = explode( '|', $tmp[$i-1] );

			echo '<tr>';
			echo '<td>' . $i . '.</td>';
			echo '<td><input type="text" name="todo' . $i 
				. '" value="' . $data[0] . '"></td>';
			echo '</tr>';
		}
	}

	function create_edit_form( $day )
	{
		// Convert date to finnish format
		$tmp = explode( '-', $day );
		$tmp = $tmp[2] . '.' . $tmp[1] . '.' . $tmp[0];

		$todo_file = 'users/' . $_SESSION['todo_username'] . '/'
			. $day . '.txt';

		echo '<div id="edit">';
		echo '<form action="edit.php?day=' . $day . '" method="post">';
	
		echo '<table>';

		echo '<tr>';
		echo '<td colspan="2">';
		echo '<b>My todo for ' . $tmp . '</b>';
		echo '</td>';
		echo '</tr>';

		if(! file_exists( $todo_file ) )
			create_empty_inputs();
		else
			create_inputs_from_file( $todo_file );

		echo '<tr>';
		echo '<td colspan="2">';
		echo '<input type="submit" value="Save">';
		echo '</td>';
		echo '</tr>';

		echo '</table>';
		echo '</form>';
		echo '</div>';
	}

	function check_user_folder()
	{
		$path = 'users/' . $_SESSION['todo_username'];

		// Is there folder for username at all? If not, create it.
		if(! file_exists( $path ) )
		{
			// If folder creation fails, exit this function.
			if(!  mkdir( $path, 0777 ) )
			{
				$_SESSION['errorMsg'] = 'Can\'t create folder for user.';
				return;
			}
		}

		return true;
	}

	function check_post_values()
	{
		$max = 4;

		if(! isset( $_POST['todo1'] ) )
			return;

		// Make sure that the directory exists.
		if(! check_user_folder() )
			return false;

		// Date where we write these modifications.
		if(! isset( $_GET['day'] ) )
			$day = date( 'Y-m-d' );
		else
			$day = $_GET['day'];

		// File where we save modifications.
		$path = 'users/' . $_SESSION['todo_username'];
		$todo_file = $path . '/' . $day . '.txt';

		// If file already exists, then read the data to array.
		// This is needed if we want to keep status still saved
		// even after the rewrite of a file.
		if( file_exists( $todo_file ) )
			$old_data = file( $todo_file, FILE_IGNORE_NEW_LINES );

		$fh = fopen( $todo_file, 'w' );

		if(! $fh )
		{
			$_SESSION['errorMsg'] = 'Can\'t open daily todo file '	
				. 'for writing.';
			return;
		}

		// Write values to file.
		for( $i=1; $i < $max; $i++ )
		{
			// Make sure that the value will be set
			if(! isset( $_POST['todo' . $i] ) )
				$_POST['todo' . $i] = '';

			$status = 'NOT';

			if( isset( $old_data[$i-1] ) )
			{
				$row = explode( '|', $old_data[$i-1] );
				$status = $row[1];
			}
			
			// Do not allow user to add pipe character!
			$_POST['todo' . $i] = str_replace( '|', '', 
				$_POST['todo' . $i] );

			fwrite( $fh, $_POST['todo' . $i] . '|' . $status . "\n" );
		}

		fclose( $fh );
		header( 'Location: index.php?day=' . $day );
	}

	function main()
	{
		if( isset( $_GET['day'] ) )
			$day = $_GET['day'];
		else
			$day = date( 'Y-m-d' );

		check_post_values();
		create_html_start();
		show_error_msg();
		create_edit_form( $day );
		create_html_end();
	}


	main();

?>
