<?php

	/*
	My Daily Todo
	General functions. Part of My Daily Todo.
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
	function create_html_start()
	{
		echo '<html>';
		echo '<head>';
		echo '<title>My Daily Todo</title>';
		echo '<link rel=stylesheet type=text/css href="mydailytodo.css">';
		echo '</head>';
		echo '<body>';
		echo '<div id="top_banner">';
		echo '<h2>My daily todo</h2>';
		echo '</div>';
	}

	function create_html_end()
	{
		$url = 'http://github.com/stargazers/MyDailyTodo/tree/master';

		echo '<div id="footer">';
		echo 'This code is licensed under AGPL. Source code is available '
			. 'at <a href="' . $url . '">GitHub</a><br/>';
		echo 'Author: Aleksi Räsänen '
			. '<a href="mailto:aleksi.rasanen@runosydan.net">'
			. 'aleksi.rasanen@runosydan.net</a><br/>';
		echo '</div>';
		echo '</body>';
		echo '</html>';
	}

	function show_error_msg()
	{
		if( isset( $_SESSION['errorMsg'] ) )
		{
			echo '<div id="error">';
			echo $_SESSION['errorMsg'];
			echo '</div>';

			unset( $_SESSION['errorMsg'] );
		}
	}
?>
