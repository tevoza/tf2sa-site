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
SELECT Players.PlayerName Player, COUNT(DISTINCT GameID) Matches, SUM(Kills) TotalKills,SUM(Kills)/COUNT(DISTINCT GameID) Avg
FROM PlayerStats, Players 
WHERE PlayerStats.SteamID=Players.SteamID AND ClassID != 7
GROUP BY PlayerStats.SteamID
HAVING COUNT(ClassID) > 20
ORDER BY Avg DESC
";
$res = mysqli_query($db, $q);
?>

<main class="page-body">
<p>

<?php
$data->printTable($res);
?>

</p>
</main>

</section>

<section>
</section>
</html>
