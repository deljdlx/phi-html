<?php

namespace Phi\HTML;

class Element implements \ArrayAccess, \JsonSerializable, \Countable
{



    protected $key = 0;
    protected $name;
    protected $contentText = '';

    protected $isAtomic = false;

    /**
     * @var Document
     */
    protected $document;

    /**
     * @var Element[]
     */
    protected $children = [];


    /**
     * @var Collection
     */
    protected $childrenByTag;



    /**
     * @var Collection[]
     */
    protected  $registeredCollections = [];

    protected $css;




    /**
     * @var Element
     */
    protected $parent;

    /**
     * @var Attribute[]
     */
    protected $attributes = [];

    protected $rawAttribute = '';


    protected $rawHTML;
    protected $compiled = true;


    public function __construct($name, $atomic = false)
    {

        $this->css = new Style();
        $this->name = $name;
        $this->isAtomic = $atomic;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }


    public function setRawAttributes($buffer)
    {
        $this->rawAttribute = $buffer;
        return $this;
    }



    public function setDocument($document)
    {
        $this->document = $document;
        return $this;
    }

    public function getDocument()
    {
        return $this->document;
    }



    public function jsonSerialize() {

        return array(
            'class' => get_class($this),
            'id' => $this->id,
            'name' => $this->name,
            'children' => $this->children,
            'content' => $this->contentText,
            'childrenByTag' => $this->childrenByTag,
        );
    }


    public function reset()
    {

        $this->children = [];
        $this->childrenByTag = [];
        return $this;
    }


    public function __get($propertyName)
    {

        if($propertyName == 'innerHTML') {
            return $this->render();
        }


        if(!isset($this->childrenByTag[$propertyName])) {

            $collection = new Collection($propertyName);
            $element = $this->createElement($propertyName);
            $collection->addElement($element);

            $this->childrenByTag[$propertyName] = $collection;
        }

        return $this->childrenByTag[$propertyName];
    }


    public function getCollection($propertyName) {
        if(!isset($this->childrenByTag[$propertyName])) {
            $collection = new Collection($propertyName);
            $this->childrenByTag[$propertyName] = $collection;
        }
        return $this->childrenByTag[$propertyName];
    }

    public function registerToCollection(Collection $collection)
    {
        $this->registeredCollections[] = $collection;
        return $this;
    }



    public function __set($propertyName, $value)
    {
        if($propertyName == 'innerHTML') {
            $this->reset();
            $this->parseHTML($value);
            return $this;
        }
        else if($value instanceof  Element) {
            $collection = $this->getCollection($propertyName);
            $collection->addElement($value);

            $this->children[] = $value;
        }


        return $this;
    }


    public function addChildren($children, $index = null)
    {
        if($index === null) {
            foreach ($children as $child) {
                $this->addChild($child);
            }
        }
        else {
            array_splice($this->children, $index, 0, $children);

            foreach ($children as $child) {
                $child->setParent($this);
                $child->setDocument($this->document);
                $collection = $this->getCollection($child->getName());
                $collection->addElement($child);
            }


            foreach ($this->children as $key => $child) {
                $child->setKey($key);
            }
        }

        return $this;
    }


    public function getParent()
    {
        return $this->parent;
    }

    public function addChild($child, $index = null)
    {
        if(is_array($child)) {
            return $this->addChildren($child, $index);
        }

        $child->setDocument($this->document);
        $child->setParent($this);

        $collection = $this->getCollection($child->getName());
        $collection->addElement($child);

        if($index === null) {
            $this->children[] = $child;
            $child->setKey(count($this->children)-1);
        }
        else {
            array_splice($this->children, $index, 0, array($child));
            $this->rebuild();
        }
        return $this;
    }

    public function rebuild()
    {
        foreach ($this->children as $key => $child) {
            $child->setKey($key);
        }
        return $this;
    }


    /**
     * @param $string
     * @return Element[]
     */
    public function createFromString($string)
    {
        $element = new Element('fragment');
        $element->html($string, true);
        if($this->document) {
            $element->setDocument($this->document);
        }
        return $element->children;

    }


