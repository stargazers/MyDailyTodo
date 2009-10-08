<?php

	/*
	Long term TODOs editing and viewing. Part of My Daily Todo -project.
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

	function delete_todo( $id )
	{
		$todo_file = 'users/' . $_SESSION['username'] 
			. '/future_todo.txt';

		// Make sure that todo file exists at all.
		if(! file_exists( $todo_file ) )
			return false;

		// Read old data from file
		$data = file( $todo_file, FILE_IGNORE_NEW_LINES );
		$i = 0;

		// We rewrite the whole file, so open it for writing.
		$fh = fopen( $todo_file, 'w' );

		if(! $fh )
		{
			echo 'Failed to open TODO file for writing.';
			return false;
		}

		foreach( $data as $line )
		{
			$i++;

			if( $i != $id )
				fwrite( $fh, $line . "\n" );
		}

		fclose( $fh );
		return true;
	}

	function check_post_values()
	{
		// Is there POST-data where are new TODO's?
		if( isset( $_POST['todo'] ) )
		{
			$todo_file = 'users/' . $_SESSION['username'] 
				. '/future_todo.txt';

			// Try to open todo-file for writing.
			$fh = fopen( $todo_file, 'w' );

			if(! $fh )
			{
				echo 'Error when writing file!';
				return;
			}

			// Write all non-empty lines to TODO-file.
			foreach( $_POST['todo'] as $item )
			{
				if( $item != '' )
					fwrite( $fh, $item . "\n" );
			}

			fclose( $fh );
		}
	}

	function show_future_todos()
	{
		$todo_file = 'users/' . $_SESSION['username'] 
			. '/future_todo.txt';

		// If there is TODO-file, then list all
		// old items here and add link "Delete"
		if( file_exists( $todo_file ) )
		{
			$data = file( $todo_file, FILE_IGNORE_NEW_LINES );

			echo '<table>';
			$i = 0;
			foreach( $data as $line )
			{
				$i++;
				echo '<tr>';
				echo '<td valign="top">' . $i . '</td>';
				echo '<td valign="top">' . $line . '</td>';
				echo '<td width="10%" valign="top">';
				echo '<a href="future.php?action=delete&id=' 
					. $i . '">Delete</a></td>';
				echo '</tr>';
			}
			echo '</table>';

			return true;
		}

		return false;
	}

	function list_todos()
	{
		echo '<div id="future">';
		echo '<h2>Things what I want to make some day</h2>';

		if(! show_future_todos() )
			echo 'Nothing to do in the future. Just enjoy your life!';

		echo '<br /><br /><hr />';
		echo '<a href="future.php?action=edit">Modify TODOs</a>';
		echo '<a href="index.php">Back to main page</a>';
		echo '<br /><br />';
		echo '<a href="logout.php">Logout</a>';
		echo '</div>';
	}

	function show_edit()
	{
		echo '<div id="future">';
		echo '<h2>Edit things what I will make some day</h2>';

		$todo_file = 'users/' . $_SESSION['username'] 
			. '/future_todo.txt';

		echo '<form action="future.php" method="post">';
		echo '<table>';

		// If there is TODO-file, then list all
		// old items here and add link "Delete"
		if( file_exists( $todo_file ) )
		{
			$data = file( $todo_file, FILE_IGNORE_NEW_LINES );

			$i = 0;
			foreach( $data as $line )
			{
				$i++;
				echo '<tr>';
				echo '<td>' . $i . '</td>';
				echo '<td align="center">'
					. '<input type="text" name="todo[' . $i 
					. ']" value="' . $line . '"></td>';
				echo '</tr>';
			}
		}

		echo '<tr>';
		echo '<td>New:</td>';
		echo '<td align="center">';
		echo '<input type="text" name="todo[' . ( $i+1 ) . ']">';
		echo '</td>';
		echo '</tr>';

		echo '<tr><td colspan="2" align="center">';
		echo '<input type="submit" value="Modify">';
		echo '</td></tr>';

		echo '</table>';
		echo '</form>';

		echo '<br /><br /><hr />';
		echo '<a href="future.php?action=list">List TODOs</a>';
		echo '<a href="index.php">Back to main page</a>';
		echo '<br /><br />';
		echo '<a href="logout.php">Logout</a>';
		echo '</div>';
	}

	function main()
	{
		if( isset( $_GET['action'] ) )
			$action = $_GET['action'];
		else
			$action = 'list';

		create_html_start();
		check_post_values();

		if( $action == 'list' )
		{
			list_todos();
		}
		else if( $action == 'edit' )
		{
			show_edit();
		}
		else if( $action == 'delete' )
		{
			if( isset( $_GET['id'] ) )
			{
				delete_todo( $_GET['id']  );
				list_todos();
			}
		}

		create_html_end();
	}

	main();
?>
