<?php

/**
 * class TextField
 */
class TextField
{
    public int $start = 0;
    public int $end = 0;
    protected int $length = 0;

    /**
     * @param $pos
     * @return void
     */
    public function setCarriage($pos){
        $this->start = $pos;
        $this->end = $pos;
        $this->length = 0;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $start
     * @param int $end
     * @return void
     */
    public function setAllCoords(int $start, int $end){
        $this->start = $start;
        $this->end = $end;
        $this->length = $end - $start;
    }
}
