<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2008 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 21/07/2008 by Paretje
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

$l['upload'] = "Upload";
$l['cantmakedir'] = "Can't make this directory:<br />\n{1}";
$l['not_writable'] = "The follow directory has no chmod 777:<br />\n{1}";
$l['not_deleteable'] = "The follow file/directory couldn't be deleted, possible it's because it has no chmod 777:<br />\n";
$l['not_copyable'] = "The follow file couldn't be deleted, possible it's because the source file isn't readable, or the destination already exists and has no chmod 777:<br />\n";

$l['nav_manage_gamedata'] = "Manage Gamedata";

$l['nav_manage_gamedata_desc'] = "Here you can manage the gamedata on your board.";

$l['gamedata_upload'] = "Upload gamedata";
$l['gamedata_directory'] = "Make subdirectory";
$l['directorydoesntexist'] = "The directory you want to access doesn't exist.<br />\nClick <a href=\"index.php?module=games/gamedata&amp;action=add_directory&amp;name={1}\">here</a> to make the folder.";
$l['no_gamedata'] = "There are no files or directories in this directory.";

$l['error_missing_file'] = "You didn't upload a file, or there was a problem with the upload of the file";
$l['error_missing_directoryname'] = "You didn't enter a name for the directory";

$l['delete_file_confirmation'] = "Are you sure you want to delete this file?";
$l['delete_directory_confirmation'] = "Are you sure you want to delete this directory?";

$l['added_file'] = "The file is successfully added.";
$l['added_directory'] = "The directory is successfully added.";
$l['deleted_file'] = "The file is successfully deleted.";
$l['deleted_directory'] = "The directory is successfully deleted.";
?>