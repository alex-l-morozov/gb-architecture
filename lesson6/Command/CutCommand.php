<?php

/**
 * class CutCommand
 */
class CutCommand extends Command
{
    protected string $removedText;

    /**
     * @return bool
     */
    public function execute(): bool
    {
        ($this->backup)??$this->saveBackup();
        $this->removedText = $this->selectedTextToClipboard();
        $this->deleteSelectedTextFromFileByLengthText($this->removedText);
        return true;
    }

    /**
     * @return void
     */
    public function undo()
    {
        $this->pasteInnerTextToFile($this->removedText);
    }
}
