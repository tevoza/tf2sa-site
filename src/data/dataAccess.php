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

  public function printPollOptions($PollID)
  {
    $q="SELECT Topic FROM Polls WHERE PollID = ".$PollID; 
    $res = mysqli_query($this->dbCon, $q);
    $topic = mysqli_fetch_row($res)[0];
    $q="
    SELECT Option, COALESCE(COUNT(v.UserID)) Votes, o.PollID, o.PollOptionID
    FROM PollOptions o 
    LEFT JOIN PollVotes v 
    ON v.PollOptionID=o.PollOptionID 
    WHERE o.PollID = ".$PollID." 
    GROUP BY o.PollOptionID"; 
    $res = mysqli_query($this->dbCon, $q);

    echo "
      <div id=comment-poll>
      <h2> Poll: {$topic} </h2> 
    ";
    while($row = mysqli_fetch_row($res))
    {
      echo "{$row[0]} <i>({$row[1]} votes)</i> <td class='item'><a color='white' href='vote.php?pollid={$row[2]}&optionid={$row[3]}'>Vote!</a></td> <br>";
    }
    echo '</div>';
  }

  public function printPlayerTable($res){
    echo "<table style='width:80%', class='sortable'>";
    echo "<tr style='color:white; text-align: left;'>";
    //Print headers.
    for($i = 0; $i < mysqli_num_fields($res); $i++)
    {
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

  public function printPost($res){
    echo"<table style='width:100%'; border='1'>";
    echo"<tr style='color:white'; text-align: left; border='1'>";
    echo "<th style = 'background-color: #B40E1F'; 'color: #F0F0F0';>Posts</th>";
    echo"<th style = 'background-color: #B40E1F'; 'color: #F0F0F0';>Autor</th>";
    echo "<th style = 'background-color: #B40E1F'; 'color: #F0F0F0';>Date</th>";
    echo"</tr>";
     
    while($row = mysqli_fetch_assoc($res))
    {
      echo "<tr style='color:white;text-align:left'>";
      echo"<td class='topic'>";
      echo '<h3><a href="topic.php?id=' . $row['ThreadID'] . '">' . $row['Topic'] . '</a><h3>';
      echo"</td>";
      echo"<td class='author'>";
      echo $row['UserName'];
      echo"</td>";
      echo"<td class='date'>";
      echo date("Y-m-d", $row['Date']);
      echo"</td>";
      echo "</tr>";
    }
    echo"</table>";
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
