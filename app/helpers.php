<?php
if (!function_exists('urlify')) {
    function urlify($text)
    {
        $reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        if (preg_match($reg_exUrl, $text, $url))
            return preg_replace($reg_exUrl, "<a href=\"{$url[0]}\">{$url[0]}</a> ", $text);
        else
            return $text;
    }
}
