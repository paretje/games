<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2015 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 19/05/2015 by Paretje
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
require_once MYBB_ROOT."games/templates.php";

if($mybb->user['uid'] == 1)
{
	$updated = array('games', 'games_bit', 'games_categories',
		'games_favourites', 'games_menu', 'games_online',
		'games_stats_randomgames', 'games_scores',
		'games_search_bar', 'games_stats',
		'games_tournaments_add_game', 'games_tournaments_bar',
		'games_user_settings');
	foreach($updated as $name)
	{
		$db->update_query("games_templates",
			array('template' => $theme_templates[$name]),
			"title='".$name."' AND theme='1'");
	}

	$db->write_query("ALTER TABLE `".TABLE_PREFIX."users`
		ADD `games_champnotify_pm` INT(1) NOT NULL DEFAULT '1',
		ADD `games_champnotify_email` INT(1) NOT NULL DEFAULT '0';");

	echo "Your Game Section version has been upgraded successfully!";
}
?>
