<?php
/**
 * Created for the project "bim"
 *
 * @author: Stanislav Semenov (CJP2600)
 * @email: cjp2600@ya.ru
 *
 * @date: 21.01.2015
 * @time: 22:42
 */

class InitCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        # init (create table)
        if ($this->createTable()){
            $this->padding("Create migrations table : ".$this->color(strtoupper("completed"),\ConsoleKit\Colors::GREEN));
        }

    }

    /**
     * createTable
     * @return bool
     * @throws Exception
     */
    public function createTable()
    {
        global $DB;
        $this->errors = false;
        if ( !$DB->Query("SELECT 'file' FROM bim_migrations", true) ) {
            $this->errors = $DB->RunSQLBatch(__DIR__.'/../db/install/install.sql');
        } else {
            $this->info("Migration table all ready exists");
            return false;
        }
        if ($this->errors !== false ) {
           throw new Exception(implode("", $this->errors));
            return false;
        }
        return true;
    }

}
