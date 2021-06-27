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
<?php
if (!array_key_exists('sessionid', $_SESSION)) //user logged in
{
	echo "please log in to vote";
	die();
}

//CHECK IF USER EXISTS
$data = new dataAccess();
$db = $data->getDbCon();
$q = "SELECT UserID FROM Users WHERE SessionID = '".$_SESSION['sessionid']."'";
$res = mysqli_query($db, $q);
$UserID = mysqli_fetch_row($res)[0];

//TODO check poll also exists for people who type in address bar.

//check if they've voted
$q = "
SELECT * FROM PollVotes
WHERE PollID={$_GET['pollid']} AND UserID={$UserID}";
$res = mysqli_query($db, $q);

if (mysqli_num_rows($res) == 0) //no vote has been recorded for, insert one.
{
	$q = "INSERT INTO PollVotes (UserID, PollID, PollOptionID)
	VALUES ({$UserID}, {$_GET['pollid']}, {$_GET['optionid']})";
	$res = mysqli_query($db, $q);
	echo "vote recorded!";
}
else //update whetever previous vote
{
	$q = "UPDATE PollVotes
	SET PollOptionID={$_GET['optionid']}
	WHERE UserID={$UserID} AND PollID = {$_GET['pollid']}";
	$res = mysqli_query($db, $q);
	echo "vote updated!";
}

mysqli_close($db);
?>
