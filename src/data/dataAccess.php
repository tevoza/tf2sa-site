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
        mysqli_close($this->dbCon);
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

    public function printTable($res){
        echo "<table style='width:70%'>";
        echo "<tr style='color:white; text-align: left;'>";
        echo "<th>Rank</th>";
        //Print headers.
        for($i = 0; $i < mysqli_num_fields($res); $i++){
            $field = mysqli_fetch_field($res);
            echo "<th>{$field->name}</th>";
        }
        echo "</tr>";

        //Print table data
        $rank = 0;
        while($row = mysqli_fetch_row($res))
        {
            ++$rank;
            echo "<tr style='color:white;text-align:left'>";
            echo "<td>{$rank}</td>";
            foreach($row as $_column) {
                echo "<td>{$_column}</td>";
            }
            echo "</tr>";
        }

    }
}

?>
