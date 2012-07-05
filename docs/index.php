<?php
if (empty($_GET)) {
  $pages = array('introduction', 'setup', 'admin', 'shop', 'pro', 'modules') ;
  showPage($pages, $toc=true) ;
}
else {
  $pages = array_keys($_GET) ;
  showPage($pages) ;
}

function showTOC() {
  include("toc.php") ;
}

function showPage($pages = array(), $toc=false) {
  $title = mkTitle($pages) ;
  ezppHeader($title) ;
  if ($toc) {
    $pdfOnly = true ;
    if ($pdfOnly) echo "<div class='toc'>" ;
    showTOC() ;
    if ($pdfOnly) echo "</div>" ;
  }
  $chap = 0 ;
  foreach ($pages as $page) {
    if (!file_exists("$page.html")) $page = "introduction" ;
    $GLOBALS['page'] = $page ;
    echo "<div class='page-break'></div>" ;
    $chap++ ;
    if (count($pages)>1) echo "<h3 style='margin:5em 0 0.4em 0'>Chapter {$chap}</h3><hr />" ;
    include("$page.html") ;
  }
  ezppFooter() ;
}

function mkTitle($pages) {
  $title = array("ezPayPal Help") ;
  $title[] = "<a href='index.php'>Show All</a> | <a href='ezpp.pdf'>PDF</a>" ;
  return $title ;
}

function ezppHeader($title, $showLinks=false) {
  $pwd = '..' ;
  if ($showLinks) $linkText = "<a id='logo' href='http://buy.thulasidas.com/ezpaypal' title='ezPayPal'><img src='$pwd/ezPayPal.png' width='188' height='72' alt='Ez-PayPal'></a>" ;
  else $linkText = "<img src='$pwd/ezPayPal.png' width='188' height='72' alt='Ez-PayPal'>" ;

  printf("<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'
'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=UTF-8'>
<title>%s</title>
<link rel='stylesheet' href='$pwd/ezpp.css' media='screen'>
<link rel='stylesheet' href='$pwd/ezpp.css' media='print'>
<link rel='stylesheet' href='$pwd/editableSelect.css' media='screen'>
<script type='text/javascript' src='$pwd/editableSelect.js'></script>
</head>
<body style='font-size:11pt'>
<script type='text/javascript' src='$pwd/wz_tooltip.js'></script>
<div id='ezcontainer' style='width:800px'>
  <div id='ezheader'>
    $linkText
    <p id='info'>%s</p>
  </div>
  <div id='nav'>
    <ul id='sub_nav'>
      <li>%s</li>
    </ul>
  </div>
  <div class='clear'></div>
     <div id='ezcontent'>
       <div>", $title[0], $title[1], $title[0]) ;
  echo '    </div>
    <div style="padding:0 3px 5px 3px;">
    <div style="padding:3px; padding-top:1px; padding-bottom:5px;">
<!-- End of ezppHeader() -->
';
}
function ezppFooter($showLinks=false) {
  $year = date('Y') ;
  if ($showLinks) $linkText = sprintf('<a href="http://affiliates.thulasidas.com/"><font color="#f37">ez</font><em><font color="#25d">Affiliates</font></em>: Join Us</a> and Earn 50%% Revenue share!<br /><small>Powered by <a href="http://buy.thulasidas.com/ezpaypal"><font color="#f37">ez</font><em><font color="#25d">Pay</font><font color="#1ad">Pal</font></em></a>. Copyright &copy;%s&nbsp;Manoj Thulasidas. &nbsp;All Rights Reserved.</small>', $year) ;
  else $linkText = '<small><font color="#f37">ez</font><em><font color="#25d">Pay</font><font color="#1ad">Pal</font></em> Help System.</small>' ;
  printf("<!-- Start of ezppFooter() -->
    </div>
  </div>
</div>
  <div id='ezfooter'>$linkText</div>
</div>
</body>
</html>
") ;
  exit() ;
}
function page($page, $pdfOnly=true) {
  $GLOBALS['page'] = $page ;
  if ($pdfOnly) echo "<div class='page-break'>" ;
  include("$page.html") ;
  if ($pdfOnly) echo "</div>" ;
}
function img($img) {
  $page = $GLOBALS['page'] ;
  echo "<p><center><img src='img/$page-$img.png' /></center></p>" ;
}
function ezpp() {
  echo '<font color="#f37">ez</font><em><font color="#25d">Pay</font><font color="#1ad">Pal</font></em>' ;
}