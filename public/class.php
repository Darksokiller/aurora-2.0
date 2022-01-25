<?php
// class Test
// {
//     public $someString;
//     public function __construct($string)
//     {
//         $array = ['one', 'two', 'three', 'four'];
//         foreach($array as $value) {
//             $newVar = $value;
//         }
//         $this->someString = $string;
//     }
// }
// $object = new Test('Some string');


// class User
// {
//     public $name;
//     public $userName;
//     public $age;
//     public $email;

//     public function __construct()
//     {
//         $data = [$name = 'Evan Smith', $userName = 'Fire1659', $email = 'eevansocom@gmail.com', $age ='22'];
//         foreach($data as $value) {
//             $newVar = $value;
//         }
//     }
// }

// $evanObject = new User();

class basket
{
    public $itemsTotal;
    public $shippingCost;
    public $discount;

    public function calculateSubTotal() {
       $subTotal = $this->itemsTotal + $this->shippingCost - $this->discount;

       return $subTotal;
    }
}

$basket = new basket();

$basket->itemsTotal = 50;
$basket->shippingCost = 10;
$basket->discount = 30;

$subTotal = $basket->calculateSubTotal();