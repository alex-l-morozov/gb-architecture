<?php

/**
 * Class DeveloperObserver
 */
class DeveloperObserver implements SplObserver
{
    private string $name;
    public string $language;

    /**
     * @param string $name
     * @param string $language
     */
    public function __construct(string $name, string $language)
    {
        $this->name = $name;
        $this->language = $language;
    }

    /**
     * @param SplSubject $subject
     * @return void
     */
    public function update(SplSubject $subject)
    {
        echo "Оповестить $this->name $this->language подписчика о новой вакансии " .
            $subject->lastAddedJob->name . PHP_EOL;
    }
}
