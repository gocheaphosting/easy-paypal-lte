<?php
$pages = array('introduction', 'setup', 'admin', 'shop', 'pro', 'modules') ;
$chap = 1;
foreach ($pages as $page) {
  toc($page, $chap) ;
  $chap++ ;
}

function toc($page, $chap) {
  $remote_site_data = file_get_contents("http://buy.thulasidas.com/docs/index.php?$page");
  $dom_document = new DOMDocument();
  @$dom_document->loadHTML($remote_site_data);
  $h1 = $dom_document->getElementsByTagName('h1');
  $h2 = $dom_document->getElementsByTagName('h2');
  $h3 = $dom_document->getElementsByTagName('h3');
  foreach ($h1 as $header) {
    echo "<h3 style='font-size:1em;margin:1.2em 0 -0.5em 0'>Chapter {$chap}</h3>" ;
    echo "<h1 style='font-size:1.1em'>" . trim($header->nodeValue) . "</h1>" ;
  }
  foreach ($h2 as $header) {
    echo "<h2 style='font-size:1.0em; margin:0 1em 0 0;'>" . trim($header->nodeValue) . "</h2>";
  }
}
