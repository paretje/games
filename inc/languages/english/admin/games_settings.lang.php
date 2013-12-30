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

$l['nav_manage_settings'] = "Manage Settings";

$l['nav_manage_settings_desc'] = "This section allows you to manage all the settings relating to the Game Section installation on your board.";

$l['setting_count'] = "1 Setting";
$l['settings_count'] = "{1} Settings";
$l['change'] = "Change";

$l['edited_settings'] = "The settings are successfully edited.";

$l['setting_group_games_general'] = "General Options";
$l['setting_group_games_display'] = "Display Options";
$l['setting_group_games_statistics'] = "Statistics Options";
$l['setting_group_games_tournaments'] = "Tournaments System";

$l['setting_group_games_general_desc'] = "This group contains various settings.";
$l['setting_group_games_display_desc'] = "This group contains options to configure the look of the Game Section.";
$l['setting_group_games_statistics_desc'] = "This group contains the options used by the statistics of the Game Section.";
$l['setting_group_games_tournaments_desc'] = "This group contains the settings to configure the tournaments system of the Game Section.";

$l['setting_games_closed'] = "Game Section Closed";
$l['setting_games_banned'] = "Banned Usernames";

$l['setting_games_maxgames'] = "Default Games Per Page";
$l['setting_games_set_maxgames'] = "User Selectable Games Per Page";
$l['setting_games_sortby'] = "Default, Sort Games By";
$l['setting_games_order'] = "Default Order";
$l['setting_games_maxscores'] = "Default Scores Per Page";
$l['setting_games_set_maxscores'] = "User Selectable Scores Per Page";
$l['setting_games_new_game'] = "Days For New Game";
$l['setting_games_catsperline'] = "Categories Per Line";
$l['setting_games_online'] = "View Who's Online On";

$l['setting_games_stats_global'] = "View Statistics On Global";
$l['setting_games_stats_cats'] = "View Statistics Of Categories";
$l['setting_games_stats_games_max'] = "Number of Games";
$l['setting_games_stats_lastchamps_max'] = "Number of Last Champions";
$l['setting_games_stats_lastscores_max'] = "Number of Last Scores";
$l['setting_games_stats_bestplayers'] = "Show Best Players";
$l['setting_games_stats_randomgames'] = "Show Random Games";
$l['setting_games_stats_randomgames_max'] = "Number of Random Games";
$l['setting_games_stats_lastchamps_advanced'] = "Advanced Last Champions";
$l['setting_games_stats_lastchamps_advanced_max'] = "Number of Logged Last Champions";
$l['setting_games_stats_userstats_multipages'] = "Multipages in User Statistics";

$l['setting_games_tournaments_activated'] = "Tournaments System is Activated";
$l['setting_games_tournaments_set_rounds'] = "Selectable Rounds of a Tournament";
$l['setting_games_tournaments_set_roundtime'] = "Selectable Number of Round Days";

$l['setting_games_closed_desc'] = "Here you can set if you want to close the Game Section.<br />\n<br />\n<strong>Administrators will have still access to the Game Section</strong>";
$l['setting_games_banned_desc'] = "Here you can add the usernames of users that you will ban from the Game Section.<br />\n<br />\nExamle: User 1,User2,user3";

$l['setting_games_maxgames_desc'] = "Default games shown per page.";
$l['setting_games_set_maxgames_desc'] = "Enter the options users should be able to select, as their maximum of games per page, separated by commas.";
$l['setting_games_sortby_desc'] = "Select by what the games must to be sorted.";
$l['setting_games_order_desc'] = "Select the order to sort the games.";
$l['setting_games_maxscores_desc'] = "Default scores shown per page.";
$l['setting_games_set_maxscores_desc'] = "Enter the options users should be able to select, as their maximum of scores per page, separated by commas.";
$l['setting_games_new_game_desc'] = "The number of days a game will be marked as new.";
$l['setting_games_catsperline_desc'] = "Select the number of categories per line you want in the categories-box.";
$l['setting_games_online_desc'] = "Select where you want to view the \"Who's Online\"-box.";

$l['setting_games_stats_global_desc'] = "View Game Section statistics on the global page?";
$l['setting_games_stats_cats_desc'] = "View the statistics of the category where you are?";
$l['setting_games_stats_games_max_desc'] = "The number of last games and most played games that must be shown in the statistics.";
$l['setting_games_stats_lastchamps_max_desc'] = "The number of last champions that must be shown in the statistics.";
$l['setting_games_stats_lastscores_max_desc'] = "The number of last scores that must be shown in the statistics.";
$l['setting_games_stats_bestplayers_desc'] = "Show the 3 best players of your board.";
$l['setting_games_stats_randomgames_desc'] = "Show random games in the statistics.";
$l['setting_games_stats_randomgames_max_desc'] = "The number of random games that must be shown in the statistics.";
$l['setting_games_stats_lastchamps_advanced_desc'] = "Do you want to hold a log of the last champions on your Game Section?<br />\n<br />\n<strong>Note:</strong> When this option was disabled, and you activate it now, then you have to run \"Repair Advanced Last Champions\".";
$l['setting_games_stats_lastchamps_advanced_max_desc'] = "The number of champions that must be logged and shown.<br />\n<br />\n<strong>Note:</strong> When you change this option, you have to run \"Repair Advanced Last Champions\".";
$l['setting_games_stats_userstats_multipages_desc'] = "Do you want to activate the multipages for the User Statistics.";

$l['setting_games_tournaments_activated_desc'] = "Do you want to activate the tournaments system of the Game Section?";
$l['setting_games_tournaments_set_rounds_desc'] = "Enter the options users should be able to select as the number of rounds of a tournament.";
$l['setting_games_tournaments_set_roundtime_desc'] = "Enter the options users should be able to select as the maximum number of days for one round of a tournament.";

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
