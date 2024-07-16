<?php

namespace davidxu\adminlte4\helpers;

class RegularHelper
{
    /**
     * China Mobile
     *
     * @return string
     */
    public static function chinaMobile(): string
    {
//        return '/^[1][3456789][0-9]{9}$/';
        return '/^1(3\d|4[5-9]|5[0-35-9]|6[567]|7[0-8]|8\d|9[0-35-9])\d{8}$/';
    }

    /**
     * China telephone
     * Format：XXXX-XXXXXXX，XXXX-XXXXXXXX，XXX-XXXXXXX，XXX-XXXXXXXX，XXXXXXX，XXXXXXXX
     *
     * @return string
     */
    public static function chinaTelephone(): string
    {
        return '/^(\(\d{3,4}\)|\d{3,4}-)?\d{7,8}$/';
    }

    /**
     * China ID card
     *
     * @return string
     */
    public static function chinaIdCard(): string
    {
        // return '/^\d{15}|\d{}18$/';
        return '/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/';
    }
}