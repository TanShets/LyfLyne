<?php
    function encode($word){
        if(strlen($word) > 15)
            return null;
        $enc_word = "";
        $isNumber = null;
        for($i = 0; $i < strlen($word); $i++){
            if(is_numeric($word[$i])){
                $val = ord($word[$i]) - ord('0');
                $x = chr(ord('A') - $val);
                $y = chr(ord('a') + 2 * $val);
                $z = chr(40 + $val / 2);
                $isNumber = true;
            }
            elseif(ctype_alpha($word[$i])){
                $val = ord($word[$i]) - ord('A');
                $x = chr(40 + $val);
                $y = chr(ord('a') - $val / 2);
                $z = chr(ord('0') + $val / 3);
                $isNumber = false;
            }
            else{
                return null;
            }

            $enc_word = $enc_word.$x.$y.$z;
            if($isNumber)
                $enc_word = $enc_word.'~';
        }
        return $enc_word;
    }

    function encode_level_7($word){
        $level7Ops = array(
            array(87, 40, 15, 60, 23, 38, 67),
            array(5, -5, 57, 25, 62, -3, 50),
            array(7, 20, -15, -23, -10, 22, -17),
            array(-39, -6, -18, -56, -32, -37, -28)
        );

        $level7_mod1 = 10;
        $level7_mod2 = 7;

        //$temp_word = detail.toCharArray();
        $encoded_word = "";
        $length = count($level7Ops[0]);
        $mod1 = $level7_mod1;
        $mod2 = $level7_mod2;

        for($i = 0; $i < strlen($word); $i++){
            //echo $word[$i]."<br>";
            if($word[$i] >= ' ' && $word[$i] < ')'){
                $index = 0;
                $encoded_word .= "&";
            }
            else if($word[$i] >= ')' && $word[$i] < 'A'){
                $index = 1;
                $encoded_word .= "?";
            }
            else if($word[$i] >= 'A' && $word[$i] < 'a'){
                $index = 2;
                $encoded_word .= "^";
            }
            else if($word[$i] >= 'a' && $word[$i] <= '~'){
                $index = 3;
                $encoded_word .= "~";
            }
            else{
                return null;
            }

            $offset = ((($i + 1) * $length) % $mod1) % $mod2;
            //echo $word[$i].": ";
            $temp_wordx = "";
            for($j = 0; $j < $length; $j++){
                $temp = ord($word[$i]) + $level7Ops[$index][($j + $offset) % $length];
                //$temp_wordx = $temp_wordx.chr($temp);
                $encoded_word .= chr($temp);
                //echo $encoded_word."<br>";
            }
            //$encoded_word .= $temp_wordx;
            //echo $encoded_word."<br>";
        }
        return $encoded_word;
    }

    function decode_level_7($word){
        $level7Ops = array(
            array(87, 40, 15, 60, 23, 38, 67),
            array(5, -5, 57, 25, 62, -3, 50),
            array(7, 20, -15, -23, -10, 22, -17),
            array(-39, -6, -18, -56, -32, -37, -28)
        );

        $level7_mod1 = 10;
        $level7_mod2 = 7;
        
        $length = count($level7Ops[0]);
        $decoded_word = "";
        $mod1 = $level7_mod1;
        $mod2 = $level7_mod2;

        for($i = 0; $i < strlen($word); $i += $length + 1){
            switch($word[$i]){
                case '&':{
                    $index = 0;
                    break;
                }
                case '?':{
                    $index = 1;
                    break;
                }
                case '^':{
                    $index = 2;
                    break;
                }
                case '~':{
                    $index = 3;
                    break;
                }
                default:
                    return null;
            }
            $offset = (((($i / ($length + 1)) + 1) * $length) % $mod1) % $mod2;
            $temp = ord($word[$i + (($length - $offset) % $length) + 1]) - $level7Ops[$index][0];
            $decoded_word .= chr($temp);
        }
        return $decoded_word;
    }

    function decode($word){
        $final_word = "";
        $length = strlen($word);
        for($i = 0; $i < $length; $i += 3){
            if($i < $length - 3 && $word[$i + 3] == '~'){
                $val = ord('A') - ord($word[$i]);
                $x = chr($val + ord('0'));
                $i++;
            }
            else{
                $val = ord($word[$i]) - 40;
                $x = chr($val + ord('A'));
            }
            $final_word = $final_word.$x;
        }
        return $final_word;
    }

    function print_word(){
        echo "x";
    }

    function generateOTP(){
        $size = 6;

        $otp = "";
        for($i = 0; $i < $size; $i++){
            $num = chr(rand(0, 100) % 10 + ord('0'));
            $otp = $otp.$num;
        }

        return $otp;
    }
?>