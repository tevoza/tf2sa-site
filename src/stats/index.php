<?php
define('BASE_DIR', __DIR__.'/../../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
include_once(BASE_DIR.'src/data/dataAccess.php');    
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();
?>
<body id="stats">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<p>

<?php
$data = new dataAccess();
$db = $data->getDbCon();
$q="
SELECT Players.PlayerName Player, COUNT(DISTINCT GameID) Matches, SUM(Airshots) Airshots,AVG(Airshots) Average
FROM PlayerStats, Players 
WHERE PlayerStats.SteamID=Players.SteamID AND ClassID = 0
GROUP BY PlayerStats.SteamID
HAVING Matches > 20
ORDER BY AVG(Airshots) DESC
";
$res = mysqli_query($db, $q);
?>

<h1 style="color:#52FFB8"><b>airshots</b></h1>
<?php $data->printTable($res); ?>

</p>
</div>
</div>
</body>
</html>
