<?php
/**
 * Created for the project "bim-core"
 *
 * @author: Stanislav Semenov (CJP2600)
 * @email: cjp2600@ya.ru
 *
 * @date: 23.01.2015
 * @time: 8:05
 */

namespace Bim;


class Revision {

    public function response($response = false)
    {
        if (isset($response['type']) && $response['type'] == "success") {
            return true;
        } else if ($response['type'] == "error"){
            if (isset($response['error_text'])){
                throw new Exception(str_replace("<br>",PHP_EOL,$response['error_text']));
            }
        }
        return is_bool($response) ? $response : false;
    }

}