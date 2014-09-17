<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2014 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 17/09/2014 by Paretje
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

// TODO: Add tools
$plugins->add_hook("global_start", "games_global");
$plugins->add_hook("index_start", "games_index");
$plugins->add_hook("xmlhttp", "games_xmlhttp");
$plugins->add_hook("member_do_login_end", "games_member_login");
$plugins->add_hook("member_logout_end", "games_member_logout");
$plugins->add_hook("fetch_wol_activity_end", "games_online_activity");
$plugins->add_hook("build_friendly_wol_location_end", "games_online_location");
$plugins->add_hook("admin_tools_get_admin_log_action", "games_admin_log");
$plugins->add_hook("admin_config_settings_begin", "games_config_settings");
$plugins->add_hook("admin_user_users_merge_commit", "games_users_merge");
$plugins->add_hook("admin_user_users_delete_commit", "games_users_delete");
$plugins->add_hook("admin_user_groups_edit_graph_tabs", "games_groups_graph_tabs");
$plugins->add_hook("admin_user_groups_edit_graph", "games_groups_graph");
$plugins->add_hook("admin_user_groups_edit_commit", "games_groups_commit");

function games_info()
{
	return array(
		"name"		=> "Game Section",
		"description"	=> "The Game Section is a powerfull plugin for the  MyBB forum software, creating a whole environment to play games on your board.",
		"website"	=> "http://www.gamesection.org",
		"author"	=> "Paretje",
		"authorsite"	=> "http://www.gamesection.org",
		"version"	=> "1.3.0",
		"guid"		=> "db37073977904e9458f54937ceb13a9f",
		"compatibility" => "18*"
	);
}

