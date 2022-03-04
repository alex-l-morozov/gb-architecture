<?php

/**
 * Interface INotification
 */
interface INotification
{
    /**
     * @param string $text
     * @return string
     */
    public function send(string $text) : string;
}

/**
 * Class Notification
 */
class Notification implements INotification
{
    /**
     * @param string $text
     * @return string
     */
    public function send(string $text): string
    {
        return 'Notification method send : ' . $text;
    }
}

/**
 * Class EmailNotification
 */
class EmailNotification extends Notification
{
    /**
     * @var Notification|INotification
     */
    protected Notification $notification;

    /**
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @param string $text
     * @return string
     */
    public function send(string $text): string
    {
        echo 'Email send : ' . $text . PHP_EOL;
        return $this->notification->send($text);
    }
}

/**
 * Class SmsNotification
 */
class SmsNotification extends Notification
{
    /**
     * @var Notification|INotification
     */
    protected Notification $notification;

    /**
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @param string $text
     * @return string
     */
    public function send(string $text): string
    {
        echo 'SMS send : ' . $text . PHP_EOL;
        return $this->notification->send($text);
    }
}