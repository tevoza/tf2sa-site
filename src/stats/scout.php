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
<button class="tablink" onclick="openPage('Home', this, 'red')">Home</button>
<button class="tablink" onclick="openPage('News', this, 'green')" id="defaultOpen">News</button>
<script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
<body id="stats">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<?php $sidebar->printSideBar('scout')?>
<div id="content">
  <?php
  $cutoff = time() - (60 * 60 * 24 * 365 * $_ENV['RECENT_THRESH_YEARS']);
  $data = new dataAccess();
  $db = $data->getDbCon();
  $q="
SELECT ps.SteamID, PlayerName Player, COUNT(DISTINCT ps.GameID) Matches, ROUND(SUM(Playtime)/3600, 1) Hours, ROUND(AVG(Kills),1) Kills, ROUND(AVG(Deaths), 1) Deaths,
  ROUND(AVG(Assists), 1) Assists, ROUND(SUM(Damage)/SUM(Playtime)*60,1) DPM, ROUND(SUM(DamageTaken)/SUM(Playtime)*60,1) DTM, ROUND(SUM(HealsReceived)/SUM(Playtime)*60,1) HRM
FROM PlayerStats ps, Players p, Games g, ClassStats cs
WHERE ps.SteamID=p.SteamID AND ps.GameID=g.GameID and ps.PlayerStatsID=cs.PlayerStatsID AND Date > ".$cutoff." AND ClassID IN (1)
GROUP BY ps.SteamID
HAVING Matches > ".$_ENV['MIN_MATCHES']."
ORDER BY DPM DESC
  ";
  $res = mysqli_query($db, $q);
  $q="
SELECT ps.SteamID, PlayerName Player, COUNT(DISTINCT ps.GameID) Matches, ROUND(SUM(Playtime)/3600, 1) Hours, ROUND(AVG(Kills),1) Kills, ROUND(AVG(Deaths), 1) Deaths,
  ROUND(AVG(Assists), 1) Assists, ROUND(SUM(Damage)/SUM(Playtime)*60,1) DPM, ROUND(SUM(DamageTaken)/SUM(Playtime)*60,1) DTM, ROUND(SUM(HealsReceived)/SUM(Playtime)*60,1) HRM
FROM PlayerStats ps, Players p, Games g, ClassStats cs
WHERE ps.SteamID=p.SteamID AND ps.GameID=g.GameID and ps.PlayerStatsID=cs.PlayerStatsID AND ClassID IN (1)
GROUP BY ps.SteamID
HAVING Matches > ".$_ENV['MIN_MATCHES']."
ORDER BY DPM DESC
  ";
  $resAll = mysqli_query($db, $q);
  ?>
  <h1 style="color:#52FFB8;text-align:left"><b>scout</b></h1>
  <div id="Home" class="tabcontent">
    <?php $data->printPlayerTable($res); ?>
  </div>

  <div id="News" class="tabcontent">
    <?php $data->printPlayerTable($resAll); ?>
  </div>
  <?php mysqli_close($db); ?>
</div>

</div>
</div>
</body>
</html>
  <script>
  function openPage(pageName, elmnt, color) {
  // Hide all elements with class="tabcontent" by default */
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Remove the background color of all tablinks/buttons
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].style.backgroundColor = "";
  }

  // Show the specific tab content
  document.getElementById(pageName).style.display = "block";

  // Add the specific color to the button used to open the tab content
  elmnt.style.backgroundColor = color;
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
  </script>
