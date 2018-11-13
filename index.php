<?php
require "assets/Parsedown.php";
require "config/application.php";
require "Jotebook.php";

$canonical = (isset($_GET['p']) && $_GET['p']!="") ? $_GET['p'] : 'home';

$JB = new Jotebook();

# ~ Start Parsedown
$JB->PARSEDOWN = new Parsedown();

# ~ Select Theme
$JB->selectTheme("default");

$JB->run("OHNP",$canonical);