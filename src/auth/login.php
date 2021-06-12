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
<body id="login">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<div id="auth">
<p>

<h1 style="color:#52FFB8;text-align:left"><b>login</b></h1>

<?php 
$ValidLogin = False;
if (isset($_POST['username']))
{
  $data = new dataAccess();
  $db = $data->getDbCon();
  //CHECK IF USER EXISTS
  $q = "SELECT UserID FROM Users 
  WHERE UserName = '".addslashes($_POST['username'])."' AND PassHash = '".hash('sha256', $_POST['password'])."'";
  $res = mysqli_query($db, $q);

  if (mysqli_num_rows($res) == 1) 
  {
    $ValidLogin = True;
    echo 'success! welcome back '.($_POST['username']);
    //Add session here
    $UserID = mysqli_fetch_row($res)[0];
    $_SESSION['sessionid'] = hash('sha256', time().$UserID);
    $q = "UPDATE Users
    SET SessionID = '".$_SESSION['sessionid']."'
    WHERE UserID = ".$UserID;
    $res = mysqli_query($db, $q);
    echo '<br>'.$_SESSION['sessionid'];
  }
  else
  {
    echo "Error: unrecognized account details.<br>";
  }

  mysqli_close($db);
}

if ($ValidLogin == False)
{
echo '
<form method="post" action="'.$_SERVER['PHP_SELF'].'">
  <label for="username">username</label><br>
  <input type="text" id="username" name="username"> <br>
  <label for="password">password</label><br>
  <input type="password" id="password" name="password"> <br>
  <br><input type="submit" value="Login">
</form>
';
}

?>

</p>
</div>
</div>
<footer id="footer">tf2sa</footer>
</div>
</body>
</html>
