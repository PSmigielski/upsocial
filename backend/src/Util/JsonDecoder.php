<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\Request;

class JsonDecoder
{
    public static function decode(Request $request): array
    {
        $reqData = [];
        if ($content = $request->getContent()) {
            $reqData = json_decode($content, true);
        }
        return $reqData;
    }
}
