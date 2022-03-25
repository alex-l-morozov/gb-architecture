<?php

/**
 * class YandexMoneyStrategy
 */
class YandexMoneyStrategy implements IPayMethod
{
    /**
     * @param float $price
     * @return bool
     */
    public function requestPayment(float $price): bool
    {
        if (true){
            echo 'Оплата через ' . __CLASS__ . ' суммы ' . $price . PHP_EOL;
            echo 'Оплата прошла успешно' . PHP_EOL;
            return true;
        }
        return false;

    }

    /**
     * @param $phone
     * @return string
     */
    public function responsePayment($phone): string
    {
        return 'Заказ успешно оплачен отправить на номер ' . $phone;
    }
}
