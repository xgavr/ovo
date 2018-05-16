<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Filter;

use Zend\Filter\AbstractFilter;
use cijic\phpMorphy\Morphy;
use cijic\phpMorphy\MorphyServiceProvider;

/**
 * Морфологический аналих строки
 *
 * @author Daddy
 */

class MorphyFilter extends AbstractFilter
{
        
    const DICTONARY_DIR      = './data/dictonary/'; // папка словарей
    
    /*
     * Стоп слова для поиска
     */
    private $stopword;
    
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
        
        $this->stopword = file($this::DICTONARY_DIR.'stopword.txt', FILE_IGNORE_NEW_LINES);
    }
    
    protected function cleanUpRu($str)
    {
        $str = str_replace("-", " ", $str);
        $str = str_replace("\r\n", " ", $str);
        $str = str_replace("\r", " ", $str);
        $str = str_replace("\n", " ", $str);
        $str = str_replace(".", " ", $str);
        $str = ereg_replace("[^0-9 абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ]", "", $str);
        return $str;        
    }
    
    protected function cleanUpEn($str)
    {
        $str = str_replace("-", " ", $str);
        $str = str_replace("\r\n", " ", $str);
        $str = str_replace("\r", " ", $str);
        $str = str_replace("\n", " ", $str);
        $str = str_replace(".", " ", $str);
        $str = ereg_replace("[^0-9 abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ]", "", $str);
        return $str;        
    }

    public function filter($value)
    {        
        $result = '';
        
        $textRu = $this->cleanUpRu($value);
        $testEn = $this->cleanUpEn($value);
        $aTextRu = explode(' ', $textRu);
        
        $morphy = new Morphy('ru');
        foreach ($aTextRu as $word){
            //$pseudo_root = $morphy->getPseudoRoot(mb_strtoupper(trim($word), $this->options['encoding']));
            $pseudo_root = $morphy->getBaseForm(mb_strtoupper(trim($word), $this->options['encoding']));
            
            if (is_array($pseudo_root)){
                foreach ($pseudo_root as $roots){

                    $slovo=mb_strtolower(trim($roots), $this->options['encoding']);
                    if (strlen( $slovo)>3 && !in_array($slovo, $this->stopword) && count($roots)==1 ){
                        $result .= $slovo." ";                      
                    }

                }
            } else {
                $slovo=mb_strtolower(trim($word), $this->options['encoding']);
                if (!in_array($slovo, $this->stopword)){
                    $result .= $slovo." ";                      
                }                
            }   
        }    
        
        
        $aTextEn = explode(' ', $textEn);
        
        $morphy = new Morphy('en');
        foreach ($aTextEn as $word){
            //$pseudo_root = $morphy->getPseudoRoot(mb_strtoupper(trim($word), $this->options['encoding']));
            $pseudo_root = $morphy->getBaseForm(mb_strtoupper(trim($word), $this->options['encoding']));
            
            if (is_array($pseudo_root)){
                foreach ($pseudo_root as $roots){

                    $slovo=mb_strtolower(trim($roots), $this->options['encoding']);
                    if (strlen( $slovo)>3 && !in_array($slovo, $this->stopword) && count($roots)==1 ){
                        $result .= $slovo." ";                      
                    }

                }
            } else {
                $slovo=mb_strtolower(trim($word), $this->options['encoding']);
                if (!in_array($slovo, $this->stopword)){
                    $result .= $slovo." ";                      
                }                
            }   
        }    
                
        return $result;
    }
    
}
