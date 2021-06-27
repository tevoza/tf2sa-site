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
<body id="post">
<div id="page-container">

<?php 
$template->printHeader(BASE_DIR);
$data = new dataAccess();
$db = $data->getDbCon();

$sql = "
SELECT Topic, Comments.Content, Comments.ThreadID, UserName, Comments.Date
FROM Threads 
INNER JOIN Comments
ON Threads.ThreadId =" .mysqlI_real_escape_string($db,$_GET['id']). " AND Comments.ThreadID =" .mysqlI_real_escape_string($db,$_GET['id'])." AND Comments.Date = Threads.Date
INNER JOIN Users
ON Threads.UserID = Users.UserID";
$res = mysqli_query($db, $sql);

$sql2 = "
SELECT Comments.CommentID, Comments.Content, Comments.ThreadID, UserName, Comments.Date
FROM Threads 
INNER JOIN Comments
ON Threads.ThreadId =" .mysqlI_real_escape_string($db,$_GET['id']). " AND Comments.ThreadID =" .mysqlI_real_escape_string($db,$_GET['id'])." AND Comments.Date != Threads.Date
INNER JOIN Users
ON Comments.UserID = Users.UserID
ORDER BY Comments.date 
DESC";
$res2 = mysqli_query($db, $sql2);
?>

<div id="content-wrapTopics">
<div id="content-wrap3">

<?php 
if(!$res)
{
	echo 'The thread could not be displayed, please try again later.' . mysqli_error($db);
}
else
{
	if (!array_key_exists('sessionid', $_SESSION))
	{
		while($row = mysqli_fetch_assoc($res))
		{
			echo '<p><font face ="Arial" size = "8" style="color:#52FFB8;text-align:left;">' . $row['Topic']. '</font><br>' ;
			echo '<font face = "comic sans" size = "2"> posted by: ' .$row['UserName']. '. On: ' .date("Y-m-d", $row['Date']).'</font> </p>';
			echo ' <br>';
			echo '<font face = "comic sans" size = "5">' .$row['Content']. '</font>' ;
		}
	}
	else
	{
		while($row = mysqli_fetch_assoc($res))
		{
			echo '<p><font face ="Arial" size = "8" style="color:#52FFB8;text-align:left;">' . $row['Topic']. '</font><br>' ;
			echo '<font face = "comic sans" size = "2"> posted by: ' .$row['UserName']. '. On: ' .date("Y-m-d", $row['Date']).'</font> </p>';
			echo '<form action="reply.php" method="get" >';
			$id = mysqlI_real_escape_string($db,$_GET['id']);
			//created the hyperlink 
			echo '<h3 style="height: 30px; width: 120px; margin-right:50%; margin-top:30%; color:#52FFB8;"> <a href="reply.php?id=' .$id.'">Reply</a></h3><br>';
			echo '</form>';
			echo '<font face = "comic sans" size = "5">' .$row['Content']. '</font>' ;
		}
	}
}

?>
</p>
</div>
<hr style="width:100%;text-align:center;margin-left:0;height:0">
<?php

if(!$res2)
{
	    echo 'The thread could not be displayed, please try again later.' . mysqli_error($db);
}
else
{

	while($row = mysqli_fetch_assoc($res2))
	{
		//check for poll entries and thread
		$CommentID = $row['CommentID'];
		$q = "SELECT PollID, Topic FROM Polls WHERE CommentID=".$CommentID;	
		$ThreadRES = mysqli_query($db, $q);
		$q = "SELECT ImageHash FROM Images WHERE CommentID={$CommentID}";
		$ImageQry = mysqli_query($db, $q);

		echo '<div id="comment-wrap">';
			//comment id
			echo 
			'<div id="comment-id">
				<font face="comic sans" size="2"> ' .$row['UserName']. '<br>' .date("Y-m-d", $row['Date']).'</font>
			</div>';

			//comment content
			echo '<div id="comment-content">';
				//image stuff
				if (mysqli_num_rows($ImageQry) > 0){echo "<img src='files/" . mysqli_fetch_row($ImageQry)[0] . "'>";}
				echo '<div id="comment-text"> <font face = "comic sans" size = "5">' .$row['Content']. '</font> </div>';
				if (mysqli_num_rows($ThreadRES) > 0){$data->printPollOptions(mysqli_fetch_row($ThreadRES)[0]);}
			echo '</div>';
				
		echo '</div>';
	}

}
//print_r($sql."\n");
//print_r($sql2);

?>

</div>	
</div>
</body>
</html>
