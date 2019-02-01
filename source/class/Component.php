<?php

namespace Phi\HTML;



use Phi\Template\PHPTemplate;

class Component extends PHPTemplate
{


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



    public function addCSSFile($css)
    {

        if(!$css instanceof CSSFile) {
            $css = new CSSFile($css);
            $this->cssFiles[] = $css;
        }
        else {
            $this->cssFiles[] = $css;
        }
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


    public function addJavascriptFile($javascript)
    {
        if(!$javascript instanceof JavascriptFile) {
            $javascript = new JavascriptFile($javascript);
        }

        $this->javascriptFiles[] = $javascript;
        return $javascript;
    }


    public function getCSSTags()
    {
        return $this->cssFiles;
    }

    /**
     * @return JavascriptFile[]
     */
    public function getJavascriptTags()
    {
        return $this->javascriptFiles;
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



    public function render()
    {

        if($this->template) {
            $this->dom->html(parent::render());
        }
        $buffer = $this->dom->render();


        return $buffer;



    }


}
