<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2013 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 31/12/2013 by Paretje
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

require_once MYBB_ROOT."inc/class_xml.php";
require_once MYBB_ROOT."inc/class_parser.php";

$bbparser = new postParser;
$parser_options = array(
	"allow_html" => 1,
	"allow_mycode" => 1,
	"allow_smilies" => 1,
	"allow_imgcode" => 0,
	"filter_badwords" => 0
);

$plugins->run_hooks("admin_games_version_start");

$page->add_breadcrumb_item($lang->gamesection, "index.php?module=games");
$page->add_breadcrumb_item($lang->nav_version, "index.php?module=games/version");

if($mybb->input['action'] == "")
{
	$sub_tabs = array();
	$sub_tabs['version'] = array(
		'title' => $lang->nav_version,
		'link' => "index.php?module=games/version",
		'description' => $lang->nav_version_desc
	);
}

// Get most recently released version
$version_controlfile = fetch_remote_file("http://versions.gamesection.org/dev_version.xml");
$parser = new XMLParser($version_controlfile);
$current_version = $parser->get_tree();

// Get currently used version
require_once MYBB_ROOT."inc/plugins/games.php";
$info = games_info();

if($current_version['version_check']['version']['value'] != $info['version'])
{
	$latest_version = "<span style=\"color: red\">".$current_version['version_check']['version']['value']."</span>";
}
else
{
	$latest_version = "<span style=\"color: green\">".$current_version['version_check']['version']['value']."</span>";
}

$version_information = $bbparser->parse_message($current_version['version_check']['information']['value'], $parser_options);

$page->output_header($lang->nav_version);
$page->output_nav_tabs($sub_tabs, 'version');

$table = new Table;
$table->construct_cell($lang->version_your, array("width" => "40%"));
$table->construct_cell($info['version']);
$table->construct_row();

$table->construct_cell($lang->version_latest, array("width" => "40%"));
$table->construct_cell($latest_version);
$table->construct_row();

$table->construct_cell("<strong>".$lang->version_information."</strong>", array("width" => "40%", "style" => "vertical-align: top;"));
$table->construct_cell($version_information);
$table->construct_row();

$table->construct_cell("");
$table->construct_cell("<a href=\"".$current_version['version_check']['download']['value']."\">".$lang->version_download."</a>");
$table->construct_row();

$plugins->run_hooks("admin_games_version_end");

$table->output($lang->nav_version);
$page->output_footer();
?>
