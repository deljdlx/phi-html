<?php

namespace Phi\HTML\Element;


use Phi\HTML\Element;

class Input extends Element
{

    public function __construct()
    {
        parent::__construct('input', true);
    }


}