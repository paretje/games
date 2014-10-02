<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2014 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 02/10/2014 by Paretje
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

require_once MYBB_ROOT."inc/functions_games.php";
require_once MYBB_ROOT."inc/functions_upload.php";

$plugins->run_hooks("admin_games_games_start");

$page->add_breadcrumb_item($lang->gamesection, "index.php?module=games");

if($mybb->input['action'] == "" || $mybb->input['action'] == "add" || $mybb->input['action'] == "add_manual" || $mybb->input['action'] == "edit")
{
	$sub_tabs = array();
	$sub_tabs['overview'] = array(
		'title' => $lang->nav_overview,
		'link' => "index.php?module=games/games",
		'description' => $lang->nav_overview_desc
	);
	$sub_tabs['add_game'] = array(
		'title' => $lang->nav_add_game,
		'link' => "index.php?module=games/games&amp;action=add",
		'description' => $lang->nav_add_game_desc
	);
	$sub_tabs['add_game_manual'] = array(
		'title' => $lang->nav_add_game_manual,
		'link' => "index.php?module=games/games&amp;action=add_manual",
		'description' => $lang->nav_add_game_manual_desc
	);
}

// TODO: Control if there are no missing or unnecessary plugin hooks
if($mybb->input['action'] == "add")
{
	$plugins->run_hooks("admin_games_games_add_tar_start");

	if($mybb->request_method == "post")
	{
		if(!intval($mybb->input['cid']) && intval($mybb->input['cid']) !== 0)
		{
			$errors[] = $lang->error_missing_category;
		}
		elseif($mybb->input['cid'] != 0)
		{
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_categories WHERE cid='".intval($mybb->input['cid'])."'");
			$cat = $db->fetch_array($query);
			$cat_test = $db->num_rows($query);
			if($cat_test == 0)
			{
				$errors[] = $lang->catdoesntexist;
			}
		}
		if(!intval($mybb->input['active']) && intval($mybb->input['active']) !== 0)
		{
			$errors[] = $lang->error_missing_active;
		}
		if(intval($_FILES['game_tar']['size']) == 0 || !is_uploaded_file($_FILES['game_tar']['tmp_name']))
		{
			$errors[] = $lang->error_missing_game_tar;
		}

		if(!is_writable(MYBB_ADMIN_DIR."games"))
		{
			$errors[] = $lang->sprintf($lang->not_writable, $config['admin_dir']."/games");
		}
		if(!is_writable(MYBB_ROOT."games"))
		{
			$errors[] = $lang->sprintf($lang->not_writable, "games");
		}
		if(!is_writable(MYBB_ROOT."games/images"))
		{
			$errors[] = $lang->sprintf($lang->not_writable, "games/images");
		}

		$filename = explode(".tar", $_FILES['game_tar']['name']);
		$filename = my_substr($filename[0], 5);
		// TODO: count?
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games WHERE name='".$db->escape_string($filename)."'");
		$game_test = $db->num_rows($query);
		if($game_test > 0 && $mybb->input['force'] != 1)
		{
			$errors[] = $lang->gamealreadyexist;
		}

		if(!$errors)
		{
			$file_tar = upload_file($_FILES['game_tar'], MYBB_ADMIN_DIR."games", $_FILES['game_tar']['name']);
			if($file_tar['error'])
			{
				$errors[] = $lang->error_uploadfailed;
			}

			if(!$errors)
			{
				require_once MYBB_ROOT."inc/3rdparty/tar/Tar.php";
				
				$tar = new Archive_Tar(MYBB_ADMIN_DIR."games/".$_FILES['game_tar']['name']);
				$tar->extract(MYBB_ADMIN_DIR."games");
				
				if($tar == 0)
				{
					$errors[] = $lang->tar_problem;
				}

				if(!$errors)
				{
					@unlink(MYBB_ADMIN_DIR."games/".$_FILES['game_tar']['name']);

					if(!@copy(MYBB_ADMIN_DIR."games/".$filename.".swf", MYBB_ROOT."games/".$filename.".swf"))
					{
						$errors[] = $lang->error_missing_game_tar_swf;
					}
					else
					{
						@my_chmod(MYBB_ROOT."games/".$filename.".swf", 0777);
						@unlink(MYBB_ADMIN_DIR."games/".$filename.".swf");
					}
					if(!@copy(MYBB_ADMIN_DIR."games/".$filename."1.gif", MYBB_ROOT."games/images/".$filename."1.gif"))
					{
						$errors[] = $lang->error_missing_game_tar_gif1;
					}
					else
					{
						@my_chmod(MYBB_ROOT."games/images/".$filename."1.gif", 0777);
						@unlink(MYBB_ADMIN_DIR."games/".$filename."1.gif");
					}
					if(!@copy(MYBB_ADMIN_DIR."games/".$filename."2.gif", MYBB_ROOT."games/images/".$filename."2.gif"))
					{
						$errors[] = $lang->error_missing_game_tar_gif2;
					}
					else
					{
						@my_chmod(MYBB_ROOT."games/images/".$filename."2.gif", 0777);
						@unlink(MYBB_ADMIN_DIR."games/".$filename."2.gif");
					}
					if(is_dir(MYBB_ADMIN_DIR."games/gamedata/".$filename))
					{
						gamedata_copy(MYBB_ADMIN_DIR."games/gamedata/".$filename, MYBB_ROOT."arcade/gamedata/".$filename);
						gamedata_delete(MYBB_ADMIN_DIR."games/gamedata/".$filename);
					}

					if(!$errors)
					{
						// TODO: Note that this could be a serious security risk.
						// Some kind of control, or a simply parsing the content
						// would probably safer.
						require_once(MYBB_ADMIN_DIR."games/".$filename.".php");

						if($config['highscore_type'] == "low" || $config['highscore_type'] == "ASC")
						{
							$high = "ASC";
						}
						else
						{
							$high = "DESC";
						}

						$insert_game = array(
							'cid'		=> intval($mybb->input['cid']),
							'title'		=> $db->escape_string($config['gtitle']),
							'name'		=> $db->escape_string($config['gname']),
							'description'	=> $db->escape_string($config['gwords']),
							'purpose'	=> $db->escape_string($config['object']),
							'keys'		=> $db->escape_string($config['gkeys']),
							'bgcolor'	=> $db->escape_string($config['bgcolor']),
							'width'		=> intval($config['gwidth']),
							'height'	=> intval($config['gheight']),
							'dateline'	=> TIME_NOW,
							'score_type'	=> $db->escape_string($high),
							'active'	=> intval($mybb->input['active'])
						);

						$plugins->run_hooks("admin_games_games_add_tar_do");

						$gid = $db->insert_query("games", $insert_game);

						if(intval($mybb->input['cid']) !== 0)
						{
							log_admin_action($gid, $config['gtitle'], $mybb->input['cid'], $cat['title']);
						}
						else
						{
							log_admin_action($gid, $config['gtitle'], $mybb->input['cid'], $lang->game_cat_no);
						}

						flash_message($lang->added_game, 'success');
						admin_redirect("index.php?module=games/games");
					}
				}
			}
		}
	}

	$page->add_breadcrumb_item($lang->nav_add_game);
	$page->output_header($lang->nav_add_game);
	$page->output_nav_tabs($sub_tabs, 'add_game');

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$categories[0] = $lang->game_cat_no;
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_categories ORDER BY title ASC");
	while($category = $db->fetch_array($query))
	{
		$categories[$category['cid']] = $category['title'];
	}

	if(!isset($mybb->input['force']))
	{
		$mybb->input['force'] = 0;
	}

	$form = new Form("index.php?module=games/games&amp;action=add", "post", false, true);
	$form_container = new FormContainer($lang->nav_add_game_tar);

	$form_container->output_row($lang->game_tar." <em>*</em>", false, $form->generate_file_upload_box('game_tar', array('id' => 'game_tar')), 'game_tar');
	$form_container->output_row($lang->game_cat." <em>*</em>", false, $form->generate_select_box('cid', $categories, $mybb->input['cid'], array('id' => 'cid')), 'cid');
	$form_container->output_row($lang->game_active." <em>*</em>", false, $form->generate_yes_no_radio('active', $mybb->input['active']), 'active');
	$form_container->output_row($lang->game_force, $lang->game_force_desc, $form->generate_yes_no_radio('force', $mybb->input['force']), 'force');

	$plugins->run_hooks("admin_games_games_add_tar_end");

	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
	$page->output_footer();
}
if($mybb->input['action'] == "add_manual")
{
	$plugins->run_hooks("admin_games_games_add_manual_start");

	if($mybb->request_method == "post")
	{
		$cat = control_games_input($errors);

		// TODO: Wouldn't it be better to use COUNT(), or is this just as fast?
		$query = $db->simple_select("games", "gid", "name='".$db->escape_string($mybb->input['name'])."'");
		if($db->num_rows($query) != 0 && $mybb->input['force'] != 1)
		{
			$errors[] = $lang->gamealreadyexist;
		}

		if(!$errors)
		{
			$insert_game = array(
				'cid'		=> intval($mybb->input['cid']),
				'title'		=> $db->escape_string($mybb->input['title']),
				'name'		=> $db->escape_string($mybb->input['name']),
				'description'	=> $db->escape_string($mybb->input['description']),
				'purpose'	=> $db->escape_string($mybb->input['purpose']),
				'keys'		=> $db->escape_string($mybb->input['keys']),
				'bgcolor'	=> $db->escape_string($mybb->input['bgcolor']),
				'width'		=> intval($mybb->input['width']),
				'height'	=> intval($mybb->input['height']),
				'dateline'	=> TIME_NOW,
				'score_type'	=> $db->escape_string($mybb->input['score_type']),
				'active'	=> intval($mybb->input['active'])
			);

			$plugins->run_hooks("admin_games_games_add_manual_do");

			$gid = $db->insert_query("games", $insert_game);
			log_admin_action($gid, $mybb->input['title'], $mybb->input['cid'], $cat['title']);

			flash_message($lang->added_game, 'success');
			admin_redirect("index.php?module=games/games");
		}
	}

	$page->add_breadcrumb_item($lang->nav_add_game);
	$page->output_header($lang->nav_add_game);
	$page->output_nav_tabs($sub_tabs, 'add_game_manual');

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	// Fill the default values when the user starts to add a game manually
	if(!isset($mybb->input['bgcolor']))
	{
		$mybb->input['bgcolor'] = "000000";
	}
	if(!isset($mybb->input['width']))
	{
		$mybb->input['width'] = 500;
	}
	if(!isset($mybb->input['height']))
	{
		$mybb->input['height'] = 500;
	}
	if(!isset($mybb->input['force']))
	{
		$mybb->input['force'] = 0;
	}
	
	build_games_form($lang->nav_add_game, "add_manual", $mybb->input);
	$page->output_footer();
}
elseif($mybb->input['action'] == "edit")
{
	$plugins->run_hooks("admin_games_games_edit_start");

	$query = $db->simple_select("games", "*", "gid='".intval($mybb->input['gid'])."'");
	$game = $db->fetch_array($query);
	if($db->num_rows($query) == 0)
	{
		flash_message($lang->gamedoesntexist, 'error');
		admin_redirect("index.php?module=games/games");
	}

	if($mybb->request_method == "post")
	{
		$cat = control_games_input($errors);

		if(!$errors)
		{
			// Tournaments stats are kept in cache, and should be
			// updated if a game a game gets enabled or disabled.
			if($mybb->input['active'] === 0 && $game['active'] === 1)
			{
				$tournaments_stats = $cache->read("games_tournaments_stats");
				$query = $db->simple_select("games_tournaments", "status", "gid='".intval($mybb->input['gid'])."'");
				while($tournaments = $db->fetch_array($query))
				{
					$tournaments_stats[$tournaments['status']]--;
				}
				$cache->update("games_tournaments_stats", $tournaments_stats);
			}
			elseif($mybb->input['active'] === 1 && $game['active'] === 0)
			{
				$tournaments_stats = $cache->read("games_tournaments_stats");
				$query = $db->simple_select("games_tournaments", "status", "gid='".intval($mybb->input['gid'])."'");
				while($tournaments = $db->fetch_array($query))
				{
					$tournaments_stats[$tournaments['status']]++;
				}
				$cache->update("games_tournaments_stats", $tournaments_stats);
			}

			$update_game = array(
				'cid'		=> intval($mybb->input['cid']),
				'title'		=> $db->escape_string($mybb->input['title']),
				'name'		=> $db->escape_string($mybb->input['name']),
				'description'	=> $db->escape_string($mybb->input['description']),
				'purpose'	=> $db->escape_string($mybb->input['purpose']),
				'keys'		=> $db->escape_string($mybb->input['keys']),
				'bgcolor'	=> $db->escape_string($mybb->input['bgcolor']),
				'width'		=> $db->escape_string($mybb->input['width']),
				'height'	=> $db->escape_string($mybb->input['height']),
				'score_type'	=> $db->escape_string($mybb->input['score_type']),
				'active'	=> intval($mybb->input['active'])
			);

			// If the score type of the game changes (which
			// determines wheter lower or higher scores are better)
			// the champion-reference has to be updated.
			if($mybb->input['score_type'] != $game['score_type'])
			{
				$query = $db->simple_select("games_scores", "sid",
					"gid='".intval($mybb->input['gid'])."'",
					array(
						'order_by' => 'score',
						'order_dir' => $db->escape_string($mybb->input['score_type']),
						'limit_start' => 0,
						'limit' =>1
					)
				);
				if($db->num_rows($query) == 1)
				{
					$update_game['champion'] = $score['sid'];
				}
			}

			$plugins->run_hooks("admin_games_games_edit_do");

			$db->update_query("games", $update_game, "gid='".intval($mybb->input['gid'])."'");
			log_admin_action($mybb->input['gid'], $mybb->input['title'], $mybb->input['cid'], $cat['title']);

			flash_message($lang->edited_game, 'success');
			admin_redirect("index.php?module=games/games");
		}
	}

	$page->add_breadcrumb_item($lang->edit_game);
	$page->output_header($lang->edit_game);

	$sub_tabs['edit_game'] = array(
		'title' => $lang->nav_edit_game,
		'link' => "index.php?module=games/games&amp;action=edit",
		'description' => $lang->nav_edit_game_desc
	);
	$page->output_nav_tabs($sub_tabs, 'edit_game');

	/* When there are errors, it means the user has changed some values when
	 * he tried to submit his changed, it failed. So, if there were errors,
	 * could better show the new values of the fields he/she tried to submit
	 * in order to make it easier to fix the error, instead of having to
	 * redo all changes. */
	if($errors)
	{
		$page->output_inline_error($errors);
		build_games_form($lang->nav_edit_game, "edit", $mybb->input);
	}
	else
	{
		build_games_form($lang->nav_edit_game, "edit", $game);
	}

	$page->output_footer();
}
elseif($mybb->input['action'] == "delete")
{
	$plugins->run_hooks("admin_games_games_delete");

	if($mybb->input['no'])
	{
		admin_redirect("index.php?module=games/games");
	}

	$gid = intval($mybb->input['gid']);
	$query = $db->simple_select("games", "*", "gid='".$gid."'");
	$game = $db->fetch_array($query);
	if($db->num_rows($query) == 0)
	{
		flash_message($lang->gamedoesntexist, 'error');
		admin_redirect("index.php?module=games/games");
	}

	if($mybb->request_method == "post")
	{
		if($mybb->input['files'])
		{
			if(!is_writable(MYBB_ROOT."games"))
			{
				$errors[] = $lang->sprintf($lang->not_writable, "games");
			}
			if(!is_writable(MYBB_ROOT."games/images"))
			{
				$errors[] = $lang->sprintf($lang->not_writable, "games/images");
			}

			if(!$errors)
			{
				if(!@unlink(MYBB_ROOT."games/".$game['name'].".swf"))
				{
					$errors[] = $lang->sprintf($lang->not_deleteable, "games/".$game['name'].".swf");
				}
				if(!@unlink(MYBB_ROOT."games/images/".$game['name']."1.gif"))
				{
					$errors[] = $lang->sprintf($lang->not_deleteable, "games/images/".$game['name']."1.gif");
				}
				if(!@unlink(MYBB_ROOT."games/images/".$game['name']."2.gif"))
				{
					$errors[] = $lang->sprintf($lang->not_deleteable, "games/images/".$game['name']."2.gif");
				}
				if(is_dir(MYBB_ROOT."arcade/gamedata/".$game['name']))
				{
					gamedata_delete(MYBB_ROOT."arcade/gamedata/".$game['name']);
				}
			}
		}

		if(!$errors)
		{
			$tournaments_stats = $cache->read("games_tournaments_stats");
			$query = $db->simple_select("games_tournaments", "status", "gid='".$gid."'");
			while($tournaments = $db->fetch_array($query))
			{
				$tournaments_stats[$tournaments['status']]--;
			}
			$cache->update("games_tournaments_stats", $tournaments_stats);

			$db->delete_query("games", "gid='".$gid."'");
			$db->delete_query("games_favourites", "gid='".$gid."'");
			$db->delete_query("games_scores", "gid='".$gid."'");
			$db->delete_query("games_rating", "gid='".$gid."'");
			// TODO: will this work with all DBMS systems?
			$db->delete_query("games_tournaments_players",
				"tid IN (SELECT tid FROM ".TABLE_PREFIX."games_tournaments WHERE gid='".$gid."')");
			$db->delete_query("games_tournaments", "gid='".$gid."'");

			$plugins->run_hooks("admin_games_games_delete_do");

			if(intval($game['cid']) !== 0)
			{
				$query = $db->simple_select("games_categories", "title", "cid='".$game['cid']."'");
				$cat = $db->fetch_array($query);
			}
			else
			{
				$cat['title'] = $lang->game_cat_no;
			}
			log_admin_action($gid, $game['title'], $game['cid'], $cat['title']);

			flash_message($lang->deleted_game, 'success');
			admin_redirect("index.php?module=games/games");
		}
		else
		{
			foreach($errors as $error)
			{
				$flash_errors .= "<li>".$error."</li>\n";
			}
			flash_message("<ul>\n".$flash_errors."\n</ul>", 'error');
			admin_redirect("index.php?module=games/games");
		}
	}
	else
	{
		$page->output_header();
		$form = new Form("index.php?module=games/games&amp;action=delete&amp;gid=".$mybb->input['gid']."&amp;my_post_key=".$mybb->post_code, 'post');
		echo "<div class=\"confirm_action\">\n";
		echo "<p>".$lang->delete_game_confirmation."</p>\n";
		echo "<br />\n";
		echo "<p class=\"buttons\">\n";
		echo $form->generate_submit_button($lang->yes, array('class' => 'button_yes'));
		echo $form->generate_submit_button($lang->delete_gamefiles, array("name" => "files", 'class' => 'button_yes'));
		echo $form->generate_submit_button($lang->no, array("name" => "no", 'class' => 'button_no'));
		echo "</p>\n";
		echo "</div>\n";
		$form->end();
		$page->output_footer();
	}
}
else
{
	$page->add_breadcrumb_item($lang->nav_overview);
	$page->output_header($lang->gamesection);
	$page->output_nav_tabs($sub_tabs, 'overview');
	build_games_search_form();

	// Handle search query
	if(is_array($mybb->input['search']))
	{
		// TODO: add g or not? So, remove g for the joined query, or add
		// it for the normal one (is this possible, or is there some
		// kind of escaping?)
		// Alternative: use regularexpression to add/delete it afterwards
		$where = "g.title LIKE '%".$mybb->input['search']['title']."%'"
			." AND g.name LIKE '%".$mybb->input['search']['name']."%'"
			." AND g.description LIKE '%".$mybb->input['search']['description']."%'";
		if($mybb->input['search']['cid'] != 0)
		{
			$where .= " AND g.cid='".$mybb->input['search']['cid']."'";
		}
		if($mybb->input['search']['active'] != 0)
		{
			if($mybb->input['search']['active'] == 2)
			{
				$where .= " AND g.active='0'";
			}
			else
			{
				$where .= " AND g.active='1'";
			}
		}
	}

	// Build the multipage navigation
	$pag = $mybb->input['page'] ? intval($mybb->input['page']) : 1;
	$sortby = isset($mybb->input['sortby']) ? $mybb->input['sortby'] : "title";
	$order = isset($mybb->input['order']) ? $mybb->input['order'] : "ASC";
	$perpage = $mybb->input['perpage'] ? intval($mybb->input['perpage']) : 20;
	$start = ($pag-1) * $perpage;

	// TODO: COUNT?
	$query = $db->simple_select("games g", "gid", $where);
	$count = $db->num_rows($query);

	// TODO: built simply the complete URL with empty and default parameters
	//	 after all, it's better then this hack, isn't it?
	$addr = explode("&page=", $_SERVER['QUERY_STRING']);
	$pagination = draw_admin_pagination($pag, $perpage, $count, "index.php?".$addr['0']);
	echo $pagination."<br /><br />";

	// Test if this is a search query, or an ordinary requist ...
	if(is_array($mybb->input['search']))
	{
		$where_sql = "WHERE ".$where;
	}
	$query = $db->query("SELECT DISTINCT g.*, c.title AS catname
		FROM ".TABLE_PREFIX."games g
		LEFT JOIN ".TABLE_PREFIX."games_categories c ON (g.cid=c.cid)
		".$where_sql."
		ORDER BY g.".$sortby." ".$order."
		LIMIT ".$start.",".$perpage);

	// Stop here if there are no games to show ...
	if($db->num_rows($query) == 0)
	{
		$page->output_error("<p><em>".$lang->no_games."</em></p>");
		$page->output_footer();
	}

	$plugins->run_hooks("admin_games_games_default_start");

	$table = new Table;

	while($games = $db->fetch_array($query))
	{
		if($games['cid'] == 0)
		{
			$category_edit = "";
			$games['catname'] = "<strong>".$lang->na."</strong>";
		}
		else
		{
			$category_edit = "<a href=\"index.php?module=games/categories&amp;action=edit&amp;cid=".$games['cid']."\">".$lang->edit_cat."</a><br />";
			$games['catname'] = "<a href=\"index.php?module=games/games&amp;search[cid]cid=".$games['cid']."\">".$games['catname']."</a>";
		}

		$pubdate = my_date($mybb->settings['dateformat'].", ".$mybb->settings['timeformat'], $games['dateline']);
		$lang_active = "active_".$games['active'];

		$table->construct_cell("<strong><a href=\"index.php?module=games/games&amp;action=edit&amp;gid=".$games['gid']."\">".$games['title']."</a></strong><br />
<a href=\"index.php?module=games/games&amp;action=edit&amp;gid=".$games['gid']."\"><img src=\"../games/images/".$games['name']."1.gif\" border=\"0\" alt=\"\" />", array("class" => "align_center", "width" => 150));
		$table->construct_cell($games['description'], array("class" => "align_center"));
		$table->construct_cell("<a href=\"index.php?module=games/games&amp;action=edit&amp;gid=".$games['gid']."\">".$lang->edit_game."</a><br />
<a href=\"index.php?module=games/games&amp;action=delete&amp;gid=".$games['gid']."&amp;my_post_key=".$mybb->post_code."\">".$lang->delete_game."</a><br />
".$category_edit."
<a href=\"index.php?module=games/gamedata&amp;directory=".$games['name']."\">".$lang->gamedata."</a><br /><br />
<a href=\"../games.php?action=play&amp;gid=".$games['gid']."\">".$lang->play_game."</a>", array("width" => 125));
		$table->construct_cell("<strong>".$lang->game_cat.":</strong> ".$games['catname']."<br >
<strong>".$lang->addedon."</strong> ".$pubdate."<br />
<strong>".$lang->played."</strong> ".$games['played']."<br />
<strong>".$lang->active."</strong> ".$lang->$lang_active, array("width" => 125));

		$plugins->run_hooks("admin_games_games_default_while");

		$table->construct_row();
	}

	$plugins->run_hooks("admin_games_games_default_end");

	$table->output($lang->gamesection);
	echo $pagination;
	$page->output_footer();
}

function build_games_form($title, $action, $values)
{
	global $lang, $db, $plugins;

	$categories[0] = $lang->game_cat_no;
	$query = $db->simple_select("games_categories", "cid, title", "", array('order_by' => 'title', 'order_dir' => 'asc'));
	while($category = $db->fetch_array($query))
	{
		$categories[$category['cid']] = $category['title'];
	}

	$score_type = array(
		'DESC'		=> $lang->game_high,
		'ASC'		=> $lang->game_low
	);

	$form = new Form("index.php?module=games/games&amp;action=".$action, "post");
	$form_container = new FormContainer($title);
	
	if($action == "edit")
	{
		echo $form->generate_hidden_field("gid", $values['gid']);
	}

	$form_container->output_row($lang->game_title." <em>*</em>", false, $form->generate_text_box('title', $values['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->game_name." <em>*</em>", $lang->game_name_desc, $form->generate_text_box('name', $values['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->game_cat." <em>*</em>", false, $form->generate_select_box('cid', $categories, $values['cid'], array('id' => 'cid')), 'cid');
	$form_container->output_row($lang->game_description, false, $form->generate_text_area('description', $values['description'], array('id' => 'description')), 'description');
	$form_container->output_row($lang->game_purpose, false, $form->generate_text_area('purpose', $values['purpose'], array('id' => 'purpose')), 'purpose');
	$form_container->output_row($lang->game_keys, false, $form->generate_text_area('keys', $values['keys'], array('id' => 'keys')), 'keys');
	$form_container->output_row($lang->game_bgcolor." <em>*</em>", false, $form->generate_text_box('bgcolor', $values['bgcolor'], array('id' => 'bgcolor')), 'bgcolor');
	$form_container->output_row($lang->game_width." <em>*</em>", false, $form->generate_text_box('width', $values['width'], array('id' => 'width')), 'width');
	$form_container->output_row($lang->game_height." <em>*</em>", false, $form->generate_text_box('height', $values['height'], array('id' => 'height')), 'height');
	$form_container->output_row($lang->game_score_type." <em>*</em>", false, $form->generate_select_box('score_type', $score_type, $values['score_type'], array('id' => 'score_type')), 'score_type');
	$form_container->output_row($lang->game_active." <em>*</em>", false, $form->generate_yes_no_radio('active', $values['active']), 'active');
	$form_container->output_row($lang->game_force, $lang->game_force_desc, $form->generate_yes_no_radio('force', $values['force']), 'force');

	$plugins->run_hooks("admin_games_games_form");

	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
}

// TODO: Wait a minute ... isn't pass by reference deprecated?
// Note: returns category string
function control_games_input(&$errors)
{
	global $mybb, $lang, $db;

	if(empty($mybb->input['title']))
	{
		$errors[] = $lang->error_missing_title;
	}
	if(empty($mybb->input['name']))
	{
		$errors[] = $lang->error_missing_name;
	}
	if(!intval($mybb->input['cid']) && intval($mybb->input['cid']) !== 0)
	{
		$errors[] = $lang->error_missing_category;
	}
	elseif($mybb->input['cid'] != 0)
	{
		$query = $db->simple_select("games_categories", "title", "cid='".intval($mybb->input['cid'])."'");
		$cat = $db->fetch_array($query);
		if($db->num_rows($query) == 0)
		{
			$errors[] = $lang->catdoesntexist;
		}
	}
	else
	{
		$cat['title'] = $lang->game_cat_no;
	}
	if(!isset($mybb->input['bgcolor']))
	{
		$errors[] = $lang->error_missing_bgcolor;
	}
	if(!intval($mybb->input['width']) || $mybb->input['width'] < 1)
	{
		$errors[] = $lang->error_missing_width;
	}
	if(!intval($mybb->input['height']) || $mybb->input['height'] < 1)
	{
		$errors[] = $lang->error_missing_height;
	}
	if(!isset($mybb->input['score_type']) || ($mybb->input['score_type'] != "ASC" && $mybb->input['score_type'] != "DESC"))
	{
		$errors[] = $lang->error_missing_score_type;
	}
	if(!intval($mybb->input['active']) && intval($mybb->input['active']) !== 0)
	{
		$errors[] = $lang->error_missing_active;
	}

	return $cat;
}

function build_games_search_form()
{
	global $lang, $db, $mybb;

	// Generate the search form
	$categories[0] = $lang->game_cat_no;
	$query = $db->simple_select("games_categories", "cid, title", "", array('order_by' => 'title', 'order_dir' => 'asc'));
	while($category = $db->fetch_array($query))
	{
		$categories[$category['cid']] = $category['title'];
	}

	$actives = array(
		'0'		=> $lang->active_all,
		'1'		=> $lang->active_active,
		'2'		=> $lang->active_inactive
	);
	$sortby = array(
		'title'		=> $lang->sortby_title,
		'name'		=> $lang->sortby_name,
		'dateline'	=> $lang->sortby_dateline,
		'played'	=> $lang->sortby_played
	);
	$orderdir = array(
		'ASC'		=> $lang->order_asc,
		'DESC'		=> $lang->order_desc
	);

	$form = new Form("index.php", "get");
	echo $form->generate_hidden_field("module", "games/games")."\n";
	$table = new Table;
	$table->construct_cell("<strong>".$lang->game_title.":</strong>
".$form->generate_text_box("search[title]", $mybb->input['search']['title'], array("style" => "width: 100px;"))."
<strong>".$lang->game_name.":</strong>
".$form->generate_text_box("search[name]", $mybb->input['search']['name'], array("style" => "width: 100px;"))."
<strong>".$lang->game_description.":</strong>
".$form->generate_text_box("search[description]", $mybb->input['search']['description'], array("style" => "width: 100px;"))."
<strong>".$lang->game_cat.":</strong>
".$form->generate_select_box("search[cid]", $categories, $mybb->input['search']['cid'])."
<strong>".$lang->active."</strong>
".$form->generate_select_box("search[active]", $actives, intval($mybb->input['search']['active']))."
<br />
<br />
<strong>".$lang->sortby."</strong>
".$form->generate_select_box("sortby", $sortby, $mybb->input['sortby'])."
<strong>".$lang->order."</strong>
".$form->generate_select_box("order", $orderdir, $mybb->input['order'])."
<strong>".$lang->gamesperpage."</strong>
".$form->generate_text_box("perpage", $mybb->input['perpage'], array("style" => "width: 20px;"))."
".$form->generate_submit_button($lang->go));
	$table->construct_row();
	$table->output($lang->search);
	$form->end();


}
?>
