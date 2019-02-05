<?php

namespace Phi\HTML;

use Phi\Template\Traits\MustacheTemplate;
use Phi\Core\Exception;


class Document extends Component
{
    use MustacheTemplate;


    const DEFAULT_DOCTYPE = '<!doctype html>';
    const DEFAULT_END_PAGE_ID = 'phi-html-body-end';
    const DEFAULT_MAIN_CONTAINER_ID = 'phi-main-container';



    protected $bodyEndSelector = '#phi-html-body-end';

    protected $doctype;


    protected $endPageId;
    protected $mainContainerId;


    protected $injectedCSS = array();
    protected $injectedJavascripts = array();


    /**
     * @var Element
     */
    public $dom;

    protected $html;

    protected $components = [];
    protected $componentsByName= [];

    protected $customTags = [];


    public $endBody = '';


    public function __construct()
    {
        parent::__construct();

        $this->dom = new Element('html');
        $this->dom->setDocument($this);

        //$this->dom['style'] = 'height:100%;';

        $this->html = &$this->dom;


        $this->dom->innerHTML='
            <head>
                <meta charset="utf-8" />
                <title>Phi document</title>
            </head>
            <body style="min-height: 100%; height:100%; margin:0">
                <div id="'.$this->getMainContainerId().'"></div>
                <a id="'.$this->getEndPageId().'"></a>
            </body>
        ';
    }

    public function find($query, Collection $collection = null)
    {
        return $this->dom->find($query, $collection);
    }


    public function setHTML($html, $parse = false)
    {
        //$this->dom->html($this->normalizeHTML($html), $parse);
        $this->dom = new Element('html');
        $this->html = &$this->dom;
        $this->dom->html($this->normalizeHTML($html), $parse);

        $htmlAttributes = preg_replace('`.*?<html(.*?)>.*`si', '$1', $html);

        $this->dom->setRawAttributes($htmlAttributes);

        return $this;
    }

    protected function normalizeHTML($html)
    {
        $content = preg_replace('`.*?<html.*?>(.*?)</html>.*`is', '$1', $html);

        return $content;
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


    public function getCustomElement($name, $simpleXML)
    {

        if(isset($this->customTags[$name]))
        {

            if(class_exists($this->customTags[$name])) {
                /**
                 * @var CustomTag $element
                 */
                $element = new $this->customTags[$name];
                $element->setDocument($this);


                foreach ($simpleXML->parameter as $parameter) {
                    $parameterName = (string) $parameter['name'];
                    $element->setParameter($parameterName, $parameter);
                }

                return $element;
            }
        }
        return false;
    }


    public function googleNoTranslation()
    {
        $this->dom->head->append('<meta name="google" content="notranslate" />');
        return $this;
    }

    public function registerComponent($component, $selector = null, $name = null)
    {

        if($selector) {
            $this->components[$selector] = $component;
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



        foreach ($cssFiles as $cssFile) {
            $cssKey = $cssFile->getKey();

            if(!isset($this->injectedCSS[$cssKey])) {
                $this->injectedCSS[$cssKey] = true;
                $this->dom->head->append($cssFile->render()."\n");
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
        $this->compile();
        $buffer = $this->dom->render();
        return $this->getDoctype()."\n".$buffer;



    }








}











/*
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body);
echo '</pre>';
print_r($this->body->render());
die('EXIT '.__FILE__.'@'.__LINE__);
*/



/*

echo '<hr/>';

$this->body->innerHTML = '
            <div><button>hello 0</button></div>
            <div><button>hello 1</button></div>';



$this->body->div[1]->button->html('<img src="https://www.fillmurray.com/150/30"/>');
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';




echo '<hr/>';
$this->body->div[1]->button['onClick'] = '(function() {alert(2);})()';
$this->body->div[1]->button->img['src'] = 'https://www.fillmurray.com/50/50';
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';





echo '<hr/>';
$this->body->div[1]->button->img['src']='https://www.fillmurray.com/150/150';
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';



echo '<hr/>';
$this->body->div->button->img['src']='https://www.fillmurray.com/50/50';
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';



echo '<hr/>';
$this->body->div->button->html('<img src="https://www.fillmurray.com/200/200"/>');
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';




echo '<hr/>';
$this->body->append('<button>append</button>');
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';

echo '<hr/>';
$this->body->button['onClick']='alert("append")';
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';


































$this->body->div->innerHTML ='hello all';
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';

$this->body->div[0]->innerHTML ='hello 0';
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';



$this->body->div[1]->innerHTML ='<button>button</button>';
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';



$this->body->div->button['style'] = 'background-color: #F0F';
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';

$this->body->div['style'] = 'border:  3px dashed #A00';
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';

$this->body->div[0]['style'] ='background-color: #FFA';
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';

$this->body->div[1]->button->html('click clik');
echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
print_r($this->body->render());
echo '</pre>';

*/




