<?php
namespace User\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Password;
use Laminas\Validator\Identical;
use Laminas\Validator\Db\RecordExists;
use Laminas\Form\Form;
use Laminas\InputFilter\Input;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use User\Filter\PasswordFilter;
use Laminas\Filter\StringToLower;
use Laminas\Validator\StringLength;
use Laminas\Validator\Db\NoRecordExists;

class ResetPassword extends Form
{
    public function __construct($name, $options)
    {
        parent::__construct('reset_password');
        parent::setOptions($options);
        $this->table = $this->options['db'];

        $this->add([
            'name' => 'resetTimeStamp',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'validationTimeStamp',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'email',
            'type' => 'text',
            'options' => [
                'label' => 'Email'
            ]
        ]);
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password'
            ]
        ]);
        $this->add([
            'name' => 'conf_password',
            'type' => 'password',
            'options' => [
                'label' => 'Confirm Password'
            ]
        ]);
        if ($this->options['enableCaptcha']) {
            $this->add([
                'name' => 'captcha',
                'type' => \Laminas\Form\Element\Captcha::class,
                'options' => [
                    'label' => 'Rewrite Captcha text:',
                    'captcha' => new \Laminas\Captcha\Image([
                        'name' => 'myCaptcha',
                        'messages' => [
                            'badCaptcha' => 'incorrectly rewritten image text'
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
        $inputFilter = new InputFilter();

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
                    'name' => RecordExists::class,
                    'options' => [
                        'table' => $this->table->getTable(),
                        'field' => 'email',
                        'dbAdapter' => $this->table->getAdapter(),
                        'messages' => [
                            \Laminas\Validator\Db\RecordExists::ERROR_NO_RECORD_FOUND => 'Email not found!!',
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
        return $inputFilter;
    }
}
