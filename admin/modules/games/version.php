<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2010 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 01/01/2010 by Paretje
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
require_once MYBB_ROOT."inc/class_xml.php";
require_once MYBB_ROOT."inc/class_parser.php";

//Plugin
$plugins->run_hooks("admin_games_version_start");

//Navigation
$page->add_breadcrumb_item($lang->gamesection, "index.php?module=games");
$page->add_breadcrumb_item($lang->nav_version, "index.php?module=games/version");

//Declare the sub-tabs
if($mybb->input['action'] == "" || $mybb->input['action'] == "credits")
{
	$sub_tabs = array();
	$sub_tabs['version'] = array(
		'title' => $lang->nav_version,
		'link' => "index.php?module=games/version",
		'description' => $lang->nav_version_desc
	);
	$sub_tabs['credits'] = array(
		'title' => $lang->nav_credits,
		'link' => "index.php?module=games/version&amp;action=credits",
		'description' => $lang->nav_credits_desc
	);
}

if($mybb->input['action'] == "credits")
{
	//Navigation and header
	$page->output_header($lang->nav_credits);
	
	//Show the sub-tabs
	$page->output_nav_tabs($sub_tabs, 'credits');
	
	//Plugin
	$plugins->run_hooks("admin_games_version_credits_start");
	
	//Start table
	$table = new Table;
	$table->construct_header($lang->credits_developers, array("class" => "align_center", "width" => "33%"));
	$table->construct_header($lang->credits_translators, array("class" => "align_center", "width" => "33%"));
	$table->construct_header($lang->credits_supportteam, array("class" => "align_center", "width" => "33%"));
	
	$table->construct_cell("<a href=\"http://www.Online-Urbanus.be\">Paretje</a>");
	$table->construct_cell("<a href=\"http://camufla.cl.nu\">camufla</a>");
	$table->construct_cell("<a href=\"http://camufla.cl.nu\">camufla</a>");
	$table->construct_row();
	
	$table->construct_cell("");
	$table->construct_cell("<a href=\"http://community.gamesection.org/member.php?action=profile&uid=704\">dr34m</a>");
	$table->construct_cell("<a href=\"http://www.chat2b.be\">destroyer</a>");
	$table->construct_row();
	
	$table->construct_cell("");
	$table->construct_cell("<a href=\"http://www.mixland.dk/new\">Fedtmule</a>");
	$table->construct_cell("<a href=\"http://community.gamesection.org/member.php?action=profile&uid=704\">dr34m</a>");
	$table->construct_row();
	
	$table->construct_cell("");
	$table->construct_cell("<a href=\"http://community.gamesection.org/member.php?action=profile&uid=101\">gastel</a>");
	$table->construct_cell("<a href=\"http://www.mixland.dk/new\">Fedtmule</a>");
	$table->construct_row();
	
	$table->construct_cell("");
	$table->construct_cell("<a href=\"http://www.mybb.fr\">Le Poulpe</a>");
	$table->construct_cell("<a href=\"http://community.gamesection.org/member.php?action=profile&uid=101\">gastel</a>");
	$table->construct_row();
	
	$table->construct_cell("");
	$table->construct_cell("<a href=\"http://www.Online-Urbanus.be\">Paretje</a>");
	$table->construct_cell("<a href=\"http://www.mybb.fr\">Le Poulpe</a>");
	$table->construct_row();
	
	$table->construct_cell("");
	$table->construct_cell("<a href=\"http://community.gamesection.org/member.php?action=profile&uid=129\">Susanne</a>");
	$table->construct_cell("<a href=\"http://www.Online-Urbanus.be\">Paretje</a>");
	$table->construct_row();
	
	$table->construct_cell("");
	$table->construct_cell("<a href=\"http://ots-s.pl\">Victor</a>");
	$table->construct_cell("<a href=\"http://community.gamesection.org/member.php?action=profile&uid=129\">Susanne</a>");
	$table->construct_row();
	
	$table->construct_cell("");
	$table->construct_cell("");
	$table->construct_cell("<a href=\"http://ots-s.pl\">Victor</a>");
	$table->construct_row();
	
	//Plugin
	$plugins->run_hooks("admin_games_version_credits_end");
	
	//End of table and AdminCP footer
	$table->output($lang->nav_credits);
	$page->output_footer();
}
else
{
	//Load and read the latest version information
	$version_controlfile = fetch_remote_file("http://versions.gamesection.org/version.xml");
	$parser = new XMLParser($version_controlfile);
	$current_version = $parser->get_tree();
	
	//Load your version
	require_once MYBB_ROOT."inc/plugins/games.php";
	$info = games_info();
	
	//Control version
	if($current_version['version_check']['version']['value'] != $info['version'])
	{
		$latest_version = "<span style=\"color: red\">".$current_version['version_check']['version']['value']."</span>";
	}
	else
	{
		$latest_version = "<span style=\"color: green\">".$current_version['version_check']['version']['value']."</span>";
	}
	
	//Navigation and header
	$page->output_header($lang->nav_version);
	
	//Show the sub-tabs
	$page->output_nav_tabs($sub_tabs, 'version');
	
	//Plugin
	$plugins->run_hooks("admin_games_version_default_start");
	
	//Start table
	$table = new Table;
	
	$table->construct_cell($lang->version_your, array("width" => "40%"));
	$table->construct_cell($info['version']);
	$table->construct_row();
	
	$table->construct_cell($lang->version_latest, array("width" => "40%"));
	$table->construct_cell($latest_version);
	$table->construct_row();
	
	//Version information
	$bbparser = new postParser;
	$parser_options = array(
		"allow_html" => 1,
		"allow_mycode" => 1,
		"allow_smilies" => 1,
		"allow_imgcode" => 0,
		"filter_badwords" => 0
	);
	
	$version_information = $bbparser->parse_message($current_version['version_check']['information']['value'], $parser_options);
	
	$table->construct_cell("<strong>".$lang->version_information."</strong>", array("width" => "40%", "style" => "vertical-align: top;"));
	$table->construct_cell($version_information);
	$table->construct_row();
	
	$table->construct_cell("");
	$table->construct_cell("<a href=\"".$current_version['version_check']['download']['value']."\">".$lang->version_download."</a>");
	$table->construct_row();
	
	//Plugin
	$plugins->run_hooks("admin_games_version_default_end");
	
	//End of table and AdminCP footer
	$table->output($lang->nav_version);
	$page->output_footer();
}
?>
