<?php

/**
 * class CopyCommand
 */
class CopyCommand extends Command
{

    /**
     * @return bool
     */
    public function execute(): bool
    {
        $this->selectedTextToClipboard();
        return false;
    }

    /**
     * @return void
     */
    public function undo(){}
}
