<?php
require "assets/Parsedown.php";
require "config/application.php";
require "Jotebook.php";

$canonical = (isset($_GET['p']) && $_GET['p']!="") ? $_GET['p'] : 'home';

$JB = new Jotebook($canonical);
$JB->PARSEDOWN = new Parsedown();
$JB->run();