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


use ConsoleKit\Colors;
use ConsoleKit\Console;

class Revision {

    public function response($response = false)
    {
        $console = new Console();
        $mig = str_replace("Migration","",get_called_class());
        if (isset($response['type']) && $response['type'] == "success") {
            return true;
        } else if ($response['type'] == "error"){
            if (isset($response['error_text'])){
               // throw new \Exception(str_replace("<br>",PHP_EOL,$response['error_text']));
                $console->writeln(Colors::colorize("     - error : " . $mig, Colors::RED)." ".Colors::colorize("(".str_replace("<br>",", ",$response['error_text']).")",Colors::YELLOW));
                return false;
            }
        }

        if (!$response) {
            $console->writeln(Colors::colorize("     - error : " . $mig, Colors::RED)." ".Colors::colorize("(Method Up return false)",Colors::YELLOW));
        }

        return is_bool($response) ? $response : false;
    }

}