<?php

class dataAccess
{
    public $dbCon;
    function __construct(){
        $this->dbCon = mysqli_connect("127.0.0.1", $_ENV['MYSQL_USR'], $_ENV['MYSQL_PWD'], $_ENV['MYSQL_DB']) or die(mysqli_error());
    }

    public function getDbCon(){
        return $this->dbCon;
    }

    function __destruct(){
    }

    private function getHtmlRootFolder(string $root = '/var/www/') {

    // -- try to use DOCUMENT_ROOT first --
    $ret = str_replace(' ', '', $_SERVER['DOCUMENT_ROOT']);
    $ret = rtrim($ret, '/') . '/';

    // -- if doesn't contain root path, find using this file's loc. path --
    if (!preg_match("#".$root."#", $ret)) {
      $root = rtrim($root, '/') . '/';
      $root_arr = explode("/", $root);
      $pwd_arr = explode("/", getcwd());
      $ret = $root . $pwd_arr[count($root_arr) - 1];
    }

    return (preg_match("#".$root."#", $ret)) ? rtrim($ret, '/') . '/' : null;
}

  public function printPlayerTable($res){
    echo "<table style='width:80%', class='sortable'>";
    echo "<tr style='color:white; text-align: left;'>";
    //Print headers.
    for($i = 0; $i < mysqli_num_fields($res); $i++){
        $field = mysqli_fetch_field($res);
        if ($i != 0) {
          echo "<th>{$field->name}</th>";
        }
    }
    echo "</tr>";

    //Print table data
    while($row = mysqli_fetch_row($res))
    {
      for ($x = 1; $x < count($row); $x++)
      {
        if ($x == 1) {
          echo "<td class='item'><a color='white' href='player.php?steamid={$row[0]}'>{$row[$x]}</a></td>";
        }
        else
        {
          echo "<td class='item'>{$row[$x]}</td>";
        }
      }
      echo "</tr>";
    }

  }
  public function printTable($res){
      echo "<table style='width:70%'";
      echo "<tr style='color:white; text-align: left;'>";
      //Print headers.
      for($i = 0; $i < mysqli_num_fields($res); $i++){
          $field = mysqli_fetch_field($res);
          echo "<th style='text-align:left'>{$field->name}</th>";
      }
      echo "</tr>";

      //Print table data
      $rank = 0;
      while($row = mysqli_fetch_row($res))
      {
          ++$rank;
          echo "<tr style='color:white;text-align:left'>";
          for ($x = 0; $x < count($row); $x++)
          {
            echo "<td>{$row[$x]}</td>";
          }
          echo "</tr>";
      }

  }
}

?>
