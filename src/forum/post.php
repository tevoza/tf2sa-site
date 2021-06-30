<?php
define('BASE_DIR', __DIR__.'/../../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="input.js"></script>
<body id="forum">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div>
<h1 style="color:#52FFB8;text-align:left; height=100px;">Create Post</h1>
<?php

if($_SERVER['REQUEST_METHOD'] != "POST")
{
  ?> 
	<form id="uploadForm" enctype="multipart/form-data">
		<label>Select image to upload</label>
		<input type="file" name="fileToUpload" id="fileToUpload">
		<input type="submit" value="upload image" name="submit">
	</form>
	<div class="progress">
			<div class="progress-bar"></div>
	</div>
	<div id="uploadStatus"></div>

	<form method="post" action="">
		<input id="imgHash" type="hidden" name="imgHash"/>

		Topic: <br>
		<textarea name="topic_name" placeholder="Topic of thread" required></textarea><br>
		<div>
		Comment: <br>
		<textarea name="comment" placeholder="Comment on the thread" required /></textarea><br>
		<br><fieldset style="width:300px">
			<legend><label for="poll"> Create a poll? (Optional) </label></legend>
			<div>
				<label for="title">Title</label>
				<input type="text" name="title" id="title" placeholder="Poll Title">
			</div>
			<div>	
				<label for="answers">Poll Answer Options (input per line)</label>
			</div>
			<div>       
				<textarea name="answers" id="answers" ></textarea>
			</div> 
		</fieldset><br>
		</div>
<input type="submit" value="Add Thread" /><br>

  </form>
	<?php 
}
else
{
  $data = new dataAccess();
  $db = $data->getDbCon();
  $now = time();
  $topic = $_POST['topic_name'];
  $comment = $_POST['comment'];
  //Create Queries necessary for adding tables to databases
   $sql = "INSERT 
   INTO Threads 
   (UserID, Topic, Date) 
   VALUES 
   ((SELECT UserID from Users where SessionID = '".$_SESSION['sessionid']."'),
   '".addslashes($topic)."',
   ".time().")";
   
   //for threadid used a subquery that checks for the userid and when the thread was posted, if thread and first comment added at the same time 
   //to avert error from before 
   $sql1 = "INSERT 
   INTO Comments 
   (ThreadID, Content, Date, UserID) 
   VALUES 
   ((SELECT ThreadID from Threads where UserID = (SELECT UserID from Users where SessionID = '".$_SESSION['sessionid']."') AND Date = ".time()."),
   '".addslashes($comment)."',
   ".time().",
   (SELECT UserID from Users where SessionID = '".$_SESSION['sessionid']."'))";
   
   //Process Queries
    $result = mysqli_query($db, $sql);
	$result = mysqli_query($db, $sql1);
	
	$q = "SELECT UserID 
	FROM Users 
	WHERE SessionID = '".$_SESSION['sessionid']."'";
	$res = mysqli_query($db, $q);
	$UserID = mysqli_fetch_row($res)[0];
	$q = "SELECT CommentID FROM Comments
	WHERE ThreadID= (SELECT ThreadID from Threads where UserID = (SELECT UserID from Users where SessionID = '".$_SESSION['sessionid']."') AND Date = ".time().") AND Content = '".addslashes($comment)."'";
	$res = mysqli_query($db, $q);
	$CommentID = mysqli_fetch_row($res)[0];
	if (!empty($_POST['imgHash'])) {
		$q = "INSERT INTO Images (CommentID, ImageHash) VALUES ({$CommentID}, '{$_POST['imgHash']}')";
		$res = mysqli_query($db, $q);
	}
    
	if (!$result)
	{
		//something went wrong, display the error
		printf("error: %s\n", mysqli_error($db));
	}
	else
	{ 
		if ((empty($_POST['title'])) && (!empty($_POST['answers']))){
			echo 'The poll must have a topic to go by.';
		}
		else if((!empty($_POST['title'])) && (empty($_POST['answers']))){
			echo 'The poll must have options to vote for.';
		}
		else if ((!empty($_POST['title'])) && (!empty($_POST['answers']))){
			
			$sql2 = "SELECT CommentID
			FROM Comments
			WHERE ThreadID = (SELECT ThreadID from Threads where UserID = (SELECT UserID from Users where SessionID = '".$_SESSION['sessionid']."') AND Date = ".time().") AND Content = '".addslashes($comment)."'";
			$res = mysqli_query($db, $sql2);
			$CommentID = mysqli_fetch_row($res)[0];
			$topic = $_POST['title'];
			$answers = explode(PHP_EOL, $_POST['answers']);
			$num_of_answers = count($answers);
			
			if($num_of_answers<2){
				
				echo 'The poll must have more than one voting option.';
			
			}else{
				
				$sql3 ="INSERT INTO Polls (CommentID, Topic)
				VALUES (".$CommentID.",'".addslashes($topic)."')";
				$res2 = mysqli_query($db, $sql3);
				if (!$res2)
					{
						//something went wrong, display the error
						printf("error: %s\n", mysqli_error($db));
					}
				else{
						//the topic last where clause is unnecessary but humour me 
						$sql = "SELECT PollID
						FROM Polls
						WHERE CommentID = '" .$CommentID."' AND Topic = '".addslashes($topic)."'";
						$res3 = mysqli_query($db, $sql);
						$PollID = mysqli_fetch_row($res3)[0];
						
						foreach($answers as $answer){
							if (empty($answer)) continue;
							$sql4 = "INSERT INTO PollOptions (PollID, Option)
							VALUES (".$PollID.",'".addslashes($answer)."')";
							$res4 = mysqli_query($db, $sql4);
							if (!$res4)
					{
						//something went wrong, display the error
						printf("error: %s\n", mysqli_error($db));
					}
							
						}
						echo 'Comment and Polls successfully added.';
				}
			}
		}
		else{
			echo 'Comment successfully added.';
		}
		
	}
}
?>
</div>
