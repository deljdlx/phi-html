<?php

namespace Phi\HTML;

use Phi\Core\Exception;

class ElementFactory
{

    private $elementsMapping = [];

    private static $instance;


    /**
     * @return static
     */
    public static function getInstance()
    {
        if(static::$instance === null) {
            static::$instance  = new static;
        }
        return static::$instance;
    }


    protected function __construct()
    {

    }

    public function registerElement($elementName, $className)
    {
        if(!class_exists($className)) {
            throw new Exception('Can not register HTML element "'.$elementName.'". Class "'.$className.'" does not exists');
        }
        $this->elementsMapping[$elementName] = $className;
        return $this;
    }



    public function getElement($elementName)
    {
        if(array_key_exists($elementName, $this->elementsMapping)) {
            return new $this->elementsMapping[$elementName];
        }
        else {

            $className = '\Phi\HTML\Element\\'.ucfirst($elementName);

            if(class_exists($className)) {
                return new $className($elementName);
            }
            else {
                return new Element($elementName);
            }
        }
    }





}
