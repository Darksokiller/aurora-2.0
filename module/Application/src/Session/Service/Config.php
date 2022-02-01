<?php
namespace Application\Session\Service;

use Laminas\Session\Config\SessionConfig;

class Config extends SessionConfig
{
    protected $name = '_AURORA';
    //protected $rememberMeSeconds = 86400;
    protected $useCookies = true;
    protected $options = [
    ];
    //protected $cookieLifetime = 86400;
    public function __construct() {
        //die('seccion config class is loading');
        $this->setOptions($this->options);
    }
    public function setOptions($options)
    {
        parent::setOptions($options);
        return $this;
    }
}