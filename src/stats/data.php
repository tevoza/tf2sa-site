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
SELECT * from Progress WHERE SteamID={$_POST['steamid']}
";
$res = mysqli_query($db, $q);
$data = array();
foreach ($res as $row){
  $data[]=$row;
}

echo json_encode($data);
mysqli_close($db);
?>
