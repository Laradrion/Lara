<?php
/**
 * All requests routed through here. This is an overview of what actaully happens during
 * a request.
 *
 * @package LaraCore
 */

//
// PHASE: BOOTSTRAP
//
define('LARA_INSTALL_PATH', dirname(__FILE__));
define('LARA_SITE_PATH', LARA_INSTALL_PATH . '/site');

require(LARA_INSTALL_PATH.'/src/bootstrap.php');

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