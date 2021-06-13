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
   
    echo 
	'<form method="post" action="">
        Reply: <br>
		<textarea name="reply" /></textarea><br>
        <input type="submit" value="Add Comment" /><br>
     </form>';
}
else
{
  $data = new dataAccess();
  $db = $data->getDbCon();
  $reply = $_POST['reply'];

   $sql1 = "INSERT INTO Comments (Posted_to, Content, Date, Post_by) VALUES ((SELECT ThreadID from Threads where ThreadID ='" .mysqlI_real_escape_string($db,$_GET['id']). "'), '".addslashes($reply)."',".time().",(SELECT UserID from Users where SessionID = '".$_SESSION['sessionid']."'))";
	$result = mysqli_query($db, $sql1);
    
	if(!$result)
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