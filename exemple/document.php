<?php


require(__DIR__.'/_autoload.php');


$document = new \Phi\HTML\Document();


$document->dom->body->html('<h2>hello world document</h2>', true);


echo $document->render();