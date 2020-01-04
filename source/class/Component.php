<?php

namespace Phi\HTML;



use Phi\HTML\Traits\HasAsset;
use Phi\Template\Template;


class Component
{

    const RESOURCE_PRIORITY_DEFAULT = 2048;
    const RESOURCE_PRIORITY_INCLUDE = 4096;
    const RESOURCE_PRIORITY_REQUIRE = 8192;


    use HasAsset;

    //const RESOURCE_PRIORITY_REQUIRE = 4096;


    /** @var  Template */
    protected $template;



    public $dom;

    protected $builded = false;


    public function __construct()
    {
        $this->template = new Template();
        $this->dom = new Element('');
    }

    public function setTemplate(Template $template)
    {

        $template->setVariables(
            $this->template->getVariables()
        );

        $this->template = $template;
        return $this;
    }

    public function loadTemplate($templateFile)
    {
        $this->template->loadFile($templateFile);
        return $this;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param $path
     * @return $this
     */



    /**
     * @return Element
     */
    public function getDom()
    {
        if(!$this->builded) {
            $this->build();
        }
        return $this->dom;
    }

    /**
     * @param $query
     * @param Collection|null $collection
     * @return Collection
     */
    public function find($query, Collection $collection = null)
    {
        return $this->dom->find($query, $collection);
    }


    /**
     * @param Element $element
     * @return $this
     */
    public function addChild(Element $element)
    {
        $this->dom->addChild($element);
        return $this;
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

        $this->dom->html($this->template->render(), true);
        $this->builded = true;
        return $this;
    }



    public function render()
    {

        if(!$this->builded) {
            $this->build();
        }

        $buffer = $this->dom->render();


        return $buffer;



    }


}
