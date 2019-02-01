<?php

namespace Phi\HTML;

class Style
{

    protected $properties = [];

    public function set($property, $value)
    {
        $this->properties[$property] = $value;
        return $this;
    }


    public function render() {

        $buffer ='';
        foreach ($this->properties as $name => $value) {
            $buffer .= $name. ': '.$value.'; ';
        }
        return $buffer;

    }

}