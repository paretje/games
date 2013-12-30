<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2009 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 29/03/2009 by Paretje
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 ***************************************************************************/

function task_gamescleanup($task)
{
	global $db, $lang;
	
	require_once MYBB_ROOT."inc/class_games.php";
	$games_core = new games;
	
	//Load the language file
	$lang->load("games_tasks");
	
	//Clear out sessions older than 24h
	$db->delete_query("games_sessions", "lastchange<'".(TIME_NOW-$games_core->session_time)."'");
	
	add_task_log($task, $lang->task_gamescleanup_ran);
}
?>