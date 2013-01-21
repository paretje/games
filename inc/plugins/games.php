<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2013 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 21/01/2013 by Paretje
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

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

//Pluins
$plugins->add_hook("global_start", "games_global");
$plugins->add_hook("index_start", "games_index");
$plugins->add_hook("xmlhttp", "games_xmlhttp");
$plugins->add_hook("member_do_login_end", "games_member_login");
$plugins->add_hook("member_logout_end", "games_member_logout");
$plugins->add_hook("fetch_wol_activity_end", "games_online_activity");
$plugins->add_hook("build_friendly_wol_location_end", "games_online_location");
$plugins->add_hook("admin_tools_get_admin_log_action", "games_admin_log");
$plugins->add_hook("datahandler_user_update", "games_users_edit");
$plugins->add_hook("admin_user_users_merge_commit", "games_users_merge");
$plugins->add_hook("admin_user_users_delete_commit", "games_users_delete");
$plugins->add_hook("admin_user_groups_edit_graph_tabs", "games_groups_graph_tabs");
$plugins->add_hook("admin_user_groups_edit_graph", "games_groups_graph");
$plugins->add_hook("admin_user_groups_edit_commit", "games_groups_commit");

function games_info()
{
	return array(
		"name"		=> "Game Section",
		"description"	=> "Makes a powerfull system for playing games on your MyBB board.",
		"website"	=> "http://www.gamesection.org",
		"author"	=> "Paretje",
		"authorsite"	=> "http://www.gamesection.org",
		"version"	=> "1.2.3-1",
		"guid"		=> "db37073977904e9458f54937ceb13a9f",
		"compatibility" => "14*,16*"
	);
}

