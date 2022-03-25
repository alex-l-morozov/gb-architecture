<?php

/**
 * class PasteCommand
 */
class PasteCommand extends Command
{
    protected string $insertedText;

    /**
     * @return bool
     */
    public function execute(): bool
    {
        if (empty($this->editor->clipboard))
        {
            return false;
        }
        ($this->backup)??$this->saveBackup();
        $this->insertedText = $this->editor->clipboard;
        $this->pasteInnerTextToFile($this->insertedText);
        return true;
    }

    /**
     * @return void
     */
    public function undo()
    {
        $this->deleteSelectedTextFromFileByLengthText($this->insertedText);
    }
}
