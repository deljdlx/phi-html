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


        $attributeSelector = false;
        $selector = $this->selector;
        if(preg_match('`(.*?)\[(.+?)\]`', $this->selector, $data)) {
            $attributeSelector = $data[2];
            $selector = $data[1];
        }


        if(preg_match('`^\.([A-z\-]+)$`', $selector, $data)) {
            $className = $data[1];
            $this->getByClassName($className);
        }

        if(preg_match('`^#([A-z\-]+)$`', $selector, $data)) {
            $id = $data[1];
            $this->getById($id);
        }

        if(preg_match('`^([A-z]+)$`', $selector, $data)) {
            $tagName = $data[1];
            $this->getByTagName($tagName);
        }

        if($attributeSelector) {
            $attributeData = explode('=', $attributeSelector);
            $attributeName = $attributeData[0];
            $attributeValue = $attributeData[1];

            $keyToRemove = [];
            foreach ($this->collection->getElements() as $key => $item) {
                if((string) $item->getAttribute($attributeName)->getValue() != $attributeValue) {
                    $keyToRemove[] = $key;
                }
            }
            foreach ($keyToRemove as $key) {
                $this->collection->removeElementByKey($key);
            }
        }


        return $this->collection;

    }


    /**
     * @param $tagName
     * @return Collection
     */
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


    /**
     * @param $id
     * @return Collection
     */
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


    /**
     * @param $className
     * @return Collection
     */
    public function getByClassName($className)
    {

        foreach ($this->element->getChildren() as $element) {

            $elementClassName = $element->getAttribute('class')->getValue();

            if($elementClassName) {
                if(preg_match('`(^| )'.$className.'( |$)`', $elementClassName)) {
                    $this->collection->addElement($element);
                }

            }
            $element->find($this->selector, $this->collection);
        }
        return $this->collection;
    }




}