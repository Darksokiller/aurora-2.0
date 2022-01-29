<?php
namespace User\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\Form\Element\Captcha;

class UserForm extends Form
{
    public function __construct($name = null, array $options = [])
    {
        if (is_array($options) && ! empty($options)) {
            parent::setOptions($options);
            //var_dump($options);
        }
        // We will ignore the name provided to the constructor
        parent::__construct('RegistrationForm');
        $this->add([
            'name' => 'id',
            'type' => 'hidden'
        ]);
        $this->add([
            'name' => 'regDate',
            'type' => 'hidden'
        ]);
        $this->add([
            'name' => 'userName',
            'type' => 'text',
            'options' => [
                'label' => 'User Name'
            ]
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
