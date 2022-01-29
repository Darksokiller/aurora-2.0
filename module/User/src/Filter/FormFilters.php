<?php
namespace User\Filter;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\StringLength;
use Laminas\Validator\Db\NoRecordExists;
use User\Filter\PasswordFilter;
use Laminas\Filter\StringToLower;
use Laminas\Validator\Identical;

class FormFilters
{
    protected $inputFilter;

    /**
     */
    public function __construct($dbAdapter = null, $tablegateway = null)
    {
        $this->dbAdapter = $dbAdapter;
        $this->table = $tablegateway;
    }
    public function getLoginFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }
        
        $inputFilter = new InputFilter();
        
        $inputFilter->add([
            'name' => 'userName',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name' => 'password',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);
        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }
        
        $inputFilter = new InputFilter();
        
        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'userName',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                    'name' => NoRecordExists::class,
                    'options' => [
                        'table' => $this->table->getTable(),
                        'field' => 'userName',
                        'dbAdapter' => $this->table->getAdapter(),
                    ],
                ],
                
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                    'name' => NoRecordExists::class,
                    'options' => [
                        'table' => $this->table->getTable(),
                        'field' => 'email',
                        'dbAdapter' => $this->table->getAdapter(),
                    	'messages' => [
                    			\Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Sorry, that email is already in use!!',
                    	],
                    ],
                ],
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'password',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
                ['name' => PasswordFilter::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'conf_password',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                    'name' => Identical::class,
                    'options' => [
                        'token' => 'password',
                        'messages' => [
                            \Laminas\Validator\Identical::NOT_SAME => 'Passwords are not the same',
                        ],
                    ],
                ],
            ],
        ]);
        
        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
    public function getEditUserFilter($table, $userId)
    {
        $this->table = $table;
        if ($this->inputFilter) {
            return $this->inputFilter;
        }
        
        $inputFilter = new InputFilter();
        
        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
        $inputFilter->add([
            'name' => 'email',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                    'name' => NoRecordExists::class,
                    'options' => [
                        'table' => $this->table->getTable(),
                        'field' => 'email',
                        'exclude' => [
                            'field' => 'id',
                            'value' => $userId,
                        ],
                        'dbAdapter' => $this->table->getAdapter(),
                    ],
                ],
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'password',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
                ['name' => PasswordFilter::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'conf_password',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                    'name' => Identical::class,
                    'options' => [
                        'token' => 'password',
                        'messages' => [
                            \Laminas\Validator\Identical::NOT_SAME => 'Passwords are not the same',
                        ],
                    ],
                ],
            ],
        ]);
        
        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}

