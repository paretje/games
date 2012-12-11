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

//Function to create multipages in the AdminCP
function admin_multipages($page, $pages, $url)
{
	global $theme, $templates, $lang, $mybb;
	
	if($pages != 0)
	{
		//Prevpage
		if($page > 1)
		{
			$prev_page = $page - 1;
			
			$prevpage = "<span><a href=\"".$url."&amp;page=".$prev_page."\">&lt; ".$lang->multipage_previous."</a></span> ";
		}
		
		//Nextpage
		if($page < $pages)
		{
			$next_page = $page + 1;
			
			$nextpage = " <span><a href=\"".$url."&amp;page=".$next_page."\">".$lang->multipage_next." &gt;</a></span>";
		}
		
		//Start value of multipages
		$start = $page -4;
		
		if($start < 1)
		{
			$start = 1;
		}
		
		//End value of multipages
		$end = $page + 4;
		
		if($end > $pages)
		{
			$end = $pages;
		}
		
		//Multipages
		for($i = $start; $i<=$end; $i++)
		{
			if($i == $page)
			{
				$mppage .= " <span><strong>[".$i."]</strong></span>";
			}
			else
			{
				$mppage .= " <span><a href=\"".$url."&amp;page=".$i."\">".$i."</a></span>";
			}
		}
		
		//Output
		$lang->multipage_pages = $lang->sprintf($lang->multipage_pages, $pages);
		
		$start = "<span><a href=\"".$url."&amp;page=1\">&laquo; ".$lang->multipage_first."</a></span> ";
		$end = " <span><a href=\"".$url."&amp;page=".$pages."\">".$lang->multipage_last." &raquo;</a></span>";
		$multipage = $lang->multipage_pages." ".$start.$prevpage.$mppage.$nextpage.$end;
		
		return $multipage;
	}
}

//Function to compute the time of something
function nice_time_gs($stamp)
{
	global $lang;
	
	//Secs
	$daysecs = 24*60*60;
	$hoursecs = 60*60;
	$minutesecs = 60;
	
	//Days
	$days = floor($stamp/$daysecs);
	$stamp %= $daysecs; //That's the same as $stamp = $stamp / $daysecs
	
	//Hours
	$hours = floor($stamp/$hoursecs);
	$stamp %= $hoursecs;
	
	//Minutes
	$minutes = floor($stamp/$minutesecs);
	$stamp %= $minutesecs;
	
	//Seconds
	$seconds = $stamp;
	
	//Language
	if($days == 1)
	{
		$nicetime['days'] = "1 ".$lang->short_day;
	}
	elseif($days > 1)
	{
		$nicetime['days'] = $days." ".$lang->short_days;
	}
	
	if($hours == 1)
	{
		$nicetime['hours'] = "1 ".$lang->short_hour;
	}
	elseif($hours > 1)
	{
		$nicetime['hours'] = $hours." ".$lang->short_hours;
	}
	
	if($minutes == 1)
	{
		$nicetime['minutes'] = "1 ".$lang->short_minute;
	}
	elseif($minutes > 1)
	{
		$nicetime['minutes'] = $minutes." ".$lang->short_minutes;
	}
	
	if($seconds == 1)
	{
		$nicetime['seconds'] = "1 ".$lang->short_second;
	}
	elseif($seconds > 1)
	{
		$nicetime['seconds'] = $seconds." ".$lang->short_seconds;
	}
	
	if(is_array($nicetime))
	{
		return implode(", ", $nicetime);
	}
}

//Function to upload a file
function upload_file_gs($file, $path, $filename="")
{
	global $plugins;
	if(!$filename)
	{
		$filename = $file['name'];
	}
	$upload['original_filename'] = preg_replace("#/$#", "", $file['name']); // Make the filename safe
	$filename = preg_replace("#/$#", "", $filename); // Make the filename safe
	$moved = @move_uploaded_file($file['tmp_name'], $path."/".$filename);
	if(!$moved)
	{
		$upload['error'] = 2;
		return $upload;
	}
	@chmod($path."/".$filename, 0777);
	$upload['filename'] = $filename;
	$upload['path'] = $path;
	$upload['type'] = $file['type'];
	$upload['size'] = $file['size'];
	$plugins->run_hooks_by_ref("upload_file_end", $upload);
	return $upload;
}

