<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2008 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 07/07/2008 by Paretje
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

//Define MyBB and includes
define("IN_MYBB", 1);

define("NO_ONLINE", 1);

require_once "../inc/init.php";

//Load theme
$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".intval($mybb->input['tid'])."'");
$theme = $db->fetch_array($query);

//Make CSS header
header("Content-type: text/css");

echo "/**
 * CSS for Game Section 1.2 theme \"".$theme['name']."\"
 */\n\n";
echo $theme['CSS'];
?>
