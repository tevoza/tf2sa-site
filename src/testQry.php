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
  UNIQUE(a.EndDate) EndDate, IFNULL(p.Kills, 0) Kills, IFNULL(p.DPM, 0) DPM,
  IFNULL(p.Headshots, 0) Headshots, IFNULL(p.Airshots, 0) Airshots
FROM 
  Progress a
LEFT JOIN
(
  SELECT
    EndDate, Kills, DPM, Headshots, Airshots
  FROM
    Progress
  WHERE SteamID = 76561198216208760
) AS p on p.EndDate = a.EndDate
HAVING 
  a.EndDate > (SELECT MIN(EndDate) FROM Progress a WHERE SteamID=76561198216208760) AND
  a.EndDate < (SELECT MAX(EndDate) FROM Progress a WHERE SteamID=76561198216208760)
";
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
