<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2008 The Game Section Development Group
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

function games_meta()
{
	global $page, $lang, $plugins;

	$sub_menu = array();
	$sub_menu['10'] = array("id" => "games", "title" => $lang->nav_games, "link" => "index.php?module=games/games");
	$sub_menu['15'] = array("id" => "gamedata", "title" => $lang->nav_gamedata, "link" => "index.php?module=games/gamedata");
	$sub_menu['20'] = array("id" => "categories", "title" => $lang->nav_categories, "link" => "index.php?module=games/categories");
	$sub_menu['30'] = array("id" => "settings", "title" => $lang->nav_settings, "link" => "index.php?module=games/settings");
	$sub_menu['40'] = array("id" => "themes", "title" => $lang->nav_themes, "link" => "index.php?module=games/themes");
	$sub_menu['50'] = array("id" => "templates", "title" => $lang->nav_templates, "link" => "index.php?module=games/templates");
	$sub_menu['60'] = array("id" => "tools", "title" => $lang->nav_tools, "link" => "index.php?module=games/tools");
	$sub_menu['70'] = array("id" => "version", "title" => $lang->nav_version, "link" => "index.php?module=games/version");
	
	$plugins->run_hooks("admin_games_menu", $sub_menu);
	
	$page->add_menu_item($lang->gamesection, "games", "index.php?module=games", 60, $sub_menu);
	
	return true;
}

function games_action_handler($action)
{
	global $page, $lang, $plugins;
	
	$page->active_module = "games";
	
	$actions = array(
		'games' => array('active' => 'games', 'file' => 'games.php'),
		'gamedata' => array('active' => 'gamedata', 'file' => 'gamedata.php'),
		'categories' => array('active' => 'categories', 'file' => 'categories.php'),
		'settings' => array('active' => 'settings', 'file' => 'settings.php'),
		'themes' => array('active' => 'themes', 'file' => 'themes.php'),
		'templates' => array('active' => 'templates', 'file' => 'templates.php'),
		'tools' => array('active' => 'tools', 'file' => 'tools.php'),
		'version' => array('active' => 'version', 'file' => 'version.php')
	);
	
	$plugins->run_hooks("admin_games_action_handler", $actions);

	if(!isset($actions[$action]))
	{
		$page->active_action = "games";
		return "games.php";
	}
	else
	{
		$page->active_action = $actions[$action]['active'];
		return $actions[$action]['file'];
	}
}

function games_admin_permissions()
{
	global $lang, $plugins;
	
	$admin_permissions = array(
		"games"			=> $lang->can_manage_games,
		"gamedata"		=> $lang->can_manage_gamedata,
		"categories"		=> $lang->can_manage_categories,
		"settings"		=> $lang->can_manage_settings,
		"themes"		=> $lang->can_manage_themes,
		"templates"		=> $lang->can_manage_templates,
		"tools"			=> $lang->can_manage_tools,
		"version"		=> $lang->can_manage_version
	);
	
	$plugins->run_hooks("admin_games_permissions", $admin_permissions);
	
	return array("name" => $lang->gamesection, "permissions" => $admin_permissions, "disporder" => 60);
}
?>
