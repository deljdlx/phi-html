<?php

namespace Phi\HTML\Element;


use Phi\HTML\Element;

class Thead extends Element
{

    protected $items;

    public function __construct()
    {
        parent::__construct('thead');
    }


    public function setItems(array $items)
    {
        $this->items = $items;

        $tr = new Tr();

        foreach ($items as $value) {
            $th = new Th();
            $th->html($value);

            $tr->append($th);
        }

        $this->append($tr);

        return $this;
    }





}