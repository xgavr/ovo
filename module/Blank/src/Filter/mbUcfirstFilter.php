<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blank\Filter;

use Zend\Filter\AbstractFilter;

/**
 * Дата в формате ру
 *
 * @author Daddy
 */

class mbUcfirstFilter extends AbstractFilter
{
        
    // Доступные опции фильтра.
    protected $options = [
        'encoding' => 'utf8',
    ];    
    

    // Конструктор.
    public function __construct($options = null) 
    {     
        // Задает опции фильтра (если они предоставлены).
        if(is_array($options)) {
            if(isset($options['encoding'])){
                $this->options['encoding'] = $options['encoding'];
            }
        }    
    }
    
    public function filter($value)
    {        
        $strlen = mb_strlen($value, $this->options['encoding']);
        $firstChar = mb_substr($value, 0, 1, $this->options['encoding']);
        $then = mb_substr($value, 1, $strlen - 1, $this->options['encoding']);
        return mb_strtoupper($firstChar, $this->options['encoding']) . $then;
    }
    
}
