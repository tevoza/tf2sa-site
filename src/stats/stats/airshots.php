<?php
define('BASE_DIR',__DIR__.'/../');
include_once(BASE_DIR.'/vendor/autoload.php');
include BASE_DIR.'data/dataAccess.php';
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
?>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>TF2SA</title>
    <link rel="stylesheet" href=<?php echo BASE_DIR.'/styles.css'?>>
</head>

<body>
<div class="wrapper">
<section>
<header class="page-header">
    <div>
        <h1 style="float: left;"><img src="../res/tf2sa.jpg" height="70px" width="70px" border="0px"></h1>
        <h1><b> TF2SA Pugs </b></h1>
<?php echo BASE_DIR.'styles.css'?>
    </div>
</header>

<header class="page-header">
    <div>
    <ul>                                                       
        <li><a href="../index.php">Home</a></li>
        <li><a href="stats.php">Stats</a> </li>
    </ul>                                           
    </div>
</header>

<header class="page-header">
    <div>
    <ul>                                                       
        <li><a href="overall.php">Overall</a></li>
        <li><a href="airshots.php">Airshots</a></li>
        <li><a href="damage.php">Damage</a></li>
        <li><a href="kills.php">Kills</a></li>
        <li><a href="scout.php">Scout</a></li>
        <li><a href="soldier.php">Soldier</a></li>
        <li><a href="demo.php">Demo</a></li>
        <li><a href="medic.php">Medic</a></li>
    </ul>                                           
    </div>
</header>
<pre>
<?php
var_dump(BASE_DIR);
var_dump($_ENV);
$data = new dataAccess();
$db = $data->getDbCon();
$q="
SELECT Players.PlayerName Player, COUNT(DISTINCT GameID) Matches, SUM(PlayerStats.Airshots) TotalAirshots,AVG(PlayerStats.Airshots) AvgAirshots
FROM PlayerStats, Players 
WHERE PlayerStats.SteamID=Players.SteamID AND ClassID = 0
GROUP BY PlayerStats.SteamID
HAVING COUNT(ClassID) > 20
ORDER BY AVG(Airshots) DESC
";
$res = mysqli_query($db, $q);
?>

</pre>
<main class="page-body">
<p>
<i>The stats displayed here attempts to disregard overall factors which would skew the data.
For example, medic playtime is not considered for stats associated with damage-dealing clases.
Airshots are only considered when playing soldier and demo, etc.</i> 
<?php
$data->printTable($res);
?>

</p>
</main>

</section>

<section>
</section>
</html>
