<?php
/**
* Helpers for the template file.
*/
$lara->data['header'] = '<h1>Header: Lara</h1>';
$lara->data['main']   = '<p>Main: Now with a theme engine, Not much more to report for now.</p>';
$lara->data['footer'] = '<p>Footer: &copy; Lara by Jonas Lindström (jonas@laradrion.com)</p>';


/**
* Print debuginformation from the framework.
*/
function get_debug() {
  $lara = CLara::Instance();
  $html = "<h2>Debuginformation</h2><hr><p>The content of the config array:</p><pre>" . htmlentities(print_r($lara->config, true)) . "</pre>";
  $html .= "<hr><p>The content of the data array:</p><pre>" . htmlentities(print_r($lara->data, true)) . "</pre>";
  $html .= "<hr><p>The content of the request array:</p><pre>" . htmlentities(print_r($lara->request, true)) . "</pre>";
  return $html;
}