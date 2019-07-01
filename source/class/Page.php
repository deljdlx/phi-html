<?php

namespace Phi\HTML;


use Phi\Template\Traits\MustacheTemplate;
use Phi\Core\Exception;


class Page extends Document
{

    use MustacheTemplate;


    const DEFAULT_DOCTYPE = '<!doctype html>';
    const DEFAULT_END_PAGE_ID = 'phi-html-body-end';
    const DEFAULT_MAIN_CONTAINER_ID = 'phi-main-container';


    /**
     * @var Document;
     */
    public $document;

    /** @var Element  */
    public $dom;



    protected $bodyEndSelector = '#phi-html-body-end';

    protected $doctype;


    protected $endPageId;
    protected $mainContainerId;


    protected $injectedCSS = array();
    protected $injectedJavascripts = array();



    protected $components = [];
    protected $componentsByName= [];


    public $endBody = '';


    public function __construct()
    {

        $this->document = new Document();
        $this->dom = $this->document->dom;

        $this->dom->html('
            <head>
                <meta charset="utf-8" />
                <title>Phi page</title>
            </head>
            <body style="min-height: 100%; height:100%; margin:0">
                <div id="'.$this->getMainContainerId().'"></div>
                <a id="'.$this->getEndPageId().'"></a>
            </body>
        ', true);

    }



    public function getMainContainerId()
    {
        if($this->mainContainerId === null) {
            return static::DEFAULT_MAIN_CONTAINER_ID;
        }
        else {
            return $this->mainContainerId;
        }
    }


    public function setMainContent($content)
    {
        $this->registerComponent($content, '#'.$this->getMainContainerId());



        return $this;
    }

    public function getMainContent()
    {
        return  $this->find('#'.$this->getMainContainerId())->first();
    }


    public function getEndPageId()
    {
        if($this->endPageId === null) {
            return static::DEFAULT_END_PAGE_ID;
        }
        else {
            return $this->endPageId;
        }
    }


    public function getDoctype()
    {
        if($this->doctype === null) {
            return static::DEFAULT_DOCTYPE;
        }
        else {
            return $this->doctype;
        }
    }





    public function googleNoTranslation()
    {
        $this->dom->head->append('<meta name="google" content="notranslate" />');
        return $this;
    }

    public function clear($selector)
    {
        if(array_key_exists($selector, $this->components)) {
            $this->components[$selector] = null;
        }

        return $this;
    }

    public function registerComponent($component, $selector = null, $name = null)
    {

        if($selector) {
            if(isset($this->components[$selector])) {
                if(!$this->components[$selector] instanceof  ComponentCollection) {
                    $currentComponent = $this->components[$selector];
                    $this->components[$selector] = new ComponentCollection();
                    $this->components[$selector]->addComponent($currentComponent);
                }
                $this->components[$selector]->addComponent($component);
            }
            else {
                $this->components[$selector] = $component;
            }

        }
        else {
            $this->components[] = $component;
        }

        if( $name!== null) {
            $this->componentsByName[$name] = $component;
        }

        return $this;
    }

    public function getComponentByName($name)
    {
        if(array_key_exists($name, $this->componentsByName)) {
            return $this->componentsByName[$name];
        }
        else {
            throw new Exception('Component with name'.$name.' does not exists');
        }
    }

    public function reset()
    {
        $this->components = array();
        $this->cssFiles = array();
        $this->javascriptFiles = array();
        return $this;
    }


    protected function injectResources()
    {

        $javascriptAnchor = $this->dom->find($this->bodyEndSelector);


        $cssFiles = array();
        $javascriptFiles = array();



        foreach ($this->getCSSTags(true) as $cssFile) {
            $cssFiles[] = $cssFile;
        }

        foreach ($this->getJavascriptTags(true) as $javascriptFile) {
            $javascriptAnchor->before($javascriptFile);
        }


        foreach ($this->components as $selector => $component) {

            if($component instanceof Component) {

                foreach ($component->getCSSTags(true) as $cssFile) {
                    $cssFiles[] = $cssFile;
                }

                foreach ($component->getJavascriptTags(true) as $javascriptFile) {
                    $javascriptFiles[] = $javascriptFile;
                }
            }
        }



        $currentCSSCollection = $this->dom->head->find('link[rel=stylesheet]');
        foreach ($cssFiles as $cssFile) {
            $cssKey = $cssFile->getKey();

            if(!isset($this->injectedCSS[$cssKey])) {
                $this->injectedCSS[$cssKey] = true;
                $this->dom->head->append($cssFile->render()."\n");
            }
        }

        //keep priority

        if($currentCSSCollection->length()) {
            foreach ($currentCSSCollection->getElements() as $link) {
                $this->dom->head->append($link);
                $this->dom->head->append("\n");
            }
        }

        foreach ($javascriptFiles as $javascriptFile) {
            $javascriptKey = $javascriptFile->getSource();



            if(!isset($this->injectedJavascripts[$javascriptKey])) {
                $this->injectedJavascripts[$javascriptKey] = true;
                $javascriptAnchor->before($javascriptFile);
            }

        }
    }





    public function compile()
    {


        foreach ($this->components as $selector => $component) {


            if(is_string($selector)) {

                if(is_string($component)) {
                    $this->dom->find($selector)->html($component);
                }
                else {
                    $this->dom->find($selector)->html($component->render());
                }
            }
        }

        $this->injectResources();

        $this->dom->body->append($this->endBody);
    }


    public function render()
    {
        $this->build();
        $this->compile();
        $buffer = $this->dom->render();
        return $this->getDoctype()."\n".$buffer;



    }








}

