<?php

namespace Phi\HTML;

class JavascriptFile extends Element
{




    protected $src;

    protected $key;


    public function __construct($src, array $extraAttributes = null)
    {
        parent::__construct('script');
        $this->setAttribute('src', $src);
        $this->setAttribute('type', "text/javascript");

        if(is_array($extraAttributes)) {
            foreach ($extraAttributes as $key => $value) {
                $this->setAttribute($key, $value);
            }
        }
    }


    public function getSource()
    {
        return (string) $this->getAttribute('src');
    }

}
