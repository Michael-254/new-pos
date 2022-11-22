<?php
namespace Vancha\Mpesa\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class Mpesa
 * @package Vancha\Mpesa\Facade
 */
class Mpesa extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Mpesa';
    }


}