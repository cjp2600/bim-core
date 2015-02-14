<?php
/**
 * .
 * init BIM migrations.
 *
 * Documentation: http://cjp2600.github.io/bim-core/
 *
 */
class InitCommand extends BaseCommand
{
    public function execute(array $args, array $options = array())
    {
        # init (create table)
        if ( Bim\Db\Entity\MigrationsTable::createTable() ){
            $this->padding("Create migrations table : ".$this->color(strtoupper("completed"),\ConsoleKit\Colors::GREEN));
        } else {
            $this->padding("Create migrations table : ".$this->color(strtoupper("already exist"),\ConsoleKit\Colors::YELLOW));
        }
    }

}
