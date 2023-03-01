<?php

namespace App\Utils;

class Censurator
{
    const BAN_WORDS = ['michel', 'extincteur', 'chocolatine'];

    public function purify (string $uncensored){

        return str_ireplace(self::BAN_WORDS, '****', $uncensored);

    }

}