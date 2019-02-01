<?php

namespace Phi\HTML;

class CSSFile extends Element
{




    protected $href;

    protected $key;


    public function __construct($href)
    {
        parent::__construct('link', true);
        $this->setAttribute('href', $href);
        $this->setAttribute('rel', "stylesheet");
    }

    public function getSource()
    {
        return (string) $this->getAttribute('href');
    }


    public function getKey()
    {
        if($this->key === null) {
            return (string) $this->getAttribute('href');
        }
        else {
            return $this->key;
        }
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

}
