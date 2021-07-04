<?php
define('BASE_DIR', __DIR__.'/../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();
?>
<body id="index">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">

<div id="info">
  <h2 style="color:#52FFB8;text-align:left"><b>welcome</b></h1>
Welcome to TF2SA. The home of local South African pugs.
We host 3 servers: <br>
<ol>
    <li> TF2SA Pug </t> 129.232.150.23:27016  </li> <br>
    <li> TF2SA MGE  129.232.150.23:27016  </li> <br>
    <li> TF2SA Jump (All Maps) | tempus.tf  129.232.150.23:27016 </li> <br>
</ol>
</div>

<div id="report">
  <h2 style="color:#52FFB8;text-align:left"><b>two-week summary</b></h1>
  <?php 
  $data = new dataAccess();
  $db = $data->getDbCon();
  $thresh = time() - 14*24*60*60;
  $q="
  SELECT 
    COUNT(DISTINCT g.GameID) Games, COUNT(DISTINCT ps.SteamID)
  FROM
    Games g, PlayerStats ps
  WHERE
    g.GameID = ps.GameID AND Date > {$thresh}
  ";
  if ($res = mysqli_query($db, $q))
  {
    $res    = mysqli_fetch_row($res);
    echo "
    <ul>
      <li>{$res[0]} games</li>
      <li>{$res[1]} unique players</li>
    ";
  }
  $q=
  "
  SELECT
    Map, COUNT(Map) Played
  FROM
    Games
  WHERE
    Date > {$thresh}
  GROUP BY
    Map
  ORDER BY
    Played DESC
  ";
  if ($res = mysqli_query($db, $q))
  {
    $data->printTable($res);
  }
  mysqli_close($db);
  ?>
  </ul> 

</div>

</div>
<footer id="footer">tf2sa</footer>
</div>
</body>
</html>
