<?php
define('BASE_DIR', __DIR__.'/../../');//define as appropriate
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();
?>
<body id="index">
<div id="page-container">
<?php $template->printHeader(); ?>
<div id="content-wrap">
<p>

Here's a webpage

</p>
</div>
<footer id="footer">tf2sa</footer>
</div>
</body>
</html>
