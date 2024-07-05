<?php
function convertNumberToWord($num = false) {
    $num = str_replace(array(',', ' '), '' , trim($num));
    if(! $num) {
        return false;
    }
    $num = (int) $num;
    $words = array();
    $list1 = array('', 'bir', 'ikki', 'uch', 'to\'rt', 'besh', 'olti', 'yetti', 'sakkiz', 'to\'qqiz', 'o\'n', 'o\'n bir',
        'o\'n ikki', 'o\'n uch', 'o\'n to\'rt', 'o\'n besh', 'o\'n olti', 'o\'n etti', 'o\'n sakkiz', 'o\'n to\'qqiz'
    );
    $list2 = array('', 'o\'n', 'yigirma', 'o\'ttiz', 'qirq', 'ellik', 'oltmish', 'yetmish', 'sakson', 'to\'qson', 'yuz');
    $list3 = array('', 'ming', 'million', 'milliard', 'trillion', 'kvadrillion', 'kvintillion', 'sekstilion', 'septillion',
        'oktilion', 'nonillion', 'decillion', 'undecillion', 'duodilion', 'tredesilion', 'kvattuordesilion',
        'kvindesilyon', 'sexdecillion', 'septendesilion', 'oktodesilyon', 'novemdecillion', 'vigintilion'
    );
    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' yuz' . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ( $tens < 20 ) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
    return implode(' ', $words);
}

function num_to_text($num) {
    $text = convertNumberToWord($num);

    $num_arr = explode(".", $num);
    $fraction = $num_arr[1];

	if ($fraction) {
        if ($fraction == 5) {
            $text .= " yarim";
        } else {
            $yuzlar = "";
            for ($x = 0; $x < strlen($fraction); $x++) {
                $yuzlar .= "0";
            }
            $test = str_replace("bir ", "", convertNumberToWord("1$yuzlar"));
            $text .= " butun $test dan " . convertNumberToWord($fraction);
        }
    }

    return $text;
}
?>