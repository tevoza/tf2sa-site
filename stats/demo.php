<html lang="en">

<head>
    <meta charset="utf-8">
    <title>TF2SA</title>
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
<div class="wrapper">
<section>
<header class="page-header">
    <div>
        <h1 style="float: left;"><img src="../res/tf2sa.jpg" height="70px" width="70px" border="0px"></h1>
        <h1><b> TF2SA Pugs </b></h1>
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
<?php
include '../data/dataAccess.php';
$data = new dataAccess();
$db = $data->getDbCon();
$q="
SELECT Players.PlayerName Player, COUNT(DISTINCT GameID) Matches, SUM(Playtime)/3600 Time, SUM(Kills) TotalKills,SUM(Kills)/COUNT(DISTINCT GameID) AvgKills, SUM(PlayerStats.Damage) TotalDamage,AVG(Damage)/AVG(Playtime)*60 DPM
FROM PlayerStats, Players 
WHERE PlayerStats.SteamID=Players.SteamID AND ClassID = 4
GROUP BY PlayerStats.SteamID
HAVING COUNT(ClassID) > 20
ORDER BY DPM DESC
";
$res = mysqli_query($db, $q);
?>

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
