<?php

namespace Phi\HTML\Element;


use Phi\HTML\Element;

class Select extends Element
{

    /**
     * @var Option[]
     */
    private $options = array();


    public function __construct()
    {
        parent::__construct('select', false);
    }


    public function addOption(Option $option)
    {
        $this->options[] = $option;
        return $this;
    }


    public function render()
    {
        $this->children = $this->options;

        return parent::render();
    }


}