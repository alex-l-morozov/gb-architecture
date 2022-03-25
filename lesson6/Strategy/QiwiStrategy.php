<?php

/**
 * class QiwiStrategy
 */
class QiwiStrategy implements IPayMethod
{
    /**
     * @param float $price
     * @return bool
     */
    public function requestPayment(float $price): bool
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
