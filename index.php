<?php
//
// PHASE: BOOTSTRAP
//
define('LARA_INSTALL_PATH', dirname(__FILE__));
define('LARA_SITE_PATH', LARA_INSTALL_PATH . '/site');

require(LARA_INSTALL_PATH.'/src/CLara/bootstrap.php');

$lara = CLara::Instance();

//
// PHASE: FRONTCONTROLLER ROUTE
//
$lara->FrontControllerRoute();

//
// PHASE: THEME ENGINE RENDER
//
$lara->ThemeEngineRender();
?>