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

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

//Requires
require_once MYBB_ROOT."inc/class_games.php";
$games_core = new games;

//Plugin hook
$plugins->run_hooks("games_submit_start");

//When user isn't logged in, he gets an error
if($mybb->user['uid'] == 0)
{
	error_no_permission();
}

//IBProArcade, IBProArcade v32 and vBulletin Arcade v3
if(floatval($mybb->input['gscore']) && !empty($mybb->input['gname']) && $mybb->request_method == "post")
{
	$name = $db->escape_string(stripslashes($mybb->input['gname']));
	$score = floatval($mybb->input['gscore']);
}
else
{
	error($lang->noinput, $lang->error);
}

//Session and settings
$games_core->run_settings();
$games_core->session_start();

//Control if score or tournament
if(intval($games_core->session['tid'][$name]))
{
	//Load tournament
	$query = $db->query("SELECT DISTINCT t.tid, t.gid, t.roundtime, t.maxtries, t.round, t.roundinformation, p.pid, p.uid, p.score, p.score_try, p.tries, p.dateline, g.score_type
	FROM ".TABLE_PREFIX."games_tournaments t
	LEFT JOIN ".TABLE_PREFIX."games_tournaments_players p ON (t.tid=p.tid AND t.round=p.rid AND p.uid='".$mybb->user['uid']."')
	LEFT JOIN ".TABLE_PREFIX."games g ON (t.gid=g.gid)
	WHERE t.tid='".$games_core->session['tid'][$name]."' AND t.status='started' AND g.name='".$name."' AND g.active='1'");
	$tournament = $db->fetch_array($query);
	$tournament_test = $db->num_rows($query);
	$tournament['roundinformation'] = unserialize($tournament['roundinformation']);
	
	//Test tournament
	if($tournament_test == 0)
	{
		//Delete session vars
		unset($games_core->session['randchar1'], $games_core->session['randchar2'], $games_core->session['tid'][$name]);
		$games_core->session_update();
	
		error($lang->tournamentdoesntexist, $lang->error);
	}
	if(!intval($tournament['uid']))
	{
		//Delete session vars
		unset($games_core->session['randchar1'], $games_core->session['randchar2'], $games_core->session['tid'][$name]);
		$games_core->session_update();
		
		error($lang->tournamentnotjoined, $lang->error);
	}
	if($tournament['tries'] >= $tournament['maxtries'])
	{
		//Delete session vars
		unset($games_core->session['randchar1'], $games_core->session['randchar2'], $games_core->session['tid'][$name]);
		$games_core->session_update();
		
		error($lang->tournamentmaxtriesreached, $lang->error);
	}
	if($tournament['roundinformation'][$tournament['round']]['starttime']+$tournament['roundtime'] < TIME_NOW)
	{
		//Delete session vars
		unset($games_core->session['randchar1'], $games_core->session['randchar2'], $games_core->session['tid'][$name]);
		$games_core->session_update();
		
		error($lang->tournamentroundended, $lang->error);
	}

	//Control score if IBProArcade v32
	if(is_file(MYBB_ROOT."arcade/gamedata/".$name."/v32game.txt"))
	{
		$controlscore = floatval($score * $games_core->session['randchar1'] ^ $games_core->session['randchar2']);
	
		if($mybb->input['enscore'] != $controlscore || !isset($games_core->session['randchar1']) || !isset($games_core->session['randchar2']))
		{
			//Delete session vars
			unset($games_core->session['randchar1'], $games_core->session['randchar2'], $games_core->session['tid'][$name]);
			$games_core->session_update();
		
			error($lang->cheatscore, $lang->error);
		}
	}

	//Delete session vars
	unset($games_core->session['randchar1'], $games_core->session['randchar2'], $games_core->session['tid'][$name]);
	$games_core->session_update();
	
	//Plugin
	$plugins->run_hooks("games_submit_tournaments_pre");

	//Strip ASC and DESC
	if($tournament['score_type'] == "ASC")
	{
		$score_type = ">";
	}
	else
	{
		$score_type = "<";
	}
	
	//Update the score if higher
	if(($tournament['score'] < $score && $tournament['score_type'] == "DESC") || ($tournament['score'] > $score && $tournament['score_type'] == "ASC"))
	{
		$update_score = array(
			'score'			=> floatval($score),
			'score_try'		=> intval($tournament['tries']+1),
			'tries'			=> intval($tournament['tries']+1),
			'dateline'		=> time()
		);
	
		//Plugin
		$plugins->run_hooks("games_submit_tournament_score");
	
		$db->update_query("games_tournaments_players", $update_score, "pid='".$tournament['pid']."'");
		
		redirect("tournaments.php?action=view&tid=".$tournament['tid'], $lang->scoreadded);
	}
	else
	{
		//Update tries
		$db->write_query("UPDATE ".TABLE_PREFIX."games_tournaments_players SET tries=tries+1 WHERE pid='".$tournament['pid']."'");
		
		redirect("tournaments.php?action=view&tid=".$tournament['tid'], $lang->scoredontadded);
	}
}
else
{
	//Load game
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE name='".$name."' AND gid='".$games_core->session['gid'][$name]."' AND active='1'");
	$game = $db->fetch_array($query);
	$game_test = $db->num_rows($query);

	//Test game
	if($game_test == 0)
	{
		//Delete session vars
		unset($games_core->session['randchar1'], $games_core->session['randchar2'], $games_core->session['gid'][$name]);
		$games_core->session_update();
	
		error($lang->gamedoesntexist, $lang->error);
	}

	//Control score if IBProArcade v32
	if(is_file(MYBB_ROOT."arcade/gamedata/".$name."/v32game.txt"))
	{
		$controlscore = floatval($score * $games_core->session['randchar1'] ^ $games_core->session['randchar2']);
	
		if($mybb->input['enscore'] != $controlscore || !isset($games_core->session['randchar1']) || !isset($games_core->session['randchar2']))
		{
			//Delete session vars
			unset($games_core->session['randchar1'], $games_core->session['randchar2'], $games_core->session['gid'][$name]);
			$games_core->session_update();
		
			error($lang->cheatscore, $lang->error);
		}
	}

	//Delete session vars
	unset($games_core->session['randchar1'], $games_core->session['randchar2'], $games_core->session['gid'][$name]);
	$games_core->session_update();

	//Gid
	$gid = $game['gid'];

	//Plugin
	$plugins->run_hooks("games_submit_pre");

	//Strip ASC and DESC
	if($game['score_type'] == "ASC")
	{
		$score_type = ">";
	}
	else
	{
		$score_type = "<";
	}

	//Has the user already a score
	$query2 = $db->query("SELECT * FROM ".TABLE_PREFIX."games_scores WHERE uid='".$mybb->user['uid']."' AND gid='".$gid."' ORDER BY score ".$game['score_type']." LIMIT 0,1");
	$old_score = $db->fetch_array($query2);
	$score_test = $db->num_rows($query2);

	if($score_test != 0)
	{
		//Yes, update him
		if(($old_score['score'] < $score && $game['score_type'] == "DESC") || ($old_score['score'] > $score && $game['score_type'] == "ASC"))
		{
			$update_score = array(
				'gid'			=> intval($gid),
				'uid'			=> intval($mybb->user['uid']),
				'username'		=> $db->escape_string($mybb->user['username']),
				'score'			=> floatval($score),
				'dateline'		=> time(),
				'ip'			=> get_ip()
			);
		
			$db->update_query("games_scores", $update_score, "gid='".$gid."' AND uid='".$mybb->user['uid']."'");
		
			//Plugin
			$plugins->run_hooks("games_submit_update_score");
		}
		else
		{
			redirect("games.php?action=scores&gid=".$gid, $lang->scoredontadded);
		}
	}
	else
	{
		//No, insert the score then
		$insert_score = array(
			'sid'			=> NULL,
			'gid'			=> intval($gid),
			'uid'			=> intval($mybb->user['uid']),
			'username'		=> $db->escape_string($mybb->user['username']),
			'score'			=>floatval($score),
			'dateline'		=> time(),
			'ip'			=> get_ip()
		);
	
		$db->insert_query("games_scores", $insert_score);
	
		//Plugin
		$plugins->run_hooks("games_submit_insert_score");
	}

	//Champion
	//Is there aleady a champion
	$query3 = $db->query("SELECT * FROM ".TABLE_PREFIX."games_champions WHERE gid='".$gid."'");
	$champ = $db->fetch_array($query3);
	$champ_test = $db->num_rows($query3);

	if($champ_test != 0)
	{
		if(($champ['score'] < $score && $game['score_type'] == "DESC") || ($champ['score'] > $score && $game['score_type'] == "ASC"))
		{
			//Yes, update the champion
			$update_champ = array(
				'uid'			=> intval($mybb->user['uid']),
				'username'		=> $db->escape_string($mybb->user['username']),
				'score'			=> floatval($score),
				'dateline'		=> TIME_NOW
			);
		
			$db->update_query("games_champions", $update_champ, "gid='".$gid."'");
		
			//Advanced Last Champions
			if($games_core->settings['stats_lastchamps_advanced'] == 1)
			{
				global $cache;
			
				//Load Last Champions
				$lastchamps = $cache->read("games_lastchamps");
			
				$lastchamps[TIME_NOW.".".$gid] = array(
					'gid'		=> $gid,
					'title'		=> $game['title'],
					'uid'		=> $mybb->user['uid'],
					'username'	=> $mybb->user['username'],
					'score'		=> floatval($score),
					'dateline'	=> TIME_NOW
				);
			
				//Only hold the number of last champions as in the settings said
				krsort($lastchamps);
				$lastchamps = array_chunk($lastchamps, $games_core->settings['stats_lastchamps_advanced_max'], true);
				$lastchamps = $lastchamps[0];
			
				$cache->update("games_lastchamps", $lastchamps);
			}
		
			//Plugin
			$plugins->run_hooks("games_submit_update_champ");
		}
	}
	else
	{
		//No, insert the champ
		$insert_champ = array(
			'gid'			=> intval($gid),
			'title'			=> $db->escape_string($game['title']),
			'uid'			=> intval($mybb->user['uid']),
			'username'		=> $db->escape_string($mybb->user['username']),
			'score'			=> floatval($score),
			'dateline'		=> TIME_NOW
		);
	
		$db->insert_query("games_champions", $insert_champ);
	
		//Advanced Last Champions
		if($games_core->settings['stats_lastchamps_advanced'] == 1)
		{
			global $cache;
		
			//Load Last Champions
			$lastchamps = $cache->read("games_lastchamps");
		
			$lastchamps[TIME_NOW.".".$gid] = array(
				'gid'		=> $gid,
				'title'		=> $game['title'],
				'uid'		=> $mybb->user['uid'],
				'username'	=> $mybb->user['username'],
				'score'		=> floatval($score),
				'dateline'	=> TIME_NOW
			);
		
			//Only hold the number of last champions as in the settings said
			krsort($lastchamps);
			$lastchamps = array_chunk($lastchamps, $games_core->settings['stats_lastchamps_advanced_max'], true);
			$lastchamps = $lastchamps[0];
		
			$cache->update("games_lastchamps", $lastchamps);
		}
	
		//Plugin
		$plugins->run_hooks("games_submit_insert_champ");
	}

	//Plugin
	$plugins->run_hooks("games_submit_end");

	redirect("games.php?action=newscore&gid=".$gid."&score=".$score, $lang->scoreadded);
}
?>
