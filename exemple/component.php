<?php


require(__DIR__.'/_autoload.php');


$component = new \Phi\HTML\Component('h1');


$component->dom->html('title component');


echo $component->render();