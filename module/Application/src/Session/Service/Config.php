<?php
namespace Application\Session\Service;

use Laminas\Session\Config\SessionConfig;

class Config extends SessionConfig
{
    protected $name = '_AURORA';
    protected $rememberMeSeconds = 300;
    public function __construct() {
        //die('seccion config class is loading');

    }
    public function setOptions($options)
    {
        return $this;
    }
}