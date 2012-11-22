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

$l['nav_manage_settings'] = "Manage Settings";

$l['nav_manage_settings_desc'] = "This section allows you to manage all the settings relating to the Game Section installation on your board.";

$l['setting_count'] = "1 Setting";
$l['settings_count'] = "{1} Settings";
$l['change'] = "Change";

$l['edited_settings'] = "The settings are successfully edited.";

$l['settings_group_title_general'] = "General Options";
$l['settings_group_title_display'] = "Display Options";
$l['settings_group_title_statistics'] = "Statistics Options";
$l['settings_group_title_tournaments'] = "Tournaments System";

$l['settings_group_desc_general'] = "This group contains various settings.";
$l['settings_group_desc_display'] = "This group contains options to configure the look of the Game Section.";
$l['settings_group_desc_statistics'] = "This group contains the options used by the statistics of the Game Section.";
$l['settings_group_desc_tournaments'] = "This group contains the settings to configure the tournaments system of the Game Section.";

$l['settings_title_closed'] = "Game Section Closed";
$l['settings_title_banned'] = "Banned Usernames";

$l['settings_title_maxgames'] = "Default Games Per Page";
$l['settings_title_set_maxgames'] = "User Selectable Games Per Page";
$l['settings_title_sortby'] = "Default, Sort Games By";
$l['settings_title_order'] = "Default Order";
$l['settings_title_maxscores'] = "Default Scores Per Page";
$l['settings_title_set_maxscores'] = "User Selectable Scores Per Page";
$l['settings_title_new_game'] = "Days For New Game";
$l['settings_title_theme'] = "Default Theme";
$l['settings_title_online'] = "View Who's Online On";

$l['settings_title_stats_global'] = "View Statistics On Global";
$l['settings_title_stats_cats'] = "View Statistics Of Categories";
$l['settings_title_stats_games_max'] = "Number of Games";
$l['settings_title_stats_lastchamps_max'] = "Number of Last Champions";
$l['settings_title_stats_lastscores_max'] = "Number of Last Scores";
$l['settings_title_stats_bestplayers'] = "Show Best Players";
$l['settings_title_stats_randomgames'] = "Show Random Games";
$l['settings_title_stats_randomgames_max'] = "Number of Random Games";
$l['settings_title_stats_lastchamps_advanced'] = "Advanced Last Champions";
$l['settings_title_stats_lastchamps_advanced_max'] = "Number of Logged Last Champions";
$l['settings_title_stats_userstats_multipages'] = "Multipages in User Statistics";

$l['settings_title_tournaments_activated'] = "Tournaments System is Activated";
$l['settings_title_tournaments_set_rounds'] = "Selectable Rounds of a Tournament";
$l['settings_title_tournaments_set_roundtime'] = "Selectable Number of Round Days";

$l['settings_desc_closed'] = "Here you can set if you want to close the Game Section.<br />\n<br />\n<strong>Administrators will have still access to the Game Section</strong>";
$l['settings_desc_banned'] = "Here you can add the usernames of users that you will ban from the Game Section.<br />\n<br />\nExamle: User 1,User2,user3";

$l['settings_desc_maxgames'] = "Default games shown per page.";
$l['settings_desc_set_maxgames'] = "Enter the options users should be able to select, as their maximum of games per page, separated by commas.";
$l['settings_desc_sortby'] = "Select by what the games must to be sorted.";
$l['settings_desc_order'] = "Select the order to sort the games.";
$l['settings_desc_maxscores'] = "Default scores shown per page.";
$l['settings_desc_set_maxscores'] = "Enter the options users should be able to select, as their maximum of scores per page, separated by commas.";
$l['settings_desc_new_game'] = "The number of days a game will be marked as new.";
$l['settings_desc_theme'] = "Select the default theme of the Game Section.";
$l['settings_desc_online'] = "Select where you want to view the \"Who's Online\"-box.";

$l['settings_desc_stats_global'] = "View Game Section statistics on the global page?";
$l['settings_desc_stats_cats'] = "View the statistics of the category where you are?";
$l['settings_desc_stats_games_max'] = "The number of last games and most played games that must be shown in the statistics.";
$l['settings_desc_stats_lastchamps_max'] = "The number of last champions that must be shown in the statistics.";
$l['settings_desc_stats_lastscores_max'] = "The number of last scores that must be shown in the statistics.";
$l['settings_desc_stats_bestplayers'] = "Show the 3 best players of your board.";
$l['settings_desc_stats_randomgames'] = "Show random games in the statistics.";
$l['settings_desc_stats_randomgames_max'] = "The number of random games that must be shown in the statistics.";
$l['settings_desc_stats_lastchamps_advanced'] = "Do you want to hold a log of the last champions on your Game Section?<br />\n<br />\n<strong>Note:</strong> When this option was disabled, and you activate it now, then you have to run \"Repair Advanced Last Champions\".";
$l['settings_desc_stats_lastchamps_advanced_max'] = "The number of champions that must be logged and shown.<br />\n<br />\n<strong>Note:</strong> When you change this option, you have to run \"Repair Advanced Last Champions\".";
$l['settings_desc_stats_userstats_multipages'] = "Do you want to activate the multipages for the User Statistics.";

$l['settings_desc_tournaments_activated'] = "Do you want to activate the tournaments system of the Game Section?";
$l['settings_desc_tournaments_set_rounds'] = "Enter the options users should be able to select as the number of rounds of a tournament.";
$l['settings_desc_tournaments_set_roundtime'] = "Enter the options users should be able to select as the maximum number of days for one round of a tournament.";

$l['settings_sortby_title'] = "Name";
$l['settings_sortby_dateline'] = "Date Added";
$l['settings_sortby_played'] = "Times Played";
$l['settings_sortby_lastplayed'] = "Last Played";
$l['settings_sortby_rating'] = "Rating";

$l['settings_order_ASC'] = "Ascending";
$l['settings_order_DESC'] = "Descending";

$l['settings_online_never'] = "Never";
$l['settings_online_only'] = "Only On Games Pages";
$l['settings_online_every'] = "Every Page";
?>
