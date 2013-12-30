<?php
/***************************************************************************
 *
 *   Game Section for MyBB
 *   Copyright: © 2006-2010 The Game Section Development Group
 *   
 *   Website: http://www.gamesection.org
 *   
 *   Last modified: 16/02/2010 by Paretje
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

$l['tournaments'] = "Tournaments";
$l['tournaments_stats_open'] = "<strong>{1}</strong> open tournaments";
$l['tournaments_stats_started'] = "<strong>{1}</strong> started tournaments";
$l['tournaments_stats_finished'] = "<strong>{1}</strong> finished tournaments";
$l['add_tournament'] = "Create tournament";
$l['your_tournament_stats'] = "Your tournament statistics";
$l['tournaments_user_nostartedgames'] = "The are no started tournaments which you have joined.";

$l['tournaments_open'] = "Open Tournaments";
$l['tournaments_started'] = "Started Tournaments";
$l['tournaments_finished'] = "Finished Tournaments";
$l['openplaces'] = "Open places";
$l['numberplayers'] = "Number of players";
$l['starteddate'] = "Started on";
$l['endeddate'] = "Ended on";
$l['no_tournaments'] = "There are no tournaments with this status.";

$l['tournaments_game'] = "Game to use for the tournament:";
$l['tournaments_rounds'] = "Rounds for the tournament:";
$l['tournaments_roundtime'] = "Time for one round:";
$l['tournaments_maxtries'] = "Maximum tries to play the game in one round:";
$l['tournaments_maxtries_desc'] = "The number of tries can't be less then one, and more then 99.";

$l['tournaments_testgame'] = "Test game";
$l['tournaments_rounds_sen'] = "{1} rounds";
$l['tournaments_roundtime_sen'] = "{1} days";

$l['added_tournament'] = "Tournament is added.";

$l['view_tournament'] = "{1} Tournament added on {2}";
$l['tournament_infobox'] = "Tournament Information";
$l['play_tournament'] = "Play tournament";
$l['join_tournament'] = "Join tournament";
$l['tournament_maxtries'] = "Maximum tries:";
$l['tournament_roundtime'] = "Days a round:";
$l['tournament_tries'] = "Tries: <strong>{1}</strong>/{2}";
$l['tournament_tries_needed'] = "{1} tries needed";
$l['tournamentdoesntexist'] = "Tournament doesn't exist.";

$l['tournamentfull'] = "The tournament is already full, you can't join anymore!";
$l['alreadyjoined'] = "You've already joined this tournament!";
$l['joined_tournament'] = "You've joined the tournament";

$l['tournamentnotjoined'] = "You haven't joined this tournament.";
$l['tournamentmaxtriesreached'] = "You've reached the maxtries of this tournament.";
$l['tournamentended'] = "The round is ended.";

$l['tournamentsstatistics'] = "Tournament Statistics";
$l['tournamentswon'] = "Tournaments won:";
$l['tournamentsjoined'] = "Tournaments joined:";

$l['options_tournaments'] = "Tournaments Options";
$l['tournament_notify'] = "Notify me by email when a new tournament round in which you're playing starts.";

$l['emailsubject_newtournamentround'] = "New Tournament Round Started at {1}";
$l['email_newtournamentround'] = "{1},

Round number {3} of {4} from {5}. To play this tournament, you can follow this link:

{6}/tournaments.php?action=view&tid={2}

You can disable new message notifications on your account options page:

{6}/games.php?action=settings

Thank you,
{5} Staff
{6}";
?>
