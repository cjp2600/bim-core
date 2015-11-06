<?php
namespace Bim;

use ConsoleKit\Console;
use Noodlehaus\Config;

/**
 * Bitrix Migration (BIM)
 * Documentation: http://cjp2600.github.io/bim-core/
 */
class Migration {

    /**
     * init bim applications
     * @throws \Exception
     */
    public static function init()
    {
        $conf = new Config(__DIR__."/config/commands.json");
        $console = new Console($conf->get("commands"));

        # check migration init
        Bim\Db\Entity\MigrationsTable::checkMigrationTable();

        # run commands
        $console->run();
    }

}