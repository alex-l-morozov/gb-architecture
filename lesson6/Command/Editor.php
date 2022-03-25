<?php

/**
 * class Editor
 */
class Editor
{
    private CommandHistory $history;
    public File $file;
    public  string $clipboard ='';

    /**
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
        $this->history = new CommandHistory();
    }

    /**
     * @return void
     */
    public function copy(){
        $this->executeCommand(new CopyCommand($this));
    }

    /**
     * @return void
     */
    public function cut(){
        $this->executeCommand(new CutCommand($this));
    }

    /**
     * @return void
     */
    public function paste()
    {
        $this->executeCommand(new PasteCommand($this));
    }

    /**
     * @param Command $command
     * @return void
     */
    public function executeCommand(Command $command){
        if ($command->execute()){
            $this->history->push($command);
        }
    }

    /**
     * @return void
     */
    public function undo(){
        $lastCommand = $this->history->pop();
        if (isset($lastCommand)){
            $lastCommand->undo();
        }else {
            echo 'Nothing to undo';
        }
    }

    /**
     * @param string $text
     * @return void
     */
    public function setClipboard(string $text){
        $this->clipboard = $text;
    }

    /**
     * @return string
     */
    public function getAllText(): string
    {
        return $this->file->getText();

    }

    /**
     * @param int $start
     * @param int $end
     * @return void
     */
    public function getSelectedText(int $start, int $end) : void
    {
        $this->file->getSelectedText($start, $end);
    }

    /**
     * @param int $pos
     * @return void
     */
    public function setCarriage(int $pos){
        $this->file->textField->setCarriage($pos);
    }
}
