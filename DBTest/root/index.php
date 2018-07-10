<?php
/**
 * INDEX of the root directory will process the information as needed.
 */

// INCLUDE THE MAIN required.php FILE
require_once ('../config/required.php');
require_once ("../config/router.php");
require "../vendor/autoload.php";
session_start();

$_SESSION['start'] = microtime(true);

use Router\Router;

$router = new Router($routes);