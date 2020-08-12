<?php

namespace MauticPlugin\MauticFacebookBusinessBundle\Helpers;

use Facebook\Facebook;

class Helper
{
    public static function extractPhoneNumber($commentStr)
    {
        $pattern = '/\(?([0-9]{3})\s*\)?\s*-?\s*([0-9]{3})\s*-?\s*([0-9]{4})/';
        preg_match($pattern, $commentStr, $matches);

        if (null != $matches) {
            return $matches[0];
        }

        return '';
    }
}
