<?php
define('BASE_DIR', __DIR__.'/../../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();

?>

<body id="post">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<h1 style="color:#52FFB8;text-align:left; height=100px;">Create Post</h1>
<?php

 
if($_SERVER['REQUEST_METHOD'] != "POST")
{
   
    echo 
	'<form method="post" action="">
        Topic: <input type="text" name="topic_name" /><br>
        Comment: <br>
		<textarea name="comment" /></textarea><br>
        <input type="submit" value="Add topic" /><br>
     </form>';
}
else
{
  $data = new dataAccess();
  $db = $data->getDbCon();
  
  $topic = $_POST['topic_name'];
  $comment = $_POST['comment'];
   $sql = "INSERT INTO Threads (UserID, Topic, Date) VALUES ((SELECT UserID from Users where SessionID = '".$_SESSION['sessionid']."'), '".addslashes($topic)."',".time().")";
   $sql1 = "INSERT INTO Comments (Posted_to, Content, Date, Post_by) VALUES ((SELECT ThreadID from Threads where UserID = (SELECT UserID from Users where SessionID = '".$_SESSION['sessionid']."') AND Date = ".time()."), '".addslashes($comment)."',".time().",(SELECT UserID from Users where SessionID = '".$_SESSION['sessionid']."'))";
    $result = mysqli_query($db, $sql);
	$result = mysqli_query($db, $sql1);
    
	if(!$result)
    {
        //something went wrong, display the error
        printf("error: %s\n", mysqli_error($db));
    }
    else
    {
        echo 'New thread successfully created.';
    }
}
?>

