<?php
require "assets/Parsedown.php";
require "config/application.php";
require "Jotebook.php";

$canonical = (isset($_GET['p']) && $_GET['p']!="") ? $_GET['p'] : 'home';

$JB = new Jotebook($canonical);

# ~ Start Parsedown
$JB->PARSEDOWN = new Parsedown();

# ~ Select Jotebook
$JB->selectJotebook("Jotebook_name");
$JB->run();