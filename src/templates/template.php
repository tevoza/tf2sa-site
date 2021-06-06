<?php
include_once(BASE_DIR.'vendor/autoload.php'); 
include_once(BASE_DIR.'src/data/dataAccess.php');    
class Template
{
   public $WWW;

   public function getAssetPath($asset_dir)
   {
      return $actual_link;
   }

   public function __construct() {
      $dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
      $dotenv->load();
      $this->WWW = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
   }

   public function printHead()
   {
      $ASSET_DIR = $this->WWW.$_ENV['ASSET_DIR'];
      echo '
      <html lang=en>

      <head>
         <link rel="stylesheet" type="text/css" href="'.$ASSET_DIR.'/main.css" />
         <meta charset="UTF-8">
         <title>TF2SA</title>
      </head>
      ';
   }

  public function getName()
  {
    session_start();
    if (array_key_exists('sessionid', $_SESSION))
    {
      $data = new dataAccess();
      $db = $data->getDbCon();
      //CHECK IF USER EXISTS
      $q = "SELECT UserName FROM Users 
      WHERE SessionID = '".$_SESSION['sessionid']."'";
      $res = mysqli_query($db, $q);
      $Username = mysqli_fetch_row($res)[0];
      return "welcome ".$Username;
      mysqli_close();
    }
    else
    {
      return "welcome, guest";
    }
  }

   public function printHeader()
   {
      $ASSET_DIR = $this->WWW.$_ENV['ASSET_DIR'];
      $HOME = $this->WWW.$_ENV['HOME_PG'];
      $STATS = $this->WWW.$_ENV['STATS_PG'];
      $MAPS = $this->WWW.$_ENV['MAPS_PG'];
      $FORUM = $this->WWW.$_ENV['FORUM_PG'];
      $DEMOS = $this->WWW.$_ENV['DEMOS_PG'];     
      $RULES = $this->WWW.$_ENV['RULES_PG'];
      $LOGIN = $this->WWW.$_ENV['LOGIN_PG'];
      $SIGNUP = $this->WWW.$_ENV['SIGNUP_PG'];
      echo'
      <div id="header">
         <div id="icon">
            <img src="'.$ASSET_DIR.'/tf2sa.png" width="90px" height="90px">
         </div>

         <div id="title" style="color:#52FFB8">
            <h1 style=""><b> #tf2sa pugs </b></h1>
         </div>

         <div id="user" style="color:white">
          <div id="navbar">
            '.$this->getName().'
            <ul>
              <li><a class="login" href="'.$LOGIN.'">login</a></li>
              <li><a class="signup" href="'.$SIGNUP.'">signup</a></li>
            </ul> 
          </div>
         </div>
      </div>

      <div id="navbar">
         <ul>
           <li><a class="index" href="'.$HOME.'">home</a></li>
           <li><a class="stats" href="'.$STATS.'">stats</a></li>
           <li><a class="forum" href="'.$FORUM.'">forum</a></li>
           <li><a class="rules" href="'.$RULES.'">rules</a></li>
           <li><a href="'.$MAPS.'">maps</a></li>
           <li><a href="'.$DEMOS.'">demos</a></li>
         </ul> 
      </div>
   ';
   }

}
?>
