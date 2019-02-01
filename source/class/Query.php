<?php
namespace Phi\HTML;


class Query
{

    protected $element;
    protected $selector;
    protected $collection;

    public function __construct(Element $element, $selector, Collection $collection = null)
    {
        $this->element = $element;
        $this->selector = trim($selector);
        $this->collection = $collection;

        if(!$this->collection) {
            $this->collection = new Collection($this->selector);
        }

    }


    public function find()
    {

        if(preg_match('`^\.([A-z\-]+)$`', $this->selector, $data)) {
            $className = $data[1];
            return $this->getByClassName($className);
        }

        if(preg_match('`^#([A-z\-]+)$`', $this->selector, $data)) {
            $id = $data[1];
            return $this->getById($id);
        }

        if(preg_match('`^([A-z]+)$`', $this->selector, $data)) {
            $tagName = $data[1];
            return $this->getByTagName($tagName);
        }


        return $this->collection;

    }


    public function getByTagName($tagName)
    {
        foreach ($this->element->getChildren() as $element) {

            if($element->getName() == $tagName) {
                $this->collection->addElement($element);
            }
            $element->find($this->selector, $this->collection);
        }
        return $this->collection;
    }



    public function getById($id)
    {
        foreach ($this->element->getChildren() as $element) {

            $elementId = $element->getAttribute('id')->getValue();

            if($elementId == $id) {
                $this->collection->addElement($element);
            }
            $element->find($this->selector, $this->collection);
        }
        return $this->collection;
    }


    public function getByClassName($className)
    {

        foreach ($this->element->getChildren() as $element) {

            $elementClassName = $element->getAttribute('class')->getValue();

            if($elementClassName) {
                if(preg_match('`\b'.$className.'\b`', $elementClassName)) {
                    $this->collection->addElement($element);
                }

            }
            $element->find($this->selector, $this->collection);
        }
        return $this->collection;
    }




}