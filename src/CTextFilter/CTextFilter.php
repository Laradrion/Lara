<?php

/**
 * CTextFilter to filter text.
 *
 * @package LaraCMF
 */
class CTextFilter {

    /**
     * Properties
     */
    public static $instance = null;

    /**
     * Constructor
     *
     */
    public function __construct() {
        ;
    }

    /**
     * Call each filter.
     *
     * @param string $filters as comma separated list of filter.
     * @return string the formatted text.
     */
    public static function Filter($text, $filters) {
        if (!self::$instance) {
            self::$instance = new CTextFilter();
        }
        $all = array(
            'bbcode' => 'bbcode2html',
            'link' => 'make_clickable',
            'markdown' => 'markdown',
            'nl2br' => 'nl2br',
            'typographer' => 'smartyPantsTypographer',
            'htmlpurify' => 'htmlpurify',
            'plain' => 'plain',
            'php' => 'php',
            'html' => 'html',
        );

        if ($filters != "") {
            $filter = preg_replace('/\s/', '', explode(',', $filters));
            foreach ($filter as $val) {
                if (($val == "bbcode") ||
                        ($val == "link") ||
                        ($val == "markdown") ||
                        ($val == "nl2br") ||
                        ($val == "typographer") ||
                        ($val == "plain") ||
                        ($val == "php") ||
                        ($val == "html") ||
                        ($val == "htmlpurify"))
                    $text = self::$instance->{$all[$val]}($text);
            }
        }

        return $text;
    }

    /**
     * Purify text according to HTMLPurifier. 
     *
     * @param $text string the dirty HTML.
     * @returns string as the clean HTML.
     */
    public function htmlpurify($text) {
        require_once(__DIR__ . '/php-htmlpurifier/HTMLPurifier.standalone.php');
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $purifier = new HTMLPurifier($config);
        return $purifier->purify($text);
    }

    /**
     * Format text according to PHP SmartyPants Typographer.
     *
     * @param string $text the text that should be formatted.
     * @return string as the formatted html-text.
     */
    private function smartyPantsTypographer($text) {
        require_once(__DIR__ . '/php-smartypants-typographer/smartypants.php');
        return SmartyPants($text);
    }

    /**
     * Helper, convert newline to <BR>.
     * 
     * @param string text The text to be converted.
     * @return string the formatted text.
     */
    private function nl2br($text) {
        return nl2br($text); // Not needed, but here to keep logic in Filter method intact.
    }

    /**
     * Helper, htmlentities.
     * 
     * @param string text The text to be converted.
     * @return string the formatted text.
     */
    private function plain($text) {
        return htmlEnt($text);
    }

    /**
     * Helper, html. Does nothing.
     * 
     * @param string text The text to be converted.
     * @return string the formatted text.
     */
    private function html($text) {
        return $text;
    }

    /**
     * Helper, evaluates php.
     * 
     * @param string text The text to be converted.
     * @return string the formatted text.
     */
    private function php($text) {
        return eval('?>' . $text);
    }

    /**
     * Helper, BBCode formatting converting to HTML.
     *
     * @link http://dbwebb.se/coachen/reguljara-uttryck-i-php-ger-bbcode-formattering
     * @param string text The text to be converted.
     * @return string the formatted text.
     */
    private function bbcode2html($text) {
        $search = array(
            '/\[b\](.*?)\[\/b\]/is',
            '/\[i\](.*?)\[\/i\]/is',
            '/\[u\](.*?)\[\/u\]/is',
            '/\[img\](https?.*?)\[\/img\]/is',
            '/\[url\](https?.*?)\[\/url\]/is',
            '/\[url=(https?.*?)\](.*?)\[\/url\]/is'
        );
        $replace = array(
            '<strong>$1</strong>',
            '<em>$1</em>',
            '<u>$1</u>',
            '<img src="$1" />',
            '<a href="$1">$1</a>',
            '<a href="$1">$2</a>'
        );
        return preg_replace($search, $replace, $text);
    }

    /**
     * Make clickable links from URLs in text.
     *
     * @link http://dbwebb.se/coachen/lat-php-funktion-make-clickable-automatiskt-skapa-klickbara-lankar
     * @param string $text the text that should be formatted.
     * @return string with formatted anchors.
     */
    private function make_clickable($text) {
        return preg_replace_callback(
                '#\b(?<![href|src]=[\'"])https?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', create_function(
                        '$matches', 'return "<a href=\'{$matches[0]}\'>{$matches[0]}</a>";'
                ), $text
        );
    }

    /**
     * Format text according to Markdown syntax.
     *
     * @link http://dbwebb.se/coachen/skriv-for-webben-med-markdown-och-formattera-till-html-med-php
     * @param string $text the text that should be formatted.
     * @return string as the formatted html-text.
     */
    private function markdown($text) {
        require_once(__DIR__ . '/php-markdown/Michelf/Markdown.php');
        require_once(__DIR__ . '/php-markdown/Michelf/MarkdownExtra.php');
        return \Michelf\MarkdownExtra::defaultTransform($text);
    }

}

?>