//Scan a directory
function my_scandir($directory, $sorting_order=0)
{
	if(is_dir($directory))
	{
		//Open dir
		$directory = @opendir($directory);
		
		//Dir ectries to array
		while(false !== ($file = readdir($directory)))
		{
			if($file != "." && $file != "..")
			{
				$files[] = $file;
			}
		}
		
		//Close dir
		@closedir($directory);
		
		//Output
		if(isset($files))
		{
			if($sorting_order == 1)
			{
				rsort($files);
			}
			else
			{
				sort($files);
			}
			
			return $files;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

//Function to copy the gamedata
function gamedata_copy($admin_directory, $directory)
{
	global $lang, $errors;
	
	//Control directory
	if(!is_dir($directory))
	{
		if(!@mkdir($directory))
		{
			$errors[] = $lang->sprintf($lang->cantmakedir, $directory);
		}
	}
	
	//Chmod directory
	@my_chmod($destination, 0777);
	
	//Is directory writeable?
	if(!is_writable($directory))
	{
		$errors[] = $lang->sprintf($lang->not_writable, $directory);
	}
	
	//Open directory
	$dir_gamedata = @opendir($admin_directory);
	
	//Show content
	if($dir_gamedata)
	{
		while(false !== ($file = readdir($dir_gamedata)))
		{
			if($file != "." && $file != "..")
			{
				if(is_file($admin_directory."/".$file))
				{
					if(!@copy($admin_directory."/".$file, $directory."/".$file))
					{
						$errors[] = $lang->sprintf($lang->not_copyable, $directory."/".$file);
					}
					@my_chmod($directory."/".$file, 0777);
				}
				elseif(is_dir($admin_directory."/".$file))
				{
					gamedata_copy($admin_directory."/".$file, $directory."/".$file);
				}
 			}
		}
	}
	
	@closedir($dir_gamedata);
}

//Function to delete the gamedata
function gamedata_delete($directory)
{
	global $lang, $errors;
	
	//Chmod directory
	@my_chmod($directory, 0777);
	
	//All directories writeable?
	if(!is_writable($directory))
	{
		$errors[] = $lang->sprintf($lang->not_writable, $directory);
	}
	
	//Open directory
	$dir_gamedata = @opendir($directory);
	
	//Show content
	if($dir_gamedata)
	{
		while(false !== ($file = readdir($dir_gamedata)))
		{
			if($file != "." && $file != "..")
			{
				if(is_file($directory."/".$file))
				{
					if(!@unlink($directory."/".$file))
					{
						$errors[] = $lang->sprintf($lang->not_deleteable, $directory."/".$file);
					}
				}
				elseif(is_dir($directory."/".$file))
				{
					gamedata_delete($directory."/".$file);
				}
 			}
		}
	}
	
	@closedir($dir_gamedata);
	
	if(!@rmdir($directory))
	{
		$errors[] = $lang->sprintf($lang->not_deleteable, $directory."/".$file);
	}
}

//Check if directory is empty
function is_emptydir($path)
{
	//Control path
	if(!is_dir($path))
	{
		return false;
	}
	
	//Scan directory
	$files = my_scandir($path);
	
	if(!is_array($files))
	{
		return true;
	}
}

/*******************************
 * Replacing strings in templates
 * 
 * title: The name/title of the template
 * find: An array with all patterns
 * replace: An array with all replacements
 * themes: Here, you can choose between all themes (1) and the default Game Section theme (0)
 *******************************/
function replace_templates_gs($title, $find, $replace, $themes=1)
{
	global $db;
	
	//Themes
	if($themes == 0)
	{
		$where_theme = " AND theme='1'";
	}
	
	//Load current template
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_templates WHERE title='".$title."'".$where_theme);
	while($template = $db->fetch_array($query))
	{
		$old_template = $db->escape_string($template['template']);
		
		$new_template['template'] = $db->escape_string(preg_replace($find, $replace, $template['template']));
		
		if($template['template'] != $old_template)
		{
			$db->update_query("games_templates", $new_template, "tid='".$template['tid']."'");
		}
	}
}

//Send mail because of new tournament round
function my_mail_newtournamentround($email, $username, $language, $tid, $title, $dateline)
{
	global $mybb, $lang;
	
	//Load correct language files
	if($language != "" && $lang->language_exists($language))
	{
		$uselang = $language;
	}
	elseif($mybb->settings['bblanguage'])
	{
		$uselang = $mybb->settings['bblanguage'];
	}
	else
	{
		$uselang = "english";
	}
	if($uselang == $mybb->settings['bblanguage'])
	{
		$emailsubject = $lang->emailsubject_newtournamentround;
		$emailmessage = $lang->email_newtournamentround;
	}
	else
	{
		$userlang = new MyLanguage;
		$userlang->set_path(MYBB_ROOT."inc/languages");
		$userlang->set_language($uselang);
		$userlang->load("tournaments");
		$emailsubject = $userlang->emailsubject_newtournamentround;
		$emailmessage = $userlang->email_newtournamentround;
	}
	
	$emailsubject = $lang->sprintf($emailsubject, $mybb->settings['bbname']);
	$emailmessage = $lang->sprintf($emailmessage, $username, $tid, 1, $lang->sprintf($lang->view_tournament, $title, my_date($mybb->settings['dateformat'], $dateline).", ".my_date($mybb->settings['timeformat'], $dateline)), $mybb->settings['bbname'], $mybb->settings['bburl']);
	my_mail($email, $emailsubject, $emailmessage);
}

//Function to make the Who's ionline row of the Game Section
function whos_online()
{
	global $games_core, $lang, $theme, $theme_games, $mybb, $db, $session, $cache, $plugins;
	
	//Load games
	if($games_core->settings['online_image'] == 1)
	{
		$query2 = $query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE active='1'");
		while($games = $db->fetch_array($query))
		{
			$game[$games['gid']] = $games['name'];
		}
	}
	
	//Load spiders
	$spiders = $cache->read("spiders");
	
	//Max time
	$timetosearch = TIME_NOW - $mybb->settings['wolcutoffmins']*60;
	
	//Loading online persons
	$query = $db->query("SELECT DISTINCT s.uid, s.location, s.sid, u.uid, u.username, u.usergroup, u.displaygroup, u.invisible FROM ".TABLE_PREFIX."sessions s
	LEFT JOIN ".TABLE_PREFIX."users u ON (s.uid=u.uid)
	WHERE s.time>'".$timetosearch."'
	ORDER BY time DESC");
	
	//Variables
	$membercount = 0;
	$guestcount = 0;
	$anoncount = 0;
	$botcount = 0;
	
	//Plugin
	$plugins->run_hooks("games_whosonline_start");
	
	while($online = $db->fetch_array($query))
	{
		//Strip location
		$online_loc = explode(".php", $online['location']);
		$online_loc = my_substr($online_loc[0], -strpos(strrev($online_loc[0]), "/"));
		
		//Is the location of the user the Game Section
		if($online_loc == "games" || $online_loc == "tournaments")
		{
			//Image
			if($games_core->settings['online_image'] == 1)
			{
				$loc_image_gid = explode("gid=", $online['location']);
				$loc_image_gid = explode("&", $loc_image_gid[1]);
				$loc_image_link = my_substr($online['location'], -strpos(strrev($online['location']), "/"));
				
				if(isset($loc_image_gid[0]) && isset($game[trim($loc_image_gid[0])]))
				{
					$loc_image = "<a href=\"".$loc_image_link."\"><img src=\"./games/images/".$game[trim($loc_image_gid[0])]."2.gif\" alt=\"\" /></a> ";
				}
				else
				{
					$loc_image = "<a href=\"".$loc_image_link."\"><img src=\"./games/".$theme_games['directory']."/games.png\" alt=\"\" /></a> ";
				}
			}
			
			//Plugin
			$plugins->run_hooks("games_whosonline_while_start");
			
			//Is the user registred
			if($online['uid'] != 0)
			{
				//Is the user already done
				if(!isset($doneuser[$online['uid']]))
				{
					$membercount++;
					
					//Invisible user
					if($online['invisible'] == "yes")
					{
						$anoncount++;
						
						//Cann de user view invisible users?
						if($mybb->usergroup['canviewwolinvis'] == "yes" || $user['uid'] == $mybb->user['uid'])
						{
							//View username
							$online['username'] = format_name($online['username'], $online['usergroup'], $online['displaygroup']);
							$onlinemembers .= $comma.$loc_image.build_profile_link($online['username'], $online['uid'])."*";
						}
					}
					else
					{
						//View username
						$online['username'] = format_name($online['username'], $online['usergroup'], $online['displaygroup']);
						$onlinemembers .= $comma.$loc_image.build_profile_link($online['username'], $online['uid']);
					}
					
					//User is done
					$doneuser[$online['uid']] = "OK";	
				}
			}
			else
			{
				//Is it a bot?
				$botkey = my_strtolower(str_replace("bot=", '', $online['sid']));
				
				if(my_strpos($online['sid'], "bot=") !== false && $spiders[$botkey])
				{
					//It's a searchbot
					$botcount++;
					
					$onlinemembers .= $comma.$loc_image.format_name($spiders[$botkey]['name'], $spiders[$botkey]['usergroup']);
				}
				else
				{
					//Its a guest
					$guestcount++;
					
					$onlinemembers .= $comma.$loc_image.build_profile_link($online['username'], $online['uid']);
				}
			}
			
			$comma = ", ";
			
			//Plugin
			$plugins->run_hooks("games_whosonline_while_end");
		}
	}
	
	//Online count
	$onlinecount = $membercount + $guestcount;
	
	$mostonline = $cache->read("games_mostonline");
	
	//New record?
	if($onlinecount > $mostonline['numusers'])
	{
		$time = time();
		$mostonline['numusers'] = $onlinecount;
		$mostonline['time'] = $time;
		$cache->update("games_mostonline", $mostonline);
	}
	
	//Information about record
	$recordcount = $mostonline['numusers'];
	$recorddate = my_date($mybb->settings['dateformat'], $mostonline['time']);
	$recordtime = my_date($mybb->settings['timeformat'], $mostonline['time']);
	
	//Plugin
	$plugins->run_hooks("games_whosonline_end");
	
	//Output
	$lang->online_count = $lang->sprintf($lang->online_count, my_number_format($onlinecount), my_number_format($membercount), my_number_format($anoncount), my_number_format($guestcount), $botcount, my_number_format($recordcount), $recorddate, $recordtime);
	
	eval("\$online_out = \"".$games_core->template("games_online")."\";");
	
	return $online_out;
}

//Function to make the statistics of the Game Section
function stats()
{
	global $mybb, $db, $games_core, $lang, $theme, $collapsed, $collapsedimg, $plugins;
	
	//Plugin
	$plugins->run_hooks("games_stats_start");
	
	//Category settings
	$cid = intval($mybb->input['cid']);
	if($cid != 0 && $games_core->settings['stats_cats'] == 1)
	{
		$where_cat = " AND g.cid='".$cid."'";
		$where_cat2 = " AND cid='".$cid."'";
	}
	
	//Last games
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE active='1'".$where_cat2." ORDER BY dateline DESC LIMIT 0,".$games_core->settings['stats_games_max']."");
	
	while($games = $db->fetch_array($query))
	{
		//Plugin
		$plugins->run_hooks("games_stats_lastgames");
		
		eval("\$last_games_bit .= \"".$games_core->template("games_stats_games_bit")."\";");
	}
	
	//Last champions
	$query = $db->query("SELECT c.gid, c.uid, c.username, c.score, c.dateline, g.title
	FROM ".TABLE_PREFIX."games_champions c
	LEFT JOIN ".TABLE_PREFIX."games g ON (c.gid=g.gid)
	WHERE g.active='1'".$where_cat."
	ORDER BY c.dateline DESC
	LIMIT 0,".$games_core->settings['stats_lastchamps_max']."");
	
	while($last_champs = $db->fetch_array($query))
	{
		//Plugin
		$plugins->run_hooks("games_stats_lastchamps");
		
		$pubdate = my_date($mybb->settings['dateformat'], $last_champs['dateline']);
		
		$last_champs_sen = $lang->sprintf($lang->last_champs_sen, $last_champs['uid'], $last_champs['username'], $last_champs['gid'], $last_champs['title']);
		
		eval("\$last_champs_bit .= \"".$games_core->template("games_stats_champs_bit")."\";");
	}
	
	//Language
	if($games_core->settings['stats_lastchamps_max'] == 1)
	{
		$last_champs = "last_champ";
	}
	else
	{
		$last_champs = "last_champs";
	}
	
	//Last scores
	$query = $db->query("SELECT DISTINCT s.gid, s.uid, s.username, s.score, g.title
	FROM ".TABLE_PREFIX."games_scores s
	LEFT JOIN ".TABLE_PREFIX."games g ON (s.gid=g.gid)
	WHERE g.active='1'".$where_cat."
	ORDER BY s.dateline DESC
	LIMIT 0,".$games_core->settings['stats_lastscores_max']."");
	
	while($last_scores = $db->fetch_array($query))
	{
		//Plugin
		$plugins->run_hooks("games_stats_lastscores");
		
		$last_scores['score'] = my_number_format(floatval($last_scores['score']));
		
		$last_score_sen .= $br.$lang->sprintf($lang->last_score_sen, $last_scores['uid'], $last_scores['username'], $last_scores['score'], $last_scores['gid'], $last_scores['title']);
		$br = "\n<br />";
	}
	
	//Language
	if($games_core->settings['stats_lastscores_max'] == 1)
	{
		$last_score = "last_score";
	}
	else
	{
		$last_score = "last_scores";
	}
	
	//Most played games
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE active='1'".$where_cat2." ORDER BY played DESC LIMIT 0,".$games_core->settings['stats_games_max']."");
	
	while($games = $db->fetch_array($query))
	{
		//Plugin
		$plugins->run_hooks("games_stats_mostplayedgames");
		
		eval("\$mostplayed_games_bit .= \"".$games_core->template("games_stats_games_bit")."\";");
	}
	
	//Best players
	if($games_core->settings['stats_bestplayers'] == 1)
	{
		$query = $db->query("SELECT u.uid, u.username, u.avatar, COUNT(c.gid) AS champs
		FROM ".TABLE_PREFIX."games_champions c
		LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=c.uid)
		LEFT JOIN ".TABLE_PREFIX."games g ON (c.gid=g.gid)
		WHERE g.active='1'".$where_cat."
		GROUP BY u.uid
		ORDER BY champs DESC, c.dateline ASC
		LIMIT 0,3");
		
		$rank = 0;
		
		while($bestplayers = $db->fetch_array($query))
		{
			$rank++;
			$bestplayers_place = "bestplayers_place_".$rank;
			$bestplayers_place = $lang->$bestplayers_place;
			
			$bestplayers_sen = $lang->sprintf($lang->bestplayers_sen, $bestplayers['username'], $bestplayers['champs']);
			
			if(!empty($bestplayers['avatar']))
			{
				$bestplayers_avatar = "<img src=\"".$bestplayers['avatar']."\" alt\"\" />";
			}
			else
			{
				$bestplayers_avatar = "";
			}
			
			//Plugin
			$plugins->run_hooks("games_stats_bestplayers");
			
			eval("\$bestplayers_bit .= \"".$games_core->template("games_stats_bestplayers_bit")."\";");
		}
		
		eval("\$stats_bestplayers = \"".$games_core->template("games_stats_bestplayers")."\";");
	}
	
	//Random games
	if($games_core->settings['stats_randomgames'] == 1)
	{
		//Put games in array
		$query = $db->query("SELECT gid, name, title FROM ".TABLE_PREFIX."games WHERE active='1'".$where_cat2." ORDER BY dateline");
		while($game = $db->fetch_array($query))
		{
			$games[] = $game;
		}
		
		//Load x random games
		for($i = 1; $i <= $games_core->settings['stats_randomgames_max']; $i++)
		{
			$id = mt_rand(0, count($games)-1);
			eval("\$randomgames_bit .= \"".$games_core->template("games_stats_randomgames_bit")."\";");
		}
		
		//Language
		if($games_core->settings['stats_randomgames_max'] == 1)
		{
			$randomgames = "randomgame";
		}
		else
		{
			$randomgames = "randomgames";
		}
		
		eval("\$randomgames = \"".$games_core->template("games_stats_randomgames")."\";");
	}
	
	//Plugin
	$plugins->run_hooks("games_stats_end");
	
	eval("\$stats = \"".$games_core->template("games_stats")."\";");
	
	return $stats;
}
?>
