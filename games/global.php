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

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

//Requires
require_once MYBB_ROOT."inc/functions_games.php";
require_once MYBB_ROOT."inc/class_games.php";

$games_core = new Game_Session;

//Plugin
$plugins->run_hooks("games_global_start");

//Game Section Closed
if($mybb->settings['games_closed'] == 1 && $mybb->user['usergroup'] != 4)
{
	error($lang->closed, $lang->closed_title);
}

//Global permissions
if($mybb->usergroup['canviewgames'] == 0)
{
	error_no_permission();
}
 
//Play permissions
if($mybb->input['action'] == "play")
{
	if($mybb->usergroup['canplaygames'] == 0)
	{
		error_no_permission();
	}
}
 
//Play tournaments permissions
if($mybb->input['action'] == "play" && intval($mybb->input['tid']))
{
	if($mybb->usergroup['canplaytournaments'] == 0)
	{
		error_no_permission();
	}
}
 
//Add tournaments permissions
if($mybb->input['action'] == "add" || $mybb->input['action'] == "do_add")
{
	if($mybb->usergroup['canaddtournaments'] == 0)
	{
		error_no_permission();
	}
}

//Banned users
$explode_banned = explode(",", $mybb->settings['games_banned']);

if(is_array($explode_banned))
{
	foreach($explode_banned as $key => $val)
	{
		$val = trim($val);
		
		$banned_games[$val] = true;
	}
}

//The user is banned of the Game Section, view an error
if($banned_games[$mybb->user['username']] && $mybb->user['uid'] != 0)
{
	error_no_permission();
}

//Plugin
$plugins->run_hooks("games_global_middle");

//Settings
if($mybb->user['uid'] != 0)
{
	//It's not a guest, but a member, control if he has his own settings
	if($mybb->user['games_maxgames'] == 0)
	{
		$maxgames = $mybb->settings['games_maxgames'];
	}
	else
	{
		$maxgames = $mybb->user['games_maxgames'];
	}
	
	if($mybb->user['games_maxscores'] == 0)
	{
		$maxscores = $mybb->settings['games_maxscores'];
	}
	else
	{
		$maxscores = $mybb->user['games_maxscores'];
	}

	if($mybb->user['games_sortby'] == "0" || $mybb->user['games_sortby'] == "")
	{
		$sortby = $mybb->settings['games_sortby'];
	}
	else
	{
		$sortby = $mybb->user['games_sortby'];
	}
	
	if($mybb->user['games_order'] == "0" || $mybb->user['games_order'] == "")
	{
		$order = $mybb->settings['games_order'];
	}
	else
	{
		$order = $mybb->user['games_order'];
	}
	
	if($mybb->user['games_theme'] == 0)
	{
		$theme_games_tid = $mybb->settings['games_theme'];
	}
	else
	{
		$theme_games_tid = $mybb->user['games_theme'];
	}
}
else
{
	//It's a guest, load the default settings
	$maxgames = $mybb->settings['games_maxgames'];
	$maxscores = $mybb->settings['games_maxscores'];
	$sortby = $mybb->settings['games_sortby'];
	$order = $mybb->settings['games_order'];
	$theme_games_tid = $mybb->settings['games_theme'];
}

//Tourament language files
if($mybb->settings['games_tournaments_activated'] == 1)
{
	$lang->load("tournaments");
}

//Load all templates
$gamestemplatelist .= "games, games_bit, games_bit_favourite_add, games_bit_favourite_delete, games_bit_rate, games_bit_tournament, games_categories, games_categories_bit, games_categories_bit_cur, games_champs, games_champs_bit, games_favourites, games_footer, games_menu, games_menu_lastchamps, games_menu_user, games_multipages, games_online, games_play, games_rate, games_scores, games_scores_bit, games_scores_newcomment, games_search, games_search_bar, games_search_bit, games_stats, games_stats_bestplayers, games_stats_bestplayers_bit, games_stats_champs_bit, games_stats_games_bit, games_stats_randomgames, games_stats_randomgames_bit, games_tournaments_add, games_tournaments_add_game, games_tournaments_add_game_search, games_tournaments_add_game_search_bit, games_tournaments_add_game_set, games_tournaments_add_rounds_bit, games_tournaments_add_roundtime_bit, games_tournaments_bar, games_tournaments_bar_user, games_tournaments_bar_user_add, games_tournaments_bar_user_games_bit, games_user_settings, games_user_settings_maxgames, games_user_settings_maxscores, games_user_settings_themes, games_user_stats, games_user_stats_bit";

$templates->cache($db->escape_string($gamestemplatelist));

//Breadcrump of the Game Section
add_breadcrumb($lang->gamesection, "games.php");

//Some templates
if($mybb->user['uid'] != 0)
{
	eval("\$games_menu_user = \"".$templates->get("games_menu_user", "1", "0")."\";");
}
if($mybb->settings['games_stats_lastchamps_advanced'] == 1)
{
	eval("\$games_menu_lastchamps = \"".$templates->get("games_menu_lastchamps", "1", "0")."\";");
}

eval("\$games_menu = \"".$templates->get("games_menu")."\";");

eval("\$games_footer = \"".$templates->get("games_footer")."\";");

//Plugin
$plugins->run_hooks("games_global_end");
?>
