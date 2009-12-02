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

	// Get the title of the TODO by ID.
	function get_todo_name( $id )
	{
		$todo_file = 'users/' . $_SESSION['todo_username'] 
			. '/future_todo.txt';

		// Make sure that todo file exists at all.
		if(! file_exists( $todo_file ) )
			return false;

		$data = file( $todo_file, FILE_IGNORE_NEW_LINES );
		$i = 0;

		foreach( $data as $line )
		{
			$i++;

			if( $i == $id )
				return $line;
		}

		return 'Not found, do not edit ID manually.';
	}

	// This will be called when user press 'Delete' link
	function delete_todo( $id )
	{
		if(! isset( $_GET['confirm_sent'] ) )
		{
			$name = get_todo_name( $id );
			echo '<h2>Confirm delete</h2>';
			echo 'Are you sure that you want to delete '
				. 'selected Future TODO "' . $name . '"?<br /><br />';

			// Link what will remove item.
			echo '<a href="future.php?action=delete&id=' . $id 
				. '&confirm_sent=yes">';
			echo 'Yes</a> / ';
			echo '<a href="future.php"> No</a>';
		}
		else
		{
			$todo_file = 'users/' . $_SESSION['todo_username'] 
				. '/future_todo.txt';

			// Make sure that todo file exists at all.
			if(! file_exists( $todo_file ) )
				return false;

			$data = file( $todo_file, FILE_IGNORE_NEW_LINES );
			$i = 0;

			// We rewrite the whole file, so open it for writing.
			$fh = fopen( $todo_file, 'w' );

			// We should create better handler here some day.
			if(! $fh )
				return false;

			foreach( $data as $line )
			{
				$i++;

				if( $i != $id )
					fwrite( $fh, $line . "\n" );
			}

			fclose( $fh );

			// After delete, just show all todos.
			list_todos();
		}
	}

	// This will be called when some long term todo is finished.
	function finish_todo( $id )
	{
		$todo_file = 'users/' . $_SESSION['todo_username'] 
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

			// Write all lines back to file except the item
			// what we wanted to remove.
			if( $i != $id )
			{
				fwrite( $fh, $line . "\n" );
			}

			// When we delete long term todo, we write it to
			// future_log.txt file, so we can later see what 
			// long term todos we have achieved :)
			else
			{
				$future_log = @fopen( 'users/' . $_SESSION['todo_username']
					. '/future_log.txt', 'a+' );

				if(! $future_log )
				{
					echo 'Can\'t save finished long term todos to file.';
					continue;
				}

				// Write todo and date when we removed it.
				fwrite( $future_log, $line . '|' 
					. date( 'Y-m-d' ) . "\n" );

				fclose( $future_log );
			}
		}

		fclose( $fh );
		return true;
	}

	function check_post_values()
	{
		// Is there POST-data where are new TODO's?
		if( isset( $_POST['todo'] ) )
		{
			$todo_file = 'users/' . $_SESSION['todo_username'] 
				. '/future_todo.txt';

			if(! file_exists( 'users/' . $_SESSION['todo_username'] ) )
			{
				if(! mkdir( 'users/' . $_SESSION['todo_username'], 0755 ) )
					echo 'Failed to create new folder!';
			}

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
		$todo_file = 'users/' . $_SESSION['todo_username'] 
			. '/future_todo.txt';

		// If there is TODO-file, then list all
		// old items here and add link "Delete"
		if( file_exists( $todo_file ) )
		{
			$data = file( $todo_file, FILE_IGNORE_NEW_LINES );

			echo '<table id="future_todos">';
			$i = 0;
			foreach( $data as $line )
			{
				$i++;
				echo '<tr>';
				echo '<td valign="top">' . $i . '</td>';
				echo '<td valign="top">' . $line . '</td>';
				echo '<td width="10%" valign="top">';
				echo '<a href="future.php?action=finished&id=' 
					. $i . '">Finished</a></td>';
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
		echo '<a href="future.php?action=show_finished">Show finished</a>';
		echo '<a href="index.php">Back to main page</a>';
		echo '<br /><br />';
		echo '<a href="logout.php">Logout</a>';
		echo '</div>';
	}

	function show_edit()
	{
		echo '<div id="future">';
		echo '<h2>Edit things what I will make some day</h2>';

		$todo_file = 'users/' . $_SESSION['todo_username'] 
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

	/*
		Show finished Long Term TODO's.
	*/
	function show_finished()
	{
		echo '<div id="future_log">';
		$todo_file = 'users/' . $_SESSION['todo_username'] . '/future_log.txt';

		if(! file_exists( $todo_file ) )
		{
			echo '<br />';
			echo 'There is no finished TODO\'s.<br /><br />';
			echo '<a href="future.php?action=list">Back to list</a>';
			echo '</div>';
			return;
		}

		$data = file( $todo_file, FILE_IGNORE_NEW_LINES );

		echo '<h3>Finished long term todos</h3>';
		echo '<table>';
		foreach( $data as $row )
		{
			$tmp = explode( '|', $row );
			echo '<tr>';
			echo '<td>';
			echo $tmp[0];
			echo '</td>';

			echo '<td>';
			echo $tmp[1];
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '<br />';
		echo '<a href="future.php?action=list">Back to list</a>';
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

		// Just list todos
		if( $action == 'list' )
		{
			list_todos();
		}
		// When we want to Modify todos
		else if( $action == 'edit' )
		{
			show_edit();
		}
		// When user press 'Delete' link
		else if( $action == 'delete' )
		{
			if( isset( $_GET['id'] ) )
				delete_todo( $_GET['id']);
		}
		// When user press 'Finished' link
		else if( $action == 'finished' )
		{
			if( isset( $_GET['id'] ) )
			{
				finish_todo( $_GET['id']  );
				list_todos();
			}
		}
		// When we want to list completed todos.
		else if( $action == 'show_finished' )
		{
			show_finished();
		}

		create_html_end();
	}

	main();
?>
