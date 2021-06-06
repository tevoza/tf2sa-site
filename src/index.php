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

<div id="info">
Welcome to TF2A. The home of local South African pugs.
We host 3 servers: <br>
<ol>
    <li> TF2SA Pug </t> 129.232.150.23:27016  </li> <br>
    <li> TF2SA MGE  129.232.150.23:27016  </li> <br>
    <li> TF2SA Jump (All Maps) | tempus.tf  129.232.150.23:27016 </li> <br>
</ol>
</div>

<div id="report">
<pre>
<?php 
?>
</pre>
</div>

</div>
<footer id="footer">tf2sa</footer>
</div>
</body>
</html>
