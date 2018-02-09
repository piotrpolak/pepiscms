<?php

echo $base_url . "\r\n";

foreach ($uris as $uri)
{
    echo $base_url . ( $defaul_language != $uri->language_code ? $uri->language_code . '/' : '') . $uri->page_uri . $url_suffix . "\r\n";
}
foreach ($static as $uri)
{
    echo $base_url . $uri . "\r\n";
}
?>