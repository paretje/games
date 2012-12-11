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

$theme['name'] = "Game Section Default";
$theme['directory'] = "images";
$theme['catsperline'] = "5";
$theme['css'] = "";

$theme_templates['games'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection}</title>
{\$headerinclude}
{\$rating_stylesheet}
<style type=\"text/css\">
.tournaments a:link, .tournaments a:visited, .tournaments a:hover, .tournaments a:active
{
	color: #000000;
}
</style>
<script type=\"text/javascript\" src=\"jscripts/games_rating.js?ver=120\"></script>
<script type=\"text/javascript\">
<!--
	lang.stars = new Array();
	lang.stars[1] = \"{\$lang->one_star}\";
	lang.stars[2] = \"{\$lang->two_stars}\";
	lang.stars[3] = \"{\$lang->three_stars}\";
	lang.stars[4] = \"{\$lang->four_stars}\";
	lang.stars[5] = \"{\$lang->five_stars}\";
// -->
</script>
</head>
<body>
{\$header}
{\$games_menu}
{\$stats}
{\$categories_bar}
{\$tournaments_bar}
{\$search_bar}
{\$multipages}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"4\" class=\"thead\"><strong>{\$lang->gamesection}</strong></td>
</tr>
<tr>
<td class=\"tcat\" width=\"175\" align=\"center\"><strong>{\$lang->name}</strong></td>
<td class=\"tcat\" align=\"center\"><strong>{\$lang->description}</strong></td>
<td class=\"tcat\" width=\"250\" align=\"center\"><strong>{\$lang->menu}</strong></td>
<td class=\"tcat\" width=\"200\" align=\"center\"><strong>{\$lang->champion}</strong></td>
</tr>
{\$games_bit}
</table>
{\$multipages}
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_bit'] = "<tr>
<td class=\"{\$bgcolor}\" align=\"center\">
<strong><a href=\"games.php?action=play&amp;gid={\$games[\'gid\']}\">{\$games[\'title\']}</a></strong>{\$new_game}<br />
<br />
<a href=\"games.php?action=play&amp;gid={\$games[\'gid\']}\"><img src=\"./games/images/{\$games[\'name\']}1.gif\" alt=\"\" /></a>
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$games[\'description\']}
</td>
<td class=\"{\$bgcolor}\">
<ul>
<li><a href=\"games.php?action=scores&amp;gid={\$games[\'gid\']}\">{\$lang->viewhighscores}</a></li>
<li>{\$lang->yourhighscore} <strong>{\$games[\'pscore\']}</strong></li>
<li>{\$lang->played} {\$games[\'played\']}</li>
<li>{\$lang->lastplayed} {\$lastplayed}</li>
{\$games_favourite}
{\$games_tournament}
<li>
<ul class=\"star_rating{\$not_rated}\" style=\"float:left;\" id=\"rating_game_{\$games[\'gid\']}\">
<li style=\"width: {\$games[\'width\']}%\" class=\"current_rating\" id=\"current_rating_{\$games[\'gid\']}\">{\$ratingvotesav}</li>
</ul>
</li>
</ul>
<br />
<script type=\"text/javascript\">
<!--
	Rating.build_gamesbit({\$games[\'gid\']}, { width: \'{\$games[\'width\']}\', extra_class: \'{\$not_rated}\', current_average: \'{\$ratingvotesav}\' });
// -->
</script>
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
<img src=\"./games/{\$theme_games[\'directory\']}/champ.png\" alt=\"\" /><br />
{\$champ}
</td>
</tr>";

$theme_templates['games_bit_favourite_add'] = "<li><a href=\"games.php?action=add_favourite&amp;gid={\$games[\'gid\']}\">{\$lang->add_favourite}</a></li>";

$theme_templates['games_bit_favourite_delete'] = "<li><a href=\"games.php?action=delete_favourite&amp;gid={\$games[\'gid\']}\">{\$lang->delete_favourite}</a></li>";

$theme_templates['games_bit_rate'] = "<br />
<a href=\"games.php?action=rate&amp;gid={\$games[\'gid\']}\">{\$lang->rate}</a>";

$theme_templates['games_bit_tournament'] = "<li><a href=\"tournaments.php?action=add&amp;gid={\$games[\'gid\']}\">{\$lang->add_tournament}</a></li>";

$theme_templates['games_categories'] = "<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<thead>
<tr>
<td colspan=\"{\$theme_games[\'catsperline\']}\" class=\"thead\">
<div class=\"expcolimage\"><img src=\"{\$theme[\'imgdir\']}/collapse{\$collapsedimg[\'cats\']}.gif\" id=\"cats_img\" class=\"expander\" alt=\"[-]\" /></div>
<strong>{\$lang->categories}</strong>
</td>
</tr>
</thead>
<tbody style=\"{\$collapsed[\'cats_e\']}\" id=\"cats_e\">
<tr>
{\$categories_bit}
</tr>
</tbody>
</table>";

$theme_templates['games_categories_bit'] = "<td class=\"{\$bgcolor}\" width=\"{\$procent}%\" align=\"center\">
<a href=\"games.php?cid={\$categories[\'cid\']}\">{\$categories[\'image\']}{\$categories[\'title\']}</a> ({\$categories[\'games\']})
</td>{\$tr}";

$theme_templates['games_categories_bit_cur'] = "<td class=\"{\$bgcolor}\" width=\"{\$procent}%\" align=\"center\">
<strong>{\$categories[\'image\']}{\$categories[\'title\']}</strong> ({\$categories[\'games\']})
</td>{\$tr}";

$theme_templates['games_champs'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->last_champions}</title>
{\$headerinclude}
</head>
<body>
{\$header}
{\$games_menu}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"6\" class=\"thead\"><strong>{\$lang->last_champions}</strong></td>
</tr>
<tr>
<td class=\"tcat\" width=\"200\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->username}</strong></span></td>
<td class=\"tcat\" width=\"200\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->gametitle}</strong></span></td>
<td class=\"tcat\" width=\"125\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->score}</strong></span></td>
<td class=\"tcat\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->comment}</strong></span></td>
<td class=\"tcat\" width=\"150\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->gainedat}</strong></span></td>
</tr>
{\$champs_bit}
</table>
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_champs_bit'] = "<tr>
<td class=\"{\$bgcolor}\">
<a href=\"games.php?action=stats&amp;uid={\$champ[\'uid\']}\">{\$champ[\'username\']}</a>
</td>
<td class=\"{\$bgcolor}\">
<a href=\"games.php?action=play&amp;gid={\$champ[\'gid\']}\">{\$champ[\'title\']}</a>
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$champ[\'score\']}
</td>
<td class=\"{\$bgcolor}\">
{\$champ[\'comment\']}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$pubdate}
</td>
</tr>";

$theme_templates['games_favourites'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->your_favourites}</title>
{\$headerinclude}
{\$rating_stylesheet}
<script type=\"text/javascript\" src=\"jscripts/games_rating.js?ver=120\"></script>
<script type=\"text/javascript\">
<!--
	lang.stars = new Array();
	lang.stars[1] = \"{\$lang->one_star}\";
	lang.stars[2] = \"{\$lang->two_stars}\";
	lang.stars[3] = \"{\$lang->three_stars}\";
	lang.stars[4] = \"{\$lang->four_stars}\";
	lang.stars[5] = \"{\$lang->five_stars}\";
// -->
</script>
</head>
<body>
{\$header}
{\$games_menu}
{\$multipages}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"4\" class=\"thead\"><strong>{\$lang->your_favourites}</strong></td>
</tr>
<tr>
<td class=\"tcat\" width=\"175\" align=\"center\"><strong>{\$lang->name}</strong></td>
<td class=\"tcat\" align=\"center\"><strong>{\$lang->description}</strong></td>
<td class=\"tcat\" width=\"250\" align=\"center\"><strong>{\$lang->menu}</strong></td>
<td class=\"tcat\" width=\"200\" align=\"center\"><strong>{\$lang->champion}</strong></td>
</tr>
{\$games_bit}
</table>
{\$multipages}
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_footer'] = "<br />
<!-- DON\'T REMOVE THIS COPYRIGHT -->
<span class=\"smalltext\">
Powered by the <a href=\"http://www.gamesection.org\" target=\"_blank\">Game Section</a><br />
Copyright © 2006-{\$copy_year} <strong><a href=\"http://www.gamesection.org\" target=\"_blank\">Game Section Development Group</a></strong>
</span>
<br />";

$theme_templates['games_menu'] = "<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td class=\"trow1\">
<div class=\"menu\">
<ul>
<li><a href=\"games.php\">{\$lang->gamesection}</a></li>
{\$games_menu_lastchamps}
{\$games_menu_user}
</ul>
</div>
</td>
</tr>
</table>";

$theme_templates['games_menu_lastchamps'] = "<li><a href=\"games.php?action=last_champs\">{\$lang->last_champions}</a></li>";

$theme_templates['games_menu_user'] = "<li><a href=\"games.php?action=favourites\">{\$lang->your_favourites}</a></li>
<li><a href=\"games.php?action=settings\">{\$lang->your_settings}</a></li>
<li><a href=\"games.php?action=stats\">{\$lang->your_stats}</a></li>";

$theme_templates['games_multipages'] = "<br />
{\$multipage}";

$theme_templates['games_online'] = "<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<thead>
<tr>
<td class=\"thead\">
<div class=\"expcolimage\"><img src=\"{\$theme[\'imgdir\']}/collapse{\$collapsedimg[\'online\']}.gif\" id=\"online_img\" class=\"expander\" alt=\"[-]\" /></div>
<strong>{\$lang->whosonline}</strong>
</td>
</tr>
</thead>
<tbody style=\"{\$collapsed[\'online_e\']}\" id=\"online_e\">
<tr>
<td class=\"tcat\">{\$lang->online_count}</td>
</tr>
<tr>
<td class=\"trow1\" valign=\"top\">
{\$onlinemembers}
</td>
</tr>
</tbody>
</table>";

$theme_templates['games_play'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$game[\'title\']}</title>
{\$headerinclude}
</head>
<body>
{\$header}
{\$games_menu}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\" width=\"100%\">
<tr>
<td colspan=\"2\" class=\"thead\"><strong>{\$game[\'title\']}</strong></td>
</tr>
<tr>
<td class=\"trow1\" align=\"center\">
<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\" width=\"{\$game[\'width\']}\" height=\"{\$game[\'height\']}\">
	<param name=\"movie\" value=\"./games/{\$game[\'name\']}.swf?ibpro_gameid={\$game[\'gid\']}\" />
	<param name=\"type\" value=\"application/x-shockwave-flash\" />
	<param name=\"pluginspage\" value=\"http://www.macromedia.com/go/getflashplayer/\" />
	<param name=\"bgcolor\" value=\"#{\$game[\'bgcolor\']}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"menu\" value=\"false\" />
	<param name=\"width\" value=\"{\$game[\'width\']}\" />
	<param name=\"height\" value=\"{\$game[\'height\']}\" />
	<embed src=\"./games/{\$game[\'name\']}.swf?ibpro_gameid={\$game[\'gid\']}\" width=\"{\$game[\'width\']}\" height=\"{\$game[\'height\']}\" quality=\"high\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" menu=\"false\"></embed>
	<noembed>{\$lang->flashisntinstall}</noembed>
</object>
</td>
<td class=\"trow1\" valign=\"top\" align=\"left\" width=\"200\">
<table border=\"0\" cellpadding=\"5\" width=\"100%\">
<tr>
<td class=\"trow1\" align=\"center\">
<img src=\"./games/images/{\$game[\'name\']}1.gif\" alt=\"\" />
</td>
</tr>
<tr>
<td class=\"trow2\" align=\"center\">
<img src=\"./games/{\$theme_games[\'directory\']}/champ.png\" alt=\"\" /><br />
{\$lang->champ}
</td>
</tr>
<tr>
<td class=\"trow1\" align=\"center\">
<strong>{\$lang->yourhighscore}</strong><br />
{\$game[\'pscore\']}
</td>
</tr>
<tr>
<td class=\"trow2\" align=\"center\">
<a href=\"games.php\">{\$lang->back}</a><br />
<a href=\"games.php?action=scores&amp;gid={\$game[\'gid\']}\">{\$lang->viewhighscores}</a>
</td>
</tr>
<tr>
<td class=\"trow1\" align=\"center\">
<strong>{\$lang->purpose} {\$game[\'title\']}</strong><br />
{\$game[\'what\']}
</td>
</tr>
<tr>
<td class=\"trow2\" align=\"center\">
<strong>{\$lang->keys}</strong><br />
{\$game[\'use_keys\']}
</td>
</tr>
</table>
</td>
</tr>
</table>
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_rate'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->rate_game}</title>
{\$headerinclude}
</head>
<body>
{\$header}
{\$games_menu}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td class=\"thead\"><strong>{\$lang->rate_game}</strong></td>
</tr>
<tr>
<td class=\"trow1\" valign=\"top\">
<form method=\"post\" action=\"games.php\">
<input type=\"hidden\" name=\"action\" value=\"do_rate\" />
<input type=\"hidden\" name=\"gid\" value=\"{\$game[\'gid\']}\" />
<select name=\"rating\">
<option value=\"1\">1</option>
<option value=\"2\">2</option>
<option value=\"3\" selected=\"selected\">3</option>
<option value=\"4\">4</option>
<option value=\"5\">5</option>
</select>
<br />
<input type=\"submit\" class=\"button\" value=\"{\$lang->rate}\" />
</form>
</td>
</tr>
</table>
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_scores'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->highscores}</title>
{\$headerinclude}
</head>
<body>
{\$header}
{\$games_menu}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<thead>
<tr>
<td class=\"thead\" colspan=\"3\">
<div class=\"expcolimage\"><img src=\"{\$theme[\'imgdir\']}/collapse{\$collapsedimg[\'game_info\']}.gif\" id=\"game_info_img\" class=\"expander\" alt=\"[-]\" /></div>
<strong>{\$game[\'title\']}</strong>
</td>
</tr>
</thead>
<tbody style=\"{\$collapsed[\'game_info_e\']}\" id=\"game_info_e\">
<tr>
<td class=\"trow1\" width=\"65\" align=\"center\">
<a href=\"games.php?action=play&amp;gid={\$game[\'gid\']}\"><img src=\"./games/images/{\$game[\'name\']}1.gif\" title=\"{\$lang->play_game}\" alt=\"\" /></a>
</td>
<td class=\"trow2\">
<strong>{\$lang->description}:</strong><br />
{\$game[\'description\']}
</td>
<td class=\"trow1\" width=\"250\">
<a href=\"games.php?action=play&amp;gid={\$game[\'gid\']}\" title=\"{\$lang->play_game}\">{\$lang->play_game}</a><br />
{\$game_favourite}
</td>
</tr>
</tbody>
</table>
{\$multipages}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"6\" class=\"thead\"><strong>{\$lang->highscores}</strong></td>
</tr>
<tr>
<td class=\"tcat\" width=\"10\" align=\"center\"><span class=\"smalltext\"><strong>#</strong></span></td>
<td class=\"tcat\" width=\"225\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->username}</strong></span></td>
<td class=\"tcat\" width=\"150\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->score}</strong></span></td>
<td class=\"tcat\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->comment}</strong></span></td>
<td class=\"tcat\" width=\"150\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->gainedat}</strong></span></td>
</tr>
{\$scores_bit}
</table>
{\$multipages}
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_scores_bit'] = "<tr>
<td class=\"{\$bgcolor}\" align=\"right\">
{\$rank}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
<a href=\"games.php?action=stats&amp;uid={\$scores[\'uid\']}\">{\$scores[\'username\']}</a>
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$scores[\'score\']}
</td>
<td class=\"{\$bgcolor}\">
{\$scores[\'comment\']}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$pubdate}
</td>
</tr>";

$theme_templates['games_scores_favourite_add'] = "<a href=\"games.php?action=add_favourite&amp;gid={\$game[\'gid\']}\">{\$lang->add_favourite}</a>";

$theme_templates['games_scores_favourite_delete'] = "<a href=\"games.php?action=delete_favourite&amp;gid={\$game[\'gid\']}\">{\$lang->delete_favourite}</a>";

$theme_templates['games_scores_newcomment'] = "<form method=\"post\" action=\"games.php\">
<input type=\"hidden\" name=\"action\" value=\"do_newscore\" />
<input type=\"hidden\" name=\"gid\" value=\"{\$gid}\" />
<input type=\"hidden\" name=\"page\" value=\"{\$page}\" />
<input type=\"text\" class=\"textbox\" size=\"40\" maxlength=\"120\" name=\"comment\" value=\"{\$scores[\'comment\']}\" />
<input type=\"submit\" class=\"button\" value=\"{\$lang->save}\" />
</form>";

$theme_templates['games_search'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->searchresults}</title>
{\$headerinclude}
</head>
<body>
{\$header}
{\$games_menu}
{\$multipages}
{\$search_bar}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"6\" class=\"thead\"><strong>{\$lang->searchresults_of}</strong></td>
</tr>
<tr>
<td class=\"tcat\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->gametitle}</strong></span></td>
<td class=\"tcat\" width=\"125\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->category}</strong></span></td>
<td class=\"tcat\" width=\"125\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->champion}</strong></span></td>
<td class=\"tcat\" width=\"125\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->bestscore}</strong></span></td>
<td class=\"tcat\" width=\"150\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->added}</strong></span></td>
</tr>
{\$search_bit}
</table>
{\$multipages}
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_search_bar'] = "<br />
<table border=\"0\" cellspacing=\"1\" cellpadding=\"4\" class=\"tborder\">
<thead>
<tr>
<td class=\"thead\">
<div class=\"expcolimage\"><img src=\"{\$theme[\'imgdir\']}/collapse{\$collapsedimg[\'search_bar\']}.gif\" id=\"search_bar_img\" class=\"expander\" alt=\"[-]\" /></div>
<strong>{\$lang->search}</strong>
</td>
</tr>
</thead>
<tbody style=\"{\$collapsed[\'search_bar_e\']}\" id=\"search_bar_e\">
<tr>
<td class=\"trow1\" valign=\"top\">
<form method=\"get\" action=\"games.php\">
<input type=\"hidden\" name=\"action\" value=\"do_search\" />
<strong>{\$lang->name}: </strong>
<input type=\"text\" class=\"textbox\" name=\"s\" value=\"{\$search_bar_s}\" />
<strong>{\$lang->description}: </strong>
<input type=\"text\" class=\"textbox\" name=\"des\" value=\"{\$search_bar_des}\" />
<strong>{\$lang->category}: </strong>
<select name=\"cid\">
<option value=\"\">{\$lang->all}</option>
{\$search_cats}
</select>
<input type=\"submit\" class=\"button\" value=\"{\$lang->search}\" />
</form>
</td>
</tr>
</tbody>
</table>";

$theme_templates['games_search_bit'] = "<tr>
<td class=\"{\$bgcolor}\">
<a href=\"games.php?action=play&amp;gid={\$search[\'gid\']}\">{\$search[\'title\']}</a>
{\$new_game}</td>
<td class=\"{\$bgcolor}\">
{\$search[\'cat_title\']}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$search[\'username\']}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$search[\'score\']}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$pubdate}
</td>
</tr>";

$theme_templates['games_stats'] = "<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<thead>
<tr>
<td colspan=\"3\" class=\"thead\">
<div class=\"expcolimage\"><img src=\"{\$theme[\'imgdir\']}/collapse{\$collapsedimg[\'stats\']}.gif\" id=\"stats_img\" class=\"expander\" alt=\"[-]\" /></div>
<strong>{\$lang->stats}</strong>
</td>
</tr>
</thead>
<tbody style=\"{\$collapsed[\'stats_e\']}\" id=\"stats_e\">
<tr>
<td class=\"tcat\" width=\"25%\"><span class=\"smalltext\"><strong>{\$lang->last_games}</strong></span></td>
<td class=\"tcat\"><span class=\"smalltext\"><strong>{\$lang->other_stats}</strong></span></td>
<td class=\"tcat\" width=\"25%\"><span class=\"smalltext\"><strong>{\$lang->mostplayed_games}</strong></span></td>
</tr>
<tr>
<td class=\"trow1\" valign=\"top\">
{\$last_games_bit}
</td>
<td class=\"trow1\" valign=\"top\">
{\$stats_bestplayers}
<fieldset>
<legend><strong>{\$lang->\$last_champs}</strong></legend>
<table width=\"100%\">
{\$last_champs_bit}
</table>
</fieldset>
<fieldset>
<legend><strong>{\$lang->\$last_score}</strong></legend>
{\$last_score_sen}
</fieldset>
{\$randomgames}
</td>
<td class=\"trow1\" valign=\"top\">
{\$mostplayed_games_bit}
</td>
</tr>
</tbody>
</table>";

$theme_templates['games_stats_bestplayers'] = "<fieldset>
<legend><strong>{\$lang->bestplayers}</strong></legend>
<table width=\"100%\">
<tr>
{\$bestplayers_bit}
</tr>
</table>
</fieldset>";

$theme_templates['games_stats_bestplayers_bit'] = "<td width=\"33%\" valign=\"top\">
<table width=\"100%\">
<tr>
<td class=\"tcat\" align=\"center\">
<strong>{\$bestplayers_place}:</strong>
</td>
</tr>
<tr>
<td class=\"trow2\" align=\"center\">
{\$bestplayers_sen}
</td>
</tr>
</table>
</td>";

$theme_templates['games_stats_champs_bit'] = "<tr>
<td>
{\$last_champs_sen}
</td>
<td align=\"right\" width=\"*\">
<span class=\"smalltext\">{\$pubdate}</span>
</td>
</tr>";

$theme_templates['games_stats_games_bit'] = "<a href=\"games.php?action=play&amp;gid={\$games[\'gid\']}\"><img src=\"./games/images/{\$games[\'name\']}2.gif\" alt=\"\" /> <strong>{\$games[\'title\']}</strong></a><br />";

$theme_templates['games_stats_randomgames'] = "<script type=\"text/javascript\" src=\"jscripts/randomgames.js?ver=120\"></script>
<fieldset>
<legend><strong><a onclick=\"randomgames_update({\$cid})\">{\$lang->\$randomgames}</a></strong></legend>
<div id=\"randomgames\">
{\$randomgames_bit}
</div>
</fieldset>";

$theme_templates['games_stats_randomgames_bit'] = "<a href=\"games.php?action=play&amp;gid={\$games[\$id][\'gid\']}\"><img src=\"./games/images/{\$games[\$id][\'name\']}2.gif\" alt=\"\" /> <strong>{\$games[\$id][\'title\']}</strong></a><br />";

$theme_templates['games_tournaments_add'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->add_tournament}</title>
{\$headerinclude}
<script type=\"text/javascript\">
	lang_testgame = \"{\$lang->tournaments_testgame}\";
</script>
<script type=\"text/javascript\" src=\"jscripts/tournaments.js?ver=120\"></script>
</head>
<body>
{\$header}
{\$games_menu}
<br />
<form method=\"post\" action=\"tournaments.php\">
<input type=\"hidden\" name=\"action\" value=\"do_add\" />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"2\" class=\"thead\"><strong>{\$lang->add_tournament}</strong></td>
</tr>
<tr>
<td class=\"trow1\" width=\"40%\" valign=\"top\">
<strong>{\$lang->tournaments_game}</strong>
</td>
<td class=\"trow1\" width=\"60%\">
{\$tournament_game}
</td>
</tr>
<tr>
<td class=\"trow2\" width=\"40%\" valign=\"top\">
<strong>{\$lang->tournaments_rounds}</strong>
</td>
<td class=\"trow2\" width=\"60%\">
<select name=\"rounds\">
{\$rounds_bit}
</select>
</td>
</tr>
<tr>
<td class=\"trow1\" width=\"40%\" valign=\"top\">
<strong>{\$lang->tournaments_roundtime}</strong>
</td>
<td class=\"trow1\" width=\"60%\">
<select name=\"roundtime\">
{\$roundtime_bit}
</select>
</td>
</tr>
<tr>
<td class=\"trow2\" width=\"40%\" valign=\"top\">
<strong>{\$lang->tournaments_maxtries}</strong><br />
<span class=\"smalltext\">{\$lang->tournaments_maxtries_desc}</span>
</td>
<td class=\"trow2\" width=\"60%\">
<input type=\"text\" class=\"textbox\" name=\"maxtries\" size=\"40\" maxlength=\"30\" />
</td>
</tr>
</table>
<br />
<center><input type=\"submit\" class=\"button\" value=\"{\$lang->save}\" /></center>
</form>
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_tournaments_add_game'] = "<input type=\"text\" class=\"textbox\" id=\"title\" name=\"title\" size=\"40\" maxlength=\"30\" value=\"\" />
<a onclick=\"search_games(title.value)\">{\$lang->search}</a>
<div id=\"games\"></div>";

$theme_templates['games_tournaments_add_game_search'] = "<br />
<div id=\"selected_game\" style=\"float:right;\"></div>
<select name=\"gid\" onchange=\"search_games_selected(this.value)\">
{\$games_bit}
</select>";

$theme_templates['games_tournaments_add_game_search_bit'] = "<option value=\"{\$games[\'gid\']}\">{\$games[\'title\']}</option>";

$theme_templates['games_tournaments_add_game_set'] = "<input type=\"hidden\" name=\"gid\" value=\"{\$game[\'gid\']}\" />
<a href=\"games.php?action=play&amp;gid={\$game[\'gid\']}\">{\$game[\'title\']}</a>";

$theme_templates['games_tournaments_add_rounds_bit'] = "<option value=\"{\$val}\">{\$tournaments_rounds_sen}</option>";

$theme_templates['games_tournaments_add_roundtime_bit'] = "<option value=\"{\$val}\">{\$tournaments_roundtime_sen}</option>";

$theme_templates['games_tournaments_bar'] = "<br />
<table border=\"0\" cellspacing=\"1\" cellpadding=\"4\" class=\"tborder\">
<thead>
<tr>
<td class=\"thead\" colspan=\"3\">
<div class=\"expcolimage\"><img src=\"{\$theme[\'imgdir\']}/collapse{\$collapsedimg[\'tournaments_bar\']}.gif\" id=\"tournaments_bar_img\" class=\"expander\" alt=\"[-]\" /></div>
<strong>{\$lang->tournaments}</strong>
</td>
</tr>
</thead>
<tbody style=\"{\$collapsed[\'tournaments_bar_e\']}\" id=\"tournaments_bar_e\">
<tr>
<td class=\"trow1\" align=\"center\"{\$width}>
<span class=\"tournaments\">
<a href=\"tournaments.php?status=open\">{\$lang->tournaments_stats_open}</a><br />
<a href=\"tournaments.php?status=started\">{\$lang->tournaments_stats_started}</a><br />
<a href=\"tournaments.php?status=finished\">{\$lang->tournaments_stats_finished}</a>
</span>
</td>
{\$tournaments_bar_user}
</tr>
</tbody>
</table>";

$theme_templates['games_tournaments_bar_user'] = "<td class=\"trow1\" width=\"33%\">
{\$tournaments_bar_user_games_bit}
</td>
{\$tournaments_bar_user_add}";

$theme_templates['games_tournaments_bar_user_add'] = "<td class=\"trow1\" align=\"center\" width=\"33%\">
<span class=\"tournaments\">
<strong><a href=\"tournaments.php?action=add\">{\$lang->add_tournament}</a></strong>
</span>
</td>";

$theme_templates['games_tournaments_bar_user_games_bit'] = "<a href=\"games.php?action=play&amp;gid={\$tournaments[\'gid\']}&amp;tid={\$tournaments[\'tid\']}\"><img src=\"./games/images/{\$tournaments[\'name\']}2.gif\" alt=\"\" /> <strong>{\$tournaments[\'title\']}</strong></a><br />";

$theme_templates['games_tournaments_finished'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->\$lang_string}</title>
{\$headerinclude}
</head>
<body>
{\$header}
{\$games_menu}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"4\" class=\"thead\"><strong>{\$lang->\$lang_string}</strong></td>
</tr>
<tr>
<td class=\"tcat\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->gametitle}</strong></span></td>
<td class=\"tcat\" width=\"125\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->numberplayers}</strong></span></td>
<td class=\"tcat\" width=\"125\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->champion}</strong></span></td>
<td class=\"tcat\" width=\"150\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->endeddate}</strong></span></td>
</tr>
{\$tournaments_bit}
</table>
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_tournaments_finished_bit'] = "<tr>
<td class=\"{\$bgcolor}\">
<a href=\"tournaments.php?action=view&amp;tid={\$tournaments[\'tid\']}\">{\$tournaments[\'title\']}</a>
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$tournaments[\'maxplayers\']}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$tournaments[\'champion\']}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$endeddate}
</td>
</tr>";

$theme_templates['games_tournaments_open'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->\$lang_string}</title>
{\$headerinclude}
</head>
<body>
{\$header}
{\$games_menu}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"3\" class=\"thead\"><strong>{\$lang->\$lang_string}</strong></td>
</tr>
<tr>
<td class=\"tcat\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->gametitle}</strong></span></td>
<td class=\"tcat\" width=\"125\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->openplaces}</strong></span></td>
<td class=\"tcat\" width=\"150\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->added}</strong></span></td>
</tr>
{\$tournaments_bit}
</table>
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_tournaments_open_bit'] = "<tr>
<td class=\"{\$bgcolor}\">
<a href=\"tournaments.php?action=view&amp;tid={\$tournaments[\'tid\']}\">{\$tournaments[\'title\']}</a>
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
<strong>{\$freeplaces}</strong>/{\$tournaments[\'maxplayers\']}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$addeddate}
</td>
</tr>";

$theme_templates['games_tournaments_started'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->\$lang_string}</title>
{\$headerinclude}
</head>
<body>
{\$header}
{\$games_menu}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"3\" class=\"thead\"><strong>{\$lang->\$lang_string}</strong></td>
</tr>
<tr>
<td class=\"tcat\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->gametitle}</strong></span></td>
<td class=\"tcat\" width=\"125\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->numberplayers}</strong></span></td>
<td class=\"tcat\" width=\"150\" align=\"center\"><span class=\"smalltext\"><strong>{\$lang->starteddate}</strong></span></td>
</tr>
{\$tournaments_bit}
</table>
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_tournaments_started_bit'] = "<tr>
<td class=\"{\$bgcolor}\">
<a href=\"tournaments.php?action=view&amp;tid={\$tournaments[\'tid\']}\">{\$tournaments[\'title\']}</a>
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$tournaments[\'maxplayers\']}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$starteddate}
</td>
</tr>";

$theme_templates['games_tournaments_view'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->view_tournament}</title>
{\$headerinclude}
<style type=\"text/css\">
.tournaments a:link, .tournaments a:visited, .tournaments a:hover, .tournaments a:active
{
	color: #000000;
}
</style>
</head>
<body>
{\$header}
{\$games_menu}
{\$tournament_infobox}
<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"{\$colspan}\" class=\"thead\"><strong>{\$lang->view_tournament}</strong></td>
</tr>
{\$tournament_rounds}
</table>
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_tournaments_view_infobox_finished'] = "<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"2\" class=\"thead\"><strong>{\$lang->tournament_infobox}</strong></td>
</tr>
<tr>
<td class=\"trow1\" width=\"50%\" valign=\"top\">
<a href=\"games.php?action=play&amp;gid={\$tournament[\'gid\']}\">{\$lang->play_game}</a><br />
<strong>{\$lang->added}:</strong> {\$pubdate}<br />
<strong>{\$lang->starteddate}:</strong> {\$startdate}<br />
<strong>{\$lang->endeddate}:</strong> {\$enddate}
</td>
<td class=\"trow2\" width=\"50%\" valign=\"top\">
<strong>{\$lang->champion}:</strong> {\$tournament[\'champion\']}</a><br />
<strong>{\$lang->numberplayers}:</strong> {\$tournament[\'maxplayers\']}<br />
<strong>{\$lang->tournament_maxtries}</strong> {\$tournament[\'maxtries\']}<br />
<strong>{\$lang->tournament_roundtime}</strong> {\$tournament[\'roundtime\']}
</td>
</tr>
</table>";

$theme_templates['games_tournaments_view_infobox_open'] = "<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"2\" class=\"thead\"><strong>{\$lang->tournament_infobox}</strong></td>
</tr>
<tr>
<td class=\"trow1\" width=\"50%\" valign=\"top\">
<a href=\"games.php?action=play&amp;gid={\$tournament[\'gid\']}\">{\$lang->play_game}</a><br />
{\$tournament_joinlink}
<strong>{\$lang->added}:</strong> {\$pubdate}
</td>
<td class=\"trow2\" width=\"50%\" valign=\"top\">
<strong>{\$lang->openplaces}:</strong> <strong>{\$freeplaces}</strong>/{\$tournament[\'maxplayers\']}<br />
<strong>{\$lang->tournament_maxtries}</strong> {\$tournament[\'maxtries\']}<br />
<strong>{\$lang->tournament_roundtime}</strong> {\$tournament[\'roundtime\']}
</td>
</tr>
</table>";

$theme_templates['games_tournaments_view_infobox_started'] = "<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"2\" class=\"thead\"><strong>{\$lang->tournament_infobox}</strong></td>
</tr>
<tr>
<td class=\"trow1\" width=\"50%\" valign=\"top\">
<a href=\"games.php?action=play&amp;gid={\$tournament[\'gid\']}\">{\$lang->play_game}</a><br />
{\$tournament_playlink}
<strong>{\$lang->added}:</strong> {\$pubdate}<br />
<strong>{\$lang->starteddate}:</strong> {\$startdate}
</td>
<td class=\"trow2\" width=\"50%\" valign=\"top\">
<strong>{\$lang->numberplayers}:</strong> {\$tournament[\'maxplayers\']}<br />
<strong>{\$lang->tournament_maxtries}</strong> {\$tournament[\'maxtries\']}<br />
<strong>{\$lang->tournament_roundtime}</strong> {\$tournament[\'roundtime\']}
</td>
</tr>
</table>";

$theme_templates['games_tournaments_view_rounds'] = "<tr>
<td class=\"tcat\" align=\"center\" width=\"1\" valign=\"right\"><strong>{\$rid}</strong></td>
{\$tournament_rounds_bit}
</tr>";

$theme_templates['games_tournaments_view_rounds_bit'] = "<td colspan=\"{\$colspan_round}\" class=\"{\$bgcolor} tournaments\" width=\"{\$width}%\" align=\"center\" valign=\"middle\">
<span class=\"largetext\"><strong>{\$players[\'username\']}</strong></span>
{\$tournament_rounds_bit_info}
</td>";

$theme_templates['games_tournaments_view_rounds_bit_info'] = "<br />
<br />
{\$lang->bestscore}: <strong>{\$players[\'score\']}</strong> ({\$lang_tournament_tries_needed})<br />
{\$lang_tournament_tries}<br />
{\$lang->gainedat}: {\$pubdate}";

$theme_templates['games_tournaments_view_rounds_champion'] = "<tr>
<td class=\"tcat\" align=\"center\" width=\"1\" valign=\"right\"><strong>#</strong></td>
<td colspan=\"{\$tournament[\'maxplayers\']}\" class=\"trow1 tournaments\" width=\"100%\" align=\"center\" valign=\"middle\">
<img src=\"./games/{\$theme_games[\'directory\']}/champ.png\" alt=\"\" /><br />
<span class=\"largetext\"><strong>{\$tournament[\'champion\']}</strong></span>
</td>
</tr>";

$theme_templates['games_user_settings'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->editsettings}</title>
{\$headerinclude}
{\$options_stylesheets}
</head>
<body>
{\$header}
{\$games_menu}
<br />
<form method=\"post\" action=\"games.php\">
<input type=\"hidden\" name=\"action\" value=\"do_settings\" />
<table border=\"0\" width=\"100%\">
<tr>
{\$usercpnav}
<td valign=\"top\">
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td class=\"thead\" colspan=\"2\"><strong>{\$lang->editsettings}</strong></td>
</tr>
<tr>
<td width=\"50%\" class=\"trow1\" valign=\"top\">
<fieldset class=\"trow2\">
<legend><strong>{\$lang->options_games}</strong></legend>
<span class=\"smalltext\">{\$lang->option_maxgames}</span><br />
{\$select_maxgames}
<br />
<br />
<span class=\"smalltext\">{\$lang->option_sortby}</span><br />
<select name=\"sortby\">
<option value=\"0\">{\$lang->usedefauld}</option>
<option value=\"title\"{\$select_sortby[\'title\']}>{\$lang->option_sortby_name}</option>
<option value=\"dateline\"{\$select_sortby[\'dateline\']}>{\$lang->option_sortby_dateline}</option>
<option value=\"played\"{\$select_sortby[\'played\']}>{\$lang->option_sortby_played}</option>
<option value=\"lastplayed\"{\$select_sortby[\'lastplayed\']}>{\$lang->option_sortby_lastplayed}</option>
<option value=\"rating\"{\$select_sortby[\'rating\']}>{\$lang->option_sortby_rating}</option>
</select>
<br />
<br />
<span class=\"smalltext\">{\$lang->option_order}</span><br />
<select name=\"order\">
<option value=\"0\">{\$lang->usedefauld}</option>
<option value=\"ASC\"{\$select_order[\'ASC\']}>{\$lang->option_order_asc}</option>
<option value=\"DESC\"{\$select_order[\'DESC\']}>{\$lang->option_order_desc}</option>
</select>
</fieldset>
{\$tournament_settings}
</td>
<td width=\"50%\" class=\"trow1\" valign=\"top\">
<fieldset class=\"trow2\">
<legend><strong>{\$lang->options_scores}</strong></legend>
<span class=\"smalltext\">{\$lang->option_maxscores}</span><br />
{\$select_maxscores}
</fieldset>
<br />
<fieldset class=\"trow2\">
<legend><strong>{\$lang->options_themes}</strong></legend>
<span class=\"smalltext\">{\$lang->option_themes}</span><br />
<select name=\"theme\">
<option value=\"0\">{\$lang->usedefauld}</option>
{\$select_themes}
</select>
</fieldset>
</td>
</tr>
</table>
<br />
<center><input type=\"submit\" class=\"button\" value=\"{\$lang->save}\" /></center>
</td>
</tr>
</table>
</form>
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_user_settings_maxgames'] = "<select name=\"maxgames\">
<option value=\"0\">{\$lang->usedefauld}</option>
{\$options_maxgames}
</select>";

$theme_templates['games_user_settings_maxscores'] = "<select name=\"maxscores\">
<option value=\"0\">{\$lang->usedefauld}</option>
{\$options_maxscores}
</select>";

$theme_templates['games_user_settings_themes'] = "<option value=\"{\$themes[\'tid\']}\"{\$selected}>{\$themes[\'name\']}</option>";

$theme_templates['games_user_settings_tournaments'] = "<br />
<fieldset class=\"trow2\">
<legend><strong>{\$lang->options_tournaments}</strong></legend>
<table cellspacing=\"0\" cellpadding=\"2\">
<tr>
<td valign=\"top\" width=\"1\"><input type=\"checkbox\" class=\"checkbox\" name=\"tournamentnotify\" id=\"tournamentnotify\" value=\"1\" {\$tournamentnotifycheck} /></td>
<td><span class=\"smalltext\"><label for=\"tournamentnotify\">{\$lang->tournament_notify}</label></span></td>
</tr>
</table>
</fieldset>";

$theme_templates['games_user_stats'] = "<html>
<head>
<title>{\$mybb->settings[\'bbname\']} - {\$lang->gamesection} - {\$lang->statsofuser}</title>
{\$headerinclude}
</head>
<body>
{\$header}
{\$games_menu}
<br />
<table border=\"0\" width=\"100%\" cellspacing=\"5\">
{\$multipages}
<td valign=\"top\">
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\">
<tr>
<td colspan=\"6\" class=\"thead\"><strong>{\$lang->statsofuser}</strong></td>
</tr>
<tr>
<td class=\"tcat\"><span class=\"smalltext\"><center><strong>{\$lang->gametitle}</strong></center></span></td>
<td class=\"tcat\" width=\"100\"><span class=\"smalltext\"><center><strong>{\$lang->score}</strong></center></span></td>
<td class=\"tcat\" width=\"50\"><span class=\"smalltext\"><center><strong>{\$lang->rank}</strong></center></span></td>
<td class=\"tcat\" width=\"225\"><span class=\"smalltext\"><center><strong>{\$lang->timescore}</strong></center></span></td>
<td class=\"tcat\" width=\"150\"><span class=\"smalltext\"><center><strong>{\$lang->gainedat}</strong></center></span></td>
</tr>
{\$user_stats_bit}
</table>
</td>
<td width=\"250\" valign=\"top\">
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\" width=\"200\">
<tr>
<td class=\"thead\"><strong>{\$lang->dataofuser}</strong></td>
</tr>
<tr>
<td class=\"trow1\">
<strong>{\$lang->first_places}</strong> {\$first}
</td>
</tr>
<tr>
<td class=\"trow2\">
<strong>{\$lang->second_places}</strong> {\$second}
</td>
</tr>
<tr>
<td class=\"trow1\">
<strong>{\$lang->thirth_places}</strong> {\$thirth}
</td>
</tr>
<tr>
<td class=\"trow2\">
<strong>{\$lang->tenth_places}</strong>  {\$tenth}
</td>
</tr>
<tr>
<td class=\"trow1\">
<strong>{\$lang->total_scores}</strong>  {\$total}
</td>
</tr>
{\$user_stats_bestplayers}
</table>
{\$user_stats_tournaments}
</td>
</tr>
</table>
{\$multipages}
{\$online}
{\$games_footer}
{\$footer}
</body>
</html>";

$theme_templates['games_user_stats_bestplayers'] = "<tr>
<td class=\"trow1\">
<strong>{\$lang->bestplayerrank}</strong>  {\$top100rank}
</td>
</tr>";

$theme_templates['games_user_stats_bit'] = "<tr>
<td class=\"{\$bgcolor}\">
<a href=\"games.php?action=play&amp;gid={\$games[\$gid][\'gid\']}\">{\$games[\$gid][\'title\']}</a>
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$score}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$rank}{\$slash}{\$scores}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$tottime}
</td>
<td class=\"{\$bgcolor}\" align=\"center\">
{\$pubdate}
</td>
</tr>";

$theme_templates['games_user_stats_multipages'] = "<tr>
<td valign=\"top\" colspan=\"2\">
{\$multipage}
</td>
</tr>
<tr>";

$theme_templates['games_user_stats_tournaments'] = "<br />
<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\" width=\"200\">
<tr>
<td class=\"thead\"><strong>{\$lang->tournamentsstatistics}</strong></td>
</tr>
<tr>
<td class=\"trow1\">
<strong>{\$lang->tournamentswon}</strong> {\$tournamentswon}
</td>
</tr>
<tr>
<td class=\"trow2\">
<strong>{\$lang->tournamentsjoined}</strong> {\$tournamentsjoined}
</td>
</tr>
</table>";
?>
