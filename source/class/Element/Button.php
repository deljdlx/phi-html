<?php

namespace Phi\HTML\Element;


use Phi\HTML\Element;

class Button extends Input
{

    public function __construct()
    {
        Element::__construct('button');
        $this->setAttribute('type', 'button');
    }


    public function setLabel($label)
    {
        $this->html($label);
        return $this;
    }





}