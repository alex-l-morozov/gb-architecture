<?php
spl_autoload_register(function ($classname){
   require_once $classname . '.php';
});

$orders = [];

$orders[] = new Order();
$orders[] = new Order();

$collection = new OrdersCollection($orders);
$paymentMethod = (new PayMethodsCollection())->getPaymentMethod('WebMoney');
echo $collection->pay($paymentMethod);
