<?php
namespace Bim;

use Bim\Db\Entity\MigrationsTable;
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

        # check migration table.
        MigrationsTable::checkMigrationTable();

        # run commands
        $console->run();
    }
}