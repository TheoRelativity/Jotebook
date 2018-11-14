<?php
require "assets/Parsedown.php";
require "config/application.php";
require "Jotebook.php";

$canonical = (isset($_GET['p']) && $_GET['p']!="") ? $_GET['p'] : 'home';

define("SELECTED_JOTEBOOK","OHNP");
define("SELECTED_THEME","default");
define("CURRENT_CANONICAL",$canonical);

$JB = new Jotebook(SELECTED_JOTEBOOK,CURRENT_CANONICAL);

# ~ Start Parsedown
$JB->PARSEDOWN = new Parsedown();

# ~ Select Theme
$JB->selectTheme(SELECTED_THEME);

$JB->run();