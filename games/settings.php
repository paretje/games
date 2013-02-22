<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2013 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 22/02/2013 by Paretje
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

//Setting groups
$games_settinggroups[] = array(
	'name'		=> "games_general",
	'displayorder'	=> "31",
	'gid'		=> "1"
);

$games_settinggroups[] = array(
	'name'		=> "games_display",
	'displayorder'	=> "32",
	'gid'		=> "2"
);

$games_settinggroups[] = array(
	'name'		=> "games_statistics",
	'displayorder'	=> "33",
	'gid'		=> "3"
);

$games_settinggroups[] = array(
	'name'		=> "games_tournaments",
	'displayorder'	=> "34",
	'gid'		=> "4"
);

//Settings
$games_settings[] = array(
	'name'		=> "closed",
	'optionscode'	=> "yesno",
	'value'		=> "0",
	'displayorder'	=> "1",
	'gid'		=> "1"
);

$games_settings[] = array(
	'name'		=> "banned",
	'optionscode'	=> "textarea",
	'value'		=> "",
	'displayorder'	=> "2",
	'gid'		=> "1"
);

$games_settings[] = array(
	'name'		=> "maxgames",
	'optionscode'	=> "text",
	'value'		=> "20",
	'displayorder'	=> "1",
	'gid'		=> "2"
);

$games_settings[] = array(
	'name'		=> "set_maxgames",
	'optionscode'	=> "text",
	'value'		=> "10,20,25,50,100",
	'displayorder'	=> "2",
	'gid'		=> "2"
);

$games_settings[] = array(
	'name'		=> "sortby",
	'optionscode'	=> "select\ntitle=Name\ndateline=Date Added\nplayed=Times Played\nlastplayed=Last Played\nrating=Rating",
	'value'		=> "title",
	'displayorder'	=> "3",
	'gid'		=> "2"
);

$games_settings[] = array(
	'name'		=> "order",
	'optionscode'	=> "select\nASC=Ascending\nDESC=Descending",
	'value'		=> "ASC",
	'displayorder'	=> "4",
	'gid'		=> "2"
);

$games_settings[] = array(
	'name'		=> "maxscores",
	'optionscode'	=> "text",
	'value'		=> "10",
	'displayorder'	=> "5",
	'gid'		=> "2"
);

$games_settings[] = array(
	'name'		=> "set_maxscores",
	'optionscode'	=> "text",
	'value'		=> "5,10,20,25",
	'displayorder'	=> "6",
	'gid'		=> "2"
);

$games_settings[] = array(
	'name'		=> "new_game",
	'optionscode'	=> "text",
	'value'		=> "7",
	'displayorder'	=> "7",
	'gid'		=> "2"
);

$games_settings[] = array(
	'name'		=> "theme",
	'optionscode'	=> "theme",
	'value'		=> "1",
	'displayorder'	=> "8",
	'gid'		=> "2"
);

$games_settings[] = array(
	'name'		=> "online",
	'optionscode'	=> "select\nnever=Never\nonly=Only On Games Pages\nevery=Every Page",
	'value'		=> "only",
	'displayorder'	=> "9",
	'gid'		=> "2"
);

$games_settings[] = array(
	'name'		=> "online_image",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "10",
	'gid'		=> "2"
);

$games_settings[] = array(
	'name'		=> "stats_global",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "1",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "stats_cats",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "2",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "stats_games_max",
	'optionscode'	=> "text",
	'value'		=> "15",
	'displayorder'	=> "3",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "stats_lastchamps_max",
	'optionscode'	=> "text",
	'value'		=> "5",
	'displayorder'	=> "4",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "stats_lastscores_max",
	'optionscode'	=> "text",
	'value'		=> "1",
	'displayorder'	=> "5",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "stats_bestplayers",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "6",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "stats_randomgames",
	'optionscode'	=> "yesno",
	'value'		=> "0",
	'displayorder'	=> "7",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "stats_randomgames_max",
	'optionscode'	=> "text",
	'value'		=> "1",
	'displayorder'	=> "8",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "stats_lastchamps_advanced",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "9",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "stats_lastchamps_advanced_max",
	'optionscode'	=> "text",
	'value'		=> "20",
	'displayorder'	=> "10",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "stats_userstats_multipages",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "11",
	'gid'		=> "3"
);

$games_settings[] = array(
	'name'		=> "tournaments_activated",
	'optionscode'	=> "yesno",
	'value'		=> "0",
	'displayorder'	=> "1",
	'gid'		=> "4"
);

$games_settings[] = array(
	'name'		=> "tournaments_set_rounds",
	'optionscode'	=> "text",
	'value'		=> "1,2,3",
	'displayorder'	=> "2",
	'gid'		=> "4"
);

$games_settings[] = array(
	'name'		=> "tournaments_set_roundtime",
	'optionscode'	=> "text",
	'value'		=> "1,2,3,4,5",
	'displayorder'	=> "3",
	'gid'		=> "4"
);
?>
