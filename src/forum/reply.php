<?php
define('BASE_DIR', __DIR__.'/../../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();

?>

<body id="reply">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<h1 style="color:#52FFB8;text-align:left; height=100px;">Reply</h1>
<?php

 
if($_SERVER['REQUEST_METHOD'] != "POST")
{
  ?> 
	<form action="upload.php" method="post" enctype="multipart/form-data">
		Select image to upload
		<input type="file" name="fileToUpload" id="fileToUpload">
		<input type="submit" value="upload image" name="submit">
	</form>

	<form method="post" action="">
		Reply: <br>
		<textarea name="reply" /></textarea><br>
		<input type="submit" value="Add Comment" /><br>
  </form>
	<?php 
}
else
{
  $data = new dataAccess();
  $db = $data->getDbCon();
  $reply = $_POST['reply'];

	//get user id
  $q = "SELECT UserID FROM Users 
  WHERE SessionID = '".$_SESSION['sessionid']."'";
  $res = mysqli_query($db, $q);
  $UserID = mysqli_fetch_row($res)[0];
	$ThreadID = $_GET['id'];

  $sql1 = "INSERT INTO Comments (ThreadID, Content, Date, UserID) 
  VALUES (".$ThreadID." ,'".addslashes($reply)."', ".time().", ".$UserID.")";
	$result = mysqli_query($db, $sql1);
    
	if (!$result)
	{
		//something went wrong, display the error
		printf("error: %s\n", mysqli_error($db));
	}
	else
	{
		echo 'Comment successfully added.';
	}
}
?>
