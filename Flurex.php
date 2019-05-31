<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     Flurex Encryption System
 * @copyright   2018 - 2019 Podvirnyy Nikita (KRypt0n_)
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @author      Podvirnyy Nikita (KRypt0n_)
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 */

class Flurex
{
    /**
     * Шифрование текста
     * 
     * @param string $text - текст для шифрования
     * @param string $key  - ключ для шифрования
     * 
     * @return string - возвращает зашифрованный текст
     */
    public static function encode ($text, $key)
    {
        $len    = strlen ($text);
        $key    = self::typeString ($key, $len);
        $keylen = sizeof ($key);

        $text = ($key[0] + $key[$keylen - 1]) % 2 == 0 ?
            str_rot13 ($text) : $text;
        
        $text = self::mapMove ($text, $key, 1);

        $post  = rand (1, 99999999) + $keylen;
        $enc[] = self::toStep ($post + ord (($key[0] + $key[$keylen - 1]) % 256));

        for ($i = 0; $i < $len; ++$i)
        {
            if ($key[$i % $keylen] % 2 == 0)
                $text[$i] = ~$text[$i];

            $enc[] = self::toStep (ord ($text[$i] ^ chr (($i + $key[$i] + $post) % 256)) * $key[$i % $keylen] + $post - $i);
        }

        return gzdeflate (implode (' ', $enc), 9);
    }

    /**
     * Расшифрование текста
     * 
     * @param string $text - текст для расшифрования
     * @param string $key  - ключ для расшифрования
     * 
     * @return string - возвращает расшифрованный текст
     */
    public static function decode ($text, $key)
    {
        $text   = explode (' ', gzinflate ($text));
        $len    = sizeof ($text) - 1;
        $key    = self::typeString ($key, $len);
        $keylen = sizeof ($key);

        $post = self::fromStep ($text[0]) - ord (($key[0] + $key[$keylen - 1]) % 256);
        $text = array_slice ($text, 1);

        for ($i = 0; $i < $len; ++$i)
        {
            $dec[] = chr ((self::fromStep ($text[$i]) + $i - $post) / $key[$i % $keylen]) ^ chr (($i + $key[$i] + $post) % 256);

            if ($key[$i % $keylen] % 2 == 0)
                $dec[$i] = ~$dec[$i];
        }

        $dec = self::mapMove (join ($dec), $key, 2);

        return ($key[0] + $key[$keylen - 1]) % 2 == 0 ?
            str_rot13 ($dec) : $dec;
    }

    /**
     * Перевод числа в СС с основанием 94
     * 
     * @param int $dec - число для перевода
     * 
     * @return string - возвращает полученное число
     */
    public static function toStep ($dec)
    {
        $alphabet = range ('!', '~');
        $step = [];

        while ($dec > 0)
        {
            $step[] = $alphabet[$dec % 94];

            $dec = (int)($dec / 94);
        }

        return is_array ($step) ?
            join (array_reverse ($step)) : null;
    }

    /**
     * Перевод числа из СС с основанием 94
     * 
     * @param int $step - число для перевода
     * 
     * @return int - возвращает десятичную запись числа
     */
    public static function fromStep ($step)
    {
        $alphabet = array_flip (range ('!', '~'));
        $len      = strlen ($step);

        $dec = 0;

        for ($i = 0; $i < $len; ++$i)
            $dec += $alphabet[$step[$len - $i - 1]] * pow (94, $i);

        return $dec;
    }

    /**
     * Форматирование строки
     * 
     * @param string $string - строка для форматирования
     * [@param int $len = 0] - длина форматирования
     * 
     * @return array - возвращает массив данных форматирования
     */
    protected static function typeString ($string, $len = 0)
    {
        $len = max (($slen = strlen ($string)), $len);

        for ($i = 0; $i < $len; ++$i)
            $int[] = ord ($string[$i % $slen]) + $i;

        return $int;
    }

    /**
     * Перемещение символов текста
     * 
     * @param string|array $text - символы для перемещения
     * @param array $map - карта перестановок
     * [@param int $mode = 1] - режим перестановки
     * 
     * @return string|array - возвращает перемешанный текст
     */
    protected static function mapMove ($text, $map, $mode = 1)
    {
        $len  = strlen ($text);
        $size = sizeof ($map);

        foreach ($map as $id => $way)
            if ($mode == 1)
                self::swap ($text, $way % $len, abs ($len - $id - $way) % $len);

            else self::swap ($text, abs ($len - ($size - $id - 1) - $map[$size - $id - 1]) % $len, $map[$size - $id - 1] % $len);

        return $text;
    }

    /**
     * Перестановка местами элементов массива
     * 
     * @param array &$array - массив для перестановки
     * @param mixed $a - индекс первого элемента
     * @param mixed $b - индекс второго элемента
     */
    protected static function swap (&$array, $a, $b)
    {
        $temp = $array[$a];

        $array[$a] = $array[$b];
        $array[$b] = $temp;
    }
}