function games_install()
{
	global $db, $cache, $lang;

	// Create the Game Section tables
	$db->write_query("CREATE TABLE `".TABLE_PREFIX."games` (
		`gid` INT(10) NOT NULL AUTO_INCREMENT,
		`cid` INT(5) NOT NULL,
		`title` VARCHAR(50) NOT NULL,
		`name` VARCHAR(50) NOT NULL,
		`description` TEXT NOT NULL,
		`purpose` TEXT NOT NULL,
		`keys` TEXT NOT NULL,
		`champion` INT(15) DEFAULT '0' NOT NULL,
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
		`score` FLOAT NOT NULL,
		`comment` VARCHAR(120) NOT NULL,
		`dateline` BIGINT(30) NOT NULL,
		`ip` VARCHAR(30) NOT NULL,
		PRIMARY KEY (`sid`),
		KEY `gid` (`gid`),
		KEY `uid` (`uid`)
		) ENGINE=MyISAM".$db->build_create_table_collation().";");

	/* TODO: Reconsider the necessity of this seperate table
	 * I think it was to avoid the creation of a new session after some
	 * time, but I'm not sure */
	$db->write_query("CREATE TABLE `".TABLE_PREFIX."games_sessions` (
		`uid` INT(10) NOT NULL AUTO_INCREMENT,
		`sessiondata` TEXT NOT NULL,
		`lastchange` BIGINT(30) NOT NULL,
		PRIMARY KEY (`uid`)
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

	// Insert fields in usergroups in order to make use of the MyBB permissions system
	$db->write_query("ALTER TABLE `".TABLE_PREFIX."usergroups`
		ADD `canviewgames` INT(1) NOT NULL DEFAULT '1',
		ADD `canplaygames` INT(1) NOT NULL DEFAULT '1',
		ADD `canplaytournaments` INT(1) NOT NULL DEFAULT '1',
		ADD `canaddtournaments` INT(1) NOT NULL DEFAULT '1';");

	$db->write_query("UPDATE ".TABLE_PREFIX."usergroups SET canviewgames='1', canplaygames='0', canplaytournaments='0', canaddtournaments='0' WHERE gid='1'");
	$db->write_query("UPDATE ".TABLE_PREFIX."usergroups SET canviewgames='1', canplaygames='0', canplaytournaments='0', canaddtournaments='0' WHERE gid='5'");
	$db->write_query("UPDATE ".TABLE_PREFIX."usergroups SET canviewgames='0', canplaygames='0', canplaytournaments='0', canaddtournaments='0' WHERE gid='7'");
	$cache->update_usergroups();

	// Insert fields in users to give the users the possiblity to change some settings of the Game Section
	$db->write_query("ALTER TABLE `".TABLE_PREFIX."users`
		ADD `games_maxgames` INT(2) NOT NULL DEFAULT '0',
		ADD `games_maxscores` INT(2) NOT NULL DEFAULT '0',
		ADD `games_sortby` VARCHAR(10) NOT NULL DEFAULT '0',
		ADD `games_order` VARCHAR(4) NOT NULL DEFAULT '0',
		ADD `games_tournamentnotify` INT(1) NOT NULL DEFAULT '1';");

	// Insert the permissions for the ACP
	change_admin_permission("games", false, 1);
	change_admin_permission("games", "games", 1);
	change_admin_permission("games", "categories", 1);
	change_admin_permission("games", "version", 1);

	// Insert the Game Section tasks
	require_once MYBB_ROOT."inc/functions_task.php";

	$gamescleanup_insert = array(
		'title'		=> $db->escape_string("Game Section Cleanup"),
		'description'	=> $db->escape_string("Cleans old Game Section sessions"),
		'file'		=> $db->escape_string("gamescleanup"),
		'minute'	=> 0,
		'hour'		=> $db->escape_string("*"),
		'day'		=> $db->escape_string("*"),
		'month'		=> $db->escape_string("*"),
		'weekday'	=> $db->escape_string("*"),
		'enabled'	=> 1,
		'logging'	=> 1
	);
	$gamescleanup_insert['nextrun'] = fetch_next_run($gamescleanup_insert);

	$tournamentstatus_insert = array(
		'title'		=> $db->escape_string("Game Section Tournament Status"),
		'description'	=> $db->escape_string("Automaticaly changes the status of a Game Section Tournament"),
		'file'		=> $db->escape_string("tournamentstatus"),
		'minute'	=> 0,
		'hour'		=> $db->escape_string("*"),
		'day'		=> $db->escape_string("*"),
		'month'		=> $db->escape_string("*"),
		'weekday'	=> $db->escape_string("*"),
		'enabled'	=> 1,
		'logging'	=> 1
	);
	$tournamentstatus_insert['nextrun'] = fetch_next_run($tournamentstatus_insert);

	$db->insert_query("tasks", $gamescleanup_insert);
	$db->insert_query("tasks", $tournamentstatus_insert);

	// Load settings language file as it's the place where the title and descriptions are kept
	$lang->load("games_settings", false, true);

	// Load and insert settings and settinggroups
	require_once MYBB_ROOT."games/settings.php";

	foreach($games_settinggroups as $key => $group)
	{
		$group['title'] = "setting_group_".$group['name'];
		$group['description'] = $group['title']."_desc";
		$settinggroup_insert = array(
			'name'		=> $db->escape_string($group['name']),
			'title'		=> $db->escape_string($lang->$group['title']),
			'description'	=> $db->escape_string($lang->$group['description']),
			'disporder'	=> $db->escape_string($group['displayorder']),
			'isdefault'	=> 0
		);

		$gid[$group['gid']] = $db->insert_query("settinggroups", $settinggroup_insert);
	}

	foreach($games_settings as $key => $setting)
	{
		$setting['title'] = "setting_".$setting['name'];
		$setting['description'] = $setting['title']."_desc";
		$setting_insert = array(
			'name'		=> $db->escape_string($setting['name']),
			'title'		=> $db->escape_string($lang->$setting['title']),
			'description'	=> $db->escape_string($lang->$setting['description']),
			'optionscode'	=> $db->escape_string($setting['optionscode']),
			'value'		=> $db->escape_string($setting['value']),
			'disporder'	=> $db->escape_string($setting['displayorder']),
			'gid'		=> $gid[$setting['gid']]
		);

		$db->insert_query("settings", $setting_insert);
	}

	rebuild_settings();

	// Load and insert templates
	require_once MYBB_ROOT."games/templates.php";

	// TODO: Add a template group in MyBB, and add them as default templates
	foreach($games_templates as $title => $template)
	{
		$template_insert = array(
			"title"		=> $db->escape_string($title),
			"template"	=> $db->escape_string($template),
			"sid"		=> -1,
			'version'	=> $db->escape_string($mybb->version_code),
			'dateline'	=> TIME_NOW
		);

		$db->insert_query("templates", $template_insert);
	}
}

function games_uninstall()
{
	global $db, $cache;

	// Delete the Game Section tables
	$db->write_query("DROP TABLE `".TABLE_PREFIX."games`,
		`".TABLE_PREFIX."games_categories`,
		`".TABLE_PREFIX."games_favourites`,
		`".TABLE_PREFIX."games_rating`,
		`".TABLE_PREFIX."games_scores`,
		`".TABLE_PREFIX."games_sessions`,
		`".TABLE_PREFIX."games_tournaments`,
		`".TABLE_PREFIX."games_tournaments_players`;");

	// Delete user-permissions fields
	$db->write_query("ALTER TABLE `".TABLE_PREFIX."usergroups` DROP `canviewgames`,
		DROP `canplaygames`,
		DROP `canplaytournaments`,
		DROP `canaddtournaments`;");
	$cache->update_usergroups();

	// Delete users fields
	$db->write_query("ALTER TABLE `".TABLE_PREFIX."users`
		DROP `games_maxgames`,
		DROP `games_maxscores`,
		DROP `games_sortby`,
		DROP `games_order`,
		DROP `games_tournamentnotify`;");

	// Delete Game Section ACP permissions
	change_admin_permission("games", false, -1);
	change_admin_permission("games", "games", -1);
	change_admin_permission("games", "categories", -1);
	change_admin_permission("games", "version", -1);

	// Delete tasks
	$db->delete_query("tasks", "file='gamescleanup'");
	$db->delete_query("tasks", "file='tournamentstatus'");

	// Load and delete settings and settinggroups
	require_once MYBB_ROOT."games/settings.php";

	foreach($games_settinggroups as $key => $group)
	{
		$db->delete_query("settinggroups", "name='".$db->escape_string($group['name'])."'");
	}

	foreach($games_settings as $key => $setting)
	{
		$db->delete_query("settings", "name='".$db->escape_string($setting['name'])."'");
	}

	rebuild_settings();

	// Load and delete templates
	require_once MYBB_ROOT."games/templates.php";

	foreach($games_templates as $title => $template)
	{
		$db->delete_query("templates", "title='".$db->escape_string($title)."' AND sid='-1'");
	}
}

function games_activate()
{
	global $db;

	// Update MyBB templates to integrate the Game Section in MyBB
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';

	find_replace_templatesets('header', '#'.preg_quote('{$lang->toplinks_help}</a></li>').'#', "{\$lang->toplinks_help}</a></li>
						<li><a href=\"{\$mybb->settings['bburl']}/games.php\" style=\"background-image: url('{\$mybb->settings['bburl']}/games/images/games.png')\">{\$lang->gamesection}</a></li>");

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

	// Undo Game Section changes to MyBB templates
	require_once MYBB_ROOT."inc/adminfunctions_templates.php";

	find_replace_templatesets('header', '#'.preg_quote('
						<li><a href="{$mybb->settings[\'bburl\']}/games.php" style="background-image: url(\'{$mybb->settings[\'bburl\']}/games/images/games.png\')">{$lang->gamesection}</a></li>').'#', '', 0);

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

	// Loading language file
	$lang->load("games");
}

function games_index()
{
	global $mybb, $db, $lang, $plugins;

	// Headers to prevent browser-caching
	header("Expires: Sat, 1 Jan 2000 01:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");

	// Handle IBProArcade v2 scores
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

	// Handle IBProArcade v32 scores
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
	global $mybb, $lang, $charset, $db, $templates;

	if($mybb->input['action'] == "games_randomgames" || $mybb->input['action'] == "games_search")
	{
		$lang->load("games");

		// Is the Game Section closed?
		if($mybb->settings['games_closed'] == 1 && $mybb->user['usergroup'] != 4)
		{
			xmlhttp_error($lang->closed);
		}

		header("Content-type: text/plain; charset=".$charset);
	}

	if($mybb->input['action'] == "games_randomgames")
	{
		if(intval($mybb->input['cid']) != 0 && $mybb->settings['games_stats_cats'] == 1)
		{
			$where_cat = " AND cid='".intval($mybb->input['cid'])."'";
		}

		// TODO: Can't this be done with SQL?
		$query = $db->query("SELECT gid, name, title FROM ".TABLE_PREFIX."games WHERE active='1'".$where_cat." ORDER BY dateline");
		while($game = $db->fetch_array($query))
		{
			$games[] = $game;
		}

		// Load x random games
		for($i = 1; $i <= $mybb->settings['games_stats_randomgames_max']; $i++)
		{
			$id = mt_rand(0, count($games)-1);
			eval("\$randomgames_bit .= \"".$templates->get("games_stats_randomgames_bit")."\";");
		}

		echo $randomgames_bit;
	}
	elseif($mybb->input['action'] == "games_search")
	{
		$patterns[0] = '/ /';
		$replacements[0] = "%";
		$title = preg_replace($patterns, $replacements, $db->escape_string(htmlspecialchars_decode($mybb->input['title'])));

		// If the string has less than 3 characters, quit
		if(my_strlen($mybb->input['title']) < 3)
		{
			exit;
		}

		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE title LIKE '%".$title."%' AND active='1' ORDER BY title ASC");
		$games_test = $db->num_rows($query);

		if($games_test != 0)
		{
			while($games = $db->fetch_array($query))
			{
				eval("\$games_bit .= \"".$templates->get("games_tournaments_add_game_search_bit")."\";");
			}

			eval("\$results = \"".$templates->get("games_tournaments_add_game_search")."\";");
			echo $results;
		}
	}
}

function games_member_login()
{
	global $user, $db;

	// Delete Game Section session of user
	$db->write_query("DELETE FROM ".TABLE_PREFIX."games_sessions WHERE uid='".$user['uid']."'");
}

function games_member_logout()
{
	global $mybb, $db;

	// Delete Game Section session of user
	$db->write_query("DELETE FROM ".TABLE_PREFIX."games_sessions WHERE uid='".$mybb->user['uid']."'");
}

function games_online_activity($user_activity)
{
	$split_loc = explode(".php", $user_activity['location']);
	$filename = my_substr($split_loc[0], -my_strpos(strrev($split_loc[0]), "/"));

	// TODO: Add more specific activities?
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

function games_config_settings()
{
	global $lang;
	$lang->load("games_settings");
}

function games_users_merge()
{
	global $source_user, $destination_user, $db;

	// Update all uid's in the Game Section tables
	$uid_update['uid'] = $destination_user['uid'];
	$db->update_query("games_favourites", $uid_update, "uid='".$source_user['uid']."'");
	$db->update_query("games_rating", $uid_update, "uid='".$source_user['uid']."'");
	$db->update_query("games_scores", $uid_update, "uid='".$source_user['uid']."'");

	// Delete session of source user
	$db->delete_query("games_sessions", "uid='".$source_user['uid']."'");

	/* Delete all duplicated favourites
	 * As we are merging two users, there shouldn't be more then 2 of them,
	 * otherwise there was already a problem before merging. */
	$query = $db->query("SELECT gid FROM ".TABLE_PREFIX."games_favourites WHERE uid='".$destination_user['uid']."' GROUP BY gid HAVING COUNT(*)>1");
	while($favourite = $db->fetch_array($query))
	{
		$db->delete_query("games_favourites", "gid='".$favourite['gid']."' uid='".$destination_user['uid']."'", 1);
	}

	// Delete all duplicated ratings
	$query = $db->query("SELECT gid FROM ".TABLE_PREFIX."games_rating WHERE uid='".$destination_user['uid']."' GROUP BY gid HAVING COUNT(*)>1");
	while($rating = $db->fetch_array($query))
	{
		$ratings[] = $rating['gid'];
		$db->delete_query("games_rating", "gid='".$rating['gid']."' uid='".$destination_user['uid']."'", 1);
	}

	// Recalculate rating
	$query2 = $db->query("SELECT gid, SUM(rating) as rating_sum, COUNT(*) as rating_count FROM ".TABLE_PREFIX."games_rating WHERE gid='".$gid."' ORDER BY gid");
	while($ratings_count = $db->fetch_array($query2))
	{
		$rating_update['rating'] = ceil($ratings_count['rating_sum']/$ratings_count['rating_count']);
		$db->update_query("games", $rating_update, "gid='".$ratings_count['gid']."'");
	}

	// Delete all duplicated scores (DESC)
	$query = $db->query("
		SELECT s.gid, MIN(s.score) as min_score, g.champion
		FROM ".TABLE_PREFIX."games_scores s
		LEFT JOIN ".TABLE_PREFIX."games g ON (s.gid=g.gid)
		WHERE g.score_type='DESC' AND s.uid='".$destination_user['uid']."'
		GROUP BY s.gid
		HAVING COUNT(*)>1
	");
	while($scores = $db->fetch_array($query))
	{
		$db->delete_query("games_scores", "sid!='".$scores['champion']."'
			AND gid='".$scores['gid']."' AND uid='".$destination_user['uid']."'
			AND score='".$scores['min_score']."'", 1);
	}

	// Delete all duplicated scores (ASC)
	$query = $db->query("
		SELECT s.gid, MAX(s.score) as max_score, g.champion
		FROM ".TABLE_PREFIX."games_scores s
		LEFT JOIN ".TABLE_PREFIX."games g ON (s.gid=g.gid)
		WHERE g.score_type='ASC' AND s.uid='".$destination_user['uid']."'
		GROUP BY s.gid
		HAVING COUNT(*)>1
	");
	while($scores = $db->fetch_array($query))
	{
		$db->delete_query("games_scores", "sid!='".$scores['champion']."'
			AND gid='".$scores['gid']."' AND uid='".$destination_user['uid']."'
			AND score='".$scores['max_score']."'", 1);
	}
}

// TODO: Check if everything is still OK
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

// TODO: I guess this will be more elegant with MyBB 1.8?
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
	$plugins->run_hooks("admin_user_groups_edit_games", $form_container);

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
