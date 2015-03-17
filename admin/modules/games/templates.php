<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2015 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 17/03/2015 by Paretje
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

//Plugin
$plugins->run_hooks("admin_games_templates_start");

//Navigation
$page->add_breadcrumb_item($lang->gamesection, "index.php?module=games");

if($mybb->input['action'] == "add")
{
	//Plugin
	$plugins->run_hooks("admin_games_templates_add_start");
	
	//Test theme
	if(intval($mybb->input['tid']) !== 0)
	{
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".intval($mybb->input['tid'])."'");
		$theme = $db->fetch_array($query);
		$theme_test = $db->num_rows($query);
		
		if($theme_test == 0)
		{
			$errors[] = $lang->themedoesntexist;
		}
	}
	
	//Handle the template
	if($mybb->request_method == "post")
	{
		//Check values
		if(empty($mybb->input['title']))
		{
			$errors[] = $lang->error_missing_title;
		}
		if(empty($mybb->input['template']))
		{
			$errors[] = $lang->error_missing_template;
		}
		
		//Check if there were errors, if no, continue
		if(!$errors)
		{
			//Insert template
			$insert_template = array(
				'theme'		=> intval($mybb->input['tid']),
				'title'		=> $db->escape_string($mybb->input['title']),
				'template'	=> $db->escape_string($mybb->input['template'])
			);
			
			//Plugin
			$plugins->run_hooks("admin_games_templates_add_do");
			
			$tid = $db->insert_query("games_templates", $insert_template);
			
			//Log
			if(intval($mybb->input['tid']) !== 0)
			{
				log_admin_action($tid, $mybb->input['title'], $mybb->input['tid'], $theme['name']);
			}
			else
			{
				log_admin_action($tid, $mybb->input['title'], $mybb->input['tid'], $lang->template_set_global);
			}
			
			//Redirect
			flash_message($lang->added_template, 'success');
			
			if($mybb->input['continue'])
			{
				admin_redirect("index.php?module=games/templates&amp;action=edit&amp;tid=".$tid);
			}
			else
			{
				admin_redirect("index.php?module=games/templates&amp;tid=".$mybb->input['tid']);
			}
		}
	}
	
	//Is this the Global theme?
	if(intval($mybb->input['tid']) !== 0)
	{
		//Language template set
		$lang->template_set = $lang->sprintf($lang->template_set, $theme['name']);
	}
	else
	{
		$lang->template_set = $lang->template_set_global;
	}

	//Extra header
	$page->extra_header .= '
<link href="./jscripts/codemirror/lib/codemirror.css" rel="stylesheet">
<link href="./jscripts/codemirror/theme/mybb.css" rel="stylesheet">
<script src="./jscripts/codemirror/lib/codemirror.js"></script>
<script src="./jscripts/codemirror/mode/xml/xml.js"></script>
<script src="./jscripts/codemirror/mode/javascript/javascript.js"></script>
<script src="./jscripts/codemirror/mode/css/css.js"></script>
<script src="./jscripts/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<link href="./jscripts/codemirror/addon/dialog/dialog-mybb.css" rel="stylesheet" >
<script src="./jscripts/codemirror/addon/dialog/dialog.js"></script>
<script src="./jscripts/codemirror/addon/search/searchcursor.js"></script>
<script src="./jscripts/codemirror/addon/search/search.js"></script>
';
	
	//Navigation and header
	$page->add_breadcrumb_item($lang->nav_templates, "index.php?module=games/templates");
	$page->add_breadcrumb_item($lang->template_set, "index.php?module=games/templates&amp;tid=".$mybb->input['tid']);
	$page->add_breadcrumb_item($lang->nav_add_template);
	$page->output_header($lang->nav_add_template);
	
	//Show the sub-tabs
	$sub_tabs = array();
	$sub_tabs['manage_templates'] = array(
		'title' => $lang->nav_manage_templates,
		'link' => "index.php?module=games/templates&amp;tid=".$mybb->input['tid'],
		'description' => $lang->nav_manage_templates_desc
	);
	$sub_tabs['add_template'] = array(
		'title' => $lang->nav_add_template,
		'link' => "index.php?module=games/templates&amp;action=add&amp;tid=".$mybb->input['tid'],
		'description' => $lang->nav_add_template_desc
	);
	
	$page->output_nav_tabs($sub_tabs, 'add_template');
	
	//Show the errors
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	
	//Starts the table and the form
	$form = new Form("index.php?module=games/templates&amp;action=add", "post", "add");
	echo $form->generate_hidden_field("tid", $mybb->input['tid']);
	$form_container = new FormContainer($lang->nav_add_template);
	
	$form_container->output_row($lang->template_title, false, $form->generate_text_box('title', $mybb->input['title'], array('id' => 'title')), 'title');
	$form_container->output_row("", "", $form->generate_text_area('template', $mybb->input['template'], array('id' => 'template', 'class' => '', 'style' => 'width: 100%; height: 500px;')), 'template');
	
	//Plugin
	$plugins->run_hooks("admin_games_templates_add_end");
	
	//End of table and form
	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->save_continue, array('name' => 'continue'));
	$buttons[] = $form->generate_submit_button($lang->save_return);
	$form->output_submit_wrapper($buttons);
	$form->end();

	echo "<script type=\"text/javascript\">
		var editor = CodeMirror.fromTextArea(document.getElementById(\"template\"), {
			lineNumbers: true,
			lineWrapping: true,
			mode: \"text/html\",
			tabMode: \"indent\",
			theme: \"mybb\"
		});
	</script>";
	
	$page->output_footer();
}
elseif($mybb->input['action'] == "edit")
{
	//Plugin
	$plugins->run_hooks("admin_games_templates_edit_start");
	
	//Test template
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_templates WHERE tid='".intval($mybb->input['tid'])."'");
	$template = $db->fetch_array($query);
	$template_test = $db->num_rows($query);
	
	if($template_test == 0)
	{
		flash_message($lang->templatedoesntexist, 'error');
		admin_redirect("index.php?module=games/templates");
	}
	
	//Test theme
	if($template['theme'] != 0 && !$errors)
	{
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".$template['theme']."'");
		$theme = $db->fetch_array($query);
		$theme_test = $db->num_rows($query);
		
		if($theme_test == 0)
		{
			flash_message($lang->themedoesntexist, 'error');
			admin_redirect("index.php?module=games/templates");
		}
	}
	
	//Handle the template
	if($mybb->request_method == "post")
	{
		//Check values
		if(empty($mybb->input['title']))
		{
			$errors[] = $lang->error_missing_title;
		}
		if(empty($mybb->input['template']))
		{
			$errors[] = $lang->error_missing_template;
		}
		
		//Check if there were errors, if no, continue
		if(!$errors)
		{
			//Update template
			$update_template = array(
				'title'		=> $db->escape_string($mybb->input['title']),
				'template'	=> $db->escape_string($mybb->input['template'])
			);
			
			//Plugin
			$plugins->run_hooks("admin_games_templates_edit_do");
			
			$db->update_query("games_templates", $update_template, "tid='".intval($mybb->input['tid'])."'");
			
			//Log
			if(intval($template['theme']) !== 0)
			{
				log_admin_action($mybb->input['tid'], $mybb->input['title'], $template['theme'], $theme['name']);
			}
			else
			{
				log_admin_action($mybb->input['tid'], $mybb->input['title'], $template['theme'], $lang->template_set_global);
			}
			
			//Redirect
			flash_message($lang->edited_template, 'success');
			
			if($mybb->input['continue'])
			{
				admin_redirect("index.php?module=games/templates&amp;action=edit&amp;tid=".$mybb->input['tid']);
			}
			else
			{
				admin_redirect("index.php?module=games/templates&amp;tid=".$template['theme']);
			}
		}
	}
	
	//Is this the Global theme?
	if(intval($template['theme']) !== 0)
	{
		//Language template set
		$lang->template_set = $lang->sprintf($lang->template_set, $theme['name']);
	}
	else
	{
		$lang->template_set = $lang->template_set_global;
	}
	
	//Extra header
	$page->extra_header .= "
	<link type=\"text/css\" href=\"./jscripts/codepress/languages/codepress-mybb.css\" rel=\"stylesheet\" id=\"cp-lang-style\" />
	<script type=\"text/javascript\" src=\"./jscripts/codepress/codepress.js\"></script>
	<script type=\"text/javascript\">
		CodePress.language = \'mybb\';
	</script>";
	
	//Navigation and header
	$page->add_breadcrumb_item($lang->nav_templates, "index.php?module=games/templates");
	$page->add_breadcrumb_item($lang->template_set, "index.php?module=games/templates&amp;tid=".$template['theme']);
	$page->add_breadcrumb_item($lang->nav_edit_template);
	$page->output_header($lang->nav_edit_template);
	
	//Show the sub-tabs
	$sub_tabs = array();
	$sub_tabs['manage_templates'] = array(
		'title' => $lang->nav_manage_templates,
		'link' => "index.php?module=games/templates&amp;tid=".$theme['tid'],
		'description' => $lang->nav_manage_templates_desc
	);
	$sub_tabs['add_template'] = array(
		'title' => $lang->nav_add_template,
		'link' => "index.php?module=games/templates&amp;action=add&amp;tid=".$theme['tid'],
		'description' => $lang->nav_add_template_desc
	);
	$sub_tabs['edit_template'] = array(
		'title' => $lang->nav_edit_template,
		'link' => "index.php?module=games/templates&amp;action=edit&amp;tid=".$mybb->input['tid'],
		'description' => $lang->nav_edit_template_desc
	);
	
	$page->output_nav_tabs($sub_tabs, 'edit_template');
	
	//Show the errors
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	
	//Starts the table and the form
	$form = new Form("index.php?module=games/templates&amp;action=edit", "post", "edit");
	echo $form->generate_hidden_field("tid", $mybb->input['tid']);
	$form_container = new FormContainer($lang->nav_edit_template);
	
	//Input for game
	$form_container->output_row($lang->template_title, false, $form->generate_text_box('title', $template['title'], array('id' => 'title')), 'title');
	$form_container->output_row("", "", $form->generate_text_area('template', $template['template'], array('id' => 'template', 'class' => 'codepress mybb', 'style' => 'width: 100%; height: 500px;')), 'template');
	
	//Plugin
	$plugins->run_hooks("admin_games_templates_edit_end");
	
	//End of table and form
	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->save_continue, array('name' => 'continue'));
	$buttons[] = $form->generate_submit_button($lang->save_return);
	$form->output_submit_wrapper($buttons);
	$form->end();
	
	echo "<script language=\"Javascript\" type=\"text/javascript\">
	Event.observe('edit', 'submit', function()
	{
		if($('template_cp')) {
			var area = $('template_cp');
			area.id = 'template';
			area.value = template.getCode();
			area.disabled = false;
		}
	});
