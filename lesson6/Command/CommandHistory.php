<?php

/**
 * class CommandHistory
 */
class CommandHistory
{
    private array $history;

    /**
     * @param Command $command
     * @return void
     */
    public function push(Command $command){
        $this->history[] = $command;
    }

    /**
     * @return Command
     */
    public function pop() : Command
    {
        return array_pop($this->history);
    }
}
