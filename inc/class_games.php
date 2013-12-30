<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2013 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 30/12/2013 by Paretje
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

class Game_Session
{
	// The data contained in this Game Section session
	var $data = array();
	
	/* Defines the session time of the Game Section sessions
	 * 
	 * Set here how long a session has to be hold in the database.
	 * The default value is 4 hours (14400)
	 * This value can't be 0! */
	var $session_time = 14400;
	
	// Start a new session, or load the current one if available
	function start()
	{
		global $mybb, $db;
		
		// Game Section sessions are only of benifice for registered users 
		if($mybb->user['uid'] > 0)
		{
			// Try to load an existing session
			$query = $db->query("SELECT *
				FROM ".TABLE_PREFIX."games_sessions
				WHERE uid='".$mybb->user['uid']."'
					AND lastchange>'".(TIME_NOW-$this->session_time)."' LIMIT 1");
			$session_load = $db->fetch_array($query);
			$session_test = $db->num_rows($query);
			
			// If no session exists, create one
			if($session_test != 1)
			{
				$db->delete_query("games_sessions", "uid='".$mybb->user['uid']."'");
				
				$session_create = array(
					'uid'			=> intval($mybb->user['uid']),
					'lastchange'		=> intval(TIME_NOW)
				);
				
				$db->insert_query("games_sessions", $session_create);
			}
			else
			{
				$this->data = unserialize($session_load['sessiondata']);
			}
		}
	}
	
	// Update session data
	function update()
	{
		global $mybb, $db;
		
		// Game Section sessions are only of benifice for registered users 
		if($mybb->user['uid'] > 0)
		{
			$session_update = array(
				'sessiondata'		=> serialize($this->data),
				'lastchange'		=> intval(TIME_NOW)
			);
			
			$db->update_query("games_sessions", $session_update, "uid='".$mybb->user['uid']."'");
		}
	}
}
?>
