<?php

/**
 * class File
 */
class File
{
    public string $content;
    public string $filename;
    public TextField $textField;

    /**
     * @param string $filename
     * @param string $content
     */
    public function __construct(string $filename, string $content)
    {
        $this->content = $content;
        $this->filename = $filename;
        $this->textField = new TextField();
    }

    /**
     * @return bool|string
     */
    public function getText(): bool|string
    {
        return file_get_contents($this->filename);
    }

    /**
     * @param $start
     * @param $end
     * @return void
     */
    public function getSelectedText($start, $end){
        $this->textField->setAllCoords($start, $end);
    }
}
