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
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrapTopics">
<div id="content-wrap3">
<?php
$data = new dataAccess();
$db = $data->getDbCon();

$sql = "SELECT
			Topic,
            Comments.Content,
			Comments.Post_by, 
			UserName,
			Comments.Date

        FROM
			Threads 
		INNER JOIN 
			Comments
		ON 
			Threads.ThreadId =" .mysqlI_real_escape_string($db,$_GET['id']). " AND Comments.Posted_To =" .mysqlI_real_escape_string($db,$_GET['id']).
		" INNER JOIN 
			Users
		ON 
			Threads.UserID = Users.UserID";
		
        $res = mysqli_query($db, $sql);
if(!$res)
	{
    echo 'The category could not be displayed, please try again later.' . mysqli_error($db);
	}else
	{
		if (!array_key_exists('sessionid', $_SESSION)){
			while($row = mysqli_fetch_assoc($res)){
			echo '<p><font face ="Arial" size = "8" style="color:#52FFB8;text-align:left;">' . $row['Topic']. '</font><br>' ;
			
			echo '<font face = "comic sans" size = "2"> posted by: ' .$row['UserName']. '. On: ' .date("Y-m-d", $row['Date']).'</font> </p>';
			echo ' <br>';
			echo '<font face = "comic sans" size = "5">' .$row['Content']. '</font>' ;
			
			echo '</div>';
			echo '<hr style="width:100%;text-align:center;margin-left:0;height:0">';
			}
		}else{
					while($row = mysqli_fetch_assoc($res))
        {
			echo '<p><font face ="Arial" size = "8" style="color:#52FFB8;text-align:left;">' . $row['Topic']. '</font><br>' ;
			echo '<font face = "comic sans" size = "2"> posted by: ' .$row['UserName']. '. On: ' .date("Y-m-d", $row['Date']).'</font> </p>';
			echo'<form action="reply.php">';
			echo '<input type="submit" value="Reply" style="height: 30px; width: 120px; background-color:#52FFB8; margin-right:50%; margin-top:30% ">';
			echo '</form>';
			echo '<font face = "comic sans" size = "5">' .$row['Content']. '</font>' ;
			echo '</div>';
			echo '<hr style="width:100%;text-align:center;margin-left:0;height:0">';
        }
			
		}
		
		
		


				
        }
     
	
?>
</div>	
</div>
</body>
</html>