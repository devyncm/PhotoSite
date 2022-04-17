<?php

session_start();

include_once("debughelp.php");
require_once("connect.php");
$dbh = ConnectDB();

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <title>
    Project
  </title>
  <meta charset="utf-8" />
  <meta name="Author" content="Devyn Melendez" />
  <meta name="generator" content="vim" />
  <style>
    a { text-decoration: none; }
    a:hover { text-decoration: underline; }
  </style>
</head>

<body>

<h1> Big Title </h1>

<h2> Minor Title 1 </h2>
  
<p>
    Paragraph 1
</p>

<h2> Minor Title 2 </h2>
  
<p>
    Paragraph 2
</p>


<footer style="border-top: 1px solid blue">
 <a href="http://elvis.rowan.edu/~melend53/" 
    title="Link to my home page">
    D. Provine
 </a>

<span style="float: right;">
<a href="https://validator.w3.org/check?uri=referer"
   referrerpolicy="no-referrer-when-downgrade">HTML5</a> /
<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">
    CSS3 </a>
</span>
</footer>

</body>
</html>