    protected function parseHTML($buffer)
    {

        $innerBuffer = $buffer;

        $innerBuffer = preg_replace('`(^|>)([^<>]+?)(<|$)`s', '$1<phorm-text>$2</phorm-text>$3', $innerBuffer);
        $innerBuffer = $this->sanitizeHTML($innerBuffer);


        $xml = simplexml_load_string('<?xml version="1.0"?><fragment>'.$innerBuffer.'</fragment>');


        foreach ($xml->children() as $name =>$child) {
            $element = $this->createFromSimpleXMl($name, $child);
            $this->addChild($element);
        }
    }

    public function sanitizeHTML($buffer)
    {
        $buffer= str_replace('&', '&amp;', $buffer);
        $buffer =preg_replace('`<br>`', '<br/>', $buffer);
        $buffer =preg_replace('`<hr>`', '<hr/>', $buffer);
        $buffer  =preg_replace('`<input([^/]*?)>`', '<input$1/>', $buffer);
        $buffer  =preg_replace('`<meta([^/]*?)>`', '<meta$1/>', $buffer);

        $buffer  =preg_replace_callback('`(<link.*?>)`', function($matches) {

            if(!preg_match('`/>$`', $matches[0])) {
                return preg_replace('`>$`', '/>', $matches[0]);
            }
            return $matches[0];

        }, $buffer);




        $buffer  =preg_replace_callback('`(<img\s.*?>)`si',function($matches) {
            if(!preg_match('`/>$`', $matches[0])) {
                return str_replace('>', '/>', $matches[0]);
            }
            else {
                return $matches[0];
            }

        }, $buffer);


        //$buffer  =preg_replace('`<img\s+(.(?!/>)+?)`si', '<img $1 />', $buffer);
        return $buffer;
    }


    public function createFromSimpleXML($name, \SimpleXMLElement  $simpleXML)
    {

        $element = false;


        if($this->document) {
            $element = $this->document->getCustomElement($name, $simpleXML);
        }

        if(!$element) {
            $className = '\Phi\HTML\Element\\'.ucfirst($name);
            if(class_exists($className)) {
                $element = new $className($name);
            }
            else {
                $element = new Element($name);
            }
        }
        $element->setDocument($this->document);



        if(isset($simpleXML[0])) {
            $element->contentText = (string) $simpleXML[0];
        }

        foreach ($simpleXML->attributes() as $name => $value) {
            $element[$name] = $value;
        }

        foreach ($simpleXML->children() as $name => $child) {
            $elementChild = $this->createFromSimpleXMl($name, $child);
            $element->addChild($elementChild);
        }
        return $element;
    }


    /**
     * @param $selector
     * @param Collection|null $collection
     * @return Collection
     */
    public function find($selector, Collection $collection = null)
    {
        $query = new Query($this, $selector, $collection);

        $value = $query->find();

        return $value;
    }


    /**
     * @return Element[]
     */
    public function getChildren()
    {
        return $this->children;
    }


    /**
     * @param $elementName
     * @return Element
     */
    public function createElement($elementName)
    {
        $className = '\Phi\HTML\Element\\'.ucfirst($elementName);
        if(class_exists($className)) {
            $element = new $className($elementName);
        }
        else {
            $element = new Element($elementName);
        }

        if($this->document) {
            $element->setDocument($this->document);
        }

        return $element;
    }






    public function html($html = null, $parse = false) {
        if($html) {

            $this->reset();
            $this->rawHTML = $html;

            if($parse) {
                $this->compiled = true;
                $this->parseHTML($html);

            }
            else {
                $this->compiled = false;
            }
            return $this;
        }
        else {
            return $this->render();
        }
    }




    public function prepend($element)
    {

        if(is_string($element)) {
            $element = $this->createFromString($element);
        }
        $this->insertChildAtIndex(0, $element);
    }

    public function append($element)
    {
        if(is_string($element)) {
            $this->parseHTML($element);
        }
        else if($element instanceof Element) {
            if($element->getParent()) {
                $element->detach();
            }
            $this->addChild($element);
        }
        return $this;
    }


    public function detach()
    {
        $this->parent->removeElementByIndex($this->getKey());
        return $this;
    }

    public function removeElementByIndex($index)
    {
        unset($this->children[$index]);
        $this->rebuild();
        return $this;
    }



