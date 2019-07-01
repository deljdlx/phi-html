<?php


require(__DIR__.'/_autoload.php');


$page = new \Phi\HTML\Page();


$page->dom->body->html('<h2>hello world</h2>', true);


echo $page->render();