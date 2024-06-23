<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\adminlte4\helpers;

use Exception;
use Ramsey\Uuid\Uuid;
use yii\base\InvalidArgumentException;
use yii\helpers\StringHelper as YiiStringHelper;
use Yii;

/**
 * Class StringHelper
 * @package davidxu\adminlte4\helpers
 */
class StringHelper extends YiiStringHelper
{
    /**
     * Generate a random string
     *
     * @param int $length
     * @param bool $numeric
     * @return string
     * @throws Exception
     */
    public static function random(int $length, bool $numeric = false): string
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));

        $hash = '';
        if (!$numeric) {
            $hash = chr(random_int(1, 26) + random_int(0, 1) * 32 + 64);
            $length--;
        }

        $max = strlen($seed) - 1;
        $seed = str_split($seed);
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed[random_int(0, $max)];
        }

        return $hash;
    }

    /**
     * Generate a number random string
     *
     * @param string $prefix Prefix for generated number
     * @param int $length Generated number length
     * @return string
     */
    public static function randomNum(string $prefix = '', int $length = 8): string
    {
        $str = $prefix ?? '';
        return $str . substr(implode(null,
                array_map('ord', str_split(substr(uniqid('', true), 7, 13)))),
                0, $length);
    }

    /**
     * @param string|null $type
     * @param string $name
     * @return string
     * @throws Exception
     */
    protected static function uuid(?string $type = null, string $name = 'php.net'): string
    {
        $uuid = match ($type) {
            'time' => Uuid::uuid1(),
            'md5' => Uuid::uuid3(Uuid::NAMESPACE_DNS, $name),
            'random' => Uuid::uuid4(),
            'sha1' => Uuid::uuid5(Uuid::NAMESPACE_DNS, $name),
            default => md5(uniqid(md5(microtime(true) . random_bytes(8)), true)),
        };
        return is_string($uuid) ? $uuid : $uuid->toString();
    }

    /**
     * Paras Enum attributes
     *
     * Input format a:des1,b:des2
     *
     * @param $string
     * @return array
     */
    public static function parseEnumAttr($string): array
    {
        $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
        if (strpos($string, ':')) {
            $value = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }

        return $value;
    }

    /**
     * Generates a random string of specified length.
     * The string generated matches [A-Za-z0-9_]+ and is transparent to URL-encoding.
     *
     * @param int $length the length of the key in characters
     * @return string the generated random key
     * @throws \yii\base\Exception on failure.
     */
    public static function generateRandomString(int $length = 32): string
    {
        if (!is_int($length)) {
            throw new InvalidArgumentException('First parameter ($length) must be an integer');
        }

        if ($length < 1) {
            throw new InvalidArgumentException('First parameter ($length) must be greater than 0');
        }

        $bytes = Yii::$app->security->generateRandomKey($length);
        return strtolower(substr(strtr(base64_encode($bytes), '+/', '__'), 0, $length));
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generatePureString(int $length = 32): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $maxPos = strlen($chars);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[intval(mt_rand() / mt_getrandmax() * $maxPos)];
    }
        return $str;
    }

    /**
     * @param string $name
     * @return string
     */
    public static function getInputId(string $name): string
    {
        return HtmlHelper::getInputIdByName($name);
    }

    /**
     * @param int|string $size
     * @return float|int
     */
    public static function getSizeInByte(int|string $size): float|int
    {
        preg_match('/([0-9]+)/', $size, $matches);
        $number = $matches ? $matches[0] : 0;
        preg_match('/([a-zA-Z])/', $size, $matches);
        $unit = $matches ? $matches[0] : '';
        $unit = strtoupper($unit);
        return match ($unit) {
            'K' => $number * 1024,
            'M' => $number * pow(1024, 2),
            'G' => $number * pow(1024, 3),
            'T' => $number * pow(1024, 4),
            default => $number,
        };
    }

    /**
     * To Camel-Case
     * @param string $underlined_words
     * @param string $separator
     * @return string
     */
    public static function camelize(string $underlined_words, string $separator='_'): string
    {
        $underlined_words = $separator. str_replace($separator, " ", strtolower($underlined_words));
        return ltrim(str_replace(" ", "", ucwords($underlined_words)), $separator );
    }

    /**
     * To UnderScoreCase
     * @param string $camelCaps
     * @param string $separator
     * @return string
     */
    public static function underlineze(string $camelCaps, string $separator='_'): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }

    /**
     * 将一个字符串部分字符用*替代隐藏
     *
     * @param string $string 待转换的字符串
     * @param int $begin 起始位置，从0开始计数，当$type=4时，表示左侧保留长度
     * @param int $count 需要转换成*的字符个数，当$type=4时，表示右侧保留长度
     * @param int $type 转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
     * @param string $glue 分割符
     * @return bool|string
     */
    public static function hideStr(string $string, int $begin = 3, int $count = 4, int $type = 0, string $glue = "@"): bool|string
    {
        $length = 0;
        if (empty($string)) {
            return false;
        }

        $array = [];
        if ($type === 0 || $type === 1 || $type === 4) {
            $strlen = $length = mb_strlen($string);

            while ($strlen) {
                $array[] = mb_substr($string, 0, 1, "utf8");
                $string = mb_substr($string, 1, $strlen, "utf8");
                $strlen = mb_strlen($string);
            }
        }

        switch ($type) {
            case 0 :
                for ($i = $begin; $i < ($begin + $count); $i++) {
                    isset($array[$i]) && $array[$i] = "*";
                }

                $string = implode("", $array);
                break;
            case 1 :
                $array = array_reverse($array);
                for ($i = $begin; $i < ($begin + $count); $i++) {
                    isset($array[$i]) && $array[$i] = "*";
                }

                $string = implode("", array_reverse($array));
                break;
            case 2 :
                $array = explode($glue, $string);
                $array[0] = self::hideStr($array[0], $begin, $count, 1);
                $string = implode($glue, $array);
                break;
            case 3 :
                $array = explode($glue, $string);
                $array[1] = self::hideStr($array[1], $begin, $count, 0);
                $string = implode($glue, $array);
                break;
            case 4 :
                $left = $begin;
                $right = $count;
                $tem = array();
                for ($i = 0; $i < ($length - $right); $i++) {
                    if (isset($array[$i])) {
                        $tem[] = $i >= $left ? "*" : $array[$i];
                    }
                }

                $array = array_chunk(array_reverse($array), $right);
                $array = array_reverse($array[0]);
                for ($i = 0; $i < $right; $i++) {
                    $tem[] = $array[$i];
                }
                $string = implode("", $tem);
                break;
        }

        return $string;
    }

    /**
     * @param string $string
     * @param int $sub_len
     * @param int $start
     * @param string $post
     * @param string $code
     * @return string
     */
    public static function cutString($string, $sub_len = 10, $start = 0, $post = '...', $code = 'UTF-8'): string
    {
        if (strtoupper($code) === 'UTF-8') {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);
            if (count($t_string[0]) - $start > $sub_len) {
                return implode('', array_slice($t_string[0], $start, $sub_len))."...";
            }
            return implode('', array_slice($t_string[0], $start, $sub_len));
        }

        $start *= 2;
        $sub_len *= 2;
        $strlen = strlen($string);
        $tmpStr = '';

        for($i = 0; $i < $strlen; $i++) {
            if ($i >= $start && $i< ($start + $sub_len)) {
                if(ord(substr($string, $i, 1)) > 129) {
                    $tmpStr .= substr($string, $i, 2);
                } else {
                    $tmpStr .= substr($string, $i, 1);
                }
            }
            if (ord(substr($string, $i, 1))  >129) {
                $i++;
            }
        }
        if(strlen($tmpStr) < $strlen ) {
            $tmpStr .= $post;
        }
        return $tmpStr;
    }
}
