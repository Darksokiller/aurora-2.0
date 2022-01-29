<?php

namespace Application\Form;

use Laminas\Form\Element\Email;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Validator\StringLength;
use Laminas\InputFilter\InputFilter;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\StringToLower;

use Laminas\Form\Form;


class ContactForm extends Form
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name);
        parent::setOptions($options);

        $this->add([
            'name' => 'fullName',
            'type' => 'text',
            'options' => [
                'label' => 'Full Name',
            ]
        ]);
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email',
            ]
        ]);
        $this->add([
            'name' => 'message',
            'type' => 'textarea',
            'options' => [
                'label' => 'Message',
            ]
        ]);
        if ($this->options['enableCaptcha']) {
            $this->add([
                'name' => 'captcha',
                'type' => 'captcha',
                'options' => [
                    'label' => 'Rewrite Captcha text:',
                    'captcha' => new \Laminas\Captcha\Image([
                        'name' => 'myCaptcha',
                        'messages' => [
                            'badCaptcha' => 'incorrectly rewritten image text',
                        ],
                        'wordLen' => 5,
                        'timeout' => 300,
                        'font' => $_SERVER['DOCUMENT_ROOT'] . '/fonts/arbli.ttf',
                        'imgDir' => $_SERVER['DOCUMENT_ROOT'] . '/modules/app/captcha/',
                        'imgUrl' => '/modules/app/captcha/',
                        'lineNoiseLevel' => 4,
                        'width' => 200,
                        'height' => 70
                    ]),
                ]
            ]);
        }
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id' => 'submitbutton'
            ]
        ]);
    }
    public function addInputFilter()
    {
        $filter = new InputFilter();
        $filter->add([
            'name' => 'fullName',
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
        $filter->add([
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
                ],
            ],
        ]);
        $filter->add([
            'name' => 'message',
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
                        'max' => 2000,
                    ],
                ],
            ],
        ]);
        return $filter;
    }
}
