<?php

namespace Phi\HTML\Element;


use Phi\HTML\Element;

class Form extends Element
{


    public function __construct()
    {
        parent::__construct('Form', false);
    }


    public function setMethod($method)
    {
        $this->setAttribute('method', $method);
        return $this;
    }

    public function setAction($action)
    {
        $this->setAttribute('action', $action);
        return $this;
    }




}