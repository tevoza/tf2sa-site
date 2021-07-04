<?php
define('BASE_DIR', __DIR__.'/../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
include_once(BASE_DIR.'src/data/dataAccess.php');    
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();
?>
<script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
<body id="stats">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<div id="content">
<pre>
<?php
$cutoff = time() - (60 * 60 * 24 * 365 * $_ENV['RECENT_THRESH_YEARS']);
$data = new dataAccess();
$db = $data->getDbCon();
$thresh = time() - 14*24*60*60;
$q="
    SELECT Option, COALESCE(COUNT(v.UserID)) Votes, o.PollID, o.PollOptionID
    FROM PollOptions o 
    LEFT JOIN PollVotes v 
    ON v.PollOptionID=o.PollOptionID 
    WHERE o.PollID = ".$PollID." 
    GROUP BY o.PollOptionID
";



echo $q;
$res = mysqli_query($db, $q);
$data->printTable($res); 
mysqli_close($db);
?>
</pre>
</div>

</div>
</div>
</body>
</html>
