<?php
namespace Bim;

use ConsoleKit\Console;
/**
 * Bitrix Migration (BIM)
 * Documentation: http://cjp2600.github.io/bim-core/
 */
class Migration {

    public static function init()
    {
        $conf = new \Noodlehaus\Config(__DIR__."/config/commands.json");
        $console = new Console($conf->get("commands"));
        $console->run();
    }
}