<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2013 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 31/12/2013 by Paretje
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

$plugins->run_hooks("admin_games_categories_start");

$page->add_breadcrumb_item($lang->gamesection, "index.php?module=games");
$page->add_breadcrumb_item($lang->nav_categories, "index.php?module=games/categories");

if($mybb->input['action'] == "add")
{
	$plugins->run_hooks("admin_games_categories_add_start");

	if($mybb->request_method == "post")
	{
		if(empty($mybb->input['title']))
		{
			$errors[] = $lang->error_missing_title;
		}
		if(!intval($mybb->input['active']) && intval($mybb->input['active']) !== 0)
		{
			$errors[] = $lang->error_missing_active;
		}

		if(!$errors)
		{
			$insert_category = array(
				'title'		=> $db->escape_string($mybb->input['title']),
				'image'		=> $db->escape_string($mybb->input['image']),
				'active'	=> intval($mybb->input['active'])
			);

			$plugins->run_hooks("admin_games_categories_add_do");

			$cid = $db->insert_query("games_categories", $insert_category);
			log_admin_action($cid, $mybb->input['title']);

			flash_message($lang->added_category, 'success');
			admin_redirect("index.php?module=games/categories");
		}
	}

	$page->add_breadcrumb_item($lang->nav_add_category);
	$page->output_header($lang->nav_add_category);

	$sub_tabs = array();
	$sub_tabs['overview_categories'] = array(
		'title' => $lang->nav_overview_categories,
		'link' => "index.php?module=games/categories",
		'description' => $lang->nav_overview_categories_desc
	);
	$sub_tabs['add_category'] = array(
		'title' => $lang->nav_add_category,
		'link' => "index.php?module=games/categories&amp;action=add",
		'description' => $lang->nav_add_category_desc
	);
	$page->output_nav_tabs($sub_tabs, 'add_category');

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$form = new Form("index.php?module=games/categories&amp;action=add", "post");
	$form_container = new FormContainer($lang->nav_add_category);

	$form_container->output_row($lang->cat_title." <em>*</em>", false, $form->generate_text_box('title', $mybb->input['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->cat_image, $lang->cat_image_desc, $form->generate_text_box('image', $mybb->input['image'], array('id' => 'image')), 'image');
	$form_container->output_row($lang->cat_active." <em>*</em>", false, $form->generate_yes_no_radio('active', $mybb->input['active']), 'active');

	$plugins->run_hooks("admin_games_categories_add_end");

	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
	$page->output_footer();
}
elseif($mybb->input['action'] == "edit")
{
	$plugins->run_hooks("admin_games_categories_edit_start");

	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_categories WHERE cid='".intval($mybb->input['cid'])."'");
	$category = $db->fetch_array($query);
	if($db->num_rows($query) == 0)
	{
		flash_message($lang->categorydoesntexist, 'error');
		admin_redirect("index.php?module=games/categories");
	}

	if($mybb->request_method == "post")
	{
		if(empty($mybb->input['title']))
		{
			$errors[] = $lang->error_missing_title;
		}
		if(!intval($mybb->input['active']) && intval($mybb->input['active']) !== 0)
		{
			$errors[] = $lang->error_missing_active;
		}

		if(!$errors)
		{
			$update_category = array(
				'title'		=> $db->escape_string($mybb->input['title']),
				'image'		=> $db->escape_string($mybb->input['image']),
				'active'	=> intval($mybb->input['active'])
			);

			$plugins->run_hooks("admin_games_categories_edit_do");

			$gid = $db->update_query("games_categories",
				$update_category,
				"cid='".intval($mybb->input['cid'])."'");
			log_admin_action($mybb->input['cid'], $mybb->input['title']);

			flash_message($lang->edited_category, 'success');
			admin_redirect("index.php?module=games/categories");
		}
	}

	$page->add_breadcrumb_item($lang->nav_edit_category);
	$page->output_header($lang->nav_edit_category);

	$sub_tabs = array();
	$sub_tabs['overview_categories'] = array(
		'title' => $lang->nav_overview_categories,
		'link' => "index.php?module=games/categories",
		'description' => $lang->nav_overview_categories_desc
	);
	$sub_tabs['add_category'] = array(
		'title' => $lang->nav_add_category,
		'link' => "index.php?module=games/categories&amp;action=add",
		'description' => $lang->nav_add_category_desc
	);
	$sub_tabs['edit_category'] = array(
		'title' => $lang->nav_edit_category,
		'link' => "index.php?module=games/categories&amp;action=edit",
		'description' => $lang->nav_edit_category_desc
	);
	$page->output_nav_tabs($sub_tabs, 'edit_category');

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$form = new Form("index.php?module=games/categories&amp;action=edit", "post");
	echo $form->generate_hidden_field("cid", $mybb->input['cid']);
	$form_container = new FormContainer($lang->nav_edit_category);

	$form_container->output_row($lang->cat_title." <em>*</em>", false, $form->generate_text_box('title', $category['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->cat_image, $lang->cat_image_desc, $form->generate_text_box('image', $category['image'], array('id' => 'image')), 'image');
	$form_container->output_row($lang->cat_active." <em>*</em>", false, $form->generate_yes_no_radio('active', $category['active']), 'active');

	$plugins->run_hooks("admin_games_categories_edit_end");

	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
	$page->output_footer();
}
elseif($mybb->input['action'] == "delete")
{
	$plugins->run_hooks("admin_games_categories_delete");

	// Stop if the user clicked no
	if($mybb->input['no'])
	{
		admin_redirect("index.php?module=games/categories");
	}

	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_categories WHERE cid='".intval($mybb->input['cid'])."'");
	$category = $db->fetch_array($query);
	if($db->num_rows($query) == 0)
	{
		flash_message($lang->categorydoesntexist, 'error');
		admin_redirect("index.php?module=games/categories");
	}

	if($mybb->request_method == "post")
	{
		$db->delete_query("games_categories", "cid='".intval($mybb->input['cid'])."'");

		// TODO: Make this a update_query, with an anonymous array
		$db->write_query("UPDATE ".TABLE_PREFIX."games SET cid='0' WHERE cid='".intval($mybb->input['cid'])."'");

		$plugins->run_hooks("admin_games_categories_delete_do");

		log_admin_action($mybb->input['cid'], $category['title']);

		flash_message($lang->deleted_category, 'success');
		admin_redirect("index.php?module=games/categories");
	}
	else
	{
		$page->output_confirm_action("index.php?module=games/categories&action=delete&cid=".$mybb->input['cid'], $lang->delete_category_confirmation);
	}
}
else
{
	$page->output_header($lang->nav_categories);

	$sub_tabs = array();
	$sub_tabs['overview_categories'] = array(
		'title' => $lang->nav_overview_categories,
		'link' => "index.php?module=games/categories",
		'description' => $lang->nav_overview_categories_desc
	);
	$sub_tabs['add_category'] = array(
		'title' => $lang->nav_add_category,
		'link' => "index.php?module=games/categories&amp;action=add",
		'description' => $lang->nav_add_category_desc
	);
	$page->output_nav_tabs($sub_tabs, 'overview_categories');

	$plugins->run_hooks("admin_games_categories_default_start");

	$table = new Table;
	$table->construct_header($lang->nav_categories);
	$table->construct_header($lang->controls, array(
			"class" => "align_center",
			"width" => 150)
		);

	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_categories ORDER BY title ASC");
	if($db->num_rows($query) == 0)
	{
		// TODO: Is this the usual way of doing things, <p><em>?
		$page->output_error("<p><em>".$lang->no_categories."</em></p>");
		$page->output_footer();
	}

	while($categories = $db->fetch_array($query))
	{
		$popup = new PopupMenu("cid".$categories['cid'], $lang->options);
		$popup->add_item($lang->edit, "index.php?module=games/categories&amp;action=edit&amp;cid=".$categories['cid']);
		$popup->add_item($lang->delete, "index.php?module=games/categories&amp;action=delete&amp;cid=".$categories['cid']."&amp;my_post_key=".$mybb->post_code, "return AdminCP.deleteConfirmation(this, '".$lang->delete_category_confirmation."')");
		$popup->add_item($lang->show_games, "index.php?module=games/games&amp;search[cid]=".$categories['cid']);

		$plugins->run_hooks("admin_games_categories_default_while");

		$table->construct_cell("<strong>".$categories['title']."</strong>");
		$table->construct_cell($popup->fetch(), array("class" => "align_center"));
		$table->construct_row();
	}

	$plugins->run_hooks("admin_games_categories_default_end");

	$table->output($lang->nav_categories);
	$page->output_footer();
}
?>
