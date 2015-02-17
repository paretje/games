<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2013 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 07/02/2013 by Paretje
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

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

//Requires
require_once MYBB_ROOT."inc/functions_games.php";
require_once MYBB_ROOT."inc/class_games.php";
$games_core = new games;
$games_core->run_settings();

//Plugin
$plugins->run_hooks("admin_games_tools_start");

//Navigation
$page->add_breadcrumb_item($lang->gamesection, "index.php?module=games");
$page->add_breadcrumb_item($lang->nav_tools, "index.php?module=games/tools");

if($mybb->input['action'] == "repair_scores")
{
	//Loading the scores (DESC)
	$query = $db->query("SELECT *
	FROM ".TABLE_PREFIX."games_scores s
	LEFT JOIN ".TABLE_PREFIX."games g ON (s.gid=g.gid)
	WHERE g.score_type='DESC'
	ORDER BY score DESC");
	while($score = $db->fetch_array($query))
	{
		if(!isset($scores[$score['gid']][$score['uid']]))
		{
			$scores[$score['gid']][$score['uid']] = "OK";
		}
		else
		{
			$db->delete_query("games_scores", "sid='".$scores['sid']."'");
		}
	}
	
	//Loading the scores (ASC)
	$query = $db->query("SELECT *
	FROM ".TABLE_PREFIX."games_scores s
	LEFT JOIN ".TABLE_PREFIX."games g ON (s.gid=g.gid)
	WHERE g.score_type='ASC'
	ORDER BY score ASC");
	while($score = $db->fetch_array($query))
	{
		if(!isset($scores[$score['gid']][$score['uid']]))
		{
			$scores[$score['gid']][$score['uid']] = "OK";
		}
		else
		{
			$db->delete_query("games_scores", "sid='".$scores['sid']."'");
		}
	}
	
	//Plugin
	$plugins->run_hooks("admin_games_tools_repair_scores");
	
	//Log
	log_admin_action();
	
	flash_message($lang->repaired_scores, 'success');
	admin_redirect("index.php?module=games/tools");
}
elseif($mybb->input['action'] == "repair_champions")
{
	//Loading the games and scores
	$query = $db->query("SELECT DISTINCT g.gid, g.score_type, g.title, s.uid, s.score, s.dateline, u.username, c.score AS champscore
	FROM ".TABLE_PREFIX."games g
	LEFT JOIN ".TABLE_PREFIX."games_scores s ON (g.gid=s.gid)
	LEFT JOIN ".TABLE_PREFIX."users u ON (s.uid=u.uid)
	LEFT JOIN ".TABLE_PREFIX."games_champions c ON (g.gid=c.gid)");
	while($scores = $db->fetch_array($query))
	{
		if(($champs[$scores['gid']]['score'] < $scores['score'] && $scores['score_type'] == "DESC") || ($champs[$scores['gid']]['score'] > $scores['score'] && $score['score_type'] == "ASC") || ($champs[$scores['gid']]['score'] == $scores['score'] && $champs[$scores['gid']]['dateline'] > $scores['dateline']))
		{
			$champs[$scores['gid']]['gid'] = $scores['gid'];
			$champs[$scores['gid']]['title'] = $scores['title'];
			$champs[$scores['gid']]['uid'] = $scores['uid'];
			$champs[$scores['gid']]['username'] = $scores['username'];
			$champs[$scores['gid']]['score'] = $scores['score'];
			$champs[$scores['gid']]['dateline'] = $scores['dateline'];
		}
		
		if(isset($scores['champscore']))
		{
			$champs[$scores['gid']]['champscore'] = $scores['champscore'];
		}
	}
	
	//Read the array
	if(is_array($champs))
	{
		foreach($champs as $gid => $array)
		{
			$champs_array = array(
				'gid'			=> intval($champs[$gid]['gid']),
				'title'			=> $db->escape_string($champs[$gid]['title']),
				'uid'			=> intval($champs[$gid]['uid']),
				'username'		=> $db->escape_string($champs[$gid]['username']),
				'score'			=> $db->escape_string($champs[$gid]['score']),
				'dateline'		=> $db->escape_string($champs[$gid]['dateline']),
			);
			
			if(isset($champs[$gid]['champscore']))
			{
				$db->update_query("games_champions", $champs_array, "gid='".$champs[$gid]['gid']."'");
			}
			else
			{
				$db->insert_query("games_champions", $champs_array);
			}
		}
	}
	
	//Plugin
	$plugins->run_hooks("admin_games_tools_repair_champions");
	
	//Log
	log_admin_action();
	
	flash_message($lang->repaired_champions, 'success');
	admin_redirect("index.php?module=games/tools");
}
elseif($mybb->input['action'] == "repair_last_champions")
{
	//Load last champions
	$query = $db->query("SELECT DISTINCT c.*, s.comment
	FROM ".TABLE_PREFIX."games_champions c
	LEFT JOIN ".TABLE_PREFIX."games_scores s ON (c.gid=s.gid AND c.uid=s.uid)
	LEFT JOIN ".TABLE_PREFIX."games g ON (c.gid=g.gid)
	WHERE g.active='1'
	ORDER BY c.dateline DESC
	LIMIT 0,".$games_core->settings['stats_lastchamps_advanced_max']);
	while($champs = $db->fetch_array($query))
	{
		$lastchamps[$champs['dateline'].".".$champs['gid']] = array(
			'gid'		=> $champs['gid'],
			'title'		=> $champs['title'],
			'uid'		=> $champs['uid'],
			'username'	=> $champs['username'],
			'score'		=> floatval($champs['score']),
			'comment'	=> $champs['comment'],
			'dateline'	=> $champs['dateline']
		);
	}
	
	//Save the cache
	if(is_array($lastchamps))
		krsort($lastchamps);
	$cache->update("games_lastchamps", $lastchamps);
	
	//Plugin
	$plugins->run_hooks("admin_games_tools_repair_last_champions");
	
	//Log
	log_admin_action();
	
	flash_message($lang->repaired_last_champions, 'success');
	admin_redirect("index.php?module=games/tools");
}
elseif($mybb->input['action'] == "repair_rating")
{
	//Load ratings
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_rating ORDER BY rating DESC");
	while($rating = $db->fetch_array($query))
	{
		//Control double ratings
		if(!isset($ratings[$rating['gid']][$rating['uid']]))
		{
			$ratings[$rating['gid']][$rating['uid']] = "OK";
		}
		else
		{
			$db->delete_query("games_rating", "rid='".$rating['rid']."'");
		}
		
		//Rate count_chars
		$ratings[$rating['gid']]['rating'] += $rating['rating'];
		
		//Count ratings
		$ratings[$rating['gid']]['rate_count']++;
	}
	
	//Load games
	$query2 = $db->query("SELECT * FROM ".TABLE_PREFIX."games");
	while($games = $db->fetch_array($query2))
	{
		if($ratings[$games['gid']]['rate_count'] != 0)
		{
			$rating = $ratings[$games['gid']]['rating'] / $ratings[$games['gid']]['rate_count'];
			$rating = round($rating, 2);
			
			//Plugin
			$plugins->run_hooks("admin_games_tools_repair_rating_while");
			
			$db->query("UPDATE ".TABLE_PREFIX."games SET rating='".$rating."', numratings='".$ratings[$games['gid']]['rate_count']."' WHERE gid='".$games['gid']."'");
		}
		else
		{
			$db->query("UPDATE ".TABLE_PREFIX."games SET rating='0', numratings='0' WHERE gid='".$games['gid']."'");
		}
	}
	
	//Plugin
	$plugins->run_hooks("admin_games_tools_repair_rating");
	
	//Log
	log_admin_action();
	
	flash_message($lang->repaired_rating, 'success');
	admin_redirect("index.php?module=games/tools");
}
elseif($mybb->input['action'] == "repair_favourites")
{
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_favourites");
	while($favourite = $db->fetch_array($query))
	{
		if(!isset($favourites[$favourite['gid']][$favourite['uid']]))
		{
			$favourites[$favourite['gid']][$favourite['uid']] = "OK";
		}
		else
		{
			$db->delete_query("games_favourites", "fid='".$favourite['fid']."'");
		}
	}
	
	//Plugin
	$plugins->run_hooks("admin_games_tools_repair_favourites");
	
	//Log
	log_admin_action();
	
	flash_message($lang->repaired_favourites, 'success');
	admin_redirect("index.php?module=games/tools");
}
elseif($mybb->input['action'] == "repair_tournaments_stats")
{
	//Load the number of tournament s of each tournament status
	$query = $db->query("SELECT status, COUNT(tid) AS tournaments
	FROM ".TABLE_PREFIX."games_tournaments
	GROUP BY status
	ORDER BY status ASC");
	while($tournaments = $db->fetch_array($query))
	{
		$tournaments_stats[$tournaments['status']] = $tournaments['tournaments'];
	}
	
	//Update tournament statistics
	$cache->update("games_tournaments_stats", $tournaments_stats);
	
	//Plugin
	$plugins->run_hooks("admin_games_tools_repair_tournaments_stats");
	
	//Log
	log_admin_action();
	
	flash_message($lang->repaired_tournaments_stats, 'success');
	admin_redirect("index.php?module=games/tools");
}
elseif($mybb->input['action'] == "cleanup_gamedata")
{
	//Load games
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games");
	while($games = $db->fetch_array($query))
	{
		$game_names[$games['name']] = $games['gid'];
	}
	
	//Load folders
	$files = my_scandir(MYBB_ROOT."arcade/gamedata");
	
	if(is_array($files))
	{
		foreach($files as $id => $file)
		{
			if(is_dir(MYBB_ROOT."arcade/gamedata/".$file))
			{
				//Check if folder is empty
				if(is_emptydir(MYBB_ROOT."arcade/gamedata/".$file))
				{
					if(!@rmdir(MYBB_ROOT."arcade/gamedata/".$file))
					{
						$errors[] = $lang->sprintf($lang->not_deleteable, "arcade/gamedata/".$file);
					}
				}
				//Check if folder is from a game on this board
				elseif(!isset($game_names[$file]))
				{
					gamedata_delete(MYBB_ROOT."arcade/gamedata/".$file);
				}
			}
		}
	}
	
	//Plugin
	$plugins->run_hooks("admin_games_tools_cleanup_gamedata");
	
	//When there are errors, show them
	if($errors)
	{
		//Load the errors
		foreach($errors as $error)
		{
			$flash_errors .= "<li>".$error."</li>\n";
		}
		
		flash_message("<ul>\n".$flash_errors."\n</ul>", 'error');
		admin_redirect("index.php?module=games/tools");
	}
	
	//Log
	log_admin_action();
	
	flash_message($lang->cleanedup_gamedata, 'success');
	admin_redirect("index.php?module=games/tools");
}
elseif($mybb->input['action'] == "repair_permissions")
{
	//Update usergrouppermissions
	$db->write_query("UPDATE ".TABLE_PREFIX."usergroups SET canviewgames='1', canplaygames='1', canplaytournaments='1', canaddtournaments='1'");
	$db->write_query("UPDATE ".TABLE_PREFIX."usergroups SET canviewgames='1', canplaygames='0', canplaytournaments='0', canaddtournaments='0' WHERE gid='1'");
	$db->write_query("UPDATE ".TABLE_PREFIX."usergroups SET canviewgames='1', canplaygames='0', canplaytournaments='0', canaddtournaments='0' WHERE gid='5'");
	$db->write_query("UPDATE ".TABLE_PREFIX."usergroups SET canviewgames='0', canplaygames='0', canplaytournaments='0', canaddtournaments='0' WHERE gid='7'");

	//Update usergroupschache
	$cache->update_usergroups();

	//Update adminpermissions
	change_admin_permission("games", false, 1);
	change_admin_permission("games", "games", 1);
	change_admin_permission("games", "gamedata", 1);
	change_admin_permission("games", "categories", 1);
	change_admin_permission("games", "settings", 1);
	change_admin_permission("games", "themes", 1);
	change_admin_permission("games", "templates", 1);
	change_admin_permission("games", "tools", 1);
	change_admin_permission("games", "version", 1);
	
	//Plugin
	$plugins->run_hooks("admin_games_tools_repair_permissions");
	
	//Log
	log_admin_action();
	
	flash_message($lang->repaired_permissions, 'success');
	admin_redirect("index.php?module=games/tools");
}
else
{
	//Navigation and header
	$page->output_header($lang->nav_tools);
	
	//Show the sub-tabs
	$sub_tabs = array();
	$sub_tabs['tools'] = array(
		'title' => $lang->nav_tools,
		'link' => "index.php?module=games/tools",
		'description' => $lang->nav_tools_desc
	);
	
	$page->output_nav_tabs($sub_tabs, 'tools');
	
	//Plugin
	$lang->load("games_games");
	$plugins->run_hooks("admin_games_tools_default_start");
	
	//Start table
	$table = new Table;
	
	$table->construct_cell("<a href=\"index.php?module=games/tools&amp;action=repair_scores\"><strong>".$lang->repair_scores."</strong></a><br />\n<small>".$lang->repair_scores_desc."</small>");
	$table->construct_row();
	
	$table->construct_cell("<a href=\"index.php?module=games/tools&amp;action=repair_champions\"><strong>".$lang->repair_champions."</strong></a><br />\n<small>".$lang->repair_champions_desc."</small>");
	$table->construct_row();
	
	$table->construct_cell("<a href=\"index.php?module=games/tools&amp;action=repair_last_champions\"><strong>".$lang->repair_last_champions."</strong></a><br />\n<small>".$lang->repair_last_champions_desc."</small>");
	$table->construct_row();
	
	$table->construct_cell("<a href=\"index.php?module=games/tools&amp;action=repair_rating\"><strong>".$lang->repair_rating."</strong></a><br />\n<small>".$lang->repair_rating_desc."</small>");
	$table->construct_row();
	
	$table->construct_cell("<a href=\"index.php?module=games/tools&amp;action=repair_favourites\"><strong>".$lang->repair_favourites."</strong></a><br />\n<small>".$lang->repair_favourites_desc."</small>");
	$table->construct_row();
	
	$table->construct_cell("<a href=\"index.php?module=games/tools&amp;action=repair_tournaments_stats\"><strong>".$lang->repair_tournaments_stats."</strong></a><br />\n<small>".$lang->repair_tournaments_stats_desc."</small>");
	$table->construct_row();
	
	$table->construct_cell("<a href=\"index.php?module=games/tools&amp;action=cleanup_gamedata\"><strong>".$lang->cleanup_gamedata."</strong></a><br />\n<small>".$lang->cleanup_gamedata_desc."</small>");
	$table->construct_row();
	
	$table->construct_cell("<a href=\"index.php?module=games/tools&amp;action=repair_permissions\"><strong>".$lang->repair_permissions."</strong></a><br />\n<small>".$lang->repair_permissions_desc."</small>");
	$table->construct_row();
	
	$table->construct_cell("<a href=\"index.php?module=games/games&amp;action=reset_scores&amp;gid=all\"><strong>".$lang->reset_scores."</strong></a><br />\n<small>".$lang->reset_scores_desc."</small>");
	$table->construct_row();
	
	//Plugin
	$plugins->run_hooks("admin_games_tool_default_end");
	
	//End of table and AdminCP footer
	$table->output($lang->nav_tools);
	$page->output_footer();
}
?>
