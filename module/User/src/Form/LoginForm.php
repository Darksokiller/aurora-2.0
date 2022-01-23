<?php
namespace User\Form;

use Laminas\Form\Form;
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

class LoginForm extends Form
{

    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('Login');

        $this->add([
            'name' => 'userName',
            'type' => 'text',
            'options' => [
                'label' => 'User Name'
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
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id' => 'submitbutton'
            ]
        ]);
    }
}
