<?php
if (!isset($_POST['steamid']))
{
  die();
}

define('BASE_DIR', __DIR__.'/../../');
include_once(BASE_DIR.'vendor/autoload.php'); 
include_once(BASE_DIR.'src/data/dataAccess.php');    
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$DataObj = new dataAccess();
$db = $DataObj ->getDbCon();
$q="
SELECT 
  UNIQUE(a.EndDate) EndDate, p.Kills, p.DPM,
  p.Headshots, p.Airshots
FROM 
  Progress a
LEFT JOIN
(
  SELECT
    EndDate, Kills, DPM, Headshots, Airshots
  FROM
    Progress
  WHERE SteamID = {$_POST['steamid']}
) AS p on p.EndDate = a.EndDate
HAVING 
  a.EndDate > (SELECT MIN(EndDate) FROM Progress a WHERE SteamID={$_POST['steamid']}) AND
  a.EndDate < (SELECT MAX(EndDate) FROM Progress a WHERE SteamID={$_POST['steamid']})
";
$res = mysqli_query($db, $q);
$data = array();
foreach ($res as $row){
  $data[]=$row;
}

echo json_encode($data);
mysqli_close($db);
?>
