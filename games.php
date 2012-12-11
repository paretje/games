<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2012 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 11/12/2012 by Paretje
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

//MyBB-templates
$templatelist = "multipage,multipage_nextpage,multipage_page,multipage_page_current,multipage_prevpage";

//Define MyBB and includes
define("IN_MYBB", 1);

require_once "./global.php";
require_once MYBB_ROOT."games/global.php";

//Plugin
$plugins->run_hooks("games_start");

switch($mybb->input['action'])
{
	default:
		//Control page
		if(intval($mybb->input['page']))
		{
			$page = intval($mybb->input['page']);
		}
		else
		{
			$page = 1;
		}
		
		//Load the needed stylesheet
		if(is_array($theme['stylesheets']['forumdisplay.php']['global']))
		{
			foreach($theme['stylesheets']['forumdisplay.php']['global'] as $page_stylesheet)
			{
				if($already_loaded[$page_stylesheet])
				{
					continue;
				}
				
				$rating_stylesheet .= "<link type=\"text/css\" rel=\"stylesheet\" href=\"".$mybb->settings['bburl']."/".$page_stylesheet."\" />\n";
				$already_loaded[$page_stylesheet] = 1;
			}
		}
		
		//Load language for rating
		$lang->load("ratethread");
		
		//Handle category specific controls when there is a category selected
		$where_cat = "";
		$where_cat2 = "";
		$url_cat = "";
		
		if(isset($mybb->input['cid']))
		{
			//Control category
			$cid = intval($mybb->input['cid']);
			
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_categories WHERE active='1' AND cid='".$cid."' LIMIT 0,1");
			$cat = $db->fetch_array($query);
			$cat_test = $db->num_rows($query);
			
			if($cat_test == 0)
			{
				error($lang->categorydoesntexist, $lang->error);
			}
			
			
			//Convert special chars in title
			$cat['title'] = htmlspecialchars_uni($cat['title']);
			
			//Navigation
			add_breadcrumb($cat['title']);
			
			//Where clauses
			$where_cat = " AND g.cid='".intval($mybb->input['cid'])."'";
			$where_cat2 = " AND cid='".intval($mybb->input['cid'])."'";
			
			//Multipages url
			$url_cat = "?cid=".$cid;
		}
		
		//Multipages
		$perpage = $maxgames;
		
		$start = ($page-1) * $perpage;
		
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE active='1'".$where_cat2);
		$count = $db->num_rows($query);
		
		$pages = $count / $perpage;
		$pages = ceil($pages);
		
		if($pages > 1)
		{
			$multipage = multipage($count, $perpage, $page, "games.php".$url_cat);
			
			eval("\$multipages = \"".$games_core->template("games_multipages")."\";");
		}
		
		//Game Section Stats
		if($games_core->settings['stats_global'] == 1)
		{
			$stats = stats();
		}
		
		//Tournaments bar
		if($games_core->settings['tournaments_activated'] == 1)
		{
			//Statistics
			$tournaments_stats = $cache->read("games_tournaments_stats");
			
			//Language statistics
			$lang->tournaments_stats_open = $lang->sprintf($lang->tournaments_stats_open, intval($tournaments_stats['open']));
			$lang->tournaments_stats_started = $lang->sprintf($lang->tournaments_stats_started, intval($tournaments_stats['started']));
			$lang->tournaments_stats_finished = $lang->sprintf($lang->tournaments_stats_finished, intval($tournaments_stats['finished']));
			
			//User part
			if($mybb->user['uid'] != 0)
			{
				//Loading started tournaments of the user
				$query = $db->query("SELECT DISTINCT t.tid, t.gid, g.title, g.name
				FROM ".TABLE_PREFIX."games_tournaments_players p
				LEFT JOIN ".TABLE_PREFIX."games_tournaments t ON (p.tid=t.tid)
				LEFT JOIN ".TABLE_PREFIX."games g ON (t.gid=g.gid)
				WHERE p.uid='".$mybb->user['uid']."' AND t.status='started' AND g.active='1'
				ORDER BY t.dateline DESC");
				$tournaments_test = $db->num_rows($query);
				
				while($tournaments = $db->fetch_array($query))
				{
					eval("\$tournaments_bar_user_games_bit .= \"".$games_core->template("games_tournaments_bar_user_games_bit")."\";");
				}
				
				//Test tournaments
				if($tournaments_test == 0)
				{
					$tournaments_bar_user_games_bit = $lang->tournaments_user_nostartedgames;
				}
				
				//Add tournament link
				if($mybb->usergroup['canaddtournaments'] == 1)
				{
					eval("\$tournaments_bar_user_add = \"".$games_core->template("games_tournaments_bar_user_add")."\";");
				}
				
				$width = " width=\"33%\"";
				eval("\$tournaments_bar_user = \"".$games_core->template("games_tournaments_bar_user")."\";");
			}
			
			eval("\$tournaments_bar = \"".$games_core->template("games_tournaments_bar")."\";");
		}
		
		//Search function
		$selected_cat[$cid] = " selected=\"selected\"";
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_categories WHERE active='1' ORDER BY title ASC");
		while($cats = $db->fetch_array($query))
		{
			$search_cats .= "<option value=\"".$cats['cid']."\"".$selected_cat[$cats['cid']].">".$cats['title']."</option>";
		}
		
		// Prevent XSS attacks here !
		$search_bar_s = htmlspecialchars_uni($mybb->input['s']);
		$search_bar_des = htmlspecialchars_uni($mybb->input['des']);
		
		eval("\$search_bar = \"".$games_core->template("games_search_bar")."\";");
		
		//Loading games
		$query = $db->query("SELECT DISTINCT g.gid, g.title, g.name, g.description, g.played, g.lastplayed, g.lastplayedby, g.rating, g.numratings, g.dateline, c.username, c.score, f.fid, r.rid, s.score AS pscore, u.username AS lastplayedusername
		FROM ".TABLE_PREFIX."games g
		LEFT JOIN ".TABLE_PREFIX."games_champions c ON (g.gid=c.gid)
		LEFT JOIN ".TABLE_PREFIX."games_favourites f ON (g.gid=f.gid AND f.uid='".$mybb->user['uid']."')
		LEFT JOIN ".TABLE_PREFIX."games_rating r ON (g.gid=r.gid AND r.uid='".$mybb->user['uid']."')
		LEFT JOIN ".TABLE_PREFIX."games_scores s ON (g.gid=s.gid AND s.uid='".$mybb->user['uid']."')
		LEFT JOIN ".TABLE_PREFIX."users u ON (g.lastplayedby=u.uid)
		WHERE g.active='1'".$where_cat."
		GROUP BY g.gid
		ORDER BY g.".$sortby." ".$order."
		LIMIT ".$start.",".$perpage);
		$games_test = $db->num_rows($query);
		
		//Plugin
		$plugins->run_hooks("games_default_start");
		
		//No games
		if($games_test == 0)
		{
			error($lang->no_games, $lang->error);
		}
		
		while($games = $db->fetch_array($query))
		{
			//Plugin
			$plugins->run_hooks("games_default_games_start");
			
			//Rows
			if($bgcolor == "trow1")
			{
				$bgcolor = "trow2";
			}
			else
			{
				$bgcolor = "trow1";
			}
			
			//Is this a new game?
			$date = TIME_NOW-($games_core->settings['new_game']*86400);
			
			if($games['dateline'] >= $date)
			{
				$new_game = " <img src=\"./games/".$theme_games['directory']."/new.png\" alt=\"\" />";
			}
			else
			{
				$new_game = "";
			}
			
			//Title and description
			$games['title'] = htmlspecialchars_uni($games['title']);
			$games['description'] = htmlspecialchars_uni($games['description']);
			
			//Champions of games
			if(!isset($games['username']))
			{
				$games['username'] = $lang->na;
				$games['score'] = $lang->na;
			}
			else
			{
				$games['score'] = my_number_format(floatval($games['score']));
			}
			
			$champ = $lang->sprintf($lang->champ, $games['username'], $games['score']);
			
			//If you are a memeber, whats your best score
			if($mybb->user['uid'] != 0)
			{
				if(!isset($games['pscore']))
				{
					$games['pscore'] = $lang->na;
				}
				else
				{
					$games['pscore'] = my_number_format(floatval($games['pscore']));
				}
			}
			else
			{
				$games['pscore'] = $lang->na;
			}
			
			//Played
			$games['played'] = my_number_format(floatval($games['played']));
			
			//Last played
			if(isset($games['lastplayedusername']))
			{
				$lastplayed_date = my_date($mybb->settings['dateformat'], $games['lastplayed']).", ".my_date($mybb->settings['timeformat'], $games['lastplayed']);
				
				$lastplayed = $lang->sprintf($lang->lastplayed_sen, $lastplayed_date, $games['lastplayedusername'], $games['lastplayedby']);
			}
			else
			{
				$lastplayed= "<strong>".$lang->na."</strong>";
			}
			
			//Favourite
			if($mybb->user['uid'] != 0)
			{
				if(!isset($games['fid']))
				{
					eval("\$games_favourite = \"".$games_core->template("games_bit_favourite_add")."\";");
				}
				else
				{
					eval("\$games_favourite = \"".$games_core->template("games_bit_favourite_delete")."\";");
				}
			}
			
			//Tournaments
			if($games_core->settings['tournaments_activated'] == 1 && $mybb->usergroup['canaddtournaments'] == 1 && $mybb->user['uid'] != 0)
			{
				eval("\$games_tournament = \"".$games_core->template("games_bit_tournament")."\";");
			}
			
			//Rating
			$games['width'] = round($games['rating']*20, 0);
			
			$ratingvotesav = $lang->sprintf($lang->rating_votes_average, $games['numratings'], $games['rating']);
			
			$not_rated = "";
			if($games['rid'] == 0)
			{
				$not_rated = " star_rating_notrated";
			}
			
			//Plugin
			$plugins->run_hooks("games_default_games_end");
			
			eval("\$games_bit .= \"".$games_core->template("games_bit")."\";");
		}
		
		//Categories
		$query = $db->query("SELECT c.*, COUNT(g.gid) AS games
		FROM ".TABLE_PREFIX."games_categories c
		LEFT JOIN ".TABLE_PREFIX."games g ON (c.cid=g.cid AND g.active='1')
		WHERE c.active='1'
		GROUP BY c.cid
		ORDER BY c.title ASC");
		$cat_test = $db->num_rows($query);
		
		//Cats per line
		$count = 1;
		$count2 = 0;
		
		$lines = ceil($cat_test/$theme_games['catsperline']);
		$maxcats = $lines*$theme_games['catsperline'];
		$cats = $maxcats-$cat_test;
		$procent = 100/$theme_games['catsperline'];
		$bgcolor = "";
		
		while($categories = $db->fetch_array($query))
		{
			//Cat image
			if(!empty($categories['image']))
			{
				$categories['image'] = "<img src=\"".$categories['image']."\" alt=\"\" /> ";
			}
			
			//Title
			$categories['title'] = htmlspecialchars_uni($categories['title']);
			
			//Lines
			if($count == $theme_games['catsperline'] && $maxcats != $count2)
			{
				$tr = "
</tr>
<tr>";
				$count = 1;
				$count2++;
			}
			else
			{
				$tr = "";
				
				$count++;
				$count2++;
			}
			
			//Backgroundcolor
			if($count2 != $count && $count == 2)
			{
				//Rows
				if($bgcolor == "trow1")
				{
					$bgcolor = "trow2";
				}
				else
				{
					$bgcolor = "trow1";
				}
			}
			
			//Plugin
			$plugins->run_hooks("games_default_cats");
			
			//Is this the current category?
			if($categories['cid'] == intval($mybb->input['cid']))
			{
				eval("\$categories_bit .= \"".$games_core->template("games_categories_bit_cur")."\";");
			}
			else
			{
				eval("\$categories_bit .= \"".$games_core->template("games_categories_bit")."\";");
			}
		}
		
		if($cat_test != 0)
		{
			//Cats per line fix
			for($i = 1; $i <= $cats; $i++)
			{
				$categories_bit .= "<td class=\"".$bgcolor."\"></td>";
			}
			
			eval("\$categories_bar = \"".$games_core->template("games_categories")."\";");
		}
		
		//Online
		if($games_core->settings['online'] != "never")
		{
			$online = whos_online();
		}
		
		//Plugin
		$plugins->run_hooks("games_default_end");
		
		eval("\$games_page = \"".$games_core->template("games")."\";");
		output_page($games_page);
	break;
	case 'play':
		$gid = intval($mybb->input['gid']);

		//Loading game
		$query = $db->query("SELECT DISTINCT g.gid, g.title, g.name, g.what, g.use_keys, g.bgcolor, g.width, g.height, c.username, c.score, s.score AS pscore
		FROM ".TABLE_PREFIX."games g
		LEFT JOIN ".TABLE_PREFIX."games_champions c ON (g.gid=c.gid)
		LEFT JOIN ".TABLE_PREFIX."games_scores s ON (g.gid=s.gid AND s.uid='".$mybb->user['uid']."')
		WHERE g.gid='".$gid."' AND g.active='1'
		LIMIT 0,1");
		$game = $db->fetch_array($query);
		
		//Test game
		$game_test = $db->num_rows($query);
		if($game_test == 0)
		{
			error($lang->gamedoesntexist, $lang->error);
		}
		
		//Session start
		$games_core->session_start();
		
		//Load tournament, and the session information
		if($games_core->settings['tournaments_activated'] == 1 && intval($mybb->input['tid']))
		{
			$query = $db->query("SELECT DISTINCT t.*, p.uid, p.tries
			FROM ".TABLE_PREFIX."games_tournaments t
			LEFT JOIN ".TABLE_PREFIX."games_tournaments_players p ON (t.tid=p.tid AND t.round=p.rid AND p.uid='".$mybb->user['uid']."')
			WHERE t.tid='".intval($mybb->input['tid'])."' AND t.gid='".$game['gid']."' AND t.status='started'");
			$tournament = $db->fetch_array($query);
			$tournament_test = $db->num_rows($query);
			
			//Test tournament
			$tournament['roundinformation'] = unserialize($tournament['roundinformation']);
			if($tournament_test == 0)
			{
				error($lang->tournamentdoesntexist, $lang->error);
			}
			if(!intval($tournament['uid']))
			{
				error($lang->tournamentnotjoined, $lang->error);
			}
			if($tournament['tries'] >= $tournament['maxtries'])
			{
				error($lang->tournamentmaxtriesreached, $lang->error);
			}
			if($tournament['roundinformation'][$tournament['round']]['starttime']+$tournament['roundtime'] < TIME_NOW)
			{
				error($lang->tournamentroundended, $lang->error);
			}
			
			$games_core->session['tid'][$game['name']] = $tournament['tid'];
		}
		else
		{
			$games_core->session['gid'][$game['name']] = $game['gid'];
		}
		
		//Plugin and session end
		$plugins->run_hooks("games_play_start");
		$games_core->session_update();
		
		//Navigation
		$game['title'] = htmlspecialchars_uni($game['title']);
		add_breadcrumb($game['title']);
		
		//Champion of game
		if(!isset($game['username']))
		{
			$game['username'] = "<strong>".$lang->na."</strong>";
			$game['score'] = "<strong>".$lang->na."</strong>";
		}
		else
		{
			$game['score'] = my_number_format(floatval($game['score']));
		}
		
		$lang->champ = $lang->sprintf($lang->champ, $game['username'], $game['score']);
		
		//Personal best score
		if($mybb->user['uid'] != 0)
		{
			if(!isset($game['pscore']))
			{
				$game['pscore'] = $lang->na;
			}
			else
			{
				$game['pscore'] = my_number_format(floatval($game['pscore']));
			}
		}
		else
		{
			$game['pscore'] = $lang->na;
		}
		
		//Update game
		$db->write_query("UPDATE ".TABLE_PREFIX."games SET played=played+1, lastplayed='".TIME_NOW."', lastplayedby='".$mybb->user['uid']."' WHERE gid='".$gid."'");
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugin
		$plugins->run_hooks("games_play_end");
		
		eval("\$play = \"".$games_core->template('games_play')."\";");
		output_page($play);
	break;
	case 'favourites':
		//Control page
		if(intval($mybb->input['page']))
		{
			$page = intval($mybb->input['page']);
		}
		else
		{
			$page = 1;
		}
		
		//Load the needed stylesheet
		if(is_array($theme['stylesheets']['forumdisplay.php']['global']))
		{
			foreach($theme['stylesheets']['forumdisplay.php']['global'] as $page_stylesheet)
			{
				if($already_loaded[$page_stylesheet])
				{
					continue;
				}
				
				$rating_stylesheet .= "<link type=\"text/css\" rel=\"stylesheet\" href=\"".$mybb->settings['bburl']."/".$page_stylesheet."\" />\n";
				$already_loaded[$page_stylesheet] = 1;
			}
		}
		
		//Load language for rating
		$lang->load("ratethread");
		
		//Navigation
		add_breadcrumb($lang->your_favourites);
		
		//Multipages
		$perpage = $maxgames;
		
		$start = ($page-1) * $perpage;
		
		$query = $db->query("SELECT g.*
		FROM ".TABLE_PREFIX."games_favourites f
		LEFT JOIN ".TABLE_PREFIX."games g ON (f.gid=g.gid)
		WHERE g.active='1' AND f.uid='".$mybb->user['uid']."'");
		$count = $db->num_rows($query);
		
		$pages = $count / $perpage;
		$pages = ceil($pages);
		
		if($pages > 1)
		{
			$multipage = multipage($count, $perpage, $page, "games.php?action=favourites");
			
			eval("\$multipages = \"".$games_core->template("games_multipages")."\";");
		}
		
		//Loading games
		$query = $db->query("SELECT DISTINCT g.gid, g.title, g.name, g.description, g.played, g.lastplayed, g.lastplayedby, g.rating, g.dateline, c.username, c.score, f.fid, r.rid, s.score AS pscore, u.username AS lastplayedusername, COUNT(r.rid) AS ratings
		FROM ".TABLE_PREFIX."games_favourites f
		LEFT JOIN ".TABLE_PREFIX."games g ON (f.gid=g.gid)
		LEFT JOIN ".TABLE_PREFIX."games_champions c ON (f.gid=c.gid)
		LEFT JOIN ".TABLE_PREFIX."games_rating r ON (f.gid=r.gid AND r.uid='".$mybb->user['uid']."')
		LEFT JOIN ".TABLE_PREFIX."games_scores s ON (f.gid=s.gid AND s.uid='".$mybb->user['uid']."')
		LEFT JOIN ".TABLE_PREFIX."users u ON (g.lastplayedby=u.uid)
		WHERE g.active='1' AND f.uid='".$mybb->user['uid']."'
		GROUP BY f.gid
		ORDER BY g.".$sortby." ".$order."
		LIMIT ".$start.",".$perpage);
		$games_test = $db->num_rows($query);
		
		//Plugin
		$plugins->run_hooks("games_favourites_start");
		
		//No games
		if($games_test == 0)
		{
			error($lang->no_games, $lang->error);
		}
		
		while($games = $db->fetch_array($query))
		{
			//Plugin
			$plugins->run_hooks("games_favourites_games_start");
			
			//Rows
			if($bgcolor == "trow1")
			{
				$bgcolor = "trow2";
			}
			else
			{
				$bgcolor = "trow1";
			}
			
			//Is this a new game?
			$date = TIME_NOW-($games_core->settings['new_game']*86400);
			
			if($games['dateline'] >= $date)
			{
				$new_game = " <img src=\"./games/".$theme_games['directory']."/new.png\" alt=\"\" />";
			}
			else
			{
				$new_game = "";
			}
			
			//Title and description
			$games['title'] = htmlspecialchars_uni($games['title']);
			$games['description'] = htmlspecialchars_uni($games['description']);
			
			//Champions of games
			if(!isset($games['username']))
			{
				$games['username'] = $lang->na;
				$games['score'] = $lang->na;
			}
			else
			{
				$games['score'] = my_number_format(floatval($games['score']));
			}
			
			$champ = $lang->sprintf($lang->champ, $games['username'], $games['score']);
			
			//If you are a memeber, whats your best score
			if($mybb->user['uid'] != 0)
			{
				if(!isset($games['pscore']))
				{
					$games['pscore'] = $lang->na;
				}
				else
				{
					$games['pscore'] = my_number_format(floatval($games['pscore']));
				}
			}
			else
			{
				$games['pscore'] = $lang->na;
			}
			
			//Played
			$games['played'] = my_number_format(floatval($games['played']));
			
			//Last played
			if(isset($games['lastplayedusername']))
			{
				$lastplayed_date = my_date($mybb->settings['dateformat'], $games['lastplayed']).", ".my_date($mybb->settings['timeformat'], $games['lastplayed']);
				
				$lastplayed = $lang->sprintf($lang->lastplayed_sen, $lastplayed_date, $games['lastplayedusername'], $games['lastplayedby']);
			}
			else
			{
				$lastplayed= "<strong>".$lang->na."</strong>";
			}
			
			//Favourite
			if($mybb->user['uid'] != 0)
			{
				if(!isset($games['fid']))
				{
					eval("\$games_favourite = \"".$games_core->template("games_bit_favourite_add")."\";");
				}
				else
				{
					eval("\$games_favourite = \"".$games_core->template("games_bit_favourite_delete")."\";");
				}
			}
			
			//Tournaments
			if($games_core->settings['tournaments_activated'] == 1 && $mybb->usergroup['canaddtournaments'] == 1 && $mybb->user['uid'] != 0)
			{
				eval("\$games_tournament = \"".$games_core->template("games_bit_tournament")."\";");
			}
			
			//Rating
			$games['width'] = $games['rating']*20;
			
			$ratingvotesav = $lang->sprintf($lang->rating_votes_average, $games['ratings'], $games['rating']);
			
			$not_rated = "";
			if($games['ratings'] == 0)
			{
				$not_rated = " star_rating_notrated";
			}
			
			//Plugin
			$plugins->run_hooks("games_favourites_games_end");
			
			eval("\$games_bit .= \"".$games_core->template("games_bit")."\";");
		}
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugin
		$plugins->run_hooks("games_favourites_end");
		
		eval("\$games_favourites = \"".$games_core->template("games_favourites")."\";");
		output_page($games_favourites);
	break;
	case 'add_favourite':
		$gid = intval($mybb->input['gid']);
		
		//Test user
		if($mybb->user['uid'] == 0)
		{
			error_no_permission();
		}
		
		//Test game
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE gid='".$gid."' AND active='1' LIMIT 0,1");
		$game_test = $db->num_rows($query);
		
		if($game_test == 0)
		{
			error($lang->gamedoesntexist, $lang->error);
		}
		else
		{
			//Test favourite
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_favourites WHERE gid='".$gid."' AND uid='".$mybb->user['uid']."'");
			$favourite_test = $db->num_rows($query);
			
			if($favourite_test == 1)
			{
				error($lang->alreadyfavourite, $lang->error);
			}
			
			$favourite = array(
				'gid'			=> intval($mybb->input['gid']),
				'uid'			=> intval($mybb->user['uid'])
			);
			
			//Plugin
			$plugins->run_hooks("games_add_favourite");
			
			$db->insert_query("games_favourites", $favourite);
			
			//Redirect to the page where the user came from
			if(!empty($_SERVER['HTTP_REFERER']))
			{
				$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			}
			else
			{
				$HTTP_REFERER = "games.php";
			}
			
			redirect($HTTP_REFERER, $lang->added_favourite);
		}
	break;
	case 'delete_favourite':
		$gid = intval($mybb->input['gid']);
		
		//Test user
		if($mybb->user['uid'] == 0)
		{
			error_no_permission();
		}
		
		//Test favourite
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_favourites WHERE gid='".$gid."' AND uid='".$mybb->user['uid']."'");
		$favourite_test = $db->num_rows($query);
		
		if($favourite_test == 0)
		{
			error($lang->favouritedoesntexist, $lang->error);
		}
		else
		{
			$db->write_query("DELETE FROM ".TABLE_PREFIX."games_favourites WHERE gid='".$gid."' AND uid='".$mybb->user['uid']."'");
			
			//Plugin
			$plugins->run_hooks("games_delete_favourite");
			
			//Redirect to the page where the user came from
			if(!empty($_SERVER['HTTP_REFERER']))
			{
				$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			}
			else
			{
				$HTTP_REFERER = "games.php";
			}
			
			redirect($HTTP_REFERER, $lang->deleted_favourite);
		}
	break;
	case 'rate':
		$gid = intval($mybb->input['gid']);
		
		//Test user
		if($mybb->user['uid'] == 0)
		{
			error_no_permission();
		}
		
		//Load language for rating
		$lang->load("ratethread");
		
		// Verify incoming POST request
		verify_post_check($mybb->input['my_post_key']);
		
		//Test game
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE gid='".$gid."' AND active='1' LIMIT 0,1");
		$game_test = $db->num_rows($query);
		
		if($game_test == 0)
		{
			error($lang->gamedoesntexist, $lang->error);
		}
		
		//Test rating
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_rating WHERE gid='".$gid."' AND uid='".$mybb->user['uid']."'");
		$rate_test = $db->num_rows($query);
		
		if($rate_test == 1)
		{
			error($lang->alreadyrated, $lang->error);
		}
		
		//Insert rating
		$rate = array(
			'gid'			=> intval($mybb->input['gid']),
			'uid'			=> intval($mybb->user['uid']),
			'username'		=> $db->escape_string($mybb->user['username']),
			'rating'		=> intval($mybb->input['rating']),
			'dateline'		=> TIME_NOW,
			'ip'			=> get_ip(),
		);
		
		//Plugin
		$plugins->run_hooks("games_rate_start");
		
		$db->insert_query("games_rating", $rate);
		
		//Total rating
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_rating WHERE gid='".$gid."'");
		$rate_count = $db->num_rows($query);
		
		while($rated = $db->fetch_array($query))
		{
			$rating = $rating + $rated['rating'];
		}
		
		$rating = round($rating/$rate_count, 2);
		
		//Plugin
		$plugins->run_hooks("games_rate_end");
		
		$db->write_query("UPDATE ".TABLE_PREFIX."games SET rating='".$rating."', numratings='".$rate_count."' WHERE gid='".$gid."'");
		
		//Redirect
		if($mybb->input['ajax'])
		{
			$width = $rating*20;
			$ratingvotesav = $lang->sprintf($lang->rating_votes_average, $rate_count, $rating);
			echo "<success><br />".$lang->rating_added."</success>\n";
			echo "<average>".$ratingvotesav."</average>\n";
			echo "<width>".$width."</width>";
		}
		else
		{
			redirect("games.php", $lang->rated);
		}
	break;
	case 'do_search':
		//Plugin
		$plugins->run_hooks("games_do_search_start");
		
		//Replacing
		$patterns[0] = '/ /';
		$replacements[0] = "%";
		
		//Is there searched on a title
		if(!empty($mybb->input['s']))
		{
			$title_q = $db->escape_string(preg_replace($patterns, $replacements, htmlspecialchars_decode($mybb->input['s'])));
			$where_search = " AND title LIKE '%".$title_q."%'";
			
			$s = "&s=".htmlspecialchars_uni($mybb->input['s']);
		}
		
		//Is there a category seleted
		if(intval($mybb->input['cid']))
		{
			$cid_q = intval($mybb->input['cid']);
			
			$where_search .= " AND cid='".$cid_q."'";
			
			$cid = "&cid=".intval($mybb->input['cid']);
		}
		
		//Is there searched on a description
		if(!empty($mybb->input['des']))
		{
			$des_q = $db->escape_string(preg_replace($patterns, $replacements, htmlspecialchars_decode($mybb->input['des'])));
			
			$where_search .= " AND description LIKE '%".$des_q."%'";
			
			$des = "&des=".htmlspecialchars_uni($mybb->input['des']);
		}
		
		//Test search
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE active='1' ".$where_search." LIMIT 0,1");
		$search_test = $db->num_rows($query);
		
		//Plugin
		$plugins->run_hooks("games_do_search_end");
		
		if($search_test == 0)
		{
			redirect("games.php", $lang->noresults);
		}
		
		redirect("games.php?action=search".$s.$des.$cid, $lang->searching);
	break;
	case 'search':
		//Control page
		if(intval($mybb->input['page']))
		{
			$page = intval($mybb->input['page']);
		}
		else
		{
			$page = 1;
		}
		
		//Replacing
		$patterns[0] = '/ /';
		$replacements[0] = "%";
		
		//Is there searched on a title
		if(!empty($mybb->input['s']))
		{
			$title = $db->escape_string(preg_replace($patterns, $replacements, htmlspecialchars_decode($mybb->input['s'])));
			
			$where_search = " AND title LIKE '%".$title."%'";
			$where_search2 = " AND g.title LIKE '%".$title."%'";
		}
		
		//Is there a category seleted
		if(intval($mybb->input['cid']))
		{
			$cid = intval($mybb->input['cid']);
			
			$where_search .= " AND cid='".$cid."'";
			$where_search2 .= " AND g.cid='".$cid."'";
		}
		
		//Is there searched on a description
		if(!empty($mybb->input['des']))
		{
			$des = $db->escape_string(preg_replace($patterns, $replacements, htmlspecialchars_decode($mybb->input['des'])));
			
			$where_search .= " AND description LIKE '%".$des."%'";
			$where_search2 .= " AND g.description LIKE '%".$des."%'";
		}
		
		//Test search
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE active='1' ".$where_search);
		$search_test = $db->num_rows($query);
		
		if($search_test == 0)
		{
			redirect("games.php", $lang->noresults);
		}
		
		//Test page
		$search_page_control = ($page-1) * $maxgames;
		
		if($search_page_control > $search_test)
		{
			redirect("games.php", $lang->noresults);
		}
		
		//Navigation
		add_breadcrumb($lang->searchresults);
		
		//Search function
		$selected_cat[intval($mybb->input['cid'])] = " selected=\"selected\"";
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_categories WHERE active='1' ORDER BY title ASC");
		while($cats = $db->fetch_array($query))
		{
			$search_cats .= "<option value=\"".$cats['cid']."\"".$selected_cat[$cats['cid']].">".$cats['title']."</option>";
		}
		
		// Prevent XSS attacks here !
		$search_bar_s = htmlspecialchars_uni($mybb->input['s']);
		$search_bar_des = htmlspecialchars_uni($mybb->input['des']);

		eval("\$search_bar = \"".$games_core->template("games_search_bar")."\";");
		
		//Header
		//Title specification
		if(isset($mybb->input['s']))
		{
			if(isset($mybb->input['des']))
			{
				$slash = " | ";
			}
			elseif(isset($mybb->input['cid']))
			{
				$slash = " | ";
			}
			else
			{
				$slash = "";
			}
			
			$search_sen = $lang->sprintf($lang->searchresults_name, $search_bar_s, $slash);
		}
		
		//Description specification
		if(isset($mybb->input['des']))
		{
			if(isset($mybb->input['cid']))
			{
				$slash = " | ";
			}
			else
			{
				$slash = "";
			}
			
			$search_sen .= $lang->sprintf($lang->searchresults_des, $search_bar_des, $slash);
		}
		
		//Category specification
		if(intval($mybb->input['cid']))
		{
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_categories WHERE cid='".intval($mybb->input['cid'])."' AND active='yes' LIMIT 0,1");
			$cat = $db->fetch_array($query);	
			
			$search_sen .= $lang->sprintf($lang->searchresults_cat, $cat['title']);
		}
		
		$lang->searchresults_of = $lang->sprintf($lang->searchresults_of, $search_sen);
		
		
		//Search results
		$lang->searchresults_of = htmlspecialchars_uni($lang->searchresults_of);
		
		//Multipages
		$perpage = $maxgames;
		
		$start = ($page-1) * $perpage;
		
		$pages = $search_test / $perpage;
		$pages = ceil($pages);
		
		if($pages > 1)
		{
			$addr = explode("&page=", $_SERVER['QUERY_STRING']);
			$multipage = multipage($search_test, $perpage, $page, "games.php?".$addr[0]);
			
			eval("\$multipages = \"".$games_core->template("games_multipages")."\";");
		}
		
		//Loading results
		$query = $db->query("SELECT DISTINCT g.gid, g.cid, g.title, g.dateline, c.uid, c.username, c.score, ca.title AS cat_title
		FROM ".TABLE_PREFIX."games g
		LEFT JOIN ".TABLE_PREFIX."games_champions c ON (g.gid=c.gid)
		LEFT JOIN ".TABLE_PREFIX."games_categories ca ON (g.cid=ca.cid)
		WHERE g.active='1' ".$where_search2."
		ORDER BY g.".$sortby." ".$order."
		LIMIT ".$start.",".$maxgames);
		
		//Plugin
		$plugins->run_hooks("games_search_start");
		
		while($search = $db->fetch_array($query))
		{
			//Rows
			if($bgcolor == "trow1")
			{
				$bgcolor = "trow2";
			}
			else
			{
				$bgcolor = "trow1";
			}
			
			//Pubdate
			$pubdate = my_date($mybb->settings['dateformat'], $search['dateline']).", ".my_date($mybb->settings['timeformat'], $search['dateline']);
			
			//Champion
			if(isset($search['username']))
			{
				$search['username'] = build_profile_link($search['username'], $search['uid']);
				$search['score'] = my_number_format(floatval($search['score']));
			}
			else
			{
				$search['username'] = "<strong>".$lang->na."</strong>";
				$search['score'] = "<strong>".$lang->na."</strong>";
			}
			
			//Title and description
			$search['cat_title'] = htmlspecialchars_uni($search['cat_title']);
			$search['title'] = htmlspecialchars_uni($search['title']);
			$search['description'] = htmlspecialchars_uni($search['description']);
			
			//Category
			if(isset($search['cat_title']))
			{
				$search['cat_title'] = "<a href=\"games.php?action=category&amp;cid=".$search['cid']."\">".$search['cat_title']."</a>";
			}
			else
			{
				$search['cat_title'] = "<strong>".$lang->na."</strong>";
			}
			
			//Is this a new game?
			$date = TIME_NOW-($games_core->settings['new_game']*86400);
			
			if($search['dateline'] >= $date)
			{
				$new_game = " <img src=\"./games/".$theme_games['directory']."/new.png\" alt=\"\" />";
			}
			else
			{
				$new_game = "";
			}
			
			//Plugin
			$plugins->run_hooks("games_search_while");
			
			eval("\$search_bit .= \"".$games_core->template("games_search_bit")."\";");
		}
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugin
		$plugins->run_hooks("games_search_end");
		
		eval("\$searchpage = \"".$games_core->template('games_search')."\";");
		output_page($searchpage);
	break;
	case 'stats':
		$uid = intval($mybb->input['uid']);
		
		//User-ID
		if(!intval($mybb->input['uid']))
		{
			if($mybb->user['uid'] != 0)
			{
				$uid = $mybb->user['uid'];
			}
			else
			{
				error_no_permission();
			}
		}
		else
		{
			$url_uid = "&uid=".$uid;
		}
		
		//Test user
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."users WHERE uid='".$uid."' LIMIT 0,1");
		$user = $db->fetch_array($query);
		$user_test = $db->num_rows($query);
		
		if($user_test == 0)
		{
			error($lang->userdoesntexist, $lang->error);
		}
		
		//Language variabels and navigation
		$lang->statsofuser = $lang->sprintf($lang->statsofuser, $user['username']);
		$lang->dataofuser = $lang->sprintf($lang->dataofuser, $user['username']);
		
		add_breadcrumb($lang->statsofuser);
		
		//Variables
		$first = 0;
		$second = 0;
		$thirth = 0;
		$tenth = 0;
		$total = 0;
		
		//Control if multipages is activated
		if($games_core->settings['stats_userstats_multipages'] == 1)
		{
			//Control page
			if(intval($mybb->input['page']))
			{
				$page = intval($mybb->input['page']);
			}
			else
			{
				$page = 1;
			}
			
			//Multipages
			$perpage = $maxgames;
		
			$start = ($page-1) * $perpage;
			$end = $start + $perpage;
		
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE active='1'");
			$count = $db->num_rows($query);
		
			$pages = $count / $perpage;
			$pages = ceil($pages);
		
			if($pages > 1)
			{
				$multipage = multipage($count, $perpage, $page, "games.php?action=stats".$url_uid);
			
				eval("\$multipages = \"".$games_core->template("games_user_stats_multipages")."\";");
			}
		}
		
		//Load games
		$query = $db->query("SELECT DISTINCT g.gid, g.title, g.score_type, s.sid, s.uid, s.score, s.dateline, c.uid AS champ
		FROM ".TABLE_PREFIX."games g
		LEFT JOIN ".TABLE_PREFIX."games_scores s ON (g.gid=s.gid)
		LEFT JOIN ".TABLE_PREFIX."games_champions c ON (g.gid=c.gid)
		WHERE g.active='1'
		ORDER BY g.".$sortby." ".$order);
		
		//Plugin
		$plugins->run_hooks("games_userstats_start");
		
		$games = array();
		$user_score = array();
		$game_champ = array();
		$member_scores = array();
		$game_scores = array();
		$stats_scores = array();
		$scores_higher = array();
		
		while($stats_games = $db->fetch_array($query))
		{
			//Plugin
			$plugins->run_hooks("games_userstats_while_start");
			
			//Games
			if(!isset($game_used[$stats_games['gid']]))
			{
				$games[$stats_games['gid']]['gid'] = $stats_games['gid'];
				$games[$stats_games['gid']]['title'] = $stats_games['title'];
				$games[$stats_games['gid']]['score_type'] = $stats_games['score_type'];
				
				$game_scores[$stats_games['gid']] = 0;
				$scores_higher[$stats_games['gid']] = 0;
				$game_used[$stats_games['gid']] = "OK";
			}
			
			//Score of user
			if(isset($stats_games['score']) && $stats_games['uid'] == $user['uid'])
			{
				$user_score[$stats_games['gid']]['gid'] = $stats_games['gid'];
				$user_score[$stats_games['gid']]['score'] = $stats_games['score'];
				$user_score[$stats_games['gid']]['dateline'] = $stats_games['dateline'];
				
				$total++;
				
				//First place counter
				if($stats_games['champ'] == $user['uid'])
				{
					$first++;
					$tenth++;
					
					$game_champ[$stats_games['gid']] = $user['uid'];
				}
			}
			elseif(isset($stats_games['score']))
			{
				$member_scores[$stats_games['gid']][$stats_games['sid']]['score'] = $stats_games['score'];
				$member_scores[$stats_games['gid']][$stats_games['sid']]['dateline'] = $stats_games['dateline'];
			}
			
			//Score counter
			if(isset($stats_games['score']))
			{
				$game_scores[$stats_games['gid']]++;
			}
			
			//Plugin
			$plugins->run_hooks("games_userstats_while_end");
		}
		
		//Read the scores
		foreach($member_scores as $gid => $array)
		{
			foreach($member_scores[$gid] as $sid => $array)
			{
				if($games[$gid]['score_type'] == "DESC")
				{
					if($member_scores[$gid][$sid]['score'] > $user_score[$gid]['score'])
					{
						$scores_higher[$gid]++;
					}
					elseif($member_scores[$gid][$sid]['score'] == $user_score[$gid]['score'] && $member_scores[$gid][$sid]['dateline'] < $user_score[$gid]['dateline'])
					{
						$scores_higher[$gid]++;
					}
				}
				elseif($games[$gid]['score_type'] == "ASC")
				{
					if($stats_scores[$gid][$sid]['score'] < $user_score[$gid]['score'])
					{
						$scores_higher[$gid]++;
					}
					elseif($member_scores[$gid][$sid]['score'] == $user_score[$gid]['score'] && $member_scores[$gid][$sid]['dateline'] < $user_score[$gid]['dateline'])
					{
						$scores_higher[$gid]++;
					}
				}
			}
		}
		
		//Plugin
		$plugins->run_hooks("games_search_middle");
		
		//Read the arrays
		$count = 0;
		foreach($games as $gid => $array)
		{
			$count++;
			
			//Data of user
			if($scores_higher[$gid] == 1 && isset($user_score[$gid]['score']))
			{
				$second++;
				$tenth++;
			}
			elseif($scores_higher[$gid] == 2 && isset($user_score[$gid]['score']))
			{
				$thirth++;
				$tenth++;
			}
			elseif($scores_higher[$gid] <= 9 && isset($user_score[$gid]['score']) && $scores_higher[$gid] != 0)
			{
				$tenth++;
			}
			
			//Output only when in page
			if($count > $start && $count <= $end)
			{
				//Rows
				if($bgcolor == "trow1")
				{
					$bgcolor = "trow2";
				}
				else
				{
					$bgcolor = "trow1";
				}
			
				//Plugin
				$plugins->run_hooks("games_userstats_foreach_start");
			
				//Title
				$games[$gid]['title'] = htmlspecialchars_uni($games[$gid]['title']);
			
				//Has the user a score
				if(!isset($user_score[$gid]['score']))
				{
					$score = "<strong>".$lang->na."</strong>";
					$rank = "<strong>".$lang->na."</strong>";
					$pubdate = "<strong>".$lang->na."</strong>";
					$tottime = "<strong>".$lang->na."</strong>";
				}
				else
				{
					//Score
					$score = my_number_format(floatval($user_score[$gid]['score']));
				
					//Rank
					$rank = 1+$scores_higher[$gid];
				
					//Dateline to normal format
					$pubdate = my_date($mybb->settings['dateformat'], $user_score[$gid]['dateline']).", ".my_date($mybb->settings['timeformat'], $user_score[$gid]['dateline']);
				
					//Total time
					$date = TIME_NOW;
				
					$tottime = $date-$user_score[$gid]['dateline'];
				
					if($games_core->settings['stats_short'] == 0)
					{
						$tottime = nice_time($tottime);
					}
					else
					{
						$tottime = nice_time_gs($tottime);
					}
				}
			
				//Are their no scores
				if($game_scores[$gid] == 0)
				{
					$scores = "";
					$slash = "";
				}
				else
				{
					$scores = $game_scores[$gid];
					$slash = "/";
				}
			
				//Plugin
				$plugins->run_hooks("games_userstats_foreach_end");
			
				eval("\$user_stats_bit .= \"".$games_core->template("games_user_stats_bit")."\";");
			}
		}
		
		//Best Player Ranking
		if($games_core->settings['stats_bestplayers'] == 1)
		{
			$query = $db->query("SELECT u.uid, u.username, u.avatar, COUNT(c.gid) AS champs
			FROM ".TABLE_PREFIX."games_champions c
			LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=c.uid)
			LEFT JOIN ".TABLE_PREFIX."games g ON (c.gid=g.gid)
			WHERE g.active='1'".$where_cat."
			GROUP BY u.uid
			ORDER BY champs DESC, c.dateline ASC
			LIMIT 0,100");
			$top100rank = 0;
			$done = 0;
			while($bestplayers = $db->fetch_array($query))
			{
				if(!$done)
				{
					$top100rank++;
					if($bestplayers['uid'] == $user['uid'])
					{
						$done = 1;
					}
				}
			}
			
			if($done == 0)
			{
				$top100rank = $lang->na;
			}
			
			eval("\$user_stats_bestplayers = \"".$games_core->template('games_user_stats_bestplayers')."\";");
		}
		
		//Tournament stats box
		if($games_core->settings['tournaments_activated'] == 1)
		{
			//Load tournaments
			$query = $db->query("SELECT p.tid, p.uid, t.champion
			FROM ".TABLE_PREFIX."games_tournaments_players p
			LEFT JOIN ".TABLE_PREFIX."games_tournaments t ON (p.tid=t.tid)
			LEFT JOIN ".TABLE_PREFIX."games g ON (t.gid=g.gid)
			WHERE p.uid='".$user['uid']."' AND p.rid='1' AND g.active='1'");
			$tournamentswon = 0;
			$tournamentsjoined = $db->num_rows($query);
			while($tournaments = $db->fetch_array($query))
			{
				if($tournaments['champion'] == $user['uid'])
				{
					$tournamentswon++;
				}
			}
			
			eval("\$user_stats_tournaments = \"".$games_core->template('games_user_stats_tournaments')."\";");
		}
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugin
		$plugins->run_hooks("games_userstats_end");
		
		eval("\$user_stats = \"".$games_core->template('games_user_stats')."\";");
		output_page($user_stats);
	break;
	case 'scores':
		//Control page
		if(intval($mybb->input['page']))
		{
			$page = intval($mybb->input['page']);
		}
		else
		{
			$page = 1;
		}
		
		//Control game
		$gid = intval($mybb->input['gid']);
		
		$query = $db->query("SELECT g.*, f.fid
		FROM ".TABLE_PREFIX."games g
		LEFT JOIN ".TABLE_PREFIX."games_favourites f ON (g.gid=f.gid AND f.uid='".$mybb->user['uid']."')
		WHERE g.gid='".$gid."' AND g.active='1'
		LIMIT 0,1");
		$game_test = $db->num_rows($query);
		$game = $db->fetch_array($query);
		
		if($game_test == 0)
		{
			error($lang->gamedoesntexist, $lang->error);
		}
		
		//Load parser
		require_once MYBB_ROOT."inc/class_parser.php";
		$parser = new postParser; 
		
		//Title and description
		$game['title'] = htmlspecialchars_uni($game['title']);
		$game['description'] = htmlspecialchars_uni($game['description']);
		
		//Language
		$lang->highscores = $lang->sprintf($lang->highscores, $game['title']);
		$lang->play_game = $lang->sprintf($lang->play_game, $game['title']);
		
		//Navigation
		add_breadcrumb($lang->highscores);
		
		//Favourite
		if($mybb->user['uid'] != 0)
		{
			if(!isset($game['fid']))
			{
				eval("\$game_favourite = \"".$games_core->template("games_scores_favourite_add")."\";");
			}
			else
			{
				eval("\$game_favourite = \"".$games_core->template("games_scores_favourite_delete")."\";");
			}
		}
		
		//Multipages
		$perpage = $maxscores;
		
		$start = ($page-1) * $perpage;
		
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_scores WHERE gid='".$gid."'");
		$count = $db->num_rows($query);
		
		$pages = $count / $perpage;
		$pages = ceil($pages);
		
		if($pages > 1)
		{
			$multipage = multipage($count, $perpage, $page, "games.php?action=scores&gid=".$gid);
			
			eval("\$multipages = \"".$games_core->template("games_multipages")."\";");
		}
		
		//Test scores
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_scores WHERE gid='".$gid."' ORDER BY score ".$game['score_type'].", dateline ASC LIMIT ".$start.",".$maxscores);
		$scores_test = $db->num_rows($query);
		
		//Plugin
		$plugins->run_hooks("games_scores_start");
		
		if($scores_test == 0)
		{
			error($lang->noscores, $lang->error);
		}
		
		//Plus
		$plus = ($maxscores * $page)-$maxscores;
		
		while($scores = $db->fetch_array($query))
		{
			//Rows
			if($bgcolor == "trow1")
			{
				$bgcolor = "trow2";
			}
			else
			{
				$bgcolor = "trow1";
			}
			
			//Rank
			$counter++;
			$rank = $counter+$plus;
			
			//Pubdate
			$pubdate = my_date($mybb->settings['dateformat'], $scores['dateline']).", ".my_date($mybb->settings['timeformat'], $scores['dateline']);
			
			//Score
			$scores['score'] = my_number_format(floatval($scores['score']));
			
			//Comment
			if(!empty($scores['comment']))
			{
				$scores['comment'] = $parser->parse_html($scores['comment']);
				$scores['comment'] = $parser->parse_badwords($scores['comment']);
				$scores['comment'] = $parser->parse_smilies($scores['comment']);
			}
			
			//Plugin
			$plugins->run_hooks("games_scores_while");
			
			eval("\$scores_bit .= \"".$games_core->template("games_scores_bit")."\";");
		}
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugin
		$plugins->run_hooks("games_scores_end");
		
		eval("\$scorespage = \"".$games_core->template('games_scores')."\";");
		output_page($scorespage);
	break;
	case 'do_newscore':
		//Control if the user is logged in
		if($mybb->user['uid'] == 0)
		{
			error_no_permission();
		}
		
		$gid = intval($mybb->input['gid']);
		$page = intval($mybb->input['page']);
		
		if(!isset($mybb->input['gid']) || !isset($mybb->input['page']) || !isset($mybb->input['comment']))
		{
			error($lang->noinput, $lang->error);
		}
		else
		{
			//Test game
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE gid='".$gid."' AND active='1' LIMIT 0,1");
			$game_test = $db->num_rows($query);
			
			if($game_test == 0)
			{
				error($lang->gamedoesntexist, $lang->error);
			}
			
			//Test score
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_scores WHERE gid='".$gid."' AND uid='".$mybb->user['uid']."' LIMIT 0,1");
			$score = $db->fetch_array($query);
			$score_test = $db->num_rows($query);
			
			if($score_test == 0)
			{
				error($lang->scoredoesntexist, $lang->error);
			}
			
			//Insert/update comment
			$update_comment = array(
				'comment'		=> $db->escape_string($mybb->input['comment']),
			);
			
			$db->update_query("games_scores", $update_comment, "gid='".$gid."' AND uid='".$mybb->user['uid']."'");
			
			//Plugin
			$plugins->run_hooks("games_scores_end");
			
			//Advanced Last Champions
			if($games_core->settings['stats_lastchamps_advanced'] == 1)
			{
				//Test champ
				$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_champions WHERE gid='".$gid."' AND uid='".$mybb->user['uid']."' LIMIT 0,1");
				$champ_test = $db->num_rows($query);
				
				if($champ_test == 1 && is_array($lastchamps[$score['dateline'].".".$gid]))
				{
					$lastchamps = $cache->read("games_lastchamps");
					$lastchamps[$score['dateline'].".".$gid]['comment'] = $mybb->input['comment'];
					$cache->update("games_lastchamps", $lastchamps);
				}
			}
			
			redirect("games.php?action=scores&gid=".$gid."&page=".$page, $lang->commentsaved);
		}
	break;
	case 'newscore':
		//Control if the user is logged in
		if($mybb->user['uid'] == 0)
		{
			error_no_permission();
		}
		
		$gid = intval($mybb->input['gid']);
		$score = intval($mybb->input['score']);
		
		//Control game
		$query = $db->query("SELECT g.*, f.fid
		FROM ".TABLE_PREFIX."games g
		LEFT JOIN ".TABLE_PREFIX."games_favourites f ON (g.gid=f.gid AND f.uid='".$mybb->user['uid']."')
		WHERE g.gid='".$gid."' AND g.active='1'
		LIMIT 0,1");
		$game_test = $db->num_rows($query);
		$game = $db->fetch_array($query);
		
		if($game_test == 0)
		{
			error($lang->gamedoesntexist, $lang->error);
		}
		
		//Control page
		if(!intval($mybb->input['page']) && intval($mybb->input['score']))
		{
			$scores_higher = 1;
			
			//Loading scores
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_scores WHERE gid='".$gid."'");
			$count = $db->num_rows($query);
			
			while($scores_page = $db->fetch_array($query))
			{
				if($game['score_type'] == "DESC" && $scores_page['score'] > $score)
				{
					$scores_higher++;
				}
				elseif($game['score_type'] == "ASC" && $scores_page['score'] < $score)
				{
					$scores_higher++;
				}
				elseif($scores_page['score'] == $score && $scores_page['dateline'] < TIME_NOW)
				{
					$scores_higher++;
				}
			}
			
			//Page
			$page = $scores_higher / $maxscores;
			$page = ceil($page);
		}
		else
		{
			//Loading scores
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_scores WHERE gid='".$gid."'");
			$count = $db->num_rows($query);
			
			$page = intval($mybb->input['page']);
		}
		
		//Load parser
		require_once MYBB_ROOT."inc/class_parser.php";
		$parser = new postParser; 
		
		//Title and description
		$game['title'] = htmlspecialchars_uni($game['title']);
		$game['description'] = htmlspecialchars_uni($game['description']);
		
		//Language
		$lang->highscores = $lang->sprintf($lang->highscores, $game['title']);
		$lang->play_game = $lang->sprintf($lang->play_game, $game['title']);
		
		//Navigation
		add_breadcrumb($lang->highscores);
		
		//Favourite
		if($mybb->user['uid'] != 0)
		{
			if(!isset($game['fid']))
			{
				eval("\$game_favourite = \"".$games_core->template("games_scores_favourite_add")."\";");
			}
			else
			{
				eval("\$game_favourite = \"".$games_core->template("games_scores_favourite_delete")."\";");
			}
		}
		
		//Multipages
		$perpage = $maxscores;
		
		$start = ($page-1) * $perpage;
		
		$pages = $count / $perpage;
		$pages = ceil($pages);
		
		if($pages > 1)
		{
			$multipage = multipage($count, $perpage, $page, "games.php?action=newscore&gid=".$gid);
			
			eval("\$multipages = \"".$games_core->template("games_multipages")."\";");
		}
		
		//Test scores
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_scores WHERE gid='".$gid."' ORDER BY score ".$game['score_type'].", dateline ASC LIMIT ".$start.",".$maxscores);
		$scores_test = $db->num_rows($query);
		
		//Plugin
		$plugins->run_hooks("games_newscore_start");
		
		if($scores_test == 0)
		{
			error($lang->noscores, $lang->error);
		}
		
		//Plus
		$plus = ($maxscores * $page)-$maxscores;
		
		while($scores = $db->fetch_array($query))
		{
			//Rows
			if($bgcolor == "trow1")
			{
				$bgcolor = "trow2";
			}
			else
			{
				$bgcolor = "trow1";
			}
			
			//Rank
			$counter++;
			$rank = $counter+$plus;
			
			//Pubdate
			$pubdate = my_date($mybb->settings['dateformat'], $scores['dateline']).", ".my_date($mybb->settings['timeformat'], $scores['dateline']);
			
			//Score
			$scores['score'] = my_number_format(floatval($scores['score']));
			
			//Add coment bar
			$test_new = TIME_NOW - 25;
			
			if($test_new <= $scores['dateline'] && $scores['uid'] == $mybb->user['uid'])
			{
				eval("\$scores['comment'] = \"".$games_core->template('games_scores_newcomment')."\";");
			}
			else
			{
				if(!empty($scores['comment']))
				{
					$scores['comment'] = $parser->parse_html($scores['comment']);
					$scores['comment'] = $parser->parse_badwords($scores['comment']);
					$scores['comment'] = $parser->parse_smilies($scores['comment']);
				}
			}
			
			//Plugin
			$plugins->run_hooks("games_newscore_while");
			
			eval("\$scores_bit .= \"".$games_core->template("games_scores_bit")."\";");
		}
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugin
		$plugins->run_hooks("games_newscore_end");
		
		eval("\$scorespage = \"".$games_core->template('games_scores')."\";");
		output_page($scorespage);
	break;
	case 'last_champs':
		//Load parser
		require_once MYBB_ROOT."inc/class_parser.php";
		$parser = new postParser; 
		
		//Navigation
		add_breadcrumb($lang->last_champions);
		
		//Load champions
		$lastchamps = $cache->read("games_lastchamps");
		
		//Plugin
		$plugins->run_hooks("games_champs_start");
		
		if(!is_array($lastchamps))
		{
			error($lang->nochamps, $lang->error);
		}
		
		foreach($lastchamps as $id => $champ)
		{
			//Rows
			if($bgcolor == "trow1")
			{
				$bgcolor = "trow2";
			}
			else
			{
				$bgcolor = "trow1";
			}
			
			//Pubdate
			$pubdate = my_date($mybb->settings['dateformat'], $champ['dateline']).", ".my_date($mybb->settings['timeformat'], $champ['dateline']);
			
			//Score
			$champ['score'] = my_number_format(floatval($champ['score']));
			
			//Comment
			if(!empty($champ['comment']))
			{
				$champ['comment'] = $parser->parse_html($champ['comment']);
				$champ['comment'] = $parser->parse_badwords($champ['comment']);
				$champ['comment'] = $parser->parse_smilies($champ['comment']);
			}
			
			//Plugin
			$plugins->run_hooks("games_champs_while");
			
			eval("\$champs_bit .= \"".$games_core->template("games_champs_bit")."\";");
		}
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugin
		$plugins->run_hooks("games_champs_end");
		
		eval("\$last_champs = \"".$games_core->template('games_champs')."\";");
		output_page($last_champs);
	break;
	case 'do_settings':
		//Test user
		if($mybb->user['uid'] == 0)
		{
			error_no_permission();
		}
		elseif($mybb->usergroup['canusercp'] == "no")
		{
			error_no_permission();
		}
		
		if(!isset($mybb->input['maxgames']) || !isset($mybb->input['sortby']) || !isset($mybb->input['order']) || !isset($mybb->input['maxscores']) || !isset($mybb->input['theme']))
		{
			redirect("games.php?action=settings", $lang->noinput);
		}
		else
		{
			$do_settings = array(
				'games_maxgames'		=> intval($mybb->input['maxgames']),
				'games_maxscores'		=> intval($mybb->input['maxscores']),
				'games_sortby'			=> $db->escape_string($mybb->input['sortby']),
				'games_order'			=> $db->escape_string($mybb->input['order']),
				'games_theme'			=> intval($mybb->input['theme']),
				'games_tournamentnotify'	=> intval($mybb->input['tournamentnotify'])
			);
			
			$plugins->run_hooks("games_do_settings");
			
			$db->update_query("users", $do_settings, "uid='".$mybb->user['uid']."'");
			
			redirect("games.php?action=settings", $lang->settingssaved);
		}
	break;
	case 'settings':
		//Test user
		if($mybb->user['uid'] == 0)
		{
			error_no_permission();
		}
		elseif($mybb->usergroup['canusercp'] == "no")
		{
			error_no_permission();
		}
		
		//Load the needed stylesheets
		if(is_array($theme['stylesheets']['usercp.php']['global']))
		{
			foreach($theme['stylesheets']['usercp.php']['global'] as $page_stylesheet)
			{
				if($already_loaded[$page_stylesheet])
				{
					continue;
				}
				
				$options_stylesheets .= "<link type=\"text/css\" rel=\"stylesheet\" href=\"".$mybb->settings['bburl']."/".$page_stylesheet."\" />\n";
				$already_loaded[$page_stylesheet] = 1;
			}
		}
		
		//Loading the UserCP menu
		require_once MYBB_ROOT."inc/functions_user.php";
		
		usercp_menu();
		
		//Navigation
		add_breadcrumb($lang->editsettings);
		
		//User
		$user = $mybb->user;
		
		//Plugins
		$plugins->run_hooks("games_settings_start");
		
		//Max games per page
		$explode_maxgames = explode(",", $games_core->settings['set_maxgames']);
		
		if(is_array($explode_maxgames))
		{
			foreach($explode_maxgames as $key => $val)
			{
				$val = trim($val);
				
				if($user['maxgames'] == $val)
				{
					$selected = " selected=\"selected\"";
				}
				else
				{
					$selected = "";
				}
				
				$options_maxgames .= "<option value=\"".$val."\"".$selected.">".$lang->sprintf($lang->option_maxgames_sen, $val)."</option>\n";
			}
		}
		
		eval("\$select_maxgames = \"".$games_core->template("games_user_settings_maxgames")."\";");
		
		//Sort by
		$select_sortby[$user['games_sortby']] = " selected=\"selected\"";
		
		//Order
		$select_order[$user['games_order']] = " selected=\"selected\"";
		
		//Maximum scores per page
		$explode_maxscores = explode(",", $games_core->settings['set_maxscores']);
		
		if(is_array($explode_maxscores))
		{
			foreach($explode_maxscores as $key => $val)
			{
				$val = trim($val);
				
				if($user['maxscores'] == $val)
				{
					$selected = " selected=\"selected\"";
				}
				else
				{
					$selected = "";
				}
				
				$options_maxscores .= "<option value=\"".$val."\"".$selected.">".$lang->sprintf($lang->option_maxscores_sen, $val)."</option>\n";
			}
		}
		
		eval("\$select_maxscores = \"".$games_core->template("games_user_settings_maxscores")."\";");
		
		//Themes
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE active='1'");
		
		while($themes = $db->fetch_array($query))
		{
			if($themes['tid'] == $mybb->user['games_theme'])
			{
				$selected = " selected=\"selected\"";
			}
			else
			{
				$selected = "";
			}
			
			eval("\$select_themes .= \"".$games_core->template('games_user_settings_themes')."\";");
		}
		
		//Tournament Options
		if($games_core->settings['tournaments_activated'] == 1 && $mybb->usergroup['canplaytournaments'] == 1)
		{
			//Tournament Notify
			if($mybb->user['games_tournamentnotify'] != 0)
			{
				$tournamentnotifycheck = "checked=\"checked\"";
			}
			else
			{
				$tournamentnotifycheck = '';
			}
			
			eval("\$tournament_settings = \"".$games_core->template('games_user_settings_tournaments')."\";");
		}
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugins
		$plugins->run_hooks("games_settings_end");
		
		eval("\$user_settings = \"".$games_core->template('games_user_settings')."\";");
		output_page($user_settings);
	break;
}
?>
