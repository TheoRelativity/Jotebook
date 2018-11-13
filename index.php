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

# ~ Select Jotebook to read
$JB->run("jotebook_example",$canonical);
