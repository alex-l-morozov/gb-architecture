<?php

/**
 * class WebMoneyStrategy
 */
class WebMoneyStrategy implements IPayMethod
{
    /**
     * @param float $price
     * @return bool
     */
    public function requestPayment(float $price)
    {
        return true;
    }

    /**
     * @param $phone
     * @return string
     */
    public function responsePayment($phone): string
    {
        return 'Send message ' . $phone;
    }
}
