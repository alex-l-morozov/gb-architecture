<?php

/**
 * interface IPayMethod
 */
interface IPayMethod
{
    /**
     * @param float $price
     * @return mixed
     */
    public function requestPayment(float $price);

    /**
     * @param $phone
     * @return mixed
     */
    public function responsePayment($phone);
}
