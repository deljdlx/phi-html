<?php

namespace Phi\HTML;

class CustomTag extends Element
{


    protected $parameters = [];

    public function setParameter($parameterName, $elements)
    {
        $this->parameters[$parameterName] = $elements;
    }


    public function renderParameter($parameterName)
    {

        $buffer = '';
        if(isset($this->parameters[$parameterName])) {
            $parameter = $this->parameters[$parameterName];

            if($parameter instanceof  \SimpleXMLElement) {
                $elements = $this->createFromSimpleXML($parameterName, $parameter)->getChildren();
                foreach ($elements as $element) {
                    $buffer .= $element->render();
                }
            }
            else if(is_string($parameter)) {
                $buffer .= $parameter;
            }
        }


        return $buffer;




    }


}