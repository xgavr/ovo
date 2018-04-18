<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Application\Entity\Pricesettings;

/**
 * Description of Pricesettings
 *
 * @author Daddy
 */
class PricesettingsForm extends Form
{
    /**
     * Конструктор.     
     */
    public function __construct()
    {
        // Определяем имя формы.
        parent::__construct('pricesettings-form');
     
        // Задает для этой формы метод POST.
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();         
    }
    
    /**
    * Этот метод добавляет элементы к форме (поля ввода и кнопку отправки формы).
    */
    protected function addElements() 
    {
                
        // Добавляем поле "name"
        $this->add([           
            'type'  => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'ps_name'
            ],
            'options' => [
                'label' => 'Наименование',
            ],
        ]);
        
        // Добавляем поле "article"
        $this->add([           
            'type'  => 'text',
            'name' => 'article',
            'attributes' => [
                'id' => 'ps_article'
            ],
            'options' => [
                'label' => 'Артикул',
            ],
        ]);
        
        // Добавляем поле "iid"
        $this->add([           
            'type'  => 'text',
            'name' => 'iid',
            'attributes' => [
                'id' => 'ps_iid'
            ],
            'options' => [
                'label' => 'Внутренний ID',
            ],
        ]);
        
        // Добавляем поле "producer"
        $this->add([           
            'type'  => 'text',
            'name' => 'producer',
            'attributes' => [
                'id' => 'ps_producer'
            ],
            'options' => [
                'label' => 'Производитель',
            ],
        ]);
        
        $this->add([           
            'type'  => 'text',
            'name' => 'country',
            'attributes' => [
                'id' => 'country'
            ],
            'options' => [
                'label' => 'страна',
            ],
        ]);

        // Добавляем поле "title"
        $this->add([           
            'type'  => 'text',
            'name' => 'title',
            'attributes' => [
                'id' => 'ps_title'
            ],
            'options' => [
                'label' => 'Наименование',
            ],
        ]);
        
        $this->add([           
            'type'  => 'text',
            'name' => 'description',
            'attributes' => [
                'id' => 'description'
            ],
            'options' => [
                'label' => 'Описание',
            ],
        ]);
        
        $this->add([           
            'type'  => 'text',
            'name' => 'image',
            'attributes' => [
                'id' => 'description'
            ],
            'options' => [
                'label' => 'Картинка',
            ],
        ]);
        
        // Добавляем поле "price"
        $this->add([           
            'type'  => 'text',
            'name' => 'price',
            'attributes' => [
                'id' => 'ps_price'
            ],
            'options' => [
                'label' => 'Цена',
            ],
        ]);
        
        $this->add([           
            'type'  => 'text',
            'name' => 'currency',
            'attributes' => [
                'id' => 'ps_price'
            ],
            'options' => [
                'label' => 'Валюта',
            ],
        ]);
        
        $this->add([           
            'type'  => 'text',
            'name' => 'rate',
            'attributes' => [
                'id' => 'rate'
            ],
            'options' => [
                'label' => 'Курс валюты',
            ],
        ]);
        
        // Добавляем поле "rest"
        $this->add([           
            'type'  => 'text',
            'name' => 'rest',
            'attributes' => [
                'id' => 'ps_rest'
            ],
            'options' => [
                'label' => 'Количество',
            ],
        ]);
        
        $this->add([           
            'type'  => 'text',
            'name' => 'unit',
            'attributes' => [
                'id' => 'ps_rest'
            ],
            'options' => [
                'label' => 'Единица измерения',
            ],
        ]);
        
        // Add "status" field
        $this->add([            
            'type'  => 'select',
            'name' => 'status',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    1 => 'Active',
                    2 => 'Retired',                    
                ]
            ],
        ]);
        
                
        // Добавляем кнопку отправки формы
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Сохранить',
                'id' => 'supplier_submitbutton',
            ],
        ]);        
    }
    
   /**
     * Этот метод создает фильтр входных данных (используется для фильтрации/валидации).
     */
    private function addInputFilter() 
    {
        
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        $inputFilter->add([
                'name'     => 'name',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 1024
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'article',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'iid',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'producer',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'country',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'title',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'description',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'image',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'price',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'currency',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'rate',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'rest',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'unit',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 11
                        ],
                    ],
                ],
            ]);
        
        // Add input for "status" field
        $inputFilter->add([
                'name'     => 'status',
                'required' => true,
                'filters'  => [                    
                    ['name' => 'ToInt'],
                ],                
                'validators' => [
                    ['name'=>'InArray', 'options'=>['haystack'=>[1, 2]]]
                ],
            ]); 
        
        
    }    
}
