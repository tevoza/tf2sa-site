<?php
define('BASE_DIR', __DIR__.'/../../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();
?>
<body id="rules">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<p>

we put rules here

 <?php
$data = new dataAccess();
  $db = $data->getDbCon(); 
 echo $_SESSION['sessionid']; 
 $sql = "SELECT ThreadID from Threads where UserID = (SELECT UserID from Users where SessionID = '".$_SESSION['sessionid']."')";
 $res = mysqli_query($db, $sql);
 if(!$res)
{
    echo 'The category could not be displayed, please try again later.' . mysqli_error($db);
}else{
while($row = mysqli_fetch_assoc($res)){
echo  $row['ThreadID']	;
	
}
}
 ?>

</p>
</div>
<footer id="footer">tf2sa</footer>
</div>
</body>
</html>
