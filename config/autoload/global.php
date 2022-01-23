<?php
use Laminas\Session;
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    'session' => [
        'validators' => [
        ],
    ],
    'session_config' => [
        'config_class' => Application\Session\Service\Config::class,
    ],
    'session_storage' => [
        'type' => \Laminas\Session\Storage\SessionArrayStorage::class,
    ],
    'session_validators' => [
                \Laminas\Session\Validator\RemoteAddr::class,
                \Laminas\Session\Validator\HttpUserAgent::class,
    ],
];
