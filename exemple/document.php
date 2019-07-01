<?php


require(__DIR__.'/_autoload.php');


$document = new \Phi\HTML\Document();


$document->dom->html('<body style="font-family: sans-serif; background-color: #CCC">hello world</body>');


echo $document->render();