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

class games
{
	//Template cache
	var $cache = array();
	
	//Settings
	var $settings = array();
	
	//Session variables
	var $session = array();
	
	/*******************************
	 * Defines the session time of the Game Section sessions
	 * 
	 * Set here how long a session has to be hold in the database. The default value is 4 hours (14400)
	 * This value can't be 0!
	 *
	 *******************************/
	var $session_time = 14400;
	
	//Templates cache
	function template_cache($templates)
	{
		global $db, $theme_games;
		
		//Make the variables
		$sql = "";
		
		$names = explode(",", $templates);
		
		foreach($names as $key => $title)
		{
			$sql .= " ,'".trim($title)."'";
		}
		
		//Loading templates
		$query = $db->query("SELECT title, template FROM ".TABLE_PREFIX."games_templates WHERE title IN (''".$sql.") AND theme IN ('0','".$theme_games['tid']."') ORDER BY theme ASC");
		
		while($template = $db->fetch_array($query))
		{
			//Place it in the cache
			$this->cache[$template['title']] = $template['template'];
		}
	}

	//Templates
	function template($title, $eslashes=1, $htmlcomments=1)
	{
		global $db, $theme_games, $mybb /*, $templates*/;
		
		//Is the template already loaded
		if(!isset($this->cache[$title]))
		{
			//Load template
			$query = $db->query("SELECT template FROM ".TABLE_PREFIX."games_templates WHERE title='".$title."' AND theme IN ('0','".$theme_games['tid']."') ORDER BY theme DESC LIMIT 0,1");
			
			$gettemplate = $db->fetch_array($query);
			
			//Place it in the cache
			$this->cache[$title] = $gettemplate['template'];
		}
		
		//When the template is already loaded, take then the cached template
		$template = $this->cache[$title];
		
		//htmlcomments?
		if($htmlcomments && $mybb->settings['tplhtmlcomments'] == 1)
		{
			$template = "<!-- start: ".$title." -->\n".$template."\n<!-- end: ".$title." -->";
		}
		
		//Add slashes
		if($eslashes)
		{
			$template = str_replace("\\'", "'", $db->escape_string($template));
		}
		
		return $template;
	}
	
	//Settings
	function run_settings()
	{
		global $db, $plugins;
		
		//Settings of the game section
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_settings");
		$settings_games_test = $db->num_rows($query);
		
		//Settings doesnt exist
		if($settings_games_test == 0)
		{
			die("The Game Section Settings doesn't exist.");
		}
		
		while($settings_games = $db->fetch_array($query))
		{
			$this->settings[$settings_games['name']] = $settings_games['value'];
			
			$plugins->run_hooks("games_settings_run");
		}
	}
	
	//Session-start function
	function session_start()
	{
		global $mybb, $db, $session;
		
		//Control if it's a registred user, if so, load the session
		if($mybb->user['uid'] > 0)
		{
			//Load session
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_sessions WHERE uid='".$mybb->user['uid']."' AND lastchange>'".(TIME_NOW-$this->session_time)."' LIMIT 1");
			$session_load = $db->fetch_array($query);
			$session_test = $db->num_rows($query);
			
			//Control if the session exists. If no: create a new one!
			if($session_test != 1)
			{
				//Delete the existing session
				$db->delete_query("games_sessions", "uid='".$mybb->user['uid']."'");
				
				//Create new setting
				$session_create = array(
					'uid'			=> intval($mybb->user['uid']),
					'lastchange'		=> intval(TIME_NOW)
				);
				
				$db->insert_query("games_sessions", $session_create);
			}
			else
			{
				$this->session = unserialize($session_load['sessiondata']);
			}
		}
		else
		{
			return false;
		}
	}
	
	//Session-update function
	function session_update()
	{
		global $mybb, $db, $session;
		
		//Control if it's a registred user, if so, update the session
		if($mybb->user['uid'] > 0)
		{
			$session_update = array(
				'sessiondata'		=> serialize($this->session),
				'lastchange'		=> intval(TIME_NOW)
			);
			
			$db->update_query("games_sessions", $session_update, "uid='".$mybb->user['uid']."'");
		}
		else
		{
			return false;
		}
	}
}
?>