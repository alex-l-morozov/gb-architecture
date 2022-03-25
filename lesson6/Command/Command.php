<?php

/**
 * abstract class Command
 */
abstract class Command
{
    protected  Editor $editor;
    public File $file;
    protected  string $backup;

    /**
     * @param Editor $editor
     */
    public function __construct(Editor $editor)
    {
        $this->editor = $editor;
        $this->file = $editor->file;
    }

    /**
     * @return void
     */
    protected function saveBackup(){
        $this->backup = $this->editor->getAllText();
    }

    /**
     * @return string
     */
    protected function selectedTextToClipboard() : string
    {
        return $this->editor->clipboard = substr($this->file->content,
            $this->file->textField->start,
            $this->file->textField->getLength());
    }

    /**
     * @param string $text
     * @return string
     */
    protected function deleteSelectedTextFromFileByLengthText(string $text) : string
    {
        return $this->file->content = substr_replace(
            $this->file->content,
            '',
            $this->file->textField->start,
            strlen($text)
        );
    }

    /**
     * @param string $text
     * @return string
     */
    protected function pasteInnerTextToFile(string $text) : string {
        return $this->file->content = substr_replace(
            $this->file->content,
            $text,
            $this->file->textField->start,
            0);
    }

    abstract public function undo();
    abstract public function execute();
}
