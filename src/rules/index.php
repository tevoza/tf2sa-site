<?php
define('BASE_DIR', __DIR__.'/../../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();
?>
<body id="rules">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<p>
<pre>
General rules:

Cheats and scripts:

- No cheats of any nature are allowed

- Usage of bugs are not allowed

- Usage of scripts that give clear unfair advantages are not allowed

- Players with known VAC bans for using cheats on another accounts are not eligible to play PUGs

- Ghosting is not allowed.

Player Conduct:

- Use of discriminatory language such as racist/sexist language is strictly prohibited.

- Abusive names and avatars are not allowed.

- Continued overly toxic behaviour that ruins the PUG experience for others is not allowed.

- All players must play to the best of their ability, intentionally playing worse for any reason is not allowed.

- Leaving before a game is over without getting a sub is not allowed except for when a game is generally considered to be basically over or if the reason for leaving is urgent.

- If a player has not joined the server after 10 minutes of the PUG filling and is not accounted for by someone else in the server, a sub can be called for said player.

- Having a player muted on your team during an ongoing PUG is not allowed.

Offclassing:

- Offclassing that can be deemed as unnecessary or detrimental to your team’s success is not allowed. Decisions on if an offclass was abused or not will be decided by the moderation team.

- If a player is thought to be abusing an offclass, it is the responsibility of any player in the server to report the incident in the event that no moderator/admin is present.

- As a guideline, try to keep your offclassing time below 5 minutes to make sure you keep clear of breaking offclassing rules, this is however not a hard limit and violations of offclassing rules will be
 looked at on a case by case basis.

Team selection:

- While captains ought to pick their best team possible, they must equally strive to pick a team that will lead to a balanced 6v6 experience.

- While the team captain has the final say to what class each player is assigned, they should try to be as considerate as possible regarding class preferences.


Other:

- A game that has reached a score of 3-0 with 20 minutes remaining is allowed to be conceded.

- For a game to be conceded, 7 players need to vote in favour of conceding the game.

- Players who are currently in an ongoing PUG are not allowed to add or queue to the next PUG until the current PUG is completed.

- If a game has not started within 25 minutes of the match filling with everyone ready, you are allowed to abandon said match.

- When looking for a sub for a player, players queued for the next PUG take priority in the order they have queued. If the queue is empty, the first player to join the server will be the sub.

- The moderation team reserves the right to amend these rules and implement such prospectively.

Punishment system:

- The punishment system for breaking the rules stated above will generally follow a system of 1st warning, 24 hour ban, 7 day ban, month ban and finally permanent ban.

- Violations of the rules that are considered extreme however can lead to more immediate serious punishments as deemed necessary by the moderation team.

</pre>
</p>
</div>
</div>
</body>
</html>
