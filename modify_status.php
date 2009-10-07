<?php

	/*
	My Daily Todo
	Modify task status. Part of My Daily Todo -project.
	Copyright (C) 2009 Aleksi Räsänen <aleksi.rasanen@runosydan.net>
	 
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	 
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	 
	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
	*/	

	session_start();

	// User daily TODO-file.
	$todo_file = 'users/' . $_SESSION['username'] . '/'
		. date( 'Y-m-d' ) . '.txt';

	// Check that file exists (as it should!) and read it to array.
	if( file_exists( $todo_file ) )
		$data = file( $todo_file, FILE_IGNORE_NEW_LINES );
	else
		header( 'Location: index.php' );

	// We must rewrite the whole crap, so open the file for writing.
	$fh = @fopen( $todo_file, 'w' );

	// If something fails, go to main page.
	if(! $fh )
		header( 'Location: index.php' );

	// If there is no id what we want to modify, then go to main page.
	if(! isset( $_GET['id'] ) )
		header( 'Location: index.php' );

	// Item to edit
	$id = $_GET['id'];

	$i = 0;

	foreach( $data as $row )
	{
		$i++;

		// Value and status is separated with pipe char.
		$tmp = explode( '|', $row );

		// If this is the line what must be changed, then change it.
		if( $i == $id )
		{
			if( $tmp[1] == 'OK' )
				$tmp[1] = 'NOT';
			else
				$tmp[1] = 'OK';
		}

		// Write the line back to file.
		@fwrite( $fh, $tmp[0] . '|' . $tmp[1] . "\n" );
	}

	// Close the file and return to own page.
	@fclose( $fh );
	header( 'Location: index.php' );
?>
