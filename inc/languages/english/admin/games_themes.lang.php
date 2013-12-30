<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2008 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 20/09/2008 by Paretje
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

$l['not_writable'] = "The follow directory has no chmod 777:<br />\n{1}";
$l['not_deleteable'] = "The follow file/directory couldn't be deleted, possible it's because it has no chmod 777:<br />\n";

$l['nav_overview_themes'] = "Overview of the Themes";
$l['nav_add_theme'] = "Add Theme";
$l['nav_import_theme'] = "Import Theme";
$l['nav_edit_theme'] = "Edit Theme";
$l['nav_export_theme'] = "Export Theme";

$l['nav_overview_themes_desc'] = "Here you can manage the Game Section themes on your board.";
$l['nav_add_theme_desc'] = "Here you can add a Game Section theme.";
$l['nav_import_theme_desc'] = "Here you can import a Game Section theme.";
$l['nav_edit_theme_desc'] = "Here you can edit the selected Game Section theme.";
$l['nav_export_theme_desc'] = "Here you can export a Game Section theme.";

$l['export'] = "Export";
$l['no_themes'] = "There are no themes.";

$l['theme_name'] = "Theme name";
$l['theme_directory'] = "Image directory";
$l['theme_catsperline'] = "Maximum categories per line";
$l['theme_css'] = "Theme CSS";
$l['theme_active'] = "Active theme";
$l['theme_file'] = "Import file";

$l['theme_export_copyright'] = "Copyright";
$l['theme_export_website'] = "Website";
$l['theme_export_support'] = "Support";
$l['theme_export_license'] = "License";

$l['theme_directory_desc'] = "The directory based in the games directory for the location of the images used in this theme.";
$l['theme_catsperline_desc'] = "The number of categories shown at one line on the Game Section in the overview.";
$l['theme_css_desc'] = "The additional CSS contents which is used in this theme. You can include this CSS in your templates with this code:<br />
&lt;link type=\"text/css\" rel=\"stylesheet\" href=\"{\$mybb->settings['bburl']}/games/css.php?tid={\$theme_games['tid']}\" /&gt;";

$l['theme_export_copyright_desc'] = "Copyright ...";
$l['theme_export_support_desc'] = "The place where users can get support on this theme.";
$l['theme_export_license_desc'] = "The license under which you want to license this theme. When you want to license it under a personal one, you can add a link to the text of it.";

$l['error_missing_name'] = "You didn't enter a name for this theme";
$l['error_missing_directory'] = "You didn't enter an image directory for this theme";
$l['error_missing_catsperline'] = "You didn't fill in the maximum categories per line option for this theme";
$l['error_missing_active'] = "You didn't select if you want to have the theme active";
$l['error_missing_theme_file'] = "You didn't upload a php file to import the theme from.";
$l['error_uploadfailed'] = "There is an error with the upload of the theme file.";
$l['themealreadyexist'] = "A theme with the same name already exists.";
$l['themedoesntexist'] = "The selected theme doesn't exist.";

$l['delete_theme_confirmation'] = "Are you sure you want to delete this theme?";

$l['added_theme'] = "The theme is successfully added.";
$l['imported_theme'] = "The theme is successfully imported.";
$l['edited_theme'] = "The theme is successfully edited.";
$l['deleted_theme'] = "The theme is successfully deleted.";
?>