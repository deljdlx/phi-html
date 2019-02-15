<?php

namespace Phi\HTML\Element;


use Phi\HTML\Element;

class Textarea extends Element
{

    public function __construct()
    {
        parent::__construct('textarea', false);
    }


    public function setValue($value)
    {
        $this->html(htmlentities($value));
        return $this;
    }

    public function setName($name)
    {
        $this->setAttribute('name', htmlspecialchars($name));
        return $this;
    }

}