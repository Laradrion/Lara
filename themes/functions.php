<?php

/**
 * Helpers for theming, available for all themes in their template files and functions.php.
 * This file is included right before the themes own functions.php
 */

/**
 * Print debuginformation from the framework.
 */
function get_debug() {
    // Only if debug is wanted.
    $lara = CLara::Instance();
    if (empty($lara->config['debug'])) {
        return;
    }

    // Get the debug output
    $html = null;
    if (isset($lara->config['debug']['db-num-queries']) && $lara->config['debug']['db-num-queries'] && isset($lara->db)) {
        $flash = $lara->session->GetFlash('database_numQueries');
        $flash = $flash ? "$flash + " : null;
        $html .= "<p>Database made $flash" . $lara->db->GetNumQueries() . " queries.</p>";
    }
    if (isset($lara->config['debug']['db-queries']) && $lara->config['debug']['db-queries'] && isset($lara->db)) {
        $flash = $lara->session->GetFlash('database_queries');
        $queries = $lara->db->GetQueries();
        if ($flash) {
            $queries = array_merge($flash, $queries);
        }
        $html .= "<p>Database made the following queries.</p><pre>" . implode('<br/><br/>', $queries) . "</pre>";
    }
    if (isset($lara->config['debug']['timer']) && $lara->config['debug']['timer']) {
        $html .= "<p>Page was loaded in " . round(microtime(true) - $lara->timer['first'], 5) * 1000 . " msecs.</p>";
    }
    if (isset($lara->config['debug']['lara']) && $lara->config['debug']['lara']) {
        $html .= "<hr><h3>Debuginformation</h3><p>The content of CLara:</p><pre>" . htmlent(print_r($lara, true)) . "</pre>";
    }
    if (isset($lara->config['debug']['session']) && $lara->config['debug']['session']) {
        $html .= "<hr><h3>SESSION</h3><p>The content of CLara->session:</p><pre>" . htmlent(print_r($lara->session, true)) . "</pre>";
        $html .= "<p>The content of \$_SESSION:</p><pre>" . htmlent(print_r($_SESSION, true)) . "</pre>";
    }
    return $html;
}

/**
 * Get messages stored in flash-session.
 */
function get_messages_from_session() {
    $messages = CLara::Instance()->session->GetMessages();
    $html = null;
    if (!empty($messages)) {
        foreach ($messages as $val) {
            $valid = array('info', 'notice', 'success', 'warning', 'error', 'alert');
            $class = (in_array($val['type'], $valid)) ? $val['type'] : 'info';
            $html .= "<div class='$class'>{$val['message']}</div>\n";
        }
    }
    return $html;
}

/**
 * Login menu. Creates a menu which reflects if user is logged in or not.
 */
function login_menu() {
    $nav = "";
    $lara = CLara::Instance();
    if ($lara->user->IsAuthenticated()) {
        $nav = "<nav id='login-menu' class='right'><img src='" . get_gravatar(30) . "' alt=''>";
        $items = "<a href='" . create_url('user/profile') . "'>" . $lara->user->GetAcronym() . "</a> ";
        if ($lara->user->IsAdministrator()) {
            $items .= "<a href='" . create_url('acp') . "'>acp</a> ";
        }
        $items .= "<a href='" . create_url('user/logout') . "'>logout</a> ";
    } else {
        $nav = "<nav id='login-menu' class='right'>";
        $items = "<a href='" . create_url('user/login') . "'>login</a> ";
    }
    return "$nav$items</nav>";
}

/**
 * Create a url by prepending the base_url.
 */
function base_url($url = "") {
    return CLara::Instance()->request->base_url . trim($url, '/');
}

/**
 * Return the current url.
 */
function current_url() {
    return CLara::Instance()->request->current_url;
}

/**
 * Create a url to an internal resource.
 */
function create_url($url = null, $method = null, $args = null) {
    return CLara::Instance()->request->CreateUrl($url, $method, $args);
}

/**
 * Prepend the theme_url, which is the url to the current theme directory.
 */
function theme_url($url) {
    return create_url(CLara::Instance()->themeUrl . "/{$url}");
}

/**
 * Prepend the theme_parent_url, which is the url to the current parent theme directory.
 */
function theme_parent_url($url) {
    return create_url(CLara::Instance()->themeParentUrl . "/{$url}");
}

/**
 * Prepend a dynamic theme_template_url, which is the url to main or parent template directory if a file was found.
 */
function theme_template_url($url) {
    $file = CLara::Instance()->themePath . "/{$url}";
    $return = "";
    if (is_file($file)) {
        $return = theme_url($url);
    } else {
        $file = CLara::Instance()->themeParentPath . "/{$url}";
        if (is_file($file)) {
            $return = theme_parent_url($url);
        }
    }
    return $return;
}

/**
 * Render all views.
 *
 * @param $region string the region to draw the content in.
 */
function render_views($region = 'default') {
    return CLara::Instance()->views->Render($region);
}

/**
 * Get a gravatar based on the user's email.
 */
function get_gravatar($size = null) {
    return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim(CLara::Instance()->user['email']))) . '.jpg?' . ($size ? "s=$size" : null);
}

/**
 * Escape data to make it safe to write in the browser.
 */
function esc($str) {
    return htmlEnt($str);
}

/**
 * Filter data according to a filter. Uses CTextFilter::Filter()
 *
 * @param $data string the data-string to filter.
 * @param $filter string the filter to use.
 * @returns string the filtered string.
 */
function filter_data($data, $filter) {
    return CTextFilter::Filter($data, $filter);
}

/**
 * Check if region has views. Accepts variable amount of arguments as regions.
 *
 * @param $region string the region to draw the content in.
 */
function region_has_content($region = 'default' /* ... */) {
    return CLara::Instance()->views->RegionHasView(func_get_args());
}

/**
 * Get list of tools.
 */
function get_tools() {
    global $lara;
    return <<<EOD
<p>Tools: 
<a href="http://validator.w3.org/check/referer">html5</a>
<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">css3</a>
<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css21">css21</a>
<a href="http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance">unicorn</a>
<a href="http://validator.w3.org/checklink?uri={$lara->request->current_url}">links</a>
<a href="http://qa-dev.w3.org/i18n-checker/index?async=false&amp;docAddr={$lara->request->current_url}">i18n</a>
<!-- <a href="link?">http-header</a> -->
<a href="http://csslint.net/">css-lint</a>
<a href="http://jslint.com/">js-lint</a>
<a href="http://jsperf.com/">js-perf</a>
<a href="http://www.workwithcolor.com/hsl-color-schemer-01.htm">colors</a>
<a href="http://dbwebb.se/style">style</a>
</p>

<p>Docs:
<a href="http://www.w3.org/2009/cheatsheet">cheatsheet</a>
<a href="http://dev.w3.org/html5/spec/spec.html">html5</a>
<a href="http://www.w3.org/TR/CSS2">css2</a>
<a href="http://www.w3.org/Style/CSS/current-work#CSS3">css3</a>
<a href="http://php.net/manual/en/index.php">php</a>
<a href="http://www.sqlite.org/lang.html">sqlite</a>
<a href="http://www.blueprintcss.org/">blueprint</a>
</p>
EOD;
}

?>