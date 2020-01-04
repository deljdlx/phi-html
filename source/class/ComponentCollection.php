<?php


namespace Phi\HTML;


use Phi\Template\Interfaces\Renderer;

class ComponentCollection extends Component
{

    protected $components = [];


    public function addComponent($component)
    {
        $this->components[] = $component;

        if($component instanceof Component) {
            foreach ($component->getJavascriptTags() as $priority => $javascriptList) {
                foreach ($javascriptList as $javascript) {
                    $this->addJavascriptFile($javascript, $priority);
                }
            }

            foreach ($component->getCSSTags() as $priority => $cssList) {
                foreach ($cssList as $css) {
                    $this->addCSSFile($css, $priority);
                }
            }

        }

        return $this;
    }

    public function render()
    {

        $buffer = '';
        foreach ($this->components as $component) {
            if(is_string($component)) {
                $buffer .= $component;
            }
            else if($component instanceof  Renderer) {
                $buffer .= $component->render();
            }
        }
        return $buffer;
    }







}