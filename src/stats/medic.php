<?php
define('BASE_DIR', __DIR__.'/../../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
include_once(BASE_DIR.'src/data/dataAccess.php');    
include_once('sidebar.php');    
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();
$sidebar = new Sidebar();
?>
<script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
<body id="stats">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<?php $sidebar->printSideBar('medic')?>
<div id="content">
  <?php
  $cutoff = time() - (60 * 60 * 24 * 365 * $_ENV['RECENT_THRESH_YEARS']);
  $data = new dataAccess();
  $db = $data->getDbCon();
  $q="
SELECT ps.SteamID, PlayerName Player, COUNT(DISTINCT ps.GameID) Matches, ROUND(SUM(Playtime)/3600, 1) Hours, ROUND(AVG(Kills),1) Kills, ROUND(AVG(Deaths), 1) Deaths,
  ROUND(AVG(Assists), 1) Assists, ROUND(SUM(Damage)/SUM(Playtime)*60,1) DPM, ROUND(SUM(DamageTaken)/SUM(Playtime)*60,1) DTM, ROUND(SUM(Heals)/SUM(Playtime)*60,1) HPM
FROM PlayerStats ps, Players p, Games g, ClassStats cs
WHERE ps.SteamID=p.SteamID AND ps.GameID=g.GameID and ps.PlayerStatsID=cs.PlayerStatsID AND Date > ".$cutoff." AND ClassID IN (7)
GROUP BY ps.SteamID
HAVING Matches > ".$_ENV['MIN_MATCHES']."
ORDER BY HPM DESC
  ";
  $res = mysqli_query($db, $q);
  ?>
  <h1 style="color:#52FFB8;text-align:left"><b>medic</b></h1>
  <?php 
  $data->printPlayerTable($res); 
  mysqli_close($db);
  ?>
</div>

</div>
</div>
</body>
</html>
