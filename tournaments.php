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

//Define MyBB and includes
define("IN_MYBB", 1);

require_once "./global.php";
require_once MYBB_ROOT."games/global.php";

//Control if tournaments system is activated
if($games_core->settings['tournaments_activated'] == 0)
{
	error_no_permission();
}

//Plugin
$plugins->run_hooks("games_tournaments_start");

switch($mybb->input['action'])
{
	default:
		//Test if status is selected
		if($mybb->input['status'] != "open" && $mybb->input['status'] != "started" && $mybb->input['status'] != "finished")
		{
			error($lang->noinput, $lang->error);
		}
		
		//Navigation
		$lang_string = "tournaments_".$mybb->input['status'];
		add_breadcrumb($lang->$lang_string);
		
		//Loading tournaments
		$query = $db->query("SELECT DISTINCT t.tid, t.gid, t.dateline, t.rounds, t.joinedplayers, t.status, t.champion, t.roundinformation, g.title, u.username
		FROM ".TABLE_PREFIX."games_tournaments t
		LEFT JOIN ".TABLE_PREFIX."games g ON (t.gid=g.gid)
		LEFT JOIN ".TABLE_PREFIX."users u ON (t.champion=u.uid)
		WHERE t.status='".$db->escape_string($mybb->input['status'])."' AND g.active='1'
		ORDER BY t.dateline DESC");
		$tournaments_test = $db->num_rows($query);
		
		//Plugin
		$plugins->run_hooks("games_tournaments_default_start");
		
		//No tournaments
		if($tournaments_test == 0)
		{
			error($lang->no_tournaments, $lang->error);
		}
		
		while($tournaments = $db->fetch_array($query))
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
			
			//Load roundinformation
			$tournaments['roundinformation'] = unserialize($tournaments['roundinformation']);
			
			//Free places
			$tournaments['maxplayers'] = pow(2, $tournaments['rounds']);
			if($tournaments['status'] == "open")
			{
				$freeplaces = $tournaments['maxplayers']-$tournaments['joinedplayers'];
			}
			
			//Champion
			if($tournaments['champion'] != 0)
			{
				$tournaments['champion'] = "<a href=\"games.php?action=stats&uid=".$tournaments['champion']."\">".$tournaments['username']."</a>";
			}
			else
			{
				$tournaments['champion'] = "<strong>".$lang->na."</strong>";
			}
			
			//Handle dates
			if($tournaments['status'] == "open")
			{
				$addeddate = my_date($mybb->settings['dateformat'], $tournaments['dateline']).", ".my_date($mybb->settings['timeformat'], $tournaments['dateline']);
			}
			elseif($tournaments['status'] == "started")
			{
				$starteddate = my_date($mybb->settings['dateformat'], $tournaments['dateline']).", ".my_date($mybb->settings['timeformat'], $tournaments['roundinformation']['1']['starttime']);
			}
			elseif($tournaments['status'] == "finished")
			{
				$endeddate = my_date($mybb->settings['dateformat'], $tournaments['dateline']).", ".my_date($mybb->settings['timeformat'], $tournaments['roundinformation'][$tournaments['rounds']]['endtime']);
			}
			
			//Plugin
			$plugins->run_hooks("games_tournaments_default_while");
			
			eval("\$tournaments_bit .= \"".$games_core->template("games_tournaments_".$mybb->input['status']."_bit")."\";");
		}
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugin
		$plugins->run_hooks("games_tournaments_default_end");
		
		eval("\$tournaments_page = \"".$games_core->template("games_tournaments_".$mybb->input['status'])."\";");
		output_page($tournaments_page);
	break;
	case 'do_add':
		$gid = intval($mybb->input['gid']);
		
		//Test user
		if($mybb->usergroup['canaddtournaments'] == 0 && $mybb->user['uid'] == 0)
		{
			error_no_permission();
		}
		
		if(!intval($mybb->input['gid']) || !intval($mybb->input['rounds']) || !intval($mybb->input['roundtime']) || !intval($mybb->input['maxtries']))
		{
			error($lang->noinput, $lang->error);
		}
		else
		{
			//Test game
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE gid='".$gid."' AND active='1' LIMIT 0,1");
			$game = $db->fetch_array($query);
			$game_test = $db->num_rows($query);
			
			if($game_test == 0)
			{
				error($lang->gamedoesntexist, $lang->error);
			}
			
			//Control maximum tries
			if(intval($mybb->input['maxtries']) < 1 || intval($mybb->input['maxtries']) > 99)
			{
				error($lang->tournaments_maxtries_desc, $lang->error);
			}
			
			//Input of tournament
			$add_tournament = array(
				'gid'			=> intval($mybb->input['gid']),
				'dateline'		=> TIME_NOW,
				'rounds'		=> intval($mybb->input['rounds']),
				'roundtime'		=> intval($mybb->input['roundtime'])*86400,
				'maxtries'		=> intval($mybb->input['maxtries']),
				'joinedplayers'		=> intval("1")
			);
			
			$tid = $db->insert_query("games_tournaments", $add_tournament);
			
			//Input of player
			$add_player = array(
				'tid'			=> intval($tid),
				'rid'			=> intval("1"),
				'uid'			=> intval($mybb->user['uid']),
				'username'		=> $db->escape_string($mybb->user['username'])
			);
			
			//Plugins
			$plugins->run_hooks("games_tournaments_do_add");
			
			$db->insert_query("games_tournaments_players", $add_player);
			
			//Update tournament statistics
			$tournaments_stats = $cache->read("games_tournaments_stats");
			$tournaments_stats['open']++;
			$cache->update("games_tournaments_stats", $tournaments_stats);
			
			redirect("tournaments.php?status=open", $lang->added_tournament);
		}
	break;
	case 'add':
		//Test user
		if($mybb->usergroup['canaddtournaments'] == 0 && $mybb->user['uid'] == 0)
		{
			error_no_permission();
		}
		
		//Test game
		if(intval($mybb->input['gid']))
		{
			$gid = intval($mybb->input['gid']);
			
			//Test game
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE gid='".$gid."'");
			$game = $db->fetch_array($query);
			$game_test = $db->num_rows($query);
			
			if($game_test == 0)
			{
				error($lang->gamedoesntexist, $lang->error);
			}
			
			eval("\$tournament_game = \"".$games_core->template('games_tournaments_add_game_set')."\";");
		}
		else
		{
			eval("\$tournament_game = \"".$games_core->template('games_tournaments_add_game')."\";");
		}
		
		//Navigation
		add_breadcrumb($lang->add_tournament);
		
		//Plugins
		$plugins->run_hooks("games_tournaments_add_start");
		
		//Rounds
		$explode_rounds = explode(",", $games_core->settings['tournaments_set_rounds']);
		
		if(is_array($explode_rounds))
		{
			foreach($explode_rounds as $key => $val)
			{
				$val = trim($val);
				
				$tournaments_rounds_sen = $lang->sprintf($lang->tournaments_rounds_sen, $val);
				eval("\$rounds_bit .= \"".$games_core->template("games_tournaments_add_rounds_bit")."\";");
			}
		}
		
		//Roundtime
		$explode_roundtime = explode(",", $games_core->settings['tournaments_set_roundtime']);
		
		if(is_array($explode_roundtime))
		{
			foreach($explode_roundtime as $key => $val)
			{
				$val = trim($val);
				
				$tournaments_roundtime_sen = $lang->sprintf($lang->tournaments_roundtime_sen, $val);
				eval("\$roundtime_bit .= \"".$games_core->template("games_tournaments_add_roundtime_bit")."\";");
			}
		}
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugins
		$plugins->run_hooks("games_tournaments_add_end");
		
		eval("\$add_tournament = \"".$games_core->template('games_tournaments_add')."\";");
		output_page($add_tournament);
	break;
	case 'view':
		$tid = intval($mybb->input['tid']);
		
		//Test tournament
		$query = $db->query("SELECT DISTINCT t.tid, t.gid, t.dateline, t.rounds, t.roundtime, t.maxtries, t.joinedplayers, t.status, t.champion, t.roundinformation, g.title, g.score_type, p.uid, p.username, p.score, u.username AS championname
		FROM ".TABLE_PREFIX."games_tournaments t
		LEFT JOIN ".TABLE_PREFIX."games g ON (t.gid=g.gid)
		LEFT JOIN ".TABLE_PREFIX."games_tournaments_players p ON (t.tid=p.tid AND t.rounds=p.rid AND t.champion=p.uid)
		LEFT JOIN ".TABLE_PREFIX."users u ON (t.champion=u.uid)
		WHERE t.tid='".intval($mybb->input['tid'])."' AND g.active='1'");
		$tournament = $db->fetch_array($query);
		$tournament_test = $db->num_rows($query);
		
		if($tournament_test == 0)
		{
			error($lang->tournamentdoesntexist, $lang->error);
		}
		
		//Test game
		if(empty($tournament['title']))
		{
			error($lang->gamedoesntexist, $lang->error);
		}
		
		//Navigation
		$lang->view_tournament = $lang->sprintf($lang->view_tournament, $tournament['title'], my_date($mybb->settings['dateformat'], $tournament['dateline']).", ".my_date($mybb->settings['timeformat'], $tournament['dateline']));
		add_breadcrumb($lang->view_tournament);
		
		//Load roundinformation
		$tournament['roundinformation'] = unserialize($tournament['roundinformation']);
		
		//Informationbox
		$lang->play_game = $lang->sprintf($lang->play_game, $tournament['title']);
		$pubdate = my_date($mybb->settings['dateformat'], $tournament['dateline']).", ".my_date($mybb->settings['timeformat'], $tournament['dateline']);
		$tournament['roundtime'] = $tournament['roundtime']/(60*60*24);
		$tournament['maxplayers'] = pow(2, $tournament['rounds']);
		
		if($tournament['champion'] != 0)
		{
			$tournament['champion'] = "<a href=\"games.php?action=stats&uid=".$tournament['champion']."\">".$tournament['championname']."</a>";
		}
		else
		{
			$tournament['champion'] = $lang->na;
		}
		
		//Status specific information
		if($tournament['status'] == "open")
		{
			//Join link
			if($mybb->usergroup['canplaytournaments'] == 1 && $mybb->user['uid'] != 0 && $tournament['joinedplayers'] < $tournament['maxplayers'])
			{
				//Test player table
				$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_tournaments_players WHERE tid='".intval($mybb->input['tid'])."' AND uid='".intval($mybb->user['uid'])."'");
				$player_test = $db->num_rows($query);
				
				if($player_test == 0)
				{
					$tournament_joinlink = "<a href=\"tournaments.php?action=join&amp;tid=".$tournament['tid']."\">".$lang->join_tournament."</a><br />";
				}
			}
			
			$freeplaces = $tournament['maxplayers']-$tournament['joinedplayers'];
		}
		elseif($tournament['status'] == "started")
		{
			$startdate = my_date($mybb->settings['dateformat'], $tournament['roundinformation']['1']['starttime']).", ".my_date($mybb->settings['timeformat'], $tournament['roundinformation']['1']['starttime']);
			
			//Play link
			if($mybb->usergroup['canplaytournaments'] == 1 && $mybb->user['uid'] != 0)
			{
				//Test player table
				$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_tournaments_players WHERE tid='".intval($mybb->input['tid'])."' AND rid='".count($tournament['roundinformation'])."' AND uid='".$mybb->user['uid']."'");
				$player_test = $db->num_rows($query);
				
				if($player_test == 1)
				{
					$tournament_playlink = "<a href=\"games.php?action=play&amp;gid=".$tournament['gid']."&amp;tid=".$tournament['tid']."\">".$lang->play_tournament."</a><br />";
				}
			}
		}
		elseif($tournament['status'] == "finished")
		{
			$startdate = my_date($mybb->settings['dateformat'], $tournament['roundinformation']['1']['starttime']).", ".my_date($mybb->settings['timeformat'], $tournament['roundinformation']['1']['starttime']);
			$enddate = my_date($mybb->settings['dateformat'], $tournament['roundinformation'][$tournament['rounds']]['endtime']).", ".my_date($mybb->settings['timeformat'], $tournament['roundinformation'][$tournament['rounds']]['endtime']);
		}
		
		eval("\$tournament_infobox = \"".$games_core->template('games_tournaments_view_infobox_'.$tournament['status'])."\";");
		
		//Colspan
		$colspan = $tournament['maxplayers']+1;
		
		//Plugins
		$plugins->run_hooks("games_tournaments_view_start");
		
		//Show Champion
		eval("\$tournament_rounds .= \"".$games_core->template('games_tournaments_view_rounds_champion')."\";");
		
		//Load Rounds
		for($rid = $tournament['rounds']; $rid > 0; $rid--)
		{
			$tournament_rounds_bit = "";
			$bgcolor = "";
			$colspan_round = intval(pow(2, ($rid-1)));
			$numplayers = $tournament['maxplayers']/$colspan_round;
			
			//Load Players
			$query = $db->query("SELECT *
			FROM ".TABLE_PREFIX."games_tournaments_players
			WHERE tid='".intval($mybb->input['tid'])."' AND rid='".$rid."'
			ORDER BY score ".$tournament['score_type'].", score_try ASC");
			$players_count = $db->num_rows($query);
			
			//Plugins
			$plugins->run_hooks("games_tournaments_view_rounds");
			
			while($players = $db->fetch_array($query))
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
				
				//Width
				$width = floor(100/$numplayers);
				
				//Username
				$players['username'] = "<a href=\"games.php?action=stats&uid=".$players['uid']."\">".$players['username']."</a>";
				
				//Information
				if($tournament['status'] == "finished" || $tournament['status'] == "started")
				{
					//Format score
					$players['score'] = my_number_format(floatval($players['score']));
					
					//Tournament tries
					$lang_tournament_tries = $lang->sprintf($lang->tournament_tries, $players['tries'], $tournament['maxtries']);
					$lang_tournament_tries_needed = $lang->sprintf($lang->tournament_tries_needed, $players['score_try']);
					
					//Pubdate
					if(!intval($players['dateline']))
					{
						$pubdate = $lang->na;
					}
					else
					{
						$pubdate = my_date($mybb->settings['dateformat'], $players['dateline']).", ".my_date($mybb->settings['timeformat'], $players['dateline']);
					}
					
					eval("\$tournament_rounds_bit_info = \"".$games_core->template('games_tournaments_view_rounds_bit_info')."\";");
				}
				
				//Plugins
				$plugins->run_hooks("games_tournaments_view_rounds_players");
				
				eval("\$tournament_rounds_bit .= \"".$games_core->template('games_tournaments_view_rounds_bit')."\";");
			}
			
			for($pid = $numplayers-$players_count; $pid > 0; $pid--)
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
				
				//Width
				$width = floor(100/$numplayers);
				
				//Player
				$players['username'] = $lang->na;
				
				//Plugins
				$plugins->run_hooks("games_tournaments_view_rounds_players_free");
				
				eval("\$tournament_rounds_bit .= \"".$games_core->template('games_tournaments_view_rounds_bit')."\";");
			}
			
			eval("\$tournament_rounds .= \"".$games_core->template('games_tournaments_view_rounds')."\";");
		}
		
		//Online
		if($games_core->settings['online'] == "every")
		{
			$online = whos_online();
		}
		
		//Plugins
		$plugins->run_hooks("games_tournaments_view_end");
		
		eval("\$tournament_view = \"".$games_core->template('games_tournaments_view')."\";");
		output_page($tournament_view);
	break;
	case 'join':
		//Test user
		if($mybb->usergroup['canplaytournaments'] == 0 && $mybb->user['uid'] == 0)
		{
			error_no_permission();
		}
		
		//Test tournament
		$query = $db->query("SELECT DISTINCT t.*, g.active, p.uid
		FROM ".TABLE_PREFIX."games_tournaments t
		LEFT JOIN ".TABLE_PREFIX."games g ON (t.gid=g.gid)
		LEFT JOIN ".TABLE_PREFIX."games_tournaments_players p ON (t.tid=p.tid AND p.uid='".$mybb->user['uid']."')
		WHERE t.tid='".intval($mybb->input['tid'])."' AND g.active='1'");
		$tournament = $db->fetch_array($query);
		$tournament_test = $db->num_rows($query);
		
		if($tournament_test == 0)
		{
			error($lang->tournamentdoesntexist, $lang->error);
		}
		
		if($tournament['joinedplayers'] >= pow(2, $tournament['rounds']))
		{
			error($lang->tournamentfull, $lang->error);
		}
		
		if($mybb->user['uid'] == $tournament['uid'])
		{
			error($lang->alreadyjoined, $lang->error);
		}
		
		//Update tournament
		$db->write_query("UPDATE ".TABLE_PREFIX."games_tournaments SET joinedplayers=joinedplayers+1 WHERE tid='".intval($mybb->input['tid'])."'");
		
		//Input of player
		$add_player = array(
			'tid'			=> intval($mybb->input['tid']),
			'rid'			=> intval("1"),
			'uid'			=> intval($mybb->user['uid']),
			'username'		=> $db->escape_string($mybb->user['username'])
		);
		
		//Plugins
		$plugins->run_hooks("games_tournaments_join");
		
		$db->insert_query("games_tournaments_players", $add_player);
		
		redirect("tournaments.php?action=view&amp;tid=".intval($mybb->input['tid']), $lang->joined_tournament);
	break;
}
?>
