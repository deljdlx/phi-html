<?php


function phiHTMLTag($tagName,$items, $callback = null, $options = [])
{


    $defaultOptions = [
        'before' => null,
        'after' => null,
        'class' => null
    ];

    if($options) {
        $options = array_merge(
            $defaultOptions,
            $options
        );
    }
    else {
        $options = $defaultOptions;
    }

    $class = '';
    if($options['class']) {
        $class = ' class="' . $options['class'] . '"';
    }

    $buffer = '<' . $tagName . $class . '>';

        if(is_callable($options['before'])) {
            $buffer .= $options['before']($items);
        }
        elseif(is_string($options['before'])) {
            $buffer .= $options['before'];
        }


        if (!is_scalar($items)) {
            foreach ($items as $index => $item) {
                if($callback) {
                    $buffer .= call_user_func_array(
                        $callback,
                        [$item, $index]
                    );
                }
                else {
                    $buffer .= $item;
                }
            }
        }
        else {
            if($callback) {
                $buffer .= call_user_func_array(
                    $callback,
                    [$items]
                );
            }
            else {
                $buffer .= $items;
            }
        }

        if(is_callable($options['after'])) {
            $buffer .= $options['after']($items);
        }
        elseif(is_string($options['after'])) {
            $buffer .= $options['after'];
        }

    $buffer .= '</' . $tagName . '>';
    return $buffer;
}


// ====================================================================================


function ul($items, $liFormater, $options = [])
{

    return phiHTMLTag(
        'ul',
        $items,
        fn($item) => li($liFormater($item), null, $options),
        $options
    );
}


function li($string) {
    return phiHTMLTag('li', $string);
}


// =============================================================

function table($rows, $colSelector = null, $options = [])
{
    return phiHTMLTag(
        'table',
        tbody($rows, $colSelector),
        null,
        $options
    );
}




function tbody($rows, $colSelector = null, $options = [])
{

    if(!$colSelector) {
        $colSelector = function($row) {
            if (is_array($row)) {
                return $row;
            }
            else {
                if(is_object($row)) {
                    $cols = [];
                    foreach($row as $attribute => $value) {
                        $cols[$attribute] = $value;
                    }
                    return $cols;
                }
            }
        };
    }

    $buffer = phiHTMLTag(
        'tbody',
        $rows,
        fn($row) => tr($colSelector($row)),
        $options
    );
    return $buffer;
}




function thead($items)
{
    return phiHTMLTag(
        'thead',
        $items,
        fn($item) => th($item)
    );
}


function tfooter($items)
{
    return tr(
        $items,
        fn($item) => th($item)
    );
}



function tr($cols, $callback = null)
{


    if(!$callback) {
        $callback = fn($col) => td($col);
    }


    $buffer = phiHTMLTag(
        'tr',
        $cols,
        fn($col) => $callback($col)
    );
    return $buffer;
}


function th($string)
{
    return phiHTMLTag('th', $string);
}


function td($string)
{
    return phiHTMLTag('td', $string);
}
// =============================================================

function div($string) {
    return phiHTMLTag('div', $string);
}


function span($string) {
    return phiHTMLTag('span', $string);
}

