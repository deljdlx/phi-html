<?php

namespace Phi\HTML\Traits;



use Phi\HTML\CSSFile;
use Phi\HTML\JavascriptFile;

trait HasAsset
{

    /**
     * @var JavascriptFile[]
     */
    protected $javascriptFiles = [];

    /**
     * @var CSSFile[]
     */
    protected $cssFiles = [];


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


}


