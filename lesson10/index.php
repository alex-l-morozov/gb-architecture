<?php

$str = "(1+42)^2+7*3-2"; // Выражение для примера
echo arithmetic_to_tree($str); // 1 42 + 2 ^ 7 3 * + 2 -

function arithmetic_to_tree($str): string
{
    $stack = array();
    $out = array();

    $prior = array(
        "^" => array("prior" => "4", "assoc" => "right"),
        "*" => array("prior" => "3", "assoc" => "left"),
        "/" => array("prior" => "3", "assoc" => "left"),
        "+" => array("prior" => "2", "assoc" => "left"),
        "-" => array("prior" => "2", "assoc" => "left"),
    );

    $token = preg_replace("/\s/", "", $str);
    $token = str_replace(",", ".", $token);
    $token = str_split($token);

    if (preg_match("/[\+\-\*\/\^]/", $token['0'])) {
        array_unshift($token, "0");
    }

    $last_num = true;
    foreach ($token as $key => $value) {

        if (preg_match("/[\+\-\*\/\^]/", $value)) {
            $endop = false;

            while ($endop != true) {
                $lastop = array_pop($stack);
                if ($lastop == "") {
                    $stack[] = $value;
                    $endop = true;
                } else {
                    /* Получим приоритет */
                    $curr_prior = $prior[$value]['prior'];
                    $curr_assoc = $prior[$value]['assoc'];

                    $prev_prior = $prior[$lastop]['prior'];

                    switch ($curr_assoc) {
                        case "left":

                            switch ($curr_prior) {
                                case ($curr_prior > $prev_prior):
                                    $stack[] = $lastop;
                                    $stack[] = $value;
                                    $endop = true;
                                    break;

                                case ($curr_prior <= $prev_prior):
                                    $out[] = $lastop;
                                    break;
                            }

                            break;

                        case "right":

                            switch ($curr_prior) {
                                case ($curr_prior >= $prev_prior):
                                    $stack[] = $lastop;
                                    $stack[] = $value;
                                    $endop = true;
                                    break;

                                case ($curr_prior < $prev_prior):
                                    $out[] = $lastop;
                                    break;
                            }

                            break;

                    }

                }
            }
            $last_num = false;
        } elseif (preg_match("/[0-9\.]/", $value)) {
            if ($last_num == true) {
                $num = array_pop($out);
                $out[] = $num . $value;
            } else {
                $out[] = $value;
                $last_num = true;
            }
        } elseif ($value == "(") {
            $stack[] = $value;
            $last_num = false;
        } elseif ($value == ")") {
            $bracket = false;
            while ($bracket != true) {
                $op = array_pop($stack);

                if ($op == "(") {
                    $bracket = true;
                } else {
                    $out[] = $op;
                }


            }

            $last_num = false;
        }

    }

    $stack1 = $stack;
    $rpn = $out;

    while ($stack_el = array_pop($stack1)) {
        $rpn[] = $stack_el;
    }

    $rpn_str = implode(" ", $rpn);

    echo "<pre>";
    print_r($rpn);
    print_r($out);
    print_r($stack);
    echo "</pre>";

    return $rpn_str;
}