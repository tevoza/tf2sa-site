<?php
include_once(BASE_DIR.'vendor/autoload.php'); 

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

   public function printHeader()
   {
      $ASSET_DIR = $this->WWW.$_ENV['ASSET_DIR'];
      $HOME = $this->WWW.$_ENV['HOME_PG'];
      $STATS = $this->WWW.$_ENV['STATS_PG'];
      $FORUM = $this->WWW.$_ENV['FORUM_PG'];
      echo'
      <div id="header">
         <div id="icon">
            <img src="'.$ASSET_DIR.'/tf2sa.png" width="90px" height="90px">
         </div>

         <div id="title" style="color:#52FFB8">
            <h1 style=""><b> #tf2sa pugs </b></h1>
         </div>

         <div id="user" style="color:white">
            <b> welcome, guest </b> <br>
            <b> login/signin </b>
         </div>
      </div>

      <div id="navbar">
         <ul>
           <li><a class="index" href="'.$HOME.'">home</a></li>
           <li><a class="stats" href="'.$STATS.'">stats</a></li>
           <li><a class="forum" href="'.$FORUM.'">forum</a></li>
         </ul> 
      </div>
   ';
   }

}
?>
