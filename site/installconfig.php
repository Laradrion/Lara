<?php

/**
 * Site configuration, this file is changed by user per site.
 *
 */
/*
 * Set level of error reporting
 */
error_reporting(-1);
ini_set('display_errors', 1);

/**
 * Set database(s).
 */
$lara->config['database'][0]['dsn'] = 'sqlite:' . LARA_SITE_PATH . '/data/.ht.sqlite';

/*
 * Define session name
 */
$lara->config['session_name'] = preg_replace('/[:\.\/-_]/', '', $_SERVER["SERVER_NAME"]);
$lara->config['session_key'] = 'lara';

/*
 * Define server timezone
 */
$lara->config['timezone'] = 'Europe/Stockholm';

/*
 * Define internal character encoding
 */
$lara->config['character_encoding'] = 'UTF-8';

/*
 * Define language
 */
$lara->config['language'] = 'en';

/**
 * Define the controllers, their classname and enable/disable them.
 *
 * The array-key is matched against the url, for example: 
 * the url 'developer/dump' would instantiate the controller with the key "developer", that is 
 * CCDeveloper and call the method "dump" in that class. This process is managed in:
 * $lara->FrontControllerRoute();
 * which is called in the frontcontroller phase from index.php.
 */
$lara->config['controllers'] = array(
    'index' => array('enabled' => true, 'class' => 'CCIndex'),
    'developer' => array('enabled' => true, 'class' => 'CCDeveloper'),
    'guestbook' => array('enabled' => true, 'class' => 'CCGuestbook'),
    'formtest' => array('enabled' => true, 'class' => 'CCFormTest'),
    'user' => array('enabled' => true, 'class' => 'CCUser'),
    'acp' => array('enabled' => true, 'class' => 'CCAdminControlPanel'),
    'content' => array('enabled' => true, 'class' => 'CCContent'),
    'page' => array('enabled' => true, 'class' => 'CCPage'),
    'blog' => array('enabled' => true, 'class' => 'CCBlog'),
    'theme' => array('enabled' => true, 'class' => 'CCTheme'),
    'modules' => array('enabled' => true, 'class' => 'CCModules'),
    'my' => array('enabled' => true, 'class' => 'CCMyController'),
);

/**
 * Settings for the theme.
 */
$lara->config['theme'] = array(
    'name' => 'grid',
    'path' => 'themes/grid', // Path to the theme
    'parent' => null, // Path to the parenttheme
    'stylesheet' => 'style.css', // Main stylesheet to include in template files
    'template_file' => 'index.tpl.php', // Default template file, else use default.tpl.php
    // A list of valid theme regions
    'regions' => array('navbar', 'flash', 'featured-first', 'featured-middle', 'featured-last',
        'primary', 'sidebar', 'triptych-first', 'triptych-middle', 'triptych-last',
        'footer-column-one', 'footer-column-two', 'footer-column-three', 'footer-column-four',
        'footer',
    ),
    'menu_to_region' => array('navbar' => 'navbar'),
    // Add static entries for use in the template file. 
    'data' => array(
        'header' => 'Lara',
        'slogan' => 'A PHP-based MVC-inspired CMF',
        'favicon' => 'logo_80x80.png',
        'logo' => 'logo_80x80.png',
        'logo_width' => 80,
        'logo_height' => 80,
        'footer' => '<p>Lara &copy; by Jonas Lindström (jonas@laradrion.com)</p>',
    ),
);

/**
 * Set a base_url to use another than the default calculated
 */
$lara->config['base_url'] = null;

/**
 * What type of urls should be used?
 * 
 * default      = 0      => index.php/controller/method/arg1/arg2/arg3
 * clean        = 1      => controller/method/arg1/arg2/arg3
 * querystring  = 2      => index.php?q=controller/method/arg1/arg2/arg3
 */
$lara->config['url_type'] = 1;

/**
 * Should debug info be displayed?
 * 
 */
$lara->config['debug']['lara'] = false;
$lara->config['debug']['session'] = false;
$lara->config['debug']['timer'] = true;
$lara->config['debug']['db-num-queries'] = true;
$lara->config['debug']['db-queries'] = true;

/**
 * How to hash password of new users, choose from: plain, md5salt, md5, sha1salt, sha1.
 */
$lara->config['hashing_algorithm'] = 'sha1salt';

/**
 * Allow or disallow creation of new user accounts.
 */
$lara->config['create_new_users'] = true;

/**
 * Define a routing table for urls.
 *
 * Route custom urls to a defined controller/method/arguments
 */
$lara->config['routing'] = array(
    'home' => array('enabled' => true, 'url' => 'index/index'),
);

/**
 * Define menus.
 *
 * Create hardcoded menus and map them to a theme region through $lara->config['theme'].
 */
$lara->config['menus'] = array(
    'navbar' => array(
        'home' => array('type'=>'child', 'url' => '', 'label' => 'Home'),
        'modules' => array('type'=>'child', 'url' => 'modules', 'label' => 'Modules'),
        'content' => array('type'=>'child', 'url' => 'content', 'label' => 'Content'),
        'guestbook' => array('type'=>'child', 'url' => 'guestbook', 'label' => 'Guestbook'),
        'blog' => array('type'=>'child', 'url' => 'blog', 'label' => 'Blog'),
        'callback' => 'CCoreMenu',
    ),
);
?>