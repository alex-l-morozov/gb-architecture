<?php
/*
 * 1. Создать массив на миллион элементов и отсортировать его различными способами. Сравнить скорости.
 *
 * Результат:
 * Сортировка пузырьком: 277.85009694099
 * Сортировка вставками: 698.00082087517
 * Сортировка расческой: 0.52814698219299
 * Сортировка слиянием: 2.5609660148621
 * Сортировка пирамидальная: 0.48247694969177
 * Сортировка пирамидальная SPL: 0.072990894317627
 * Сортировка быстрая наша: 0.17566204071045
 * Сортировка быстрая чужая: 0.10706496238708
 * Сортировка из PHP: 0.022159099578857
 * Сортировка выбором: 274.53276920319
 * Сортировка голубиная: 0.052000999450684
 *
 *
 * 2. Реализовать удаление элемента массива по его значению. Обратите внимание на возможные дубликаты!
 *
 */
const COUNT = 1000;
const MIN_RAND = 1;
const MAX_RAND = 300;

function _randArray($count = COUNT, $minRand = MIN_RAND, $maxRand = MAX_RAND)
{
    if ($count != COUNT && $count > $maxRand - $minRand) {
        $minRand = 1;
        $maxRand = $count * 3;
    }
    $myArray = [];
    for ($i = 0; $i < $count; $i++) {
        $num = mt_rand($minRand, $maxRand);
        $myArray[] = $num;
    }
    return $myArray;
}

function getArr(): array
{
    return _randArray(1000);
}

$arr = getArr();
$start = microtime(true);
$sortArr = sort($arr);
echo "Сортировка из PHP: ".( microtime(true) - $start) . PHP_EOL;

function binarySearch($myArray, $num)
{
    $start = 0;
    $end = count($myArray) - 1;
    $n = 0;

    while ($start <= $end) {
        $n++;

        $base = floor(($start + $end) / 2);


        echo "Проверяется элемент с индексом: $base" . PHP_EOL;

        if ($myArray[$base] == $num) {
            echo "Количество итераций: $n искомого числа $num под индексом $base" . PHP_EOL;
            unset($myArray[$base]);
            return $base;

        } elseif ($myArray[$base] < $num) {
            $start = $base + 1;
        } else {
            $end = $base - 1;
        }
    }
    echo "ЧИСЛО НЕ НАЙДЕНО! Количество итераций: $n -- $myArray[$base] -- $num" . PHP_EOL;
    return null;
}
$num = 2;
binarySearch($arr,  $num);
$test = count($arr);
echo $test;
echo 'Исходный массив ' .print_r($arr, true) . PHP_EOL;

foreach ($arr as $key) {
    $delNumber = array_search($num, $arr);
    if ($delNumber !== false) {
        unset($arr[$delNumber]);
    }
}
echo ' Массив с удаленным значением ' .print_r($arr, true) . PHP_EOL;
/*
 * 3. Подсчитать практически количество шагов при поиске описанными в методичке алгоритмами. (делаем для массивов со 100, 100000, и 1000000 элементов)
 *
 * Линейный поиск в худшем случае переберёт весь массив. То есть выполнит количество шагов равное длине массива. Если реализован выход после того как найдётся нужный элемент, то в среднем будет выполнено количество шагов равное половине длины массива.
 * Бинарный поиск сделает log n шагов, где n - длина массива.
 * Интерполяционный поиск сделает log (log n).
 */
