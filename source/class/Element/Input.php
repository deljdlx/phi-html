<?php

namespace Phi\HTML\Element;


use Phi\HTML\Element;

class Input extends Element
{

    public function __construct()
    {
        parent::__construct('input', true);
    }


    public function setValue($value)
    {
        $this->setAttribute('value', htmlspecialchars($value));
        return $this;
    }

    public function setName($name)
    {
        $this->setAttribute('name', htmlspecialchars($name));
        return $this;
    }


}