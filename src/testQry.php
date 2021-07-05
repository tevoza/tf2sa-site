<?php
define('BASE_DIR', __DIR__.'/../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
include_once(BASE_DIR.'src/data/dataAccess.php');    
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();
?>
<script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
<body id="stats">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<div id="content">
<pre>
<?php
$cutoff = time() - (60 * 60 * 24 * 365 * $_ENV['RECENT_THRESH_YEARS']);
$data = new dataAccess();
$db = $data->getDbCon();
$thresh = time() - 14*24*60*60;
$steamid = 76561198080441494;
$q="
SELECT
  COUNT(ps.PlayerStatsID) AS Matches,  MaxDamage.DmgID, MaxDamage.Damage, MaxKills.GameID, MaxKills.Kills
FROM
  (SELECT ps.GameID, cs.Kills FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 1 AND SteamID = ".$steamid." ORDER BY cs.Kills DESC LIMIT 1) AS MaxKills,
  (SELECT ps.GameID DmgID, cs.Damage FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 1 AND SteamID = ".$steamid." ORDER BY cs.Damage DESC LIMIT 1) AS MaxDamage, 
  PlayerStats ps, ClassStats cs
WHERE
  cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 1 AND SteamID = ".$steamid
;
echo $q;
$res = mysqli_query($db, $q);
$data->printTable($res); 
mysqli_close($db);
?>
</pre>
</div>

</div>
</div>
</body>
</html>
