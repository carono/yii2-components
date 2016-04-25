<?php
namespace carono\components\helpers;

class TransliteratorHelper extends \dosamigos\transliterator\TransliteratorHelper
{
    public static function urlProcess($url, $unknown = '?', $language = null, $replace = "-")
    {
        $url = preg_replace('/[^a-z0-9]/', $replace, strtolower(self::process($url, $unknown, $language)));
        do {
            $url = preg_replace("/{$replace}{$replace}/", $replace, $url, 1, $count);
        }while ($count);
        $url = trim($url, $replace);
        return $url;
    }

    public static function nativeProcess($string)
    {
        // If intl extension load
        if (extension_loaded('intl') === true) {
            $options = 'Any-Latin; Latin-ASCII; NFD; [:Nonspacing Mark:] Remove; NFC;';
            return transliterator_transliterate($options, $string);
        } else {
            throw new \Exception('Extension "intl" for transliteration not loaded');
        }
    }

    public static function process($string, $unknown = '?', $language = null)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }
        static $tail_bytes;

        if (!isset($tail_bytes)) {
            $tail_bytes = array();
            for ($n = 0; $n < 256; $n++) {
                if ($n < 0xc0) {
                    $remaining = 0;
                } elseif ($n < 0xe0) {
                    $remaining = 1;
                } elseif ($n < 0xf0) {
                    $remaining = 2;
                } elseif ($n < 0xf8) {
                    $remaining = 3;
                } elseif ($n < 0xfc) {
                    $remaining = 4;
                } elseif ($n < 0xfe) {
                    $remaining = 5;
                } else {
                    $remaining = 0;
                }
                $tail_bytes[chr($n)] = $remaining;
            }
        }

        preg_match_all('/[\x00-\x7f]+|[\x80-\xff][\x00-\x40\x5b-\x5f\x7b-\xff]*/', $string, $matches);

        $result = [];
        foreach ($matches[0] as $str) {
            if ($str[0] < "\x80") {
                $result[] = $str;
                continue;
            }

            $head = '';
            $chunk = strlen($str);
            $len = $chunk + 1;
            for ($i = -1; --$len;) {
                $c = $str[++$i];
                if ($remaining = $tail_bytes[$c]) {
                    $sequence = $head = $c;
                    do {
                        if (--$len && ($c = $str[++$i]) >= "\x80" && $c < "\xc0") {
                            $sequence .= $c;
                        } else {
                            if ($len == 0) {
                                $result[] = $unknown;
                                break 2;
                            } else {
                                $result[] = $unknown;
                                --$i;
                                ++$len;
                                continue 2;
                            }
                        }
                    }while (--$remaining);

                    $n = ord($head);
                    if ($n <= 0xdf) {
                        $ord = ($n - 192) * 64 + (ord($sequence[1]) - 128);
                    } elseif ($n <= 0xef) {
                        $ord = ($n - 224) * 4096 + (ord($sequence[1]) - 128) * 64 + (ord($sequence[2]) - 128);
                    } elseif ($n <= 0xf7) {
                        $ord = ($n - 240) * 262144 + (ord($sequence[1]) - 128) * 4096 + (ord($sequence[2]) - 128) * 64
                            + (ord($sequence[3]) - 128);
                    } elseif ($n <= 0xfb) {
                        $ord = ($n - 248) * 16777216 + (ord($sequence[1]) - 128) * 262144 + (ord($sequence[2]) - 128)
                            * 4096 + (ord($sequence[3]) - 128) * 64 + (ord($sequence[4]) - 128);
                    } elseif ($n <= 0xfd) {
                        $ord = ($n - 252) * 1073741824 + (ord($sequence[1]) - 128) * 16777216 + (ord($sequence[2])
                                - 128) * 262144 + (ord($sequence[3]) - 128) * 4096 + (ord($sequence[4]) - 128) * 64
                            + (ord($sequence[5]) - 128);
                    }
                    $result[] = static::replace($ord, $unknown, $language);
                    $head = '';
                } elseif ($c < "\x80") {
                    $result[] = $c;
                    $head = '';
                } elseif ($c < "\xc0") {
                    if ($head == '') {
                        $result[] = $unknown;
                    }
                } else {
                    $result[] = $unknown;
                    $head = '';
                }
            }
        }
        return implode('', $result);
    }

}