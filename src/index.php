<?php
define('BASE_DIR', __DIR__.'/../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();
?>
<body id="index">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">

<p>
Welcome to TF2A. The home of local South African pugs.
We host 3 servers
 <ul>
  <li>Coffee</li><br>
  <li>Tea</li><br>
  <li>Milk</li><br>
</ul>
</p>

</div>
<footer id="footer">tf2sa</footer>
</div>
</body>
</html>
