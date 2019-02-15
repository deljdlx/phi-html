<?php

namespace Phi\HTML\Element;


use Phi\HTML\Element;

class Table extends Element
{

    protected $thead;
    protected $tbody;

    public function __construct()
    {
        parent::__construct('table');

        $this->tbody = new Tbody();
        $this->append($this->tbody);
    }

    public function addRow()
    {
        $tr = new Tr();
        $this->append($tr);
        return $tr;
    }


    public function setHeaders($headers)
    {
        $this->thead = new Thead();
        $this->thead->setItems($headers);



        $this->append($this->thead);

        return $this;
    }





}