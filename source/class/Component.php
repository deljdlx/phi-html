<?php

namespace Phi\HTML;



use Phi\Template\PHPTemplate;

class Component extends PHPTemplate
{

    const RESOURCE_PRIORITY_DEFAULT = 2048;
    const RESOURCE_PRIORITY_INCLUDE = 4096;
    const RESOURCE_PRIORITY_REQUIRE = 8192;


    //const RESOURCE_PRIORITY_REQUIRE = 4096;


    protected $javascriptFiles = [];
    protected $cssFiles = [];
    protected $dom;


    public function __construct($tag = '')
    {
        $this->dom = new Element($tag);
    }


    public function find($query, Collection $collection = null)
    {
        return $this->dom->find($query, $collection);
    }


    public function addChild(Element $element)
    {
        $this->dom->addChild($element);
        return $this;
    }



    public function addCSSFile($css, $priority = null)
    {


        if($priority === null) {
            $priority = static::RESOURCE_PRIORITY_DEFAULT;
        }
        if(!array_key_exists($priority, $this->cssFiles)) {
            $this->cssFiles[$priority] = [];
        }


        if(!$css instanceof CSSFile) {
            $css = new CSSFile($css);
        }

        $this->cssFiles[$priority][] = $css;

        return $css;

    }


    public function getCSSFiles()
    {
        return $this->cssFiles;
    }

    public function getJavascriptFiles()
    {
        return $this->javascriptFiles;
    }


    public function addJavascriptFile($javascript, $priority = null)
    {

        if(!$javascript instanceof JavascriptFile) {
            $javascript = new JavascriptFile($javascript);
        }


        if($priority === null) {
            $priority = static::RESOURCE_PRIORITY_DEFAULT;
        }
        if(!array_key_exists($priority, $this->javascriptFiles)) {
            $this->javascriptFiles[$priority] = [];
        }

        $this->javascriptFiles[$priority][] = $javascript;


        return $javascript;
    }


    public function getCSSTags($flatten = false)
    {
        if(!$flatten) {
            return $this->cssFiles;
        }
        else {
            $flatten = [];
            $sorted = $this->cssFiles;
            krsort($sorted);
            foreach ($sorted as $cluster) {
                $flatten = array_merge($flatten, $cluster);
            }
            return $flatten;
        }

    }

    /**
     * @return JavascriptFile[]
     */
    public function getJavascriptTags($flatten = false)
    {
        if(!$flatten) {
            return $this->javascriptFiles;
        }
        else {
            $flatten = [];
            $sorted = $this->javascriptFiles;
            krsort($sorted);
            foreach ($sorted as $cluster) {
                $flatten = array_merge($flatten, $cluster);
            }
            return $flatten;
        }
    }



    public function getUncompiledTemplate()
    {
        $buffer = file_get_contents($this->template);
        $buffer= $this->removePHP($buffer);
        return $buffer;
    }

    public function removePHP($buffer)
    {
        $buffer = preg_replace('`<\?php.*?\?>`si', '', $buffer);
        $buffer = preg_replace('`<\?=\?>`si', '', $buffer);
        return $buffer;
    }

    /**
     * @return Element
     */
    public function getElement()
    {
        return $this->dom;
    }


    public function build()
    {
        if($this->template) {
            $this->dom->html(parent::render());
        }
    }



    public function render()
    {

        $this->build();
        $buffer = $this->dom->render();


        return $buffer;



    }


}
