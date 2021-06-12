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
<body id="signup">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<div id="auth">
<p>


<h1 style="color:#52FFB8;text-align:left"><b>signup</b></h1>
<?php 
$ValidLogin = False;
if (isset($_POST['username']))
{
  $data = new dataAccess();
  $db = $data->getDbCon();
  //CHECK IF USER EXISTS
  $q = "SELECT * FROM Users WHERE UserName = '".addslashes($_POST['username'])."'";
  $res = mysqli_query($db, $q);

  if (mysqli_num_rows($res) < 1) 
  {
    $ValidLogin = True;
    //INSERT NEW USER
    $q = "INSERT INTO Users (UserName, PassHash, JoinDate)
    VALUES ('".addslashes($_POST['username'])."','".hash('sha256', $_POST['password'])."', ".time().")";
    $res = mysqli_query($db, $q);
    if ($res == true)
    {
      echo 'success! welcome '.($_POST['username']);
    }
  }
  else
  {
    echo "Error: UserName ".($_POST['username'])." already taken";
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
  <br><input type="submit" value="signup">
</form>
';
}

?>

</pre>

</p>
</div>
<footer id="footer">tf2sa</footer>
</div>
</body>
</html>

