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

function task_tournamentstatus($task)
{
	global $db, $cache, $mybb, $lang;
	
	//Load language file
	$lang->load("games_tasks");
	$lang->load("tournaments");		//This possible, because when $this->language (here english/admin) doesn't exist, english is used
	
	//Check if tournament system is activated
	require_once MYBB_ROOT."inc/functions_games.php";
	require_once MYBB_ROOT."inc/class_games.php";
	$games_core = new games;
	
	$games_core->run_settings();
	
	if($games_core->settings['tournaments_activated'] == 1)
	{
		//Load tournament statistics
		$tournaments_stats = $cache->read("games_tournaments_stats");
	
		//Changing open status to started
		$query = $db->query("SELECT DISTINCT t.*, g.title
		FROM ".TABLE_PREFIX."games_tournaments t
		LEFT JOIN ".TABLE_PREFIX."games g ON (t.gid=g.gid)
		WHERE t.status='open' AND t.joinedplayers=POW(2, t.rounds) AND g.active='1'");
	
		while($topen = $db->fetch_array($query))
		{
			//Update tournament
			$roundinformation = array();
			$update_tournament = array();
		
			$roundinformation['1']['starttime'] = TIME_NOW;
			$update_tournament = array(
				'status'		=> $db->escape_string("started"),
				'round'			=> intval("1"),
				'roundinformation'	=> $db->escape_string(serialize($roundinformation))
			);
		
			$db->update_query("games_tournaments", $update_tournament, "tid='".$topen['tid']."'");
		
			//Update tournament statistics
			$tournaments_stats['open']--;
			$tournaments_stats['started']++;
			
			//Send mails to players
			$query = $db->query("SELECT DISTINCT u.*
			FROM ".TABLE_PREFIX."games_tournaments_players p
			LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=p.uid)
			WHERE p.tid='".$topen['tid']."' AND u.games_tournamentnotify='1'");
			
			while($players = $db->fetch_array($query))
			{
				my_mail_newtournamentround($players['email'], $players['username'], $players['language'], $topen['tid'], $topen['title'], $topen['dateline']);
			}
		}
		
		//Changing round
		$query = $db->query("SELECT t.*, g.title, g.score_type, COUNT(p.pid) AS roundplayers
		FROM ".TABLE_PREFIX."games_tournaments t
		LEFT JOIN ".TABLE_PREFIX."games g ON (t.gid=g.gid)
		LEFT JOIN ".TABLE_PREFIX."games_tournaments_players p ON (t.tid=p.tid AND t.round=p.rid AND p.score_try!=0)
		WHERE t.status='started' AND t.rounds!=t.round AND g.active='1'
		GROUP BY t.tid");
	
		while($tround = $db->fetch_array($query))
		{
			//Info
			$neededplayers = pow(2, $tround['rounds']-$tround['round']);
			$tround['roundinformation'] = unserialize($tround['roundinformation']);
			$update_tournament = array();
			
			if(($tround['roundinformation'][$tround['round']]['starttime']+$tround['roundtime']) <= TIME_NOW)
			{
				//Are there enough players to just go to the next round?
				if($tround['roundplayers'] >= $neededplayers)
				{
					//Update tournament
					$tround['roundinformation'][$tround['round']]['endtime'] = TIME_NOW;
					$tround['roundinformation'][($tround['round']+1)]['starttime'] = TIME_NOW;
					$update_tournament = array(
						'round'			=> intval($tround['round']+1),
						'roundinformation'	=> $db->escape_string(serialize($tround['roundinformation']))
					);
					$db->update_query("games_tournaments", $update_tournament, "tid='".$tround['tid']."'");
				
					//Create players
					$query = $db->query("SELECT DISTINCT u.*
					FROM ".TABLE_PREFIX."games_tournaments_players p
					LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=p.uid)
					WHERE p.tid='".$tround['tid']."' AND p.rid='".$tround['round']."' AND p.score_try!='0'
					ORDER BY p.score ".$tround['score_type'].", p.score_try ASC, p.tries ASC, p.dateline ASC
					LIMIT 0,".$neededplayers."");
			
					while($players = $db->fetch_array($query))
					{
						//Create player
						$add_player = array();
						$add_player = array(
							'tid'			=> intval($tround['tid']),
							'rid'			=> intval($tround['round']+1),
							'uid'			=> intval($players['uid']),
							'username'		=> $db->escape_string($players['username'])
						);
						$db->insert_query("games_tournaments_players", $add_player);
					
						//Send mail
						if($players['games_tournamentnotify'] == 1)
						{
							my_mail_newtournamentround($players['email'], $players['username'], $players['language'], $players['tid'], $tround['title'], $tround['dateline']);
						}
					}
				}
				//We can immediately stop the tournament
				elseif($tround['roundplayers'] == 1)
				{
					//Load best score
					$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_tournaments_players
					WHERE tid='".$tround['tid']."' AND rid='".$tround['round']."' AND score_try!='0'
					ORDER BY score ".$tround['score_type'].", score_try ASC, tries ASC, dateline ASC
					LIMIT 0,1");
					$champ = $db->fetch_array($query);
				
					//Update tournament
					$tround['roundinformation'][$tround['round']]['endtime'] = TIME_NOW;
					$tround['roundinformation'][$tround['rounds']]['endtime'] = TIME_NOW;
					$update_tournament = array(
						'status'		=> $db->escape_string("finished"),
						'champion'		=> intval($champ['uid']),
						'roundinformation'	=> $db->escape_string(serialize($tround['roundinformation']))
					);
					$db->update_query("games_tournaments", $update_tournament, "tid='".$tround['tid']."'");
				
					//Update tournament statistics
					$tournaments_stats['started']--;
					$tournaments_stats['finished']++;
				}
				//Nobody played, we just stop the tournament
				elseif($tround['roundplayers'] == 0)
				{
					//Update tournament
					$tround['roundinformation'][$tround['round']]['endtime'] = TIME_NOW;
					$tround['roundinformation'][$tround['rounds']]['endtime'] = TIME_NOW;
					$update_tournament = array(
						'status'		=> $db->escape_string("finished"),
						'roundinformation'	=> $db->escape_string(serialize($tround['roundinformation']))
					);
					$db->update_query("games_tournaments", $update_tournament, "tid='".$tround['tid']."'");
				
					//Update tournament statistics
					$tournaments_stats['started']--;
					$tournaments_stats['finished']++;
				}
				//Switch a round
				else
				{
					//Update tournament
					$round = $tround['rounds']-floor(log($tround['roundplayers'])/log(2));
					$neededplayers = pow(2, $tround['rounds']-$round);
					$tround['roundinformation'][$tround['round']]['endtime'] = TIME_NOW;
					$tround['roundinformation'][$round]['starttime'] = TIME_NOW;
					$update_tournament = array(
						'round'			=> intval($round),
						'roundinformation'	=> $db->escape_string(serialize($tround['roundinformation']))
					);
					$db->update_query("games_tournaments", $update_tournament, "tid='".$tround['tid']."'");
				
					//Create players
					$query = $db->query("SELECT DISTINCT u.*
					FROM ".TABLE_PREFIX."games_tournaments_players p
					LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=p.uid)
					WHERE p.tid='".$tround['tid']."' AND p.rid='".$tround['round']."' AND p.score_try!='0'
					ORDER BY p.score ".$tround['score_type'].", p.score_try ASC, p.tries ASC, p.dateline ASC
					LIMIT 0,".$neededplayers."");
			
					while($players = $db->fetch_array($query))
					{
						//Create player
						$add_player = array();
						$add_player = array(
							'tid'			=> intval($tround['tid']),
							'rid'			=> intval($round),
							'uid'			=> intval($players['uid']),
							'username'		=> $db->escape_string($players['username'])
						);
						$db->insert_query("games_tournaments_players", $add_player);
					
						//Send mail
						if($players['games_tournamentnotify'] == 1)
						{
							my_mail_newtournamentround($players['email'], $players['username'], $players['language'], $players['tid'], $tround['title'], $tround['dateline']);
						}
					}
				}
			}
		}
		
		//Changing started to finished
		$query = $db->query("SELECT t.*, g.title, g.score_type, COUNT(p.pid) AS roundplayers
		FROM ".TABLE_PREFIX."games_tournaments t
		LEFT JOIN ".TABLE_PREFIX."games g ON (t.gid=g.gid)
		LEFT JOIN ".TABLE_PREFIX."games_tournaments_players p ON (t.tid=p.tid AND t.round=p.rid AND p.score_try!=0)
		WHERE t.status='started' AND t.rounds=t.round AND g.active='1'
		GROUP BY t.tid");
	
		while($tstarted = $db->fetch_array($query))
		{
			//Info
			$tstarted['roundinformation'] = unserialize($tstarted['roundinformation']);
			$update_tournament = array();
			
			if(($tstarted['roundinformation'][$tstarted['round']]['starttime']+$tstarted['roundtime']) <= TIME_NOW)
			{
				//Finish the tournament
				if($tstarted['roundplayers'] >= 1)
				{
					//Load best score
					$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_tournaments_players
					WHERE tid='".$tstarted['tid']."' AND rid='".$tstarted['round']."' AND score_try!='0'
					ORDER BY score ".$tstarted['score_type'].", score_try ASC, tries ASC, dateline ASC
					LIMIT 0,1");
					$champ = $db->fetch_array($query);
				
					//Update tournament
					$tstarted['roundinformation'][$tstarted['rounds']]['endtime'] = TIME_NOW;
					$update_tournament = array(
						'status'		=> $db->escape_string("finished"),
						'champion'		=> intval($champ['uid']),
						'roundinformation'	=> $db->escape_string(serialize($tstarted['roundinformation']))
					);
					$db->update_query("games_tournaments", $update_tournament, "tid='".$tstarted['tid']."'");
				
					//Update tournament statistics
					$tournaments_stats['started']--;
					$tournaments_stats['finished']++;
				}
				//Nobody played, we just stop the tournament
				else
				{
					//Update tournament
					$tstarted['roundinformation'][$tstarted['rounds']]['endtime'] = TIME_NOW;
					$update_tournament = array(
						'status'		=> $db->escape_string("finished"),
						'roundinformation'	=> $db->escape_string(serialize($tstarted['roundinformation']))
					);
					$db->update_query("games_tournaments", $update_tournament, "tid='".$tstarted['tid']."'");
				
					//Update tournament statistics
					$tournaments_stats['started']--;
					$tournaments_stats['finished']++;
				}
			}
		}
	
		//Update tournament statistics
		$cache->update("games_tournaments_stats", $tournaments_stats);
		
		add_task_log($task, $lang->task_tournamentstatus_ran);
	}
}
?>