function games_install()
{
	global $db, $cache;

//Create tables for the Game Section
$db->write_query("CREATE TABLE `".TABLE_PREFIX."games` (
`gid` INT(10) NOT NULL AUTO_INCREMENT,
`cid` INT(5) NOT NULL,
`title` VARCHAR(50) NOT NULL,
`name` VARCHAR(50) NOT NULL,
`description` TEXT NOT NULL,
`what` TEXT NOT NULL,
`use_keys` TEXT NOT NULL,
`played` INT(15) DEFAULT '0' NOT NULL,
`lastplayed` BIGINT(30) NOT NULL,
`lastplayedby` INT(10) DEFAULT '0' NOT NULL,
`bgcolor` VARCHAR(6) DEFAULT '000000' NOT NULL,
`width` VARCHAR(4) DEFAULT '500' NOT NULL,
`height` VARCHAR(4) DEFAULT '500' NOT NULL,
`dateline` BIGINT(30) NOT NULL,
`score_type` VARCHAR(5) DEFAULT 'DESC' NOT NULL,
`rating` FLOAT NOT NULL,
`numratings` INT(5) NOT NULL,
`active` INT(1) DEFAULT '1' NOT NULL,
PRIMARY KEY (`gid`),
KEY `cid` (`cid`),
KEY `active` (`active`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_categories` (
`cid` INT(5) NOT NULL AUTO_INCREMENT,
`title` VARCHAR(40) NOT NULL,
`image` VARCHAR(255) NOT NULL,
`active` INT(1) DEFAULT '1' NOT NULL,
PRIMARY KEY (`cid`),
KEY `active` (`active`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_champions` (
`gid` INT(10) NOT NULL,
`title` VARCHAR(50) NOT NULL,
`uid` INT(10) NOT NULL,
`username` VARCHAR(120) NOT NULL,
`score` FLOAT NOT NULL,
`dateline` BIGINT(30) NOT NULL,
PRIMARY KEY (`gid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_favourites` (
`fid` INT(15) NOT NULL AUTO_INCREMENT,
`gid` INT(10) NOT NULL,
`uid` INT(10) NOT NULL,
PRIMARY KEY (`fid`),
KEY `gid` (`gid`),
KEY `uid` (`uid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_rating` (
`rid` INT(15) NOT NULL AUTO_INCREMENT,
`gid` INT(10) NOT NULL,
`uid` INT(10) NOT NULL,
`username` VARCHAR(120) NOT NULL,
`rating` INT(1) NOT NULL,
`dateline` BIGINT(30) NOT NULL,
`ip` VARCHAR(30) NOT NULL,
PRIMARY KEY (`rid`),
KEY `gid` (`gid`),
KEY `uid` (`uid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_scores` (
`sid` INT(15) NOT NULL AUTO_INCREMENT,
`gid` INT(10) NOT NULL,
`uid` INT(10) NOT NULL,
`username` VARCHAR(120) NOT NULL,
`score` FLOAT NOT NULL,
`comment` VARCHAR(120) NOT NULL,
`dateline` BIGINT(30) NOT NULL,
`ip` VARCHAR(30) NOT NULL,
PRIMARY KEY (`sid`),
KEY `gid` (`gid`),
KEY `uid` (`uid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_sessions` (
`uid` INT(10) NOT NULL AUTO_INCREMENT,
`sessiondata` TEXT NOT NULL,
`lastchange` BIGINT(30) NOT NULL,
PRIMARY KEY (`uid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_settings` (
`sid` INT(5) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(120) NOT NULL,
`title` VARCHAR(120) NOT NULL,
`description` TEXT NOT NULL,
`optionscode` TEXT NOT NULL,
`value` TEXT NOT NULL,
`displayorder` INT(5) NOT NULL,
`gid` INT(5) NOT NULL,
PRIMARY KEY (`sid`),
KEY `gid` (`gid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_settings_groups` (
`gid` INT(5) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(120) NOT NULL,
`title` VARCHAR(200) NOT NULL,
`description` TEXT NOT NULL,
`displayorder` INT(5) NOT NULL,
PRIMARY KEY (`gid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_templates` (
`tid` INT(25) NOT NULL AUTO_INCREMENT,
`theme` INT(10) NOT NULL,
`title` VARCHAR(120) NOT NULL,
`template` TEXT NOT NULL,
PRIMARY KEY (`tid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_themes` (
`tid` INT(25) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(120) NOT NULL,
`directory` VARCHAR(120) NOT NULL DEFAULT 'images',
`catsperline` INT(2) NOT NULL DEFAULT '5',
`active` INT(1) NOT NULL DEFAULT '1',
`CSS` TEXT NOT NULL,
PRIMARY KEY (`tid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_tournaments` (
`tid` INT(10) NOT NULL AUTO_INCREMENT,
`gid` INT(10) NOT NULL,
`dateline` BIGINT(30) NOT NULL,
`rounds` INT(1) NOT NULL,
`roundtime` INT(1) NOT NULL,
`maxtries` INT(2) NOT NULL,
`joinedplayers` INT(3) NOT NULL DEFAULT '1',
`status` VARCHAR(10) NOT NULL DEFAULT 'open',
`round` INT(1) NOT NULL DEFAULT '0',
`champion` INT(10) NOT NULL,
`roundinformation` TEXT NOT NULL,
PRIMARY KEY (`tid`),
KEY `gid` (`gid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_tournaments_players` (
`pid` INT(15) NOT NULL AUTO_INCREMENT,
`tid` INT(10) NOT NULL,
`rid` INT(1) NOT NULL,
`uid` INT(10) NOT NULL,
`username` VARCHAR(120) NOT NULL,
`score` FLOAT NOT NULL,
`score_try` INT(2) NOT NULL,
`tries` INT(2) NOT NULL,
`dateline` BIGINT(30) NOT NULL,
PRIMARY KEY (`pid`),
KEY `tid` (`tid`),
KEY `rid` (`rid`),
KEY `uid` (`uid`)
) ENGINE=MyISAM".$db->build_create_table_collation().";");

//Update MyBB tables for the Game Section
$db->write_query("ALTER TABLE `".TABLE_PREFIX."usergroups` ADD `canviewgames` INT(1) NOT NULL DEFAULT '1',
ADD `canplaygames` INT(1) NOT NULL DEFAULT '1',
ADD `canplaytournaments` INT(1) NOT NULL DEFAULT '1',
ADD `canaddtournaments` INT(1) NOT NULL DEFAULT '1';");

$db->write_query("ALTER TABLE `".TABLE_PREFIX."users`
ADD `games_maxgames` INT(2) NOT NULL DEFAULT '0',
ADD `games_maxscores` INT(2) NOT NULL DEFAULT '0',
ADD `games_sortby` VARCHAR(10) NOT NULL DEFAULT '0',
ADD `games_order` VARCHAR(4) NOT NULL DEFAULT '0',
ADD `games_theme` INT(10) NOT NULL DEFAULT '0',
ADD `games_tournamentnotify` INT(1) NOT NULL DEFAULT '1';");

$db->write_query("UPDATE ".TABLE_PREFIX."usergroups SET canviewgames='1', canplaygames='0', canplaytournaments='0', canaddtournaments='0' WHERE gid='1'");
$db->write_query("UPDATE ".TABLE_PREFIX."usergroups SET canviewgames='1', canplaygames='0', canplaytournaments='0', canaddtournaments='0' WHERE gid='5'");
$db->write_query("UPDATE ".TABLE_PREFIX."usergroups SET canviewgames='0', canplaygames='0', canplaytournaments='0', canaddtournaments='0' WHERE gid='7'");

//Update usergroupschache
$cache->update_usergroups();

//Update adminpermissions
change_admin_permission("games", false, 1);
change_admin_permission("games", "games", 1);
change_admin_permission("games", "gamedata", 1);
change_admin_permission("games", "categories", 1);
change_admin_permission("games", "settings", 1);
change_admin_permission("games", "themes", 1);
change_admin_permission("games", "templates", 1);
change_admin_permission("games", "tools", 1);
change_admin_permission("games", "version", 1);

//Insert tasks
require_once MYBB_ROOT."inc/functions_task.php";

$gamescleanup_insert = array(
	'title'		=> $db->escape_string("Game Section Cleanup"),
	'description'	=> $db->escape_string("Cleans old Game Section sessions"),
	'file'		=> $db->escape_string("gamescleanup"),
	'minute'	=> $db->escape_string("0"),
	'hour'		=> $db->escape_string("*"),
	'day'		=> $db->escape_string("*"),
	'month'		=> $db->escape_string("*"),
	'weekday'	=> $db->escape_string("*"),
	'enabled'	=> intval("1"),
	'logging'	=> intval("1")
);
$gamescleanup_insert['nextrun'] = fetch_next_run($gamescleanup_insert);

$tournamentstatus_insert = array(
	'title'		=> $db->escape_string("Game Section Tournament Status"),
	'description'	=> $db->escape_string("Automaticaly changes the status of a Game Section Tournament"),
	'file'		=> $db->escape_string("tournamentstatus"),
	'minute'	=> $db->escape_string("0"),
	'hour'		=> $db->escape_string("*"),
	'day'		=> $db->escape_string("*"),
	'month'		=> $db->escape_string("*"),
	'weekday'	=> $db->escape_string("*"),
	'enabled'	=> intval("1"),
	'logging'	=> intval("1")
);
$tournamentstatus_insert['nextrun'] = fetch_next_run($tournamentstatus_insert);

$db->insert_query("tasks", $gamescleanup_insert);
$db->insert_query("tasks", $tournamentstatus_insert);

//Insert the default game of the Game Section
$game_insert = array(
	'cid'		=> intval("0"),
	'title'		=> $db->escape_string("Pacman"),
	'name'		=> $db->escape_string("pacman"),
	'description'	=> $db->escape_string("Eat all the little dots without letting the ghosts get you!"),
	'what'		=> $db->escape_string("Eat all of the dots."),
	'use_keys'	=> $db->escape_string("Arrow keys to move."),
	'bgcolor'	=> $db->escape_string("000000"),
	'active'	=> intval("1"),
	'width'		=> $db->escape_string("360"),
	'height'	=> $db->escape_string("420"),
	'dateline'	=> TIME_NOW,
	'score_type'	=> $db->escape_string("DESC")
);

$db->insert_query("games", $game_insert);

//Load settings and settinggroups
require_once MYBB_ROOT."games/settings.php";

//Insert setting groups
foreach($settings_groups as $key => $group)
{
	$group_insert = array(
		'gid'		=> $group['gid'],
		'name'		=> $group['name'],
		'title'		=> $group['title'],
		'description'	=> $group['description'],
		'displayorder'	=> $group['displayorder']
	);
	
	$db->insert_query("games_settings_groups", $group_insert);
}

//Insert settings
foreach($new_settings as $key => $setting)
{
	$setting_insert = array(
		'name'		=> $setting['name'],
		'title'		=> $setting['title'],
		'description'	=> $setting['description'],
		'optionscode'	=> $setting['optionscode'],
		'value'		=> $setting['value'],
		'displayorder'	=> $setting['displayorder'],
		'gid'		=> $setting['gid']
	);
	
	$db->insert_query("games_settings", $setting_insert);
}

//Load theme and templates
require_once MYBB_ROOT."games/templates.php";

//Insert Game Section theme
$new_theme = array(
	'name'		=> $db->escape_string($theme['name']),
	'directory'	=> $db->escape_string($theme['directory']),
	'catsperline'	=> $db->escape_string($theme['catsperline']),
	'active'	=> intval("1"),
	'CSS'		=> $db->escape_string($theme['css'])
);

$db->insert_query("games_themes", $new_theme);

// Insert Game Section templates
foreach($theme_templates as $title => $template)
{
	$template_insert = array(
		"theme"		=> "1",
		"title"		=> $title,
		"template"	=> $template
	);
	
	$db->insert_query("games_templates", $template_insert);
}
}

function games_uninstall()
{
	global $db, $cache;

//Delete Game Section tables
$db->write_query("DROP TABLE `".TABLE_PREFIX."games`,
`".TABLE_PREFIX."games_categories`,
`".TABLE_PREFIX."games_champions`,
`".TABLE_PREFIX."games_favourites`,
`".TABLE_PREFIX."games_rating`,
`".TABLE_PREFIX."games_scores`,
`".TABLE_PREFIX."games_sessions`,
`".TABLE_PREFIX."games_settings`,
`".TABLE_PREFIX."games_settings_groups`,
`".TABLE_PREFIX."games_templates`,
`".TABLE_PREFIX."games_themes`,
`".TABLE_PREFIX."games_tournaments`,
`".TABLE_PREFIX."games_tournaments_players`;");

//Delete Game Section updates of the MyBB tables
$db->write_query("ALTER TABLE `".TABLE_PREFIX."usergroups` DROP `canviewgames`,
DROP `canplaygames`,
DROP `canplaytournaments`,
DROP `canaddtournaments`;");

$db->write_query("ALTER TABLE `".TABLE_PREFIX."users`
DROP `games_maxgames`,
DROP `games_maxscores`,
DROP `games_sortby`,
DROP `games_order`,
DROP `games_theme`,
DROP `games_tournamentnotify`;");

//Update usergroupschache
$cache->update_usergroups();

//Update adminpermissions
change_admin_permission("games", false, -1);
change_admin_permission("games", "games", -1);
change_admin_permission("games", "gamedata", -1);
change_admin_permission("games", "categories", -1);
change_admin_permission("games", "settings", -1);
change_admin_permission("games", "themes", -1);
change_admin_permission("games", "templates", -1);
change_admin_permission("games", "tools", -1);
change_admin_permission("games", "version", -1);

//Delete tasks
$db->delete_query("tasks", "file='gamescleanup'");
$db->delete_query("tasks", "file='tournamentstatus'");
}

function games_activate()
{
	global $db;

//Update MyBB templates
require_once MYBB_ROOT.'inc/adminfunctions_templates.php';

find_replace_templatesets('header', '#'.preg_quote('{$lang->toplinks_help}</a></li>').'#', "{\$lang->toplinks_help}</a></li>
					<li><a href=\"{\$mybb->settings['bburl']}/games.php\"><img src=\"{\$mybb->settings['bburl']}/games/images/games.png\" alt=\"\" />{\$lang->gamesection}</a></li>");

find_replace_templatesets('usercp_nav', '#'.preg_quote('{$usercpmenu}').'#', "{\$usercpmenu}
<tr>
	<td class=\"tcat\">
		<div class=\"expcolimage\"><img src=\"{\$theme['imgdir']}/collapse{\$collapsedimg['usercpgames']}.gif\" id=\"usercpgames_img\" class=\"expander\" alt=\"[-]\" title=\"[-]\" /></div>
		<div><span class=\"smalltext\"><strong>{\$lang->gamesection}</strong></span></div>
	</td>
</tr>
<tbody style=\"{\$collapsed['usercpgames_e']}\" id=\"usercpgames_e\">
	<tr><td class=\"trow1 smalltext\"><a href=\"games.php?action=settings\" class=\"usercp_nav_item usercp_nav_options\">{\$lang->your_settings}</a></td></tr>
</tbody>");
}

function games_deactivate()
{
	global $db, $cache;

//Delete Game Section updates of the MyBB templates
require_once MYBB_ROOT."inc/adminfunctions_templates.php";

find_replace_templatesets("header", '#'.preg_quote('
					<li><a href="{$mybb->settings[\'bburl\']}/games.php"><img src="{$mybb->settings[\'bburl\']}/games/images/games.png" alt="" />{$lang->gamesection}</a></li>').'#', '', 0);

find_replace_templatesets("usercp_nav", '#'.preg_quote('
<tr>
	<td class="tcat">
		<div class="expcolimage"><img src="{$theme[\'imgdir\']}/collapse{$collapsedimg[\'usercpgames\']}.gif" id="usercpgames_img" class="expander" alt="[-]" title="[-]" /></div>
		<div><span class="smalltext"><strong>{$lang->gamesection}</strong></span></div>
	</td>
</tr>
<tbody style="{$collapsed[\'usercpgames_e\']}" id="usercpgames_e">
	<tr><td class="trow1 smalltext"><a href="games.php?action=settings" class="usercp_nav_item usercp_nav_options">{$lang->your_settings}</a></td></tr>
</tbody>').'#', '', 0);
}

function games_is_installed()
{
	global $db;
	
	if($db->table_exists("games"))
	{
		return true;
	}
	
	return false;
}

function games_global()
{
	global $lang;
	
	//Loading language file
	$lang->load("games");
}

function games_index()
{
	global $mybb, $db, $lang, $plugins;
	
	//Headers to prevent that the browser holds this page in his cache
	header("Expires: Sat, 1 Jan 2000 01:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	
	//IBProArcade v2 insert of a score
	switch($mybb->input['act'])
	{
		case 'Arcade':
			switch($mybb->input['do'])
			{
				case 'newscore':
					require_once MYBB_ROOT."games/submit.php";
				break;
			}
		break;
	}
	
	//IBProArcade v32 insert of a score
	switch($mybb->input['autocom'])
	{
		case 'arcade':
			switch($mybb->input['do'])
			{
				case 'verifyscore':
					$randchar1 = rand(1, 200);
					$randchar2 = rand(1, 200);
					
					require_once MYBB_ROOT."inc/class_games.php";
					$games_core = new games;
					
					$games_core->session_start();
					$games_core->session['randchar1'] = $randchar1;
					$games_core->session['randchar2'] = $randchar2;
					$games_core->session_update();
					
					echo("&randchar=".$randchar1."&randchar2=".$randchar2."&savescore=1&blah=OK");
					exit;
				break;
				case 'savescore':
					require_once MYBB_ROOT."games/submit.php";
				break;
				case 'newscore':
					require_once MYBB_ROOT."games/submit.php";
				break;
			}
		break;
	}
}

function games_xmlhttp()
{
	global $mybb, $lang, $groupscache, $db, $charset, $theme_games, $plugins;
	
	//Game Section core settings, themes, ...
	if($mybb->input['action'] == "games_randomgames" || $mybb->input['action'] == "games_search")
	{
		//Games language
		$lang->load("games");
		
		//Requires
		require_once MYBB_ROOT."inc/class_games.php";
		$games_core = new games;
		
		//Settings of the Game Section
		$games_core->run_settings();
		
		//Game Section closed
		if($games_core->settings['closed'] == 1 && $mybb->user['usergroup'] != 4)
		{
			xmlhttp_error($lang->closed);
		}
		
		//Theme setting
		if($mybb->user['uid'] != 0 && $mybb->user['games_theme'] != "0")
		{
			$theme_games_tid = $mybb->user['games_theme'];
		}
		else
		{
			$theme_games_tid = $games_core->settings['theme'];
		}
		
		//Game Section Theme
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".$theme_games_tid."'");
		$theme_games = $db->fetch_array($query);
		$theme_games_test = $db->num_rows($query);
		
		if($theme_games_test == 0)
		{
			//The user selected theme doesn't exist, load the default theme
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".$games_core->settings['theme']."'");
			$theme_games = $db->fetch_array($query);
			$theme_games_test2 = $db->num_rows($query);
		}
		else
		{
			$theme_games_test2 = 1;
		}
		
		if($theme_games_test2 == 0)
		{
			//The standard theme doesn't exist, load the default Game Section theme
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='1'");
			$theme_games = $db->fetch_array($query);
			$theme_games_test3 = $db->num_rows($query);
		}
		else
		{
			$theme_games_test3 = 1;
		}
		
		//No theme available
		if($theme_games_test3 == 0)
		{
			xmlhttp_error("The users selected theme, the default theme and the Game Section Default theme doesn't exist.");
		}
	}
	
	//Functions
	if($mybb->input['action'] == "games_randomgames")
	{
		//Category settings
		if(intval($mybb->input['cid']) != 0 && $games_core->settings['stats_cats'] == 1)
		{
			$where_cat = " AND cid='".intval($mybb->input['cid'])."'";
		}
		
		//Put games in array
		$query = $db->query("SELECT gid, name, title FROM ".TABLE_PREFIX."games WHERE active='1'".$where_cat." ORDER BY dateline");
		while($game = $db->fetch_array($query))
		{
			$games[] = $game;
		}
		
		//Load x random games
		for($i = 1; $i <= $games_core->settings['stats_randomgames_max']; $i++)
		{
			$id = mt_rand(0, count($games)-1);
			eval("\$randomgames_bit .= \"".$games_core->template("games_stats_randomgames_bit")."\";");
		}
		
		//Output
		header("Content-type: text/plain; charset=".$charset);
		echo $randomgames_bit;
	}
	elseif($mybb->input['action'] == "games_search")
	{
		//Replacing
		$patterns[0] = '/ /';
		$replacements[0] = "%";
		
		$title = $db->escape_string(preg_replace($patterns, $replacements, htmlspecialchars_decode($mybb->input['title'])));
		
		// If the string is less than 3 characters, quit.
		if(my_strlen($mybb->input['title']) < 3)
		{
			exit;
		}
		
		//Load games
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE title LIKE '%".$title."%' AND active='1' ORDER BY title ASC");
		$games_test = $db->num_rows($query);
		
		if($games_test != 0)
		{
			while($games = $db->fetch_array($query))
			{
				eval("\$games_bit .= \"".$games_core->template("games_tournaments_add_game_search_bit")."\";");
			}
			
			//Output
			header("Content-type: text/plain; charset=".$charset);
			eval("\$results = \"".$games_core->template("games_tournaments_add_game_search")."\";");
			echo $results;
		}
	}
}

function games_member_login()
{
	global $user, $db;
	
	//Delete Game Section session of user
	$db->write_query("DELETE FROM ".TABLE_PREFIX."games_sessions WHERE uid='".$user['uid']."'");
}

function games_member_logout()
{
	global $mybb, $db;
	
	//Delete Game Section session of user
	$db->write_query("DELETE FROM ".TABLE_PREFIX."games_sessions WHERE uid='".$mybb->user['uid']."'");
}

function games_online_activity($user_activity)
{
	//Get the filename
	$split_loc = explode(".php", $user_activity['location']);
	$filename = my_substr($split_loc[0], -my_strpos(strrev($split_loc[0]), "/"));
	
	if($user_activity['activity'] == "unknown" && $filename == "games")
	{
		$user_activity['activity'] = "games";
		
		return $user_activity;
	}
}

function games_online_location($plugin_array)
{
	global $lang;
	
	if($plugin_array['user_activity']['activity'] == "games")
	{
		$plugin_array['location_name'] = $lang->sprintf($lang->viewing_gamesection, str_replace("&amp;", "&", $plugin_array['user_activity']['location']));
		
		return $plugin_array;
	}
}

function games_admin_log($plugin_array)
{
	if($plugin_array['lang_string'] == "admin_log_games_gamedata_delete")
	{
		if($plugin_array['logitem']['data'][0] == "file")
		{
			$plugin_array['lang_string'] .= "_file";
		}
		elseif($plugin_array['logitem']['data'][0] == "dir")
		{
			$plugin_array['lang_string'] .= "_dir";
		}
		
		return $plugin_array;
	}
}

function games_users_edit($user)
{
	global $old_user, $db;
	if($user->user_update_data['username'] != $old_user['username'] && $user->user_update_data['username'] != '')
	{
		$username_update = array(
			"username" => $user->user_update_data['username']
		);
		
		//Update all champions and scores
		$db->update_query("games_champions", $username_update, "uid='".$user->uid."'");
		$db->update_query("games_scores", $username_update, "uid='".$user->uid."'");
	}
	
	return $user;
}

function games_users_merge()
{
	global $source_user, $destination_user, $db;
	
	$user_update = array(
		"uid"		=> $destination_user['uid'],
		"username"	=> $destination_user['username']
	);
	
	$uid_update['uid'] = $destuser['uid'];
	
	//Update all champions and scores
	$db->update_query("games_champions", $user_update, "uid='".$source_user['uid']."'");
	$db->update_query("games_favourites", $uid_update, "uid='".$source_user['uid']."'");
	$db->update_query("games_rating", $user_update, "uid='".$source_user['uid']."'");
	$db->update_query("games_scores", $user_update, "uid='".$source_user['uid']."'");
	
	//Delete session of source user
	$db->write_query("DELETE FROM ".TABLE_PREFIX."games_sessions WHERE uid='".$source_user['uid']."'");
	
	//Delete all duplicated favourites
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_favourites WHERE uid='".$destination_user['uid']."'");
	while($favourites = $db->fetch_array($query))
	{
		if(!isset($favourite[$favourites['gid']]))
		{
			$favourite[$favourites['gid']] = "OK";
		}
		else
		{
			$db->delete_query("games_favourites", "fid='".$favourites['fid']."'");
		}
	}
	
	//Delete all duplicated ratings
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_rating ORDER BY rating DESC");
	while($rating = $db->fetch_array($query))
	{
		//Control double ratings
		if(!isset($ratings[$rating['gid']][$rating['uid']]))
		{
			$ratings[$rating['gid']][$rating['uid']] = "OK";
		}
		else
		{
			$db->delete_query("games_rating", "rid='".$rating['rid']."'");
		}
		
		//Rate count_chars
		$ratings[$rating['gid']]['rating'] += $rating['rating'];
		
		//Count ratings
		$ratings[$rating['gid']]['rate_count']++;
	}
	
	//Recount rating
	$query2 = $db->query("SELECT * FROM ".TABLE_PREFIX."games");
	while($games = $db->fetch_array($query2))
	{
		if($ratings[$games['gid']]['rate_count'] != 0)
		{
			$rating = $ratings[$games['gid']]['rating'] / $ratings[$games['gid']]['rate_count'];
			$rating = ceil($rating);
			
			$db->write_query("UPDATE ".TABLE_PREFIX."games SET rating='".$rating."' WHERE gid='".$games['gid']."'");
		}
	}
	
	//Delete all duplicated scores (DESC)
	$query = $db->query("SELECT *
	FROM ".TABLE_PREFIX."games_scores s
	LEFT JOIN ".TABLE_PREFIX."games g ON (s.gid=g.gid)
	WHERE g.score_type='DESC' AND s.uid='".$destination_user['uid']."'
	ORDER BY s.score DESC");
	while($scores = $db->fetch_array($query))
	{
		if(!isset($score[$scores['gid']]))
		{
			$score[$scores['gid']] = "OK";
		}
		else
		{
			$db->delete_query("games_scores", "sid='".$scores['sid']."'");
		}
	}
	
	//Delete all duplicated scores (ASC)
	$query = $db->query("SELECT *
	FROM ".TABLE_PREFIX."games_scores s
	LEFT JOIN ".TABLE_PREFIX."games g ON (s.gid=g.gid)
	WHERE g.score_type='ASC' AND s.uid='".$destination_user['uid']."'
	ORDER BY s.score ASC");
	while($scores = $db->fetch_array($query))
	{
		if(!isset($score[$scores['gid']]))
		{
			$score[$scores['gid']] = "OK";
		}
		else
		{
			$db->delete_query("games_scores", "sid='".$scores['sid']."'");
		}
	}
}

function games_users_delete()
{
	global $user, $db;
	
	//Delete all favourites, ratings and scores
	$db->write_query("DELETE FROM ".TABLE_PREFIX."games_favourites WHERE uid='".$user['uid']."'");
	$db->write_query("DELETE FROM ".TABLE_PREFIX."games_rating WHERE uid='".$user['uid']."'");
	$db->write_query("DELETE FROM ".TABLE_PREFIX."games_scores WHERE uid='".$user['uid']."'");
	$db->write_query("DELETE FROM ".TABLE_PREFIX."games_sessions WHERE uid='".$user['uid']."'");
	
	//Update champions
	//Loading the games and scores
	$query = $db->query("SELECT DISTINCT g.gid, g.score_type, g.title, s.uid, s.username, s.score, s.dateline, c.score AS champscore
	FROM ".TABLE_PREFIX."games g
	LEFT JOIN ".TABLE_PREFIX."games_scores s ON (g.gid=s.gid)
	LEFT JOIN ".TABLE_PREFIX."games_champions c ON (g.gid=c.gid)
	WHERE c.uid='".$user['uid']."'");
	while($scores = $db->fetch_array($query))
	{
		if(($champ[$scores['gid']]['score'] < $scores['score'] && $scores['score_type'] == DESC) || ($champ[$scores['gid']]['score'] > $scores['score'] && $score['score_type'] == ASC))
		{
			$champ[$scores['gid']]['gid'] = $scores['gid'];
			$champ[$scores['gid']]['title'] = $scores['title'];
			$champ[$scores['gid']]['uid'] = $scores['uid'];
			$champ[$scores['gid']]['username'] = $scores['username'];
			$champ[$scores['gid']]['score'] = $scores['score'];
			$champ[$scores['gid']]['dateline'] = $scores['dateline'];
		}
	}
	
	//Read the array
	if(is_array($champ))
	{
		foreach($champ as $gid => $array)
		{
			$champs_array = array(
				'gid'			=> intval($champ[$gid]['gid']),
				'title'			=> $db->escape_string($champ[$gid]['title']),
				'uid'			=> intval($champ[$gid]['uid']),
				'username'		=> $db->escape_string($champ[$gid]['username']),
				'score'			=> $db->escape_string($champ[$gid]['score']),
				'dateline'		=> $db->escape_string($champ[$gid]['dateline']),
			);
			
			$db->update_query("games_champions", $champs_array, "gid='".$champ[$gid]['gid']."'");
		}
	}
	
	//Delete champions without a second score
	$db->delete_query("games_champions", "uid='".$user['uid']."'");
}

function games_groups_graph_tabs($tabs)
{
	global $lang;
	
	$tabs['games'] = $lang->gamesection;
	
	return $tabs;
}

function games_groups_graph()
{
	global $lang, $form, $mybb, $plugins;
	
	//The Game Section Permissions
	echo "<div id=\"tab_games\">";	
	$form_container = new FormContainer($lang->gamesection);
	
	$general_options = array(
		$form->generate_check_box("canviewgames", 1, $lang->can_view_games, array("checked" => $mybb->input['canviewgames'])),
		$form->generate_check_box("canplaygames", 1, $lang->can_play_games, array("checked" => $mybb->input['canplaygames']))
	);
	$form_container->output_row($lang->gamesection, "", "<div class=\"group_settings_bit\">".implode("</div><div class=\"group_settings_bit\">", $general_options)."</div>");
	
	$tournaments_options = array(
		$form->generate_check_box("canplaytournaments", 1, $lang->can_play_tournaments, array("checked" => $mybb->input['canplaytournaments'])),
		$form->generate_check_box("canaddtournaments", 1, $lang->can_add_tournaments, array("checked" => $mybb->input['canaddtournaments']))
	);
	$form_container->output_row($lang->tournaments, "", "<div class=\"group_settings_bit\">".implode("</div><div class=\"group_settings_bit\">", $tournaments_options)."</div>");
	
	//Plugin
	$plugins->run_hooks_by_ref("admin_user_groups_edit_games", $form_container);
	
	$form_container->end();
	echo "</div>";
}

function games_groups_commit()
{
	global $updated_group, $mybb;
	
	$updated_group['canviewgames'] = $mybb->input['canviewgames'];
	$updated_group['canplaygames'] = $mybb->input['canplaygames'];
	$updated_group['canplaytournaments'] = $mybb->input['canplaytournaments'];
	$updated_group['canaddtournaments'] = $mybb->input['canaddtournaments'];
}
?>
