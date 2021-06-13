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
<body id="forum">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap2">
	
	<div id="content-wrap3">
<?php
 $data = new dataAccess();
  $db = $data->getDbCon();
$q="
  SELECT ThreadID, Topic, Users.UserName, Date 
  FROM Threads, Users
  WHERE Threads.UserID=Users.UserID 
  ORDER BY Threads.Date DESC
  ";
  $res = mysqli_query($db, $q);
	if (!array_key_exists('sessionid', $_SESSION)){
	echo
	'<h1 style="color:#52FFB8;text-align:left; height=100px;">Forum</h1>
	</div>
	
';
	}else{
		echo'
	
		<h1 style="color:#52FFB8;text-align:left; height=100px;">Forum</h1>
		<form action="post.php">
			<input type="submit" value="Create New Post" style="height: 30px; width: 120px; background-color:#52FFB8; margin-right:50%; margin-top:30% ">
	</div>
	</form>
'
;
}		
?>	
<?php 
if(!$res)
{
    echo 'The category could not be displayed, please try again later.' . mysqli_error($db);
}else{
$data->printPost($res);
}
 ?>
</div>
</div>'	


</pre>


</body>
</html>

