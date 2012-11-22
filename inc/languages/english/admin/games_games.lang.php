<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2009 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 14/04/2009 by Paretje
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

$l['cantmakedir'] = "Can't make this directory:<br />\n{1}";
$l['not_writable'] = "The follow directory has no chmod 777:<br />\n{1}";
$l['not_deleteable'] = "The follow file/directory couldn't be deleted, possible it's because it has no chmod 777:<br />\n{1}";
$l['not_copyable'] = "The follow file couldn't be copied, possible it's because the source file isn't readable, or the destination already exists and has no chmod 777:<br />\n{1}";

$l['nav_overview'] = "Overview";
$l['nav_add_game'] = "Add Game";
$l['nav_add_game_simple'] = "Add Game (Simple)";
$l['nav_add_game_tar'] = "Add Game (Tar)";
$l['nav_edit_game'] = "Edit Game";

$l['nav_overview_desc'] = "Here you can manage the games on your board.";
$l['nav_add_game_desc'] = "Here you can add a game on your board.";
$l['nav_add_game_simple_desc'] = "Here you can add a game on your board, using the files which the tar of the game contains.";
$l['nav_add_game_tar_desc'] = "Here you can add	a game on your board, using the tar-file of the game.";
$l['nav_edit_game_desc'] = "Here you can edit a game on your board.";

$l['search'] = "Search";
$l['active'] = "Active:";
$l['active_all'] = "All";
$l['active_active'] = "Active";
$l['active_inactive'] = "Inactive";
$l['sortby'] = "Sort By:";
$l['sortby_title'] = "Title";
$l['sortby_name'] = "Name";
$l['sortby_dateline'] = "Date added";
$l['sortby_played'] = "Played";
$l['order'] = "Order:";
$l['order_asc'] = "Ascending";
$l['order_desc'] = "Descending";
$l['gamesperpage'] = "Games per page:";

$l['edit_game'] = "Edit game";
$l['delete_game'] = "Delete game";
$l['edit_cat'] = "Edit category";
$l['gamedata'] = "Gamedata";
$l['play_game'] = "Play game";
$l['addedon'] = "Added on:";
$l['played'] = "Total played:";
$l['active_1'] = "Yes";
$l['active_0'] = "No";
$l['no_games'] = "There are no games.";

$l['game_title'] = "Game name";
$l['game_name'] = "Name of the files";
$l['game_cat'] = "Category";
$l['game_cat_no'] = "No category";
$l['game_description'] = "Description";
$l['game_what'] = "Purpose";
$l['game_keys'] = "Keys";
$l['game_bgcolor'] = "Background color";
$l['game_width'] = "Width";
$l['game_height'] = "Height";
$l['game_score_type'] = "Score type";
$l['game_high'] = "High";
$l['game_low'] = "Low";
$l['game_active'] = "Active game";
$l['game_force'] = "Force adding game";
$l['game_php'] = "The php file of the game";
$l['game_swf'] = "The Flash file of the game";
$l['game_gif1'] = "The large gif file of the game";
$l['game_gif2'] = "The small gif file of the game";
$l['game_gamedata'] = "Gamedata included with game";
$l['game_tar'] = "The tar file of the game";

$l['game_name_desc'] = "When your files are, for example, called example.swf, example1.gif and example2.gif, you to fill in here example.";
$l['game_force_desc'] = "Do you want to force adding the game, without controlling if the game already exists?";

$l['error_missing_title'] = "You didn't enter a name for this game";
$l['error_missing_name'] = "You didn't enter the name of the files for this game";
$l['error_missing_category'] = "There is not a category selected, or the option to don't catogorise.";
$l['catdoesntexist'] = "The selected category doesn't exist.";
$l['error_missing_bgcolor'] = "You didn't enter the background color of the game";
$l['error_missing_width'] = "You didn't enter the width of the game";
$l['error_missing_height'] = "You didn't enter the height of the game";
$l['error_missing_score_type'] = "You didn't select the score type of the game";
$l['error_missing_active'] = "You didn't select if you want to have the game active";
$l['error_missing_gamedata_sel'] = "You didn't select if there are gamedata files for this game";
$l['error_missing_game_php'] = "You didn't upload a php file, or there was a problem with the upload of the file";
$l['error_missing_game_swf'] = "You didn't upload a flash file, or there was a problem with the upload of the file";
$l['error_missing_game_gifs'] = "You didn't upload (one of) the gif files, or there was a problem with the upload of the files";
$l['error_missing_game_tar'] = "You didn't upload the tar file, or there was a problem with the upload of the file";
$l['error_missing_game_tar_swf'] = "There wasn't a flash file in the tar archive, or it couldn't be copied";
$l['error_missing_game_tar_gif1'] = "There wasn't a gif1 file in the tar archive, or it couldn't be copied";
$l['error_missing_game_tar_gif2'] = "There wasn't a gif2 file in the tar archive, or it couldn't be copied";
$l['error_uploadfailed'] = "There is an error with the upload of the game.";
$l['tar_problem'] = "There is a problem with the tar file.";
$l['gamealreadyexist'] = "A game with the same filename already exists. When you want to add the game, enable then force adding game.";
$l['gamedoesntexist'] = "The selected game doesn't exist.";

$l['delete_gamefiles'] = "Yes, and delete the files, too";

$l['delete_game_confirmation'] = "Are you sure you want to delete this game?";

$l['added_game'] = "The game is successfully added.";
$l['edited_game'] = "The game is successfully edited.";
$l['deleted_game'] = "The game is successfully deleted.";
?>