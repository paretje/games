<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2008 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 03/02/2008 by Paretje
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

//Define MyBB
define("IN_MYBB", 1);
define("NO_ONLINE", 1);

require_once "./global.php";

switch($mybb->input['sessdo'])
{
	case 'sessionstart':
		echo "&connStatus=1&initbar=".$mybb->input['gamename']."&val=x";
	break;
	case 'permrequest':
		echo "&validate=1&microone=".$mybb->input['score']."|".$mybb->input['fakekey']."&val=x";
	break;
	case 'burn':
		//Reading information
		$game_data = explode("|", $mybb->input['microone']);
		$score = $game_data[0];
		$name = trim(addslashes(stripslashes($game_data[1])));
		
		echo "<form method=\"post\" name=\"vbav3\" action=\"index.php\">
<input type=\"hidden\" name=\"act\" value=\"Arcade\">
<input type=\"hidden\" name=\"do\" value=\"newscore\">
<input type=\"hidden\" name=\"gscore\" value=\"".$score."\">
<input type=\"hidden\" name=\"gname\" value=\"".$name."\">
</form>
<script type=\"text/javascript\">
window.onload = function(){document.vbav3.submit()}
</script>";
	break;
	default:
		header("Location: games.php");
	break;
}
?>