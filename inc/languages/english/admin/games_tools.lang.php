<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2009 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 17/04/2009 by Paretje
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

$l['nav_tools_desc'] = "Here you can repair the Game Sectin with tools when there gone something wrong.";

$l['repair_scores'] = "Repair Scores";
$l['repair_champions'] = "Repair Champions";
$l['repair_last_champions'] = "Repair Last Champions";
$l['repair_rating'] = "Repair and Recount Ratings";
$l['repair_favourites'] = "Repair Favourites";
$l['repair_tournaments_stats'] = "Repair Tournament Statistics";
$l['cleanup_gamedata'] = "Clean-up Gamedata";
$l['repair_permissions'] = "Repair Permissions";

$l['repair_scores_desc'] = "This will delete the double scores on your board.";
$l['repair_champions_desc'] = "This will update the champions on your board, based on the scores.";
$l['repair_last_champions_desc'] = "This will repair the cache of the last champions.";
$l['repair_rating_desc'] = "This will recount the rating of a game, and delete the double ratings.";
$l['repair_favourites_desc'] = "This will delete the double favourites on your board.";
$l['repair_tournaments_stats_desc'] = "This will recount the tournament statistics.";
$l['cleanup_gamedata_desc'] = "This will clean up the unnecessary gamedata.";
$l['repair_permissions_desc'] = "This will repair the default usergroup and administration permissions of the Game Section.";

$l['not_writable'] = "The follow directory has no chmod 777:<br />\n{1}";
$l['not_deleteable'] = "The follow file/directory couldn't be deleted, possible it's because it has no chmod 777:<br />\n{1}";

$l['repaired_scores'] = "The scores are successfully repaired.";
$l['repaired_champions'] = "The champions are successfully repaired.";
$l['repaired_last_champions'] = "The last champions cache is successfully repaired.";
$l['repaired_rating'] = "The ratings are successfully reapaired and recounted.";
$l['repaired_favourites'] = "The favourites are successfully repaired.";
$l['repaired_tournaments_stats'] = "The tournament statistics are successfully recounted..";
$l['cleanedup_gamedata'] = "The gamedata is successfully cleaned up";
$l['repaired_permissions'] = "The permissions are successfully repaired.";
?>