    public function before($element)
    {

        if(is_string($element)) {
            $element = $this->createFromString($element);
        }
        $parent = $this->parent;
        $parent->insertChildAtIndex($this->getKey(), $element);
    }

    public function after($element)
    {

        if(is_string($element)) {
            $elements = $this->createFromString($element);
        }
        $parent = $this->parent;
        $parent->insertChildAtIndex($this->getKey()+1, $elements);
    }




    public function insertChildAtIndex($index, $element) {
        foreach ($this->children as $key => $child) {
            if($index == $key) {
                $this->addChild($element, $index);
                break;
            }
        }
        return $this;
    }





    public function getKey()
    {
        return $this->key;
    }













    public function setParent(Element $element)
    {
        $this->parent = $element;
        return $this;
    }



    public function setAttribute($name, $value)
    {
        if(!isset($this->attributes[$name])) {
            $this->attributes[$name] = new Attribute($name);
        }

        $this->attributes[$name]->setValue($value);
        return $this;
    }


    public function getAttribute($name)
    {
        if(!isset($this->attributes[$name])) {
            $this->attributes[$name] = new Attribute($name);
        }
        return $this->attributes[$name];
    }

    public function getAttributes()
    {
        return $this->attributes;
    }



    public function renderAttributes()
    {
        $buffer = '';
        foreach ($this->attributes as $attribute) {
            if($attribute->getName() !='style' && $attribute->getValue() !== null) {
                $buffer.= $attribute->getName().'="'.$attribute->getValue().'" ';
            }
        }

        if($css = $this->css->render().$this->getAttribute('style')->getValue()) {
            $buffer .=' style="'.$css.'"';
        }


        return trim($buffer);
    }



    public function addText($text)
    {

        $textNode = new Element('');
        $textNode->html($text);
        $this->addChild($textNode);
        return $this;
    }


    public function render()
    {


        if($this->name == 'phorm-text') {
            return $this->contentText;
        }



        foreach ($this->registeredCollections as $collection) {
            $collection->injectAttributesIntoElement($this);
        }


        if(empty($this->children) && strtolower($this->name) !='script' && $this->isAtomic && $this->name) {
            return '<'.$this->name.' '.$this->renderAttributes().'/>';
        }



        $buffer = '';
        if($this->name) {
            $attributeBuffer = $this->renderAttributes();
            if($this->rawAttribute) {
                $attributeBuffer .= ' '.$this->rawAttribute;
            }
            if($attributeBuffer) {
                $buffer = '<'.$this->name.' '.trim($attributeBuffer).'>';
            }
            else {
                $buffer = '<'.$this->name.'>';
            }
        }




        if(!$this->compiled) {
            $buffer .= $this->rawHTML;
        }
        else {
            foreach ($this->children as $child) {
                //$this->getCollection($child->getName());
                $buffer .= $child->render();
            }
        }


        if($this->name) {
            $buffer .= '</'.$this->name.'>';
        }



        return $buffer;

    }


    public function getName()
    {
        return $this->name;
    }




    public function __toString()
    {
        return $this->render();
    }


    //=======================================================
    public function offsetExists ($offset)
    {
        return isset($this->attributes[$offset]);
    }
    public function offsetGet ($offset )
    {
        return $this->getAttribute($offset)->value;
    }
    public function offsetSet ($offset , $value )
    {
        $this->setAttribute($offset, $value);
    }
    public function offsetUnset ($offset )
    {
        if(isset($this->attributes[$offset])) {
            unset($this->attributes[$offset]);
        }
    }

    //=======================================================

    public function css($property, $value) {
        $this->css->set($property, $value);

        return $this;
    }


    public function count()
    {
        return count($this->children);
    }


    public function toArray()
    {
        $attributes = [];
        foreach ($this->getAttributes() as $name => $attribute) {
            $attributes[$name] = (string) $attribute->getValue();
        }


        $data = array(
            $this->name => array(
                'attributes' => $attributes,
                'children' => array()
            )
        );

        foreach ($this->children as $key => $child) {
            $data[$this->name]['children'][$key] = $child->toArray();
        }

        return $data;

    }


}
