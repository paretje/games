<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2010 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 16/02/2010 by Paretje
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
$settings_groups[] = array(
	'name'		=> "general",
	'title'		=> "General Options",
	'description'	=> "This group contains various settings.",
	'displayorder'	=> "1"
);

$settings_groups[] = array(
	'name'		=> "display",
	'title'		=> "Display Options",
	'description'	=> "This group contains options to configure the look of the Game Section.",
	'displayorder'	=> "2"
);

$settings_groups[] = array(
	'name'		=> "statistics",
	'title'		=> "Statistics Options",
	'description'	=> "This group contains the options used by the statistics of the Game Section.",
	'displayorder'	=> "3"
);

$settings_groups[] = array(
	'name'		=> "tournaments",
	'title'		=> "Tournaments System",
	'description'	=> "This group contains the settings to configure the tournaments system of the Game Section.",
	'displayorder'	=> "4"
);

//Settings
$new_settings[] = array(
	'name'		=> "closed",
	'title'		=> "Game Section Closed",
	'description'	=> "Will you close the Game Section?<br />\n<br />\n<strong>Administrators will have still access to the Game Section</strong>",
	'optionscode'	=> "yesno",
	'value'		=> "0",
	'displayorder'	=> "1",
	'gid'		=> "1"
);

$new_settings[] = array(
	'name'		=> "banned",
	'title'		=> "Banned Usernames",
	'description'	=> "Here you can add the usernames of users that you will ban from the Game Section.<br />\n<br />\nExamle: User 1,User2,user3",
	'optionscode'	=> "textarea",
	'value'		=> "",
	'displayorder'	=> "2",
	'gid'		=> "1"
);

$new_settings[] = array(
	'name'		=> "maxgames",
	'title'		=> "Default Games Per Page",
	'description'	=> "Default games shown per page.",
	'optionscode'	=> "text",
	'value'		=> "20",
	'displayorder'	=> "1",
	'gid'		=> "2"
);

$new_settings[] = array(
	'name'		=> "set_maxgames",
	'title'		=> "User Selectable Games Per Page",
	'description'	=> "Enter the options users should be able to select, as their maximum of games per page, separated by commas.",
	'optionscode'	=> "text",
	'value'		=> "10,20,25,50,100",
	'displayorder'	=> "2",
	'gid'		=> "2"
);

$new_settings[] = array(
	'name'		=> "sortby",
	'title'		=> "Default, Sort Games By",
	'description'	=> "Select by what the games must to be sorted.",
	'optionscode'	=> "select\ntitle=Name\ndateline=Date Added\nplayed=Times Played\nlastplayed=Last Played\nrating=Rating",
	'value'		=> "title",
	'displayorder'	=> "3",
	'gid'		=> "2"
);

$new_settings[] = array(
	'name'		=> "order",
	'title'		=> "Default Order",
	'description'	=> "Select the order to sort the games.",
	'optionscode'	=> "select\nASC=Ascending\nDESC=Descending",
	'value'		=> "ASC",
	'displayorder'	=> "4",
	'gid'		=> "2"
);

$new_settings[] = array(
	'name'		=> "maxscores",
	'title'		=> "Default Scores Per Page",
	'description'	=> "Default scores shown per page.",
	'optionscode'	=> "text",
	'value'		=> "10",
	'displayorder'	=> "5",
	'gid'		=> "2"
);

$new_settings[] = array(
	'name'		=> "set_maxscores",
	'title'		=> "User Selectable Scores Per Page",
	'description'	=> "Enter the options users should be able to select, as their maximum of scores per page, separated by commas.",
	'optionscode'	=> "text",
	'value'		=> "5,10,20,25",
	'displayorder'	=> "6",
	'gid'		=> "2"
);

$new_settings[] = array(
	'name'		=> "new_game",
	'title'		=> "Days For New Game",
	'description'	=> "The number of days a game will be marked as new.",
	'optionscode'	=> "text",
	'value'		=> "7",
	'displayorder'	=> "7",
	'gid'		=> "2"
);

$new_settings[] = array(
	'name'		=> "theme",
	'title'		=> "Default Theme",
	'description'	=> "Select the default theme of the Game Section.",
	'optionscode'	=> "theme",
	'value'		=> "1",
	'displayorder'	=> "8",
	'gid'		=> "2"
);

