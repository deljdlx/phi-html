<?php

namespace Phi\HTML\Element;


use Phi\HTML\Element;

class Textarea extends Element
{

    public function __construct()
    {
        parent::__construct('textarea', false);
    }


}