<?php
namespace User\Filter;
use Laminas\Filter\AbstractFilter;

class RoleFilter extends AbstractFilter
{
    public function filter($value)
    {
        switch ($value) {
            case 'superAdmin':
                return 'admin';
                break;
                default:
                    return $value;
                    break;
        }
    }
}