</script>";
	
	$page->output_footer();
}
elseif($mybb->input['action'] == "revert")
{
	//Plugin
	$plugins->run_hooks("admin_games_templates_revert");
	
	//Load templates
	require_once MYBB_ROOT."games/templates.php";
	
	//Test template
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_templates WHERE tid='".intval($mybb->input['tid'])."'");
	$template = $db->fetch_array($query);
	$template_test = $db->num_rows($query);
	
	if($template_test == 0 || !isset($theme_templates[$template['title']]))
	{
		flash_message($lang->templatedoesntexist, 'error');
		admin_redirect("index.php?module=games/templates");
	}
	
	// User clicked no
	if($mybb->input['no'])
	{
		admin_redirect("index.php?module=games/templates&tid=".$template['theme']);
	}
	
	//Handle the template
	if($mybb->request_method == "post")
	{
		//Revert template
		$revert_template = array(
			"template" => $theme_templates[$template['title']]
		);
		
		//Plugin
		$plugins->run_hooks("admin_games_templates_revert_do");
		
		$db->update_query("games_templates", $revert_template, "tid=".intval($mybb->input['tid']));
		
		//Log
		if(intval($template['theme']) !== 0)
		{
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".intval($template['theme'])."'");
			$theme = $db->fetch_array($query);
			
			log_admin_action($mybb->input['tid'], $template['title'], $template['theme'], $theme['name']);
		}
		else
		{
			log_admin_action($mybb->input['tid'], $template['title'], $template['theme'], $lang->template_set_global);
		}
		
		flash_message($lang->reverted_template, 'success');
		admin_redirect("index.php?module=games/templates&tid=".$template['theme']);
	}
	else
	{
		$page->output_confirm_action("index.php?module=games/templates&action=revert&tid=".$mybb->input['tid'], $lang->revert_template_confirmation);
	}
}
elseif($mybb->input['action'] == "delete")
{
	//Plugin
	$plugins->run_hooks("admin_games_templates_delete");
	
	//Test template
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_templates WHERE tid='".intval($mybb->input['tid'])."'");
	$template = $db->fetch_array($query);
	$template_test = $db->num_rows($query);
	
	if($template_test == 0)
	{
		flash_message($lang->templatedoesntexist, 'error');
		admin_redirect("index.php?module=games/templates");
	}
	
	// User clicked no
	if($mybb->input['no'])
	{
		admin_redirect("index.php?module=games/templates&tid=".$template['theme']);
	}
	
	//Handle the template
	if($mybb->request_method == "post")
	{
		//Delete template
		$db->delete_query("games_templates", "tid='".intval($mybb->input['tid'])."'");
		
		//Plugin
		$plugins->run_hooks("admin_games_templates_delete_do");
		
		//Log
		if(intval($template['theme']) !== 0)
		{
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".intval($template['theme'])."'");
			$theme = $db->fetch_array($query);
			
			log_admin_action($mybb->input['tid'], $template['title'], $template['theme'], $theme['name']);
		}
		else
		{
			log_admin_action($mybb->input['tid'], $template['title'], $template['theme'], $lang->template_set_global);
		}
		
		flash_message($lang->deleted_template, 'success');
		admin_redirect("index.php?module=games/templates&tid=".$template['theme']);
	}
	else
	{
		$page->output_confirm_action("index.php?module=games/templates&action=delete&tid=".$mybb->input['tid'], $lang->delete_template_confirmation);
	}
}
else
{
	if(isset($mybb->input['tid']))
	{
		$tid = intval($mybb->input['tid']);
		
		//Is this the Global theme?
		if(intval($mybb->input['tid']) !== 0)
		{
			//Load theme
			$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes WHERE tid='".$tid."'");
			$theme = $db->fetch_array($query);
			$theme_test = $db->num_rows($query);
			
			if($theme_test == 0)
			{
				flash_message($lang->themedoesntexist, 'error');
				admin_redirect("index.php?module=games/templates");
			}
			
			//Load default templates
			require_once MYBB_ROOT."games/templates.php";
			
			//Language template set
			$lang->template_set = $lang->sprintf($lang->template_set, $theme['name']);
		}
		else
		{
			$lang->template_set = $lang->template_set_global;
		}
		
		//Navigation and header
		$page->add_breadcrumb_item($lang->nav_templates, "index.php?module=games/templates");
		$page->add_breadcrumb_item($lang->template_set);
		$page->output_header($lang->template_set);
		
		//Show the sub-tabs
		$sub_tabs = array();
		$sub_tabs['manage_templates'] = array(
			'title' => $lang->nav_manage_templates,
			'link' => "index.php?module=games/templates",
			'description' => $lang->nav_manage_templates_desc
		);
		$sub_tabs['add_template'] = array(
			'title' => $lang->nav_add_template,
			'link' => "index.php?module=games/templates&amp;action=add&amp;tid=".$mybb->input['tid'],
			'description' => $lang->nav_add_template_desc
		);
		
		$page->output_nav_tabs($sub_tabs, 'manage_templates');
		
		//Plugin
		$plugins->run_hooks("admin_games_templates_templates_start");
		
		//Start table
		$table = new Table;
		$table->construct_header($lang->nav_templates);
		$table->construct_header($lang->controls, array("class" => "align_center", "width" => 150));
		
		//Load the templates
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_templates WHERE theme='".$tid."' ORDER BY title ASC");
		$templates_test = $db->num_rows($query);
		
		//Test templates
		if($templates_test == 0)
		{
			$page->output_error("<p><em>".$lang->no_templates."</em></p>");
			$page->output_footer();
		}
		
		while($templates = $db->fetch_array($query))
		{
			//Controls
			$popup = new PopupMenu("tid".$templates['tid'], $lang->options);
			$popup->add_item($lang->edit, "index.php?module=games/templates&amp;action=edit&amp;tid=".$templates['tid']);
			if(isset($theme_templates[$templates['title']]))
			{
				$popup->add_item($lang->revert, "index.php?module=games/templates&amp;action=revert&amp;tid=".$templates['tid']."&amp;my_post_key=".$mybb->post_code, "return AdminCP.deleteConfirmation(this, '".$lang->revert_template_confirmation."')");
			}
			$popup->add_item($lang->delete, "index.php?module=games/templates&amp;action=delete&amp;tid=".$templates['tid']."&amp;my_post_key=".$mybb->post_code, "return AdminCP.deleteConfirmation(this, '".$lang->delete_template_confirmation."')");
			
			//Plugin
			$plugins->run_hooks("admin_games_templates_templates_while");
			
			//Output row
			$table->construct_cell("<a href=\"index.php?module=games/templates&amp;action=edit&amp;tid=".$templates['tid']."\">".$templates['title']."</a>");
			$table->construct_cell($popup->fetch(), array("class" => "align_center"));
			
			$table->construct_row();
		}
		
		//Plugin
		$plugins->run_hooks("admin_games_templates_templates_end");
		
		//End of table and AdminCP footer
		$table->output($lang->template_set);
		$page->output_footer();
	}
	else
	{
		//Navigation and header
		$page->add_breadcrumb_item($lang->nav_templates);
		$page->output_header($lang->nav_templates);
		
		//Show the sub-tabs
		$sub_tabs = array();
		$sub_tabs['manage_templates'] = array(
			'title' => $lang->nav_manage_templates,
			'link' => "index.php?module=games/templates",
			'description' => $lang->nav_manage_templates_desc
		);
		
		$page->output_nav_tabs($sub_tabs, 'manage_templates');
		
		//Plugin
		$plugins->run_hooks("admin_games_templates_sets_start");
		
		//Start table
		$table = new Table;
		$table->construct_header($lang->nav_templates);
		$table->construct_header($lang->controls, array("class" => "align_center", "width" => 150));
		
		//Default template set
		$table->construct_cell("<strong><a href=\"index.php?module=games/templates&amp;tid=0\">".$lang->template_set_global."</a></strong>");
		$table->construct_cell("<a href=\"index.php?module=games/templates&amp;tid=0\">".$lang->expand."</a>", array("class" => "align_center"));
		$table->construct_row();
		
		//Load the themes
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes ORDER BY name ASC");
		while($themes = $db->fetch_array($query))
		{
			$table->construct_cell("<strong><a href=\"index.php?module=games/templates&amp;tid=".$themes['tid']."\">".$lang->sprintf($lang->template_set, $themes['name'])."</a></strong>");
			$table->construct_cell("<a href=\"index.php?module=games/templates&amp;tid=".$themes['tid']."\">".$lang->expand."</a>", array("class" => "align_center"));
			
			//Plugin
			$plugins->run_hooks("admin_games_templates_sets_while");
			
			$table->construct_row();
		}
		
		//Plugin
		$plugins->run_hooks("admin_games_templates_sets_end");
		
		//End of table and AdminCP footer
		$table->output($lang->nav_templates);
		$page->output_footer();
	}
}
?>
