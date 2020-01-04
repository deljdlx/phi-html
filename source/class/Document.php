<?php

namespace Phi\HTML;

use Phi\Template\Traits\MustacheTemplate;


class Document extends Component
{
    use MustacheTemplate;



    /**
     * @var Element
     */
    public $dom;
    protected $html;

    protected $customTags = [];




    public function __construct()
    {
        parent::__construct();

        $this->dom = new Element('html');
        $this->dom->setDocument($this);
        $this->html = &$this->dom;

    }

    public function find($query, Collection $collection = null)
    {
        return $this->dom->find($query, $collection);
    }


    public function setHTML($html, $parse = false)
    {
        //$this->dom->html($this->normalizeHTML($html), $parse);

        $this->dom = new Element('html');
        $this->dom->setDocument($this);

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






    public function render()
    {
        return $this->dom->render();
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




