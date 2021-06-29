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
  $cutoff = time() - (60 * 60 * 24 * 365 * $_ENV['RECENT_THRESH_YEARS']);
  $data = new dataAccess();
  $db = $data->getDbCon();
  $q="
SELECT p.SteamID, p.PlayerName, a.Matches, ROUND(b.Hours, 1) Hours, ROUND(IFNULL(c.Kills, 0), 2) AS 'Kills', ROUND(IFNULL(c.Deaths, 0),2) AS 'Deaths', ROUND(IFNULL(c.Assists, 0),2) AS 'Assists', ROUND(IFNULL(e.Backstabs, 0),1) AS 'Backstabs', ROUND(IFNULL(f.Headshots, 0),1) AS 'Headshots', ROUND(IFNULL(d.Airshots, 0),2) AS 'Airshots', ROUND(IFNULL(c.DPM, 0),2) AS 'DPM', ROUND(a.DTM, 2) DTM, ROUND(a.HRM ,2) HRM FROM Players p
LEFT JOIN 
(
	SELECT p.SteamID, COUNT(p.GameID) AS 'Matches', SUM(p.DamageTaken) * 60 / SUM(g.Duration) AS 'DTM', SUM(p.HealsReceived) * 60 / SUM(g.Duration) AS 'HRM' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
  WHERE Date > ".$cutoff."
	GROUP BY p.SteamID	
) AS a ON a.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, SUM(c.Playtime) / 3600 AS 'Hours' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
  WHERE Date > ".$cutoff."
  GROUP BY p.SteamID
) AS b ON b.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, SUM(c.Kills) / Count(DISTINCT(g.GameID)) AS 'Kills', SUM(c.Deaths) / Count(DISTINCT(g.GameID)) AS 'Deaths', SUM(c.Assists) / Count(DISTINCT(g.GameID)) AS 'Assists', SUM(c.Damage) * 60 / SUM(c.Playtime) AS 'DPM' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE c.ClassID != 7 AND Date > ".$cutoff."
	GROUP BY p.SteamID
) AS c ON c.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, Sum(Airshots) / Count(DISTINCT(g.GameID)) AS 'Airshots' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE Date > ".$cutoff." AND ClassID IN (2,4)
	GROUP BY p.SteamID
) AS d ON d.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, Sum(Backstabs) / Count(g.GameID)  AS 'Backstabs' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE ClassID = 9 AND Date > ".$cutoff."
	GROUP BY p.SteamID
) AS e ON e.SteamID = p.SteamID
LEFT JOIN
(
	SELECT p.SteamID, Sum(Headshots) / Count(g.GameID)  AS 'Headshots' FROM Games g
	JOIN PlayerStats p ON g.GameID = p.GameID
	JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
	WHERE ClassID = 8 AND Date > ".$cutoff."
	GROUP BY p.SteamID
) AS f ON f.SteamID = p.SteamID
HAVING Matches > ".$_ENV['MIN_MATCHES']."
ORDER BY DPM DESC
";
  $res = mysqli_query($db, $q);
  ?>
  <h1 style="color:#52FFB8;text-align:left"><b>overall</b></h1>
  <?php 
  $data->printPlayerTable($res); 
  mysqli_close($db);
  ?>
</div>

</div>
</div>
</body>
</html>
