<?php

namespace Phi\HTML;

class JavascriptFile extends Element
{




    protected $src;

    protected $key;


    public function __construct($src)
    {
        parent::__construct('script');
        $this->setAttribute('src', $src);
        $this->setAttribute('type', "text/javascript");
    }


    public function getSource()
    {
        return (string) $this->getAttribute('src');
    }

}
