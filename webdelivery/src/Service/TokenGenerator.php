<?php
/**
 * Created by PhpStorm.
 * User: xim1k
 * Date: 28.03.19
 * Time: 18:00
 */

namespace App\Service;


class TokenGenerator
{

    public function generate()
    {
        $token = '';

        $token = time() . '_' . uniqid("", TRUE);

        return $token;
    }

}