$new_settings[] = array(
	'name'		=> "online",
	'title'		=> "View Who\'s Online On",
	'description'	=> "Select where you want to view the \"Who\'s Online\"-box.",
	'optionscode'	=> "select\nnever=Never\nonly=Only On Games Pages\nevery=Every Page",
	'value'		=> "only",
	'displayorder'	=> "9",
	'gid'		=> "2"
);

$new_settings[] = array(
	'name'		=> "online_image",
	'title'		=> "View Image In Who\' Online",
	'description'	=> "Will you show an image in function of the place where the user is on the \"Who\' Online\"-box?",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "10",
	'gid'		=> "2"
);

$new_settings[] = array(
	'name'		=> "stats_global",
	'title'		=> "View Statistics On Global",
	'description'	=> "View Game Section statistics on the global page?",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "1",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "stats_cats",
	'title'		=> "View Statistics Of Categories",
	'description'	=> "View the statistics of the category where you are?",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "2",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "stats_games_max",
	'title'		=> "Number of Games",
	'description'	=> "The number of last games and most played games that must be shown in the statistics.",
	'optionscode'	=> "text",
	'value'		=> "15",
	'displayorder'	=> "3",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "stats_lastchamps_max",
	'title'		=> "Number of Last Champions",
	'description'	=> "The number of last champions that must be shown in the statistics.",
	'optionscode'	=> "text",
	'value'		=> "5",
	'displayorder'	=> "4",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "stats_lastscores_max",
	'title'		=> "Number of Last Scores",
	'description'	=> "The number of last scores that must be shown in the statistics.",
	'optionscode'	=> "text",
	'value'		=> "1",
	'displayorder'	=> "5",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "stats_bestplayers",
	'title'		=> "Show Best Players",
	'description'	=> "Show the 3 best players of your board.",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "6",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "stats_randomgames",
	'title'		=> "Show Random Games",
	'description'	=> "Show random games in the statistics.",
	'optionscode'	=> "yesno",
	'value'		=> "0",
	'displayorder'	=> "7",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "stats_randomgames_max",
	'title'		=> "Number of Random Games",
	'description'	=> "The number of random games that must be shown in the statistics.",
	'optionscode'	=> "text",
	'value'		=> "1",
	'displayorder'	=> "8",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "stats_lastchamps_advanced",
	'title'		=> "Advanced Last Champions",
	'description'	=> "Do you want to hold a log of the last champions on your Game Section?<br />\n<br />\n<strong>Note:</strong> When this option was disabled, and you activate it now, then you have to run \"Repair Advanced Last Champions\".",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "9",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "stats_lastchamps_advanced_max",
	'title'		=> "Number of Logged Last Champions",
	'description'	=> "The number of champions that must be logged and shown.<br />\n<br />\n<strong>Note:</strong> When you change this option, you have to run \"Repair Advanced Last Champions\".",
	'optionscode'	=> "text",
	'value'		=> "20",
	'displayorder'	=> "10",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "stats_userstats_multipages",
	'title'		=> "Multipages in User Statistics",
	'description'	=> "Do you want to activate the multipages for the User Statistics.",
	'optionscode'	=> "yesno",
	'value'		=> "1",
	'displayorder'	=> "11",
	'gid'		=> "3"
);

$new_settings[] = array(
	'name'		=> "tournaments_activated",
	'title'		=> "Tournaments System is Activated",
	'description'	=> "Do you want to activate the tournaments system of the Game Section?",
	'optionscode'	=> "yesno",
	'value'		=> "0",
	'displayorder'	=> "1",
	'gid'		=> "4"
);

$new_settings[] = array(
	'name'		=> "tournaments_set_rounds",
	'title'		=> "Selectable Rounds of a Tournament",
	'description'	=> "Enter the options users should be able to select as the number of rounds of a tournament.",
	'optionscode'	=> "text",
	'value'		=> "1,2,3",
	'displayorder'	=> "2",
	'gid'		=> "4"
);

$new_settings[] = array(
	'name'		=> "tournaments_set_roundtime",
	'title'		=> "Selectable Number of Round Days",
	'description'	=> "Enter the options users should be able to select as the maximum number of days for one round of a tournament.",
	'optionscode'	=> "text",
	'value'		=> "1,2,3,4,5",
	'displayorder'	=> "3",
	'gid'		=> "4"
);
?>
