<?php
/**
 * Class Job
 */
class Job
{
    public string $name;
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

}
