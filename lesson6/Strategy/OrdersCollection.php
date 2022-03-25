<?php

/**
 * class OrdersCollection
 */
class OrdersCollection
{
    protected $orders;
    protected $buyerPhone = 123;

    /**
     * @param $orders
     */
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return void
     */
    public function renderOrders(){}

    /**
     * @return float
     */
    public function getTotalPrice(): float
    {
        return 99.99;
    }

    /**
     * @param IPayMethod $method
     * @return mixed|string
     */
    public function pay(IPayMethod $method){
        if($method->requestPayment($this->getTotalPrice())){
            return $method->responsePayment($this->buyerPhone);
        };
        return 'Оплата не прошла';
    }
}
