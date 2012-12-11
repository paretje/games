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

//Require
require_once MYBB_ROOT."inc/functions_upload.php";

//Plugin
$plugins->run_hooks("admin_games_themes_start");

//Navigation
$page->add_breadcrumb_item($lang->gamesection, "index.php?module=games");
$page->add_breadcrumb_item($lang->nav_themes, "index.php?module=games/themes");

if($mybb->input['action'] == "add")
{
	//Plugin
	$plugins->run_hooks("admin_games_themes_add_start");
	
	//Handle the theme
	if($mybb->request_method == "post")
	{
		//Check values
		if(empty($mybb->input['name']))
		{
			$errors[] = $lang->error_missing_name;
		}
		if(empty($mybb->input['directory']))
		{
			$errors[] = $lang->error_missing_directory;
		}
		if(!intval($mybb->input['catsperline']))
		{
			$errors[] = $lang->error_missing_catsperline;
		}
		if(!intval($mybb->input['active']) && intval($mybb->input['active']) !== 0)
		{
			$errors[] = $lang->error_missing_active;
		}
		
		//Test theme
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE name='".$db->escape_string($mybb->input['name'])."'");
		$theme_test = $db->num_rows($query);
		
		if($theme_test != 0)
		{
			$errors[] = $lang->themealreadyexist;
		}
		
		//Check if there were errors, if no, continue
		if(!$errors)
		{
			//Insert theme
			$insert_theme = array(
				'name'		=> $db->escape_string($mybb->input['name']),
				'directory'	=> $db->escape_string($mybb->input['directory']),
				'catsperline'	=> intval($mybb->input['catsperline']),
				'css'		=> $db->escape_string($mybb->input['css']),
				'active'	=> intval($mybb->input['active'])
			);
			
			//Plugin
			$plugins->run_hooks("admin_games_themes_add_do");
			
			$tid = $db->insert_query("games_themes", $insert_theme);
			
			//Insert the default templates
			require_once MYBB_ROOT."games/templates.php";
			
			foreach($theme_templates as $title => $template)
			{
				$template_insert = array(
					"theme"		=> $tid,
					"title"		=> $title,
					"template"	=> $template
				);
				
				$db->insert_query("games_templates", $template_insert);
			}
			
			//Log
			log_admin_action($tid, $mybb->input['name']);
			
			flash_message($lang->added_theme, 'success');
			admin_redirect("index.php?module=games/themes");
		}
	}
	
	//Navigation and header
	$page->add_breadcrumb_item($lang->nav_add_theme);
	$page->output_header($lang->nav_add_theme);
	
	//Show the sub-tabs
	$sub_tabs = array();
	$sub_tabs['overview_themes'] = array(
		'title' => $lang->nav_overview_themes,
		'link' => "index.php?module=games/themes",
		'description' => $lang->nav_overview_themes_desc
	);
	$sub_tabs['add_theme'] = array(
		'title' => $lang->nav_add_theme,
		'link' => "index.php?module=games/themes&amp;action=add",
		'description' => $lang->nav_add_theme_desc
	);
	$sub_tabs['import_theme'] = array(
		'title' => $lang->nav_import_theme,
		'link' => "index.php?module=games/themes&amp;action=import",
		'description' => $lang->nav_import_theme_desc
	);
	
	$page->output_nav_tabs($sub_tabs, 'add_theme');
	
	//Show the errors
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	
	//Selected items
	if(!isset($mybb->input['directory']))
	{
		$mybb->input['directory'] = "images";
	}
	if(!isset($mybb->input['catsperline']))
	{
		$mybb->input['catsperline'] = 5;
	}
	
	//Starts the table and the form
	$form = new Form("index.php?module=games/themes&amp;action=add", "post");
	$form_container = new FormContainer($lang->nav_add_theme);
	
	//Input for theme
	$form_container->output_row($lang->theme_name." <em>*</em>", false, $form->generate_text_box('name', $mybb->input['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->theme_directory." <em>*</em>", $lang->theme_directory_desc, $form->generate_text_box('directory', $mybb->input['directory'], array('id' => 'directory')), 'directory');
	$form_container->output_row($lang->theme_catsperline." <em>*</em>", $lang->theme_catsperline_desc, $form->generate_text_box('catsperline', $mybb->input['catsperline'], array('id' => 'catsperline')), 'catsperline');
	$form_container->output_row($lang->theme_css, $lang->theme_css_desc, $form->generate_text_area('css', $mybb->input['css'], array('id' => 'css')), 'css');
	$form_container->output_row($lang->theme_active." <em>*</em>", false, $form->generate_yes_no_radio('active', $mybb->input['active']), 'active');
	
	//Plugin
	$plugins->run_hooks("admin_games_themes_add_end");
	
	//End of table and form
	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
	$page->output_footer();
}
elseif($mybb->input['action'] == "import")
{
	//Plugin
	$plugins->run_hooks("admin_games_themes_import_start");
	
	//Handle the theme
	if($mybb->request_method == "post")
	{
		//Check values
		if(!intval($mybb->input['active']) && intval($mybb->input['active']) !== 0)
		{
			$errors[] = $lang->error_missing_active;
		}
		
		//Test file
		if(intval($_FILES['theme_file']['size']) == 0 || (strtolower($_FILES['theme_file']['type']) != "application/x-php" && strtolower($_FILES['theme_file']['type']) != "application/x-httpd-php") || !is_uploaded_file($_FILES['theme_file']['tmp_name']))
		{
			$errors[] = $lang->error_missing_theme_file;
		}
		
		//Test if directory is writable
		if(!is_writable(MYBB_ADMIN_DIR."games"))
		{
			$errors[] = $lang->sprintf($lang->not_writable, $config['admin_dir']."/games");
		}
		
		//Check if there were errors, if no, continue
		if(!$errors)
		{
			//Upload theme file
			$file = upload_file($_FILES['theme_file'], MYBB_ADMIN_DIR."games", $_FILES['theme_file']['name']);
			if($file['error'])
			{
				$errors[] = $lang->error_uploadfailed;
			}
			
			//Check if there were errors, if no, continue
			if(!$errors)
			{
				//Load theme file
				require_once MYBB_ADMIN_DIR."games/".$_FILES['theme_file']['name'];
				
				//Test theme
				$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE name='".$db->escape_string($theme['name'])."'");
				$theme_test = $db->num_rows($query);
				
				if($theme_test != 0)
				{
					$errors[] = $lang->themealreadyexist;
				}
				
				//Check if there were errors, if no, continue
				if(!$errors)
				{
					//Insert theme
					$insert_theme = array(
						'name'		=> $db->escape_string($theme['name']),
						'directory'	=> $db->escape_string($theme['directory']),
						'catsperline'	=> intval($theme['catsperline']),
						'css'		=> $db->escape_string($theme['css']),
						'active'	=> intval($mybb->input['active'])
					);
					
					//Plugin
					$plugins->run_hooks("admin_games_themes_import_do");
					
					$tid = $db->insert_query("games_themes", $insert_theme);
					
					//Insert the templates
					foreach($theme_templates as $title => $template)
					{
						$template_insert = array(
							"theme"		=> $tid,
							"title"		=> $title,
							"template"	=> $template
						);
						
						$db->insert_query("games_templates", $template_insert);
					}
					
					//Log
					log_admin_action($tid, $theme['name']);
					
					//Delete theme file
					@unlink(MYBB_ADMIN_DIR."games/".$_FILES['theme_file']['name']);
					
					flash_message($lang->imported_theme, 'success');
					admin_redirect("index.php?module=games/themes");
				}
			}
		}
	}
	
	//Navigation and header
	$page->add_breadcrumb_item($lang->nav_import_theme);
	$page->output_header($lang->nav_import_theme);
	
	//Show the sub-tabs
	$sub_tabs = array();
	$sub_tabs['overview_themes'] = array(
		'title' => $lang->nav_overview_themes,
		'link' => "index.php?module=games/themes",
		'description' => $lang->nav_overview_themes_desc
	);
	$sub_tabs['add_theme'] = array(
		'title' => $lang->nav_add_theme,
		'link' => "index.php?module=games/themes&amp;action=add",
		'description' => $lang->nav_add_theme_desc
	);
	$sub_tabs['import_theme'] = array(
		'title' => $lang->nav_import_theme,
		'link' => "index.php?module=games/themes&amp;action=import",
		'description' => $lang->nav_import_theme_desc
	);
	
	$page->output_nav_tabs($sub_tabs, 'import_theme');
	
	//Show the errors
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	
	//Starts the table and the form
	$form = new Form("index.php?module=games/themes&amp;action=import", "post", false, true);
	$form_container = new FormContainer($lang->nav_import_theme);
	
	//Input for theme
	$form_container->output_row($lang->theme_file." <em>*</em>", false, $form->generate_file_upload_box('theme_file', array('id' => 'theme_file')), 'theme_file');
	$form_container->output_row($lang->theme_active." <em>*</em>", false, $form->generate_yes_no_radio('active', $mybb->input['active']), 'active');
	
	//Plugin
	$plugins->run_hooks("admin_games_themes_import_end");
	
	//End of table and form
	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
	$page->output_footer();
}
elseif($mybb->input['action'] == "edit")
{
	//Plugin
	$plugins->run_hooks("admin_games_themes_edit_start");
	
	//Test theme
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".intval($mybb->input['tid'])."'");
	$theme = $db->fetch_array($query);
	$theme_test = $db->num_rows($query);
	
	if($theme_test == 0)
	{
		flash_message($lang->themedoesntexist, 'error');
		admin_redirect("index.php?module=games/themes");
	}
	
	//Handle the theme
	if($mybb->request_method == "post")
	{
		//Check values
		if(empty($mybb->input['name']))
		{
			$errors[] = $lang->error_missing_name;
		}
		if(empty($mybb->input['directory']))
		{
			$errors[] = $lang->error_missing_directory;
		}
		if(!intval($mybb->input['catsperline']))
		{
			$errors[] = $lang->error_missing_catsperline;
		}
		if(!intval($mybb->input['active']) && intval($mybb->input['active']) !== 0)
		{
			$errors[] = $lang->error_missing_active;
		}
		
		//Check if there were errors, if no, continue
		if(!$errors)
		{
			//Update theme
			$update_theme = array(
				'name'		=> $db->escape_string($mybb->input['name']),
				'directory'	=> $db->escape_string($mybb->input['directory']),
				'catsperline'	=> intval($mybb->input['catsperline']),
				'css'		=> $db->escape_string($mybb->input['css']),
				'active'	=> intval($mybb->input['active'])
			);
			
			//Plugin
			$plugins->run_hooks("admin_games_themes_edit_do");
			
			$db->update_query("games_themes", $update_theme, "tid='".intval($mybb->input['tid'])."'");
			
			//Log
			log_admin_action($mybb->input['tid'], $mybb->input['name']);
			
			flash_message($lang->edited_theme, 'success');
			admin_redirect("index.php?module=games/themes");
		}
	}
	
	//Navigation and header
	$page->add_breadcrumb_item($lang->nav_edit_theme);
	$page->output_header($lang->nav_edit_theme);
	
	//Show the sub-tabs
	$sub_tabs = array();
	$sub_tabs['overview_themes'] = array(
		'title' => $lang->nav_overview_themes,
		'link' => "index.php?module=games/themes",
		'description' => $lang->nav_overview_themes_desc
	);
	$sub_tabs['add_theme'] = array(
		'title' => $lang->nav_add_theme,
		'link' => "index.php?module=games/themes&amp;action=add",
		'description' => $lang->nav_add_theme_desc
	);
	$sub_tabs['import_theme'] = array(
		'title' => $lang->nav_import_theme,
		'link' => "index.php?module=games/themes&amp;action=import",
		'description' => $lang->nav_import_theme_desc
	);
	$sub_tabs['edit_theme'] = array(
		'title' => $lang->nav_edit_theme,
		'link' => "index.php?module=games/themes&amp;action=edit&tid=".$mybb->input['tid'],
		'description' => $lang->nav_edit_theme_desc
	);
	
	$page->output_nav_tabs($sub_tabs, 'edit_theme');
	
	//Show the errors
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	
	//Starts the table and the form
	$form = new Form("index.php?module=games/themes&amp;action=edit", "post");
	echo $form->generate_hidden_field("tid", $mybb->input['tid']);
	$form_container = new FormContainer($lang->nav_edit_theme);
	
	//Input for theme
	$form_container->output_row($lang->theme_name." <em>*</em>", false, $form->generate_text_box('name', $theme['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->theme_directory." <em>*</em>", $lang->theme_directory_desc, $form->generate_text_box('directory', $theme['directory'], array('id' => 'directory')), 'directory');
	$form_container->output_row($lang->theme_catsperline." <em>*</em>", $lang->theme_catsperline_desc, $form->generate_text_box('catsperline', $theme['catsperline'], array('id' => 'catsperline')), 'catsperline');
	$form_container->output_row($lang->theme_css, $lang->theme_css_desc, $form->generate_text_area('css', $theme['CSS'], array('id' => 'css')), 'css');
	$form_container->output_row($lang->theme_active." <em>*</em>", false, $form->generate_yes_no_radio('active', $theme['active'], false), 'active');
	
	//Plugin
	$plugins->run_hooks("admin_games_themes_edit_end");
	
	//End of table and form
	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
	$page->output_footer();
}
elseif($mybb->input['action'] == "delete")
{
	//Plugin
	$plugins->run_hooks("admin_games_themes_delete");
	
	//Test theme
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".intval($mybb->input['tid'])."'");
	$theme = $db->fetch_array($query);
	$theme_test = $db->num_rows($query);
	
	if($theme_test == 0)
	{
		flash_message($lang->themedoesntexist, 'error');
		admin_redirect("index.php?module=games/themes");
	}
	
	// User clicked no
	if($mybb->input['no'])
	{
		admin_redirect("index.php?module=games/themes");
	}
	
	//Handle the theme
	if($mybb->request_method == "post")
	{
		//Delete theme
		$db->delete_query("games_themes", "tid='".intval($mybb->input['tid'])."'");
		
		//Delete templates of the theme
		$db->delete_query("games_templates", "theme='".intval($mybb->input['tid'])."'");
		
		//Plugin
		$plugins->run_hooks("admin_games_themes_delete_do");
		
		//Log
		log_admin_action($mybb->input['tid'], $theme['name']);
		
		flash_message($lang->deleted_theme, 'success');
		admin_redirect("index.php?module=games/themes");
	}
	else
	{
		$page->output_confirm_action("index.php?module=games/themes&action=delete&tid=".$mybb->input['tid'], $lang->delete_theme_confirmation);
	}
}
elseif($mybb->input['action'] == "export")
{
	//Plugin
	$plugins->run_hooks("admin_games_themes_edit_start");
	
	//Test theme
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".intval($mybb->input['tid'])."'");
	$theme = $db->fetch_array($query);
	$theme_test = $db->num_rows($query);
	
	if($theme_test == 0)
	{
		flash_message($lang->themedoesntexist, 'error');
		admin_redirect("index.php?module=games/themes");
	}
	
	//Handle the theme
	if($mybb->request_method == "post")
	{
		//Load templates
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_templates WHERE theme='".intval($mybb->input['tid'])."' ORDER BY title ASC");
		while($templates = $db->fetch_array($query))
		{
			$templates['template'] = str_replace('\\\\', "\\\\\\", str_replace('$', '\$', $db->escape_string($templates['template'])));
			
			$templates_vars .= "\$theme_templates['".$templates['title']."'] = \"".$templates['template']."\";\n\n";
		}
		
		//Start header
		$header = "<?php
/**
 * ".$theme['name']." Theme for the Game Section";
		
		//Copyright
		if(!empty($mybb->input['copyright']))
		{
			$header .= "\n * Copyright ".$mybb->input['copyright'];
		}
		
		//Website
		if(!empty($mybb->input['website']))
		{
			$header .= "\n * Website: ".$mybb->input['website'];
		}
		
		//Support
		if(!empty($mybb->input['support']))
		{
			$header .= "\n * Support: ".$mybb->input['support'];
		}
		
		//License
		if(!empty($mybb->input['license']))
		{
			$header .= "
 * 
 * License: ".$mybb->input['license'];
		}
		
		//Close header + theme information + templates
		$header .= "
*/

\$theme['name'] = \"".$theme['name']."\";
\$theme['directory'] = \"".$theme['directory']."\";
\$theme['catsperline'] = \"".$theme['catsperline']."\";
\$theme['css'] = \"".$theme['CSS']."\";

".$templates_vars."
?>>";
		
		//Output created php file
		header("Content-disposition: attachment; filename=".str_replace (" ", "_", $theme['name'])."_theme.php");	
		header("Content-type: application/x-php");
		header("Content-Length: ".my_strlen($header));
		
		echo $header;
	}
	
	//Navigation and header
	$page->add_breadcrumb_item($lang->nav_export_theme);
	$page->output_header($lang->nav_export_theme);
	
	//Show the sub-tabs
	$sub_tabs = array();
	$sub_tabs['overview_themes'] = array(
		'title' => $lang->nav_overview_themes,
		'link' => "index.php?module=games/themes",
		'description' => $lang->nav_overview_themes_desc
	);
	$sub_tabs['add_theme'] = array(
		'title' => $lang->nav_add_theme,
		'link' => "index.php?module=games/themes&amp;action=add",
		'description' => $lang->nav_add_theme_desc
	);
	$sub_tabs['import_theme'] = array(
		'title' => $lang->nav_import_theme,
		'link' => "index.php?module=games/themes&amp;action=import",
		'description' => $lang->nav_import_theme_desc
	);
	$sub_tabs['export_theme'] = array(
		'title' => $lang->nav_export_theme,
		'link' => "index.php?module=games/themes&amp;action=export&tid=".$mybb->input['tid'],
		'description' => $lang->nav_export_theme_desc
	);
	
	$page->output_nav_tabs($sub_tabs, 'export_theme');
	
	//Show the errors
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	
	//Starts the table and the form
	$form = new Form("index.php?module=games/themes&amp;action=export", "post");
	echo $form->generate_hidden_field("tid", $mybb->input['tid']);
	$form_container = new FormContainer($lang->nav_export_theme);
	
	//Input for theme
	$form_container->output_row($lang->theme_export_copyright, $lang->theme_export_copyright_desc, $form->generate_text_box('copyright', my_date("Y")." ".$mybb->settings['bbname'], array('id' => 'copyright')), 'copyright');
	$form_container->output_row($lang->theme_export_website, false, $form->generate_text_box('website', $mybb->settings['bburl'], array('id' => 'website')), 'website');
	$form_container->output_row($lang->theme_export_support, $lang->theme_export_support_desc, $form->generate_text_box('support', $mybb->settings['bburl'], array('id' => 'support')), 'support');
	$form_container->output_row($lang->theme_export_license, $lang->theme_export_license_desc, $form->generate_text_box('license', "GNU/GPL v3", array('id' => 'license')), 'license');
	
	//Plugin
	$plugins->run_hooks("admin_games_themes_export_end");
	
	//End of table and form
	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->export);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
	$page->output_footer();
}
else
{
	//Navigation and header
	$page->output_header($lang->nav_themes);
	
	//Show the sub-tabs
	$sub_tabs = array();
	$sub_tabs['overview_themes'] = array(
		'title' => $lang->nav_overview_themes,
		'link' => "index.php?module=games/themes",
		'description' => $lang->nav_overview_themes_desc
	);
	$sub_tabs['add_theme'] = array(
		'title' => $lang->nav_add_theme,
		'link' => "index.php?module=games/themes&amp;action=add",
		'description' => $lang->nav_add_theme_desc
	);
	$sub_tabs['import_theme'] = array(
		'title' => $lang->nav_import_theme,
		'link' => "index.php?module=games/themes&amp;action=import",
		'description' => $lang->nav_import_theme_desc
	);
	
	$page->output_nav_tabs($sub_tabs, 'overview_themes');
	
	//Plugin
	$plugins->run_hooks("admin_games_themes_default_start");
	
	//Start table
	$table = new Table;
	$table->construct_header($lang->nav_themes);
	$table->construct_header($lang->controls, array("class" => "align_center", "width" => 150));
	
	//Load the themes
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes ORDER BY name ASC");
	$themes_test = $db->num_rows($query);
	
	//Test themes
	if($themes_test == 0)
	{
		$page->output_error("<p><em>".$lang->no_themes."</em></p>");
		$page->output_footer();
	}
	
	while($themes = $db->fetch_array($query))
	{
		//Controls
		$popup = new PopupMenu("tid".$themes['tid'], $lang->options);
		$popup->add_item($lang->edit, "index.php?module=games/themes&amp;action=edit&amp;tid=".$themes['tid']);
		$popup->add_item($lang->delete, "index.php?module=games/themes&amp;action=delete&amp;tid=".$themes['tid']."&amp;my_post_key=".$mybb->post_code, "return AdminCP.deleteConfirmation(this, '".$lang->delete_theme_confirmation."')");
		$popup->add_item($lang->export, "index.php?module=games/themes&amp;action=export&amp;tid=".$themes['tid']);
		
		//Plugin
		$plugins->run_hooks("admin_games_themes_default_while");
		
		//Output row
		$table->construct_cell("<strong>".$themes['name']."</strong>");
		$table->construct_cell($popup->fetch(), array("class" => "align_center"));
		
		$table->construct_row();
	}
	
	//Plugin
	$plugins->run_hooks("admin_games_themes_default_end");
	
	//End of table and AdminCP footer
	$table->output($lang->nav_themes);
	$page->output_footer();
}
?>