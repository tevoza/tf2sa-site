<?php
define('BASE_DIR', __DIR__.'/../../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
include_once(BASE_DIR.'src/data/dataAccess.php');    
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();

session_start();
session_destroy();
?>
<body id="logoff">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<div id="auth">
<p>

<h1 style="color:#52FFB8;text-align:left"><b>logoff</b></h1>

logged out

</p>
</div>
</div>
<footer id="footer">tf2sa</footer>
</div>
</body>
</html>
