<?php

namespace Phi\HTML\Element;


use Phi\HTML\Element;

class Br extends Element
{

    public function __construct()
    {
        parent::__construct('br', true);
    }


}