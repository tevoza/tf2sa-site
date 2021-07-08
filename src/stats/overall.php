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
<?php $sidebar->printSideBar('overall')?>
<div id="content">
  <?php
  $data = new dataAccess();
  $cutoff = time() - (60 * 60 * 24 * 365 * $_ENV['RECENT_THRESH_YEARS']);
  $db = $data->getDbCon();
  $q="
SELECT 
	p.SteamID, p.PlayerName, a.Matches, ROUND(b.Hours, 1) Hours, ROUND(IFNULL(c.Kills, 0), 2) AS 'Kills', ROUND(IFNULL(c.Deaths, 0),2) AS 'Deaths',
	ROUND(IFNULL(c.Assists, 0),2) AS 'Assists', ROUND(IFNULL(e.Backstabs, 0),1) AS 'Backstabs', ROUND(IFNULL(f.Headshots, 0),1) AS 'Headshots',
	ROUND(IFNULL(d.Airshots, 0),2) AS 'Airshots', ROUND(IFNULL(c.DPM, 0),2) AS 'DPM', ROUND(a.DTM, 2) DTM, ROUND(a.HRM ,2) HRM FROM Players p
LEFT JOIN 
(
	SELECT p.SteamID, COUNT(p.GameID) AS 'Matches', SUM(p.DamageTaken) * 60 / SUM(g.Duration) AS 'DTM', SUM(p.HealsReceived) * 60 / SUM(g.Duration) AS 'HRM' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
  WHERE Date > {$cutoff}
	GROUP BY p.SteamID	
) AS a ON a.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, SUM(c.Playtime) / 3600 AS 'Hours' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
  WHERE Date > {$cutoff}
  GROUP BY p.SteamID
) AS b ON b.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, SUM(c.Kills) / Count(DISTINCT(g.GameID)) AS 'Kills', SUM(c.Deaths) / Count(DISTINCT(g.GameID)) AS 'Deaths', SUM(c.Assists) / Count(DISTINCT(g.GameID)) AS 'Assists', SUM(c.Damage) * 60 / SUM(c.Playtime) AS 'DPM' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE c.ClassID != 7 AND Date > {$cutoff}
	GROUP BY p.SteamID
) AS c ON c.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, Sum(Airshots) / Count(DISTINCT(g.GameID)) AS 'Airshots' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE Date > {$cutoff} AND ClassID IN (2,4)
	GROUP BY p.SteamID
) AS d ON d.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, Sum(Backstabs) / Count(g.GameID)  AS 'Backstabs' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE ClassID = 9 AND Date > {$cutoff}
	GROUP BY p.SteamID
) AS e ON e.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, Sum(Headshots) / Count(g.GameID)  AS 'Headshots' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE ClassID = 8 AND Date > {$cutoff}
	GROUP BY p.SteamID
) AS f ON f.SteamID = p.SteamID
HAVING Matches > {$_ENV['MIN_MATCHES_RECENT']}
ORDER BY DPM DESC
  ";
  $res = mysqli_query($db, $q);
  $q="
SELECT 
	p.SteamID, p.PlayerName, a.Matches, ROUND(b.Hours, 1) Hours, ROUND(IFNULL(c.Kills, 0), 2) AS 'Kills', ROUND(IFNULL(c.Deaths, 0),2) AS 'Deaths',
	ROUND(IFNULL(c.Assists, 0),2) AS 'Assists', ROUND(IFNULL(e.Backstabs, 0),1) AS 'Backstabs', ROUND(IFNULL(f.Headshots, 0),1) AS 'Headshots',
	ROUND(IFNULL(d.Airshots, 0),2) AS 'Airshots', ROUND(IFNULL(c.DPM, 0),2) AS 'DPM', ROUND(a.DTM, 2) DTM, ROUND(a.HRM ,2) HRM FROM Players p
LEFT JOIN 
(
	SELECT p.SteamID, COUNT(p.GameID) AS 'Matches', SUM(p.DamageTaken) * 60 / SUM(g.Duration) AS 'DTM', SUM(p.HealsReceived) * 60 / SUM(g.Duration) AS 'HRM' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	GROUP BY p.SteamID	
) AS a ON a.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, SUM(c.Playtime) / 3600 AS 'Hours' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
  GROUP BY p.SteamID
) AS b ON b.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, SUM(c.Kills) / Count(DISTINCT(g.GameID)) AS 'Kills', SUM(c.Deaths) / Count(DISTINCT(g.GameID)) AS 'Deaths', SUM(c.Assists) / Count(DISTINCT(g.GameID)) AS 'Assists', SUM(c.Damage) * 60 / SUM(c.Playtime) AS 'DPM' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE c.ClassID != 7
	GROUP BY p.SteamID
) AS c ON c.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, Sum(Airshots) / Count(DISTINCT(g.GameID)) AS 'Airshots' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE ClassID IN (2,4)
	GROUP BY p.SteamID
) AS d ON d.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, Sum(Backstabs) / Count(g.GameID)  AS 'Backstabs' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE ClassID = 9
	GROUP BY p.SteamID
) AS e ON e.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, Sum(Headshots) / Count(g.GameID)  AS 'Headshots' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE ClassID = 8
	GROUP BY p.SteamID
) AS f ON f.SteamID = p.SteamID
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
