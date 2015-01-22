<?php
/**
 * Created for the project "bim-core"
 *
 * @author: Stanislav Semenov (CJP2600)
 * @email: cjp2600@ya.ru
 *
 * @date: 22.01.2015
 * @time: 7:48
 */

namespace Bim;

use ConsoleKit\Console;

class Migration {

    public static function init()
    {
        $conf = new \Noodlehaus\Config(__DIR__."/lib/config/commands.json");
        $console = new Console($conf->get("commands"));
        $console->run();
    }
}