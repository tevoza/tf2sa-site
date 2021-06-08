<?php
include_once(BASE_DIR.'vendor/autoload.php'); 
include_once(BASE_DIR.'src/data/dataAccess.php');    

class Sidebar
{
  function printSideBar($page)
  {
    echo'
    <div id="side-bar" style="padding-top:200px">
      <ul id="'.$page.'">
      <li><a class="overall" href="overall.php">overall</a></li>
      <li><a class="scout"href="scout.php">scout</a></li>
      <li><a class="soldier"href="soldier.php">soldier</a></li>
      <li><a class="demo"href="demo.php">demo</a></li>
      <li><a class="medic"href="medic.php">medic</a></li>
      </ul>
    </div>';
  }
}
?>
