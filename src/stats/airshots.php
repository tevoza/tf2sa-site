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

<div id="side-bar" style="padding-top:200px">
  <ul id="airshots">
    <li><a class="damage" href="damage.php">damage</a></li>
    <li><a class="kills"href="kills.php">kills</a></li>
    <li><a class="airshots"href="airshots.php">airshots</a></li>
    <li><a class="scout"href="scout.php">scout</a></li>
    <li><a class="soldier"href="soldier.php">soldier</a></li>
    <li><a class="demo"href="demo.php">demo</a></li>
    <li><a class="medic"href="medic.php">medic</a></li>
  </ul> 
</div>

<div id="content">
  <?php
  $cutoff = time() - (60 * 60 * 24 * 365 * $_ENV['RECENT_THRESH_YEARS']);
  $data = new dataAccess();
  $db = $data->getDbCon();
  $q="
SELECT PlayerName, COUNT(PlayerName) Matches,AVG(DamageTaken) DamageTaken, AVG(HealsReceived) Heals
FROM PlayerStats ps, Players p
WHERE ps.SteamID = p.SteamID 
GROUP BY PlayerName
ORDER BY Heals DESC
  ";
  $res = mysqli_query($db, $q);
  ?>
  <h1 style="color:#52FFB8;text-align:left"><b>airshots</b></h1>
  
  <?php $data->printTable($res); ?>
</div>

</div>
</div>
</body>
</html>
