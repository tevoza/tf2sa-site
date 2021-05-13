<?php
class dataAccess
{
    public $dbCon;
    function __construct(){
        $this->dbCon = mysqli_connect("127.0.0.1", "tf2sa", "tf2saAdmin", "tf2saDB") or die(mysqli_error());
    }

    public function getDbCon(){
        return $this->dbCon;
    }

    function __destruct(){
        mysqli_close($this->dbCon);
    }

    public function printTable($res){
        echo "<table style='width:50%'>";
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
