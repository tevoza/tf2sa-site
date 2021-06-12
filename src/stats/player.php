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
<?php $sidebar->printSideBar('player')?>
<div id="content">
  <?php
  if (isset($_GET['steamid'])) {
    $steamid = $_GET['steamid'];
    $cutoff = time() - (60 * 60 * 24 * 365 * $_ENV['RECENT_THRESH_YEARS']);
    $data = new dataAccess();
    $db = $data->getDbCon();
    $q="SELECT PlayerName from Players WHERE SteamID = ".$steamid;
    $res = mysqli_query($db, $q);
    $player = mysqli_fetch_row($res)[0];
    $q="Select MIN(Date) FROM Games g, PlayerStats ps where g.GameID=ps.GameID AND SteamID=".$steamid;
    $res = mysqli_query($db, $q);
    $start = mysqli_fetch_row($res)[0];
    $q="Select MAX(Date) FROM Games g, PlayerStats ps where g.GameID=ps.GameID AND SteamID=".$steamid;
    $res = mysqli_query($db, $q);
    $end =  mysqli_fetch_row($res)[0];
    $q="Select COUNT(PlayerStatsID) FROM Games g, PlayerStats ps where g.GameID=ps.GameID AND SteamID=".$steamid;
    $res = mysqli_query($db, $q);
    $games =  mysqli_fetch_row($res)[0];
  ?>
    <h1 style="color:#52FFB8;text-align:left"> <b><a href='https://steamcommunity.com/profiles/<?php echo $steamid; ?>'><?php echo $player; ?></a></b> </h1>

    <b><?php echo $games; ?></b> recorded matches <br>
    <b>first game: </b> <?php echo date("Y-m-d", $start); ?> <br>
    <b>last game: </b> <?php echo date("Y-m-d", $end); ?> <br>
    <h2> all-time bests </h2>
    <h3> scout </h3>
    
  <?php 

  $q="
SELECT COUNT(ps.PlayerStatsID) Matches, MAX(Kills) Kills, MAX(Assists) Assists, MAX(Deaths) Deaths, MAX(Damage) Damage
FROM PlayerStats ps, ClassStats c
WHERE ps.PlayerStatsID=c.PlayerStatsID AND ClassID=1 AND SteamID=".$steamid;
  $res = mysqli_query($db, $q);
  $data->printTable($res); 
   
  $q="
SELECT COUNT(ps.PlayerStatsID) Matches, MAX(Kills) Kills, MAX(Assists) Assists, MAX(Deaths) Deaths, MAX(Airshots) Airshots, MAX(Damage) Damage
FROM PlayerStats ps, ClassStats c
WHERE ps.PlayerStatsID=c.PlayerStatsID AND ClassID=2 AND SteamID=".$steamid;
  $res = mysqli_query($db, $q);
  $data->printTable($res); 
  echo "<h3> soldier </h3>";

  $q="
SELECT COUNT(ps.PlayerStatsID) Matches, MAX(Kills) Kills, MAX(Assists) Assists, MAX(Deaths) Deaths, MAX(Airshots) Airshots, MAX(Damage) Damage
FROM PlayerStats ps, ClassStats c
WHERE ps.PlayerStatsID=c.PlayerStatsID AND ClassID=4 AND SteamID=".$steamid;
  $res = mysqli_query($db, $q);
  $data->printTable($res); 
  echo "<h3> demo </h3>";
  }?>
</div>

</div>
</div>
</body>
</html>
