<?php
define('BASE_DIR', __DIR__.'/../../');
include_once(BASE_DIR.'src/templates/template.php');    
include_once(BASE_DIR.'vendor/autoload.php'); 
$dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
$dotenv->load();
$template = new Template();
$template->printHead();
?>
<body id="signup">
<div id="page-container">
<?php $template->printHeader(BASE_DIR); ?>
<div id="content-wrap">
<p>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<input type="text" name="Name">
<input type="submit" value="submit">
</form>



</p>
</div>
<footer id="footer">tf2sa</footer>
</div>
</body>
</html>
