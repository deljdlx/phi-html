<?php

namespace Phi\HTML;

use Phi\Core\Exception;

class Collection implements \ArrayAccess, \JsonSerializable, \Countable
{

    public $id;

    protected $name;

    /**
     * @var Element[]
     */
    protected $elements = [];


    /**
     * @var Element[]
     */
    protected $childrenByTag = [];
    protected $attributes = [];


    /**
     * @var Document
     */
    protected $document;


    public function __construct($name, $element = null)
    {
        $this->id = uniqid();
        $this->name = $name;
        if($element) {
            $this->addElement($element);
        }
    }

    public function setDocument(Document $document = null)
    {
        $this->document = $document;
        return $this;
    }



    public function jsonSerialize() {

        return array(
            'class' => get_class($this),
            'name' => $this->name,
            'elements' => $this->elements,
        );


    }


    public function length()
    {
      return count($this->elements);
    }

    public function count()
    {
        return $this->length();
    }

    public function __get($propertyName)
    {


        if(!isset($this->childrenByTag[$propertyName])) {

            $this->childrenByTag[$propertyName] = new Collection($propertyName);
            $this->childrenByTag[$propertyName]->setDocument($this->document);

            foreach ($this->elements as $element) {
                if(!isset($element->$propertyName)) {
                    $element->__get($propertyName);
                }
            }



            foreach ($this->elements as $element) {
                $collection = $element->getCollection($propertyName);
                $this->childrenByTag[$propertyName]->mergeCollection($collection);
            }
        }


        return $this->childrenByTag[$propertyName];
    }




    public function __call($methodName, $parameters)
    {

        $returnValues = [];

        foreach ($this->elements as $element) {
            if(method_exists($element, $methodName)) {
                $returnValue = call_user_func_array(array(
                    $element, $methodName
                ), $parameters);

                if($returnValue) {
                    $returnValues[] = $returnValue;
                }
            }
        }
        if(empty($returnValues)) {
            return $this;
        }
        else {
            if(count($returnValues) === 1) {
                return reset($returnValues);
            }
            else {
                return $returnValues;
            }

        }

    }


    public function mergeCollection($collection)
    {
        foreach ($collection->elements as $element) {
            $element->registerToCollection($this);
            $this->addElement($element);
        }
        return $this;
    }


    public function injectAttributesIntoElement(Element $element)
    {


        foreach ($this->attributes as $name => $value) {

            //if(!$element->getAttribute($name)->getValue()) {
                $element->setAttribute($name, $value);
            //}
        }
        return $this;
    }


    public function find($query)
    {
        $resultCollection = new Collection($query);
        $resultCollection->setDocument($this->document);

        foreach ($this->elements as $element) {
            $collection = $element->find($query);
            $resultCollection->mergeCollection($collection);
        }

        return $resultCollection;

    }



    public function addChild($element)
    {
        $this->childrendByTag[$element->getName()][] = $element;
        return $this;
    }


    public function __set($propertyName, $value)
    {
        if($propertyName == 'innerHTML') {
            foreach ($this->elements as $element) {
                $element->html($value);
            }
        }
        else {
            throw new Exception('fix this (tryed to set another property than innerHTML)');
        }
    }

    public function html($content, $parse = false)
    {

        foreach ($this->elements as $element) {
            $element->html($content, $parse);
        }

        return $this;
    }

    public function setAttribute($attributeName, $value)
    {
        foreach ($this->elements as $element) {
            $element->setAttribute($attributeName, $value);
        }
        return $this;
    }





    public function getElements()
    {
        return $this->elements;
    }

    public function removeElementByKey($key)
    {
        unset($this->elements[$key]);
        return $this;
    }





    public function addElement(Element $element)
    {
        $element->registerToCollection($this);
        $this->elements[] = $element;
        return $this;
    }



    public function render()
    {
        $buffer = '';

        foreach ($this->elements as $element) {
            foreach ($this->attributes as $attributeName => $attributeValue) {
                $element->setAttribute($attributeName, $attributeValue);
            }

            $buffer .= $element->render();
        }
        return $buffer;

    }










    public function offsetExists ($offset)
    {
        return isset($this->elements[$offset]);
    }
    public function offsetGet ($offset )
    {

        return $this->elements[$offset];
    }


    public function offsetSet ($offset , $value )
    {
        if(is_string($offset)) {
            $this->attributes[$offset] = $value;

        }
        else {
            $this->addElement($value, $offset);
        }

    }


    public function offsetUnset ($offset )
    {
        if(isset($this->elements[$offset])) {
            unset($this->elements[$offset]);
        }
    }


    public function addClass($class)
    {
        foreach ($this->elements as $element) {
            $element->addClass($class);
        }
        return $this;
    }


    public function prepend($content)
    {
        foreach ($this->elements as $element) {
            $element->prepend($content);
        }
        return $this;
    }


    public function append($content)
    {
        foreach ($this->elements as $element) {
            $element->append($content);
        }
        return $this;
    }


    public function before($newElement)
    {
        foreach ($this->elements as $element) {
            $element->before($newElement);
        }
        return $this;
    }

    public function after($newElement)
    {
        foreach ($this->elements as $element) {
            $element->after($newElement);
        }
        return $this;
    }



    public function css($attribute, $value)
    {
        foreach ($this->elements as $element) {
            $element->css($attribute, $value);
        }
        return $this;
    }

}
