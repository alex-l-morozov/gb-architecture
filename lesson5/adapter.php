<?php
/**
 * Interface ISquare
 */
interface ISquare
{
    /**
     * @param int $sideSquare
     * @return mixed
     */
    function squareArea(int $sideSquare);
}

/**
 * Interface ICircle
 */
interface ICircle
{
    /**
     * @param int $circumference
     * @return mixed
     */
    function circleArea(int $circumference);
}

/**
 * Class CircleAreaLib
 */
class CircleAreaLib
{
    /**
     * @param int $diagonal
     * @return float|int
     */
    public function getCircleArea(int $diagonal)
    {
        $area = (M_PI * $diagonal**2)/4;

       return $area;
   }
}

/**
 * Class SquareAreaLib
 */
class SquareAreaLib
{
    /**
     * @param int $diagonal
     * @return float|int
     */
    public function getSquareArea(int $diagonal)
    {
        $area = ($diagonal**2)/2;

        return $area;
    }
}

/**
 * Class SquareAdapter
 */
class SquareAdapter implements ISquare
{
    /**
     * @var SquareAreaLib
     */
    private SquareAreaLib  $SquareAreaLib;
    /**
     * @var float
     */
    public float $area;

    /**
     * @param int $sideSquare
     * @return mixed|void
     */
    public function squareArea(int $sideSquare)
    {
        $this->area = $this->SquareAreaLib->getSquareArea(round($sideSquare * M_SQRT2));
    }
}

/**
 * Class CircleAdapter
 */
class CircleAdapter implements ICircle
{
    /**
     * @var CircleAreaLib
     */
    private CircleAreaLib  $CircleAreaLib;
    /**
     * @var float
     */
    public float $area;

    /**
     * @param int $circumference
     * @return mixed|void
     */
    public function circleArea(int $circumference)
    {
        $this->area = $this->CircleAreaLib->getCircleArea(round($circumference/M_PI));
    }
}