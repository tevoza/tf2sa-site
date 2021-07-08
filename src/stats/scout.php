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
<?php $sidebar->printSideBar('scout')?>
<div id="content">
  <?php
  $data = new dataAccess();
  $cutoff = time() - (60 * 60 * 24 * 365 * $_ENV['RECENT_THRESH_YEARS']);
  $db = $data->getDbCon();
  $q="
SELECT 
  ps.SteamID, PlayerName Player, COUNT(DISTINCT ps.GameID) Matches, ROUND(SUM(Playtime)/3600, 1) Hours, ROUND(SUM(Kills)/COUNT(DISTINCT ps.GameID),1) Kills,
  ROUND(SUM(Deaths)/COUNT(DISTINCT ps.GameID),1) Deaths, ROUND(SUM(Assists)/COUNT(DISTINCT ps.GameID),1) Assists, ROUND(SUM(Damage)/SUM(Playtime)*60,1) DPM, 
  ROUND(SUM(DamageTaken)/SUM(Playtime)*60,1) DTM, ROUND(SUM(HealsReceived)/SUM(Playtime)*60,1) HRM
FROM
  PlayerStats ps, Players p, Games g, ClassStats cs
WHERE 
  ps.SteamID=p.SteamID AND ps.GameID=g.GameID AND ps.PlayerStatsID=cs.PlayerStatsID AND Date > {$cutoff} AND ClassID IN (1)
GROUP BY ps.SteamID
HAVING Matches > {$_ENV['MIN_MATCHES_RECENT']}
ORDER BY DPM DESC
  ";
  $res = mysqli_query($db, $q);
  $q="
SELECT 
  ps.SteamID, PlayerName Player, COUNT(DISTINCT ps.GameID) Matches, ROUND(SUM(Playtime)/3600, 1) Hours, ROUND(SUM(Kills)/COUNT(DISTINCT ps.GameID),1) Kills,
  ROUND(SUM(Deaths)/COUNT(DISTINCT ps.GameID),1) Deaths, ROUND(SUM(Assists)/COUNT(DISTINCT ps.GameID),1) Assists, ROUND(SUM(Damage)/SUM(Playtime)*60,1) DPM, 
  ROUND(SUM(DamageTaken)/SUM(Playtime)*60,1) DTM, ROUND(SUM(HealsReceived)/SUM(Playtime)*60,1) HRM
FROM
  PlayerStats ps, Players p, Games g, ClassStats cs
WHERE 
  ps.SteamID=p.SteamID AND ps.GameID=g.GameID AND ps.PlayerStatsID=cs.PlayerStatsID AND ClassID IN (1)
GROUP BY ps.SteamID
HAVING Matches > {$_ENV['MIN_MATCHES_ALL_TIME']}
ORDER BY DPM DESC
  ";
  $resAll = mysqli_query($db, $q);
  ?>

  <div >
    <button class="stat-toggle" id="all-time" onclick="openTable('stats-all', 'all-time')"><i>all-time</i></button>
    <button class="stat-toggle" id="recent" style="color:#52FFB8" onclick="openTable('stats-recent', 'recent')"><i>recent</i></button>
  </div>

  <h1 style="color:#52FFB8;text-align:left"><b>scout</b></h1>

  <div id="stats-recent" class="stats-table" style="display:block">
    <?php $data->printPlayerTable($res); ?>
  </div>

  <div id="stats-all" class="stats-table" style="display:none">
    <?php $data->printPlayerTable($resAll); ?>
  </div>

</div>

<?php mysqli_close($db); ?>

</div>
</div>
</body>
</html>

<script>
function openTable(tableName, buttonName) {
  var i;
  var x = document.getElementsByClassName("stats-table");
  var y = document.getElementsByClassName("stat-toggle");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
    x[i].style.display = "none";
  }
  for (i=0; i< y.length; i++) {
    y[i].style.color ="white";
  }
  document.getElementById(tableName).style.display = "block";
  document.getElementById(buttonName).style.color = "#52FFB8";
}
</script>
