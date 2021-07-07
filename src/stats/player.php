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
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/chart.min.js"></script>
<script type="text/javascript" src="js/plot.js"></script>

<body id="stats">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<?php $sidebar->printSideBar('player')?>
<div id="content">
  <div id="player-id">
  <?php
  if (!isset($_GET['steamid'])) 
  {
    echo "no player supplied </div>";
    die();
  }
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
  <h1 style=""> <b><a style="color:#52FFB8;text-align:left" href='https://steamcommunity.com/profiles/<?php echo $steamid; ?>'><?php echo $player; ?></a></b> </h1>
  <h6 style="padding: 15px"><?php echo $games; ?></b> recorded matches </h6> 
  <h6 style="padding: 15px"><i>first game:</i>  <?php echo date("Y-m-d", $start); ?> </h6>
  <h6 style="padding: 15px">last game:  <?php echo date("Y-m-d", $end); ?> </h6>
  </div>
  
  <center><h4 style="color:#52FFB8;"> player bests </h4></center>

  <div id="player-bests">

    <div id="player-bests-class">
      <h3>scout</h3>
      <?php
      $q=
      "
      SELECT
        COUNT(ps.PlayerStatsID) AS Matches,  MaxDamage.DmgID, MaxDamage.Damage, MaxKills.GameID, MaxKills.Kills
      FROM
        (SELECT ps.GameID, cs.Kills FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 1 AND SteamID = ".$steamid." ORDER BY cs.Kills DESC LIMIT 1) AS MaxKills,
        (SELECT ps.GameID DmgID, cs.Damage FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 1 AND SteamID = ".$steamid." ORDER BY cs.Damage DESC LIMIT 1) AS MaxDamage, 
        PlayerStats ps, ClassStats cs
      WHERE
        cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 1 AND SteamID = ".$steamid;
      if ($res=mysqli_query($db, $q)){$data->printPlayerBest($res);}
      ?>
    </div>

    <div id="player-bests-class">
      <h3>soldier</h3>
      <?php
      $q="
      SELECT
        COUNT(ps.PlayerStatsID) AS Matches, MaxKills.GameID, MaxKills.Kills, MaxDamage.GameID, MaxDamage.Damage, MaxAirshots.GameID, MaxAirshots.Airshots
      FROM
        (SELECT ps.GameID, cs.Kills FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 2 AND SteamID = ".$steamid." ORDER BY cs.Kills DESC LIMIT 1) AS MaxKills,
        (SELECT ps.GameID, cs.Damage FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 2 AND SteamID = ".$steamid." ORDER BY cs.Damage DESC LIMIT 1) AS MaxDamage,
        (SELECT ps.GameID, ps.Airshots FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 2 AND SteamID = ".$steamid." ORDER BY ps.Airshots DESC LIMIT 1) AS MaxAirshots, PlayerStats ps, ClassStats cs
      WHERE
        cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 2 AND SteamID = ".$steamid;
      if ($res=mysqli_query($db, $q)){$data->printPlayerBest($res);}
      ?>
    </div>

    <div id="player-bests-class">
      <h3>demo</h3>
      <?php
      $q="
      SELECT
         COUNT(ps.PlayerStatsID) AS Matches, MaxKills.GameID, MaxKills.Kills, MaxDamage.GameID, MaxDamage.Damage, MaxAirshots.GameID, MaxAirshots.Airshots
      FROM
        (SELECT ps.GameID, cs.Kills FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 4 AND SteamID = ".$steamid." ORDER BY cs.Kills DESC LIMIT 1) AS MaxKills,
        (SELECT ps.GameID, cs.Damage FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 4 AND SteamID = ".$steamid." ORDER BY cs.Damage DESC LIMIT 1) AS MaxDamage,
        (SELECT ps.GameID, ps.Airshots FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 4 AND SteamID = ".$steamid." ORDER BY ps.Airshots DESC LIMIT 1) AS MaxAirshots, 
        PlayerStats ps, ClassStats cs
      WHERE
        cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 4 AND SteamID = ".$steamid;
      if ($res=mysqli_query($db, $q)){$data->printPlayerBest($res);}
      ?>
    </div>

    <div id="player-bests-class">
      <h3>medic</h3>
      <?php
      $q="
      SELECT
         COUNT(ps.PlayerStatsID) AS Matches, MaxHeals.GameID, MaxHeals.Heals, MaxUbers.GameID, MaxUbers.Ubers, MaxDrops.GameID, MaxDrops.Drops
      FROM
        (SELECT ps.GameID, ps.Heals FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 7 AND SteamID = ".$steamid." ORDER BY ps.Heals DESC LIMIT 1) AS MaxHeals,
        (SELECT ps.GameID, ps.Ubers FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 7 AND SteamID = ".$steamid." ORDER BY ps.Ubers DESC LIMIT 1) AS MaxUbers,
        (SELECT ps.GameID, ps.Drops FROM ClassStats cs, PlayerStats ps WHERE cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 7 AND SteamID = ".$steamid." ORDER BY ps.Drops DESC LIMIT 1) AS MaxDrops, PlayerStats ps, ClassStats cs
      WHERE
        cs.PlayerStatsID = ps.PlayerStatsID AND ClassID = 7 AND SteamID = ".$steamid;
      if ($res=mysqli_query($db, $q)){$data->printPlayerBest($res);}
      ?>
    </div>
  </div>
  <?php mysqli_close($db); ?>

  <div id="chart-container" style="width:100%; height:auto">
    <canvas id="graphCanvas"></canvas>
  </div>
</div>

</div>
</div>
</body>
</html>
