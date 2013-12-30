<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2008 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 21/07/2008 by Paretje
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
require_once MYBB_ROOT."inc/functions_games.php";
require_once MYBB_ROOT."inc/functions_upload.php";

//Plugin
$plugins->run_hooks("admin_games_gamedata_start");

//Navigation
$page->add_breadcrumb_item($lang->gamesection, "index.php?module=games");
$page->add_breadcrumb_item($lang->nav_gamedata, "index.php?module=games/gamedata");

//Declare the sub-tabs
if($mybb->input['action'] == "" || $mybb->input['action'] == "add" || $mybb->input['action'] == "add_directory" || $mybb->input['action'] == "delete" || $mybb->input['action'] == "delete_directory")
{
	$sub_tabs = array();
	$sub_tabs['manage_gamedata'] = array(
		'title' => $lang->nav_manage_gamedata,
		'link' => "index.php?module=games/gamedata",
		'description' => $lang->nav_manage_gamedata_desc
	);
}

if($mybb->input['action'] == "add" && $mybb->request_method == "post")
{
	//Directory
	if(!empty($mybb->input['directory']))
	{
		$directory = "/".$mybb->input['directory'];
		$url_directory = "&amp;directory=".$mybb->input['directory'];
	}
	
	//Check values
	if(!is_uploaded_file($_FILES['file']['tmp_name']))
	{
		$errors[] = $lang->error_missing_file;
	}
	
	//Test if directory is writable
	if(!is_writable(MYBB_ROOT."arcade/gamedata".$directory))
	{
		$errors[] = $lang->sprintf($lang->not_writable, "arcade/gamedata".$directory);
	}
	
	//Check if there were errors, if no, continue
	if(!$errors)
	{
		//Upload file
		$file = upload_file_gs($_FILES['file'], MYBB_ROOT."arcade/gamedata".$directory, $_FILES['file']['name']);
		if($file['error'])
		{
			$errors[] = $lang->error_uploadfailed;
		}
		
		if(!$errors)
		{
			//Log
			log_admin_action("~".$directory, $_FILES['file']['name']);
			
			flash_message($lang->added_file, 'success');
			admin_redirect("index.php?module=games/gamedata".$url_directory);
		}
	}
	
	if($errors)
	{
		//Load the errors
		foreach($errors as $error)
		{
			$flash_errors .= "<li>".$error."</li>\n";
		}
		
		flash_message("<ul>\n".$flash_errors."\n</ul>", 'error');
		admin_redirect("index.php?module=games/gamedata".$url_directory);
	}
}
elseif($mybb->input['action'] == "add_directory")
{
	//Directory
	$newurl_directory = "&amp;directory=";
	
	if(!empty($mybb->input['directory']))
	{
		$directory = $mybb->input['directory']."/";
		$url_directory = "&amp;directory=".$mybb->input['directory'];
		$newurl_directory .= $mybb->input['directory']."/";
		$log_directory = "/".$mybb->input['directory'];
	}
	
	$newurl_directory .= $mybb->input['name'];
	
	//Check values
	if(empty($mybb->input['name']))
	{
		$errors[] = $lang->error_missing_directoryname;
	}
	
	//Test if directory is writable
	if(!is_writable(MYBB_ROOT."arcade/gamedata".$log_directory))
	{
		$errors[] = $lang->sprintf($lang->not_writable, "arcade/gamedata".$directory);
	}
	
	//Check if there were errors, if no, continue
	if(!$errors)
	{
		//Make directory
		if(!@mkdir(MYBB_ROOT."arcade/gamedata/".$directory.$mybb->input['name']))
		{
			$errors[] = $lang->sprintf($lang->cantmakedir, "arcade/gamedata/".$directory.$mybb->input['name']);
		}
		@my_chmod(MYBB_ROOT."arcade/gamedata/".$directory.$mybb->input['name'], 0777);
		
		if(!$errors)
		{
			//Log
			log_admin_action("~".$log_directory, $mybb->input['name']);
			
			flash_message($lang->added_directory, 'success');
			admin_redirect("index.php?module=games/gamedata".$newurl_directory);
		}
	}
	
	if($errors)
	{
		//Load the errors
		foreach($errors as $error)
		{
			$flash_errors .= "<li>".$error."</li>\n";
		}
		
		flash_message("<ul>\n".$flash_errors."\n</ul>", 'error');
		admin_redirect("index.php?module=games/gamedata".$url_directory);
	}
}
elseif($mybb->input['action'] == "delete")
{
	//Plugin
	$plugins->run_hooks("admin_games_categories_delete");
	
	//Directory
	if(!empty($mybb->input['directory']))
	{
		$directory = $mybb->input['directory']."/";
		$url_directory = "&amp;directory=".$mybb->input['directory'];
		$log_directory = "/".$mybb->input['directory'];
	}
	
	//User clicked no
	if($mybb->input['no'])
	{
		admin_redirect("index.php?module=games/gamedata".$url_directory);
	}
	
	//Handle the category
	if($mybb->request_method == "post")
	{
		//Delete file/directory
		if(is_file(MYBB_ROOT."arcade/gamedata/".$directory.$mybb->input['file']))
		{
			if(!@unlink(MYBB_ROOT."arcade/gamedata/".$directory.$mybb->input['file']))
			{
				$errors[] = $lang->sprintf($lang->not_deleteable, "arcade/gamedata/".$directory.$mybb->input['file']);
			}
			$is_file = true;
		}
		elseif(is_dir(MYBB_ROOT."arcade/gamedata/".$directory.$mybb->input['file']))
		{
			gamedata_delete(MYBB_ROOT."arcade/gamedata/".$directory.$mybb->input['file']);
			$is_dir = true;
		}
		
		//Redirect, if there are errors: show them
		if(!$errors)
		{
			if($is_file)
			{
				//Log
				log_admin_action("file", $mybb->input['directory'], $mybb->input['file']);
				
				flash_message($lang->deleted_file, 'success');
				admin_redirect("index.php?module=games/gamedata".$url_directory);
			}
			elseif($is_dir)
			{
				//Log
				log_admin_action("dir", "~".$log_directory, $mybb->input['file']);
				
				flash_message($lang->deleted_directory, 'success');
				admin_redirect("index.php?module=games/gamedata".$url_directory);
			}
		}
		else
		{
			//Load the errors
			foreach($errors as $error)
			{
				$flash_errors .= "<li>".$error."</li>\n";
			}
			
			flash_message("<ul>\n".$flash_errors."\n</ul>", 'error');
			admin_redirect("index.php?module=games/gamedata".$url_directory);
		}
	}
	else
	{
		$page->output_confirm_action("index.php?module=games/gamedata&action=delete".$url_directory."&file=".$mybb->input['file'], $lang->delete_category_confirmation);
	}
}
else
{
	//Test directory
	if(!is_dir(MYBB_ROOT."arcade/gamedata/".$mybb->input['directory']))
	{
		$lang->directorydoesntexist = $lang->sprintf($lang->directorydoesntexist, $mybb->input['directory']);
		
		flash_message($lang->directorydoesntexist, 'error');
		admin_redirect("index.php?module=games/gamedata");
	}
	
	//Location bar
	$locationbar = "<a href=\"index.php?module=games/gamedata\">~</a>";
	
	if(!empty($mybb->input['directory']))
	{
		$location_dirs = explode("/", $mybb->input['directory']);
		if(is_array($location_dirs))
		{
			foreach($location_dirs as $key => $loc)
			{
				$location .= $slash.$loc;
				$locationbar .= "/<a href=\"index.php?module=games/gamedata&amp;directory=".$location."\">".$loc."</a>";
				$slash = "/";
			}
		}
	}
	
	//Navigation and header
	$page->output_header($lang->nav_gamedata);
	
	//Show the sub-tabs
	$page->output_nav_tabs($sub_tabs, 'manage_gamedata');
	
	//Plugin
	$plugins->run_hooks("admin_games_gamedata_default_start");
	
	//Start table
	$table = new Table;
	$table->construct_header($locationbar, array("colspan" => 2));
	
	//Load gamedata files
	$files = my_scandir(MYBB_ROOT."arcade/gamedata/".$mybb->input['directory']);
	
	//Directory
	if(!empty($mybb->input['directory']))
	{
		$directory = $mybb->input['directory']."/";
		$url_directory = "&amp;directory=".$mybb->input['directory'];
	}
	
	if(is_array($files))
	{
		foreach($files as $id => $file)
		{
			if(is_file(MYBB_ROOT."arcade/gamedata/".$directory.$file))
			{
				$data .= "<span style=\"float:right;\"><a href=\"index.php?module=games/gamedata&amp;action=delete".$url_directory."&amp;file=".$file."&amp;my_post_key=".$mybb->post_code."\" onclick=\"return AdminCP.deleteConfirmation(this, '".$lang->delete_file_confirmation."')\">Delete</a></span>
".$file."<br />";
			}
			elseif(is_dir(MYBB_ROOT."arcade/gamedata/".$directory.$file))
			{
				$data .= "<span style=\"float:right;\"><a href=\"index.php?module=games/gamedata&amp;action=delete".$url_directory."&amp;file=".$file."&amp;my_post_key=".$mybb->post_code."\" onclick=\"return AdminCP.deleteConfirmation(this, '".$lang->delete_directory_confirmation."')\">Delete</a></span>
<a href=\"index.php?module=games/gamedata&amp;directory=".$directory.$file."\">".$file."/</a><br />";
			}
		}
	}
	else
	{
		$data = $lang->no_gamedata;
	}
	
	$table->construct_cell($data, array("colspan" => 2));
	$table->construct_row();
	
	$table->construct_cell("", array("style" => "background: #ADCBE6; border-bottom: 1px solid #000; border-top: 1px solid #000; border-right: 1px solid #ADCBE6; border-left: 1px solid #ADCBE6;", "colspan" => 2));
	$table->construct_row();
	
	//Add gamedata
	$table->construct_cell("<strong>".$lang->gamedata_upload."</strong>", array("width" => "25%"));
	$table->construct_cell("<form action=\"index.php?module=games/gamedata&amp;action=add\" method=\"post\" enctype=\"multipart/form-data\">
<input type=\"hidden\" name=\"my_post_key\" value=\"".$mybb->post_code."\" />
<input type=\"hidden\" name=\"directory\" value=\"".$mybb->input['directory']."\" />
<input type=\"file\" name=\"file\" size=\"25\" />
<input type=\"submit\" class=\"submit\" name=\"".$lang->upload."\" value=\"".$lang->upload."\" />
</form>");
	$table->construct_row();
	
	//Add gamedata directory
	$table->construct_cell("<strong>".$lang->gamedata_directory."</strong>", array("width" => "25%"));
	$table->construct_cell("<form action=\"index.php?module=games/gamedata&amp;action=add_directory\" method=\"post\">
<input type=\"hidden\" name=\"my_post_key\" value=\"".$mybb->post_code."\" />
<input type=\"hidden\" name=\"directory\" value=\"".$mybb->input['directory']."\" />
<input type=\"text\" name=\"name\" size=\"25\" />
<input type=\"submit\" class=\"submit\" name=\"".$lang->save."\" value=\"".$lang->save."\" />
</form>");
	$table->construct_row();
	
	//Plugin
	$plugins->run_hooks("admin_games_gamedata_default_end");
	
	//End of table and AdminCP footer
	$table->output($lang->nav_gamedata);
	$page->output_footer();
}
?>