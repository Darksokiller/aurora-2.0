<?php
namespace User\Filter;
use Laminas\Filter\FilterInterface;
class RegistrationHash implements FilterInterface
{
    /**
     * 
     * @param array $value ['email' => $email, 'timestamp' => $timestamp]
     * @return mixed 
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            // eventually do some stuff here
        }
        if(isset($value['email']) && $value['timestamp']) {
        	return password_hash($value['email'] . $value['timestamp'], PASSWORD_DEFAULT);
        }
    }
}

