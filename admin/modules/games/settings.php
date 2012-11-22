<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2009 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 24/01/2009 by Paretje
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
$plugins->run_hooks("admin_games_settings_start");

//Navigation
$page->add_breadcrumb_item($lang->gamesection, "index.php?module=games");
$page->add_breadcrumb_item($lang->nav_settings, "index.php?module=games/settings");

if($mybb->input['action'] == "edit")
{
	//Test settinggroup
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_settings_groups WHERE gid='".intval($mybb->input['gid'])."'");
	$settinggroup = $db->fetch_array($query);
	$settinggroup_test = $db->num_rows($query);
	
	if($settinggroup_test == 0)
	{
		flash_message($lang->settinggroupdoesntexist, 'error');
		admin_redirect("index.php?module=games/settings");
	}
	
	//Handle the settings
	if($mybb->request_method == "post")
	{
		if(is_array($mybb->input['settings']))
		{
			foreach($mybb->input['settings'] as $name => $value)
			{
				$db->update_query("games_settings", array('value' => addslashes($value)), "name='".addslashes($name)."'");
			}
			
			//Log
			log_admin_action();
			
			flash_message($lang->edited_settings, 'success');
			admin_redirect("index.php?module=games/settings");
		}
	}	
	
	//Settinggroup title
	$lang_grouptitle = "settings_group_title_".$settinggroup['name'];
	
	if(isset($lang->$lang_grouptitle))
	{
		$grouptitle = $lang->$lang_grouptitle;
	}
	else
	{
		$grouptitle = $settinggroup['title'];
	}
	
	//Navigation and header
	$page->add_breadcrumb_item($grouptitle, "index.php?module=games/settings");
	$page->output_header($lang->nav_settings." - ".$grouptitle);
	
	//Show the sub-tabs
	$sub_tabs = array();
	$sub_tabs['manage_settings'] = array(
		'title' => $lang->nav_manage_settings,
		'link' => "index.php?module=games/settings",
		'description' => $lang->nav_manage_settings_desc
	);
	
	$page->output_nav_tabs($sub_tabs, 'manage_settings');
	
	//Plugin
	$plugins->run_hooks("admin_games_settings_edit_start");
	
	//Starts the table and the form
	$form = new Form("index.php?module=games/settings&amp;action=edit", "post");
	echo $form->generate_hidden_field("gid", $mybb->input['gid']);
	$form_container = new FormContainer($grouptitle);
	
	//Load the settings
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."games_settings WHERE gid='".intval($mybb->input['gid'])."' ORDER BY displayorder ASC");
	
	while($settings = $db->fetch_array($query))
	{
		//Generate options
		$type = explode("\n", $settings['optionscode']);
		$type[0] = trim($type[0]);
		$setting_options = array();
		$setting_code = "";
		$setting_name = "settings[".$settings['name']."]";
		$setting_id = "setting_".$settings['name'];
		
		if($type[0] == "text" || $type[0] == "")
		{
			$setting_code = $form->generate_text_box($setting_name, $settings['value']);
		}
		else if($type[0] == "textarea")
		{
			$setting_code = $form->generate_text_area($setting_name, $settings['value']);
		}
		else if($type[0] == "yesno")
		{
			$setting_code = $form->generate_yes_no_radio($setting_name, $settings['value']);
		}
		else if($type[0] == "php")
		{
			$settings['optionscode'] = substr($settings['optionscode'], 3);
			eval("\$setting_code = \"".$settings['optionscode']."\";");
		}
		elseif($type[0] == "theme")
		{
			//Load Themes
			$query2 = $db->query("SELECT * FROM ".TABLE_PREFIX."games_themes ORDER BY name ASC");
			while($themes = $db->fetch_array($query2))
			{
				$setting_options[$themes['tid']] = $themes['name'];
			}
			
			$setting_code = $form->generate_select_box($setting_name, $setting_options, $settings['value']);
		}
		elseif($type[0] == "select" || $type[0] == "radio" || $type[0] == "checkbox")
		{
			for($i=0; $i < count($type); $i++)
			{
				$optionsexp = explode("=", $type[$i]);
				
				//Control option
				if(!isset($optionsexp[1]))
				{
					continue;
				}
				
				//Option language
				$title_lang = "settings_".$settings['name']."_".$optionsexp[0];
				
				if($lang->$title_lang)
				{
					$optionsexp[1] = $lang->$title_lang;
				}
				
				//Generate the code for the types select, radio and checkbox
				if($type[0] == "select")
				{
					$setting_options[$optionsexp[0]] = htmlspecialchars_uni($optionsexp[1]);
				}
				else if($type[0] == "radio")
				{
					if($settings['value'] == $optionsexp[0])
					{
						$setting_options[$i] = $form->generate_radio_button($setting_name, $optionsexp[0], htmlspecialchars_uni($optionsexp[1]), array("checked" => 1));
					}
					else
					{
						$setting_options[$i] = $form->generate_radio_button($setting_name, $optionsexp[0], htmlspecialchars_uni($optionsexp[1]));
					}
				}
				else if($type[0] == "checkbox")
				{
					if($setting['value'] == $optionsexp[0])
					{
						$setting_options[$i] = $form->generate_checkbox_input($setting_name, $optionsexp[0], htmlspecialchars_uni($optionsexp[1]), array("checked" => 1));
					}
					else
					{
						$setting_options[$i] = $form->generate_checkbox_input($setting_name, $optionsexp[0], htmlspecialchars_uni($optionsexp[1]));
					}
				}
			}
			
			//Paste all the code together
			if($type[0] == "select")
			{
				$setting_code = $form->generate_select_box($setting_name, $setting_options, $settings['value']);
			}
			else
			{
				$setting_code = implode("<br />", $setting_options);
			}
		}
		
		//Plugin
		$plugins->run_hooks("admin_games_settings_edit_while");
		
		//Setting title
		$lang_title = "settings_title_".$settings['name'];
		
		if(isset($lang->$lang_title))
		{
			$settings['title'] = $lang->$lang_title;
		}
		
		//Setting description
		$lang_desc = "settings_desc_".$settings['name'];
		
		if(isset($lang->$lang_desc))
		{
			$settings['description'] = $lang->$lang_desc;
		}
		
		//Output
		$form_container->output_row($settings['title'], $settings['description'], $setting_code);
	}
	
	//Plugin
	$plugins->run_hooks("admin_games_settings_edit_end");
	
	//End of table and form
	$form_container->end();
	$buttons[] = $form->generate_submit_button($lang->save);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
	$page->output_footer();
}
else
{
	//Navigation and header
	$page->output_header($lang->nav_settings);
	
	//Show the sub-tabs
	$sub_tabs = array();
	$sub_tabs['manage_settings'] = array(
		'title' => $lang->nav_manage_settings,
		'link' => "index.php?module=games/settings",
		'description' => $lang->nav_manage_settings_desc
	);
	
	$page->output_nav_tabs($sub_tabs, 'manage_settings');
	
	//Plugin
	$plugins->run_hooks("admin_games_settings_default_start");
	
	//Start table
	$table = new Table;
	
	//Load the settinggroups
	$query = $db->query("SELECT g.*, COUNT(s.sid) AS settingcount
	FROM ".TABLE_PREFIX."games_settings_groups g
	LEFT JOIN ".TABLE_PREFIX."games_settings s ON (s.gid=g.gid)
	GROUP BY g.gid
	ORDER BY g.displayorder ASC");
	
	while($settings = $db->fetch_array($query))
	{
		//Settinggroup title
		$lang_title = "settings_group_title_".$settings['name'];
		
		if(isset($lang->$lang_title))
		{
			$title = htmlspecialchars_uni($lang->$lang_title);
		}
		else
		{
			$title = htmlspecialchars_uni($settings['title']);
		}
		
		//Settinggroup description
		$lang_desc = "settings_group_desc_".$settings['name'];
		
		if(isset($lang->$lang_desc))
		{
			$description = htmlspecialchars_uni($lang->$lang_desc);
		}
		else
		{
			$description = htmlspecialchars_uni($settings['description']);
		}
		
		//Settings count
		if($settings['settingcount'] != 1)
		{
			$settings_count = $lang->sprintf($lang->settings_count, $settings['settingcount']);
		}
		else
		{
			$settings_count = $lang->setting_count;
		}
		
		//Plugin
		$plugins->run_hooks("admin_games_settings_default_while");
		
		//Output row
		$table->construct_cell("<strong><a href=\"index.php?module=games/settings&amp;action=edit&amp;gid=".$settings['gid']."\">".$title."</a></strong> (".$settings_count.")<br />
<small>".$description."</small>");
		
		$table->construct_row();
	}
	
	//Plugin
	$plugins->run_hooks("admin_games_settings_default_end");
	
	//End of table and AdminCP footer
	$table->output($lang->nav_settings);
	$page->output_footer();
}
?>