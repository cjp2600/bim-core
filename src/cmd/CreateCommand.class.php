<?php


/**
 * Main class of creating migration is required for the generation of migration files.
 *
 * The following commands create:
 *   - iblock
 *   - other
 *
 */
class CreateCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        if (isset($args[0])) {

            #chemethod
            if (strstr($args[0], ':')) {
                $ex = explode(":",$args[0]);
                $methodName = ucfirst($ex[0]).ucfirst($ex[1]);
            } else {
                throw new Exception("Improperly formatted command. Example: php bim create iblock:add");
            }

            $method = "create" . $methodName;
            if (method_exists($this,$method)) {
                $this->{$method}($args, $options);
            } else {
                throw new Exception("Missing command, see help Example: php bim help create");
            }
        } else {
            $this->createOther($args,$options);
        }
    }

    /**
     * createIblock
     * @param array $args
     * @param array $options
     */
    public function createIblockAdd(array $args, array $options = array())
    {
        # check on full mode
        $is_full = (isset($options['full'])) ? $options['full'] : false;

        # Up Wizard
        $up_data = array();
        $down_data = array();
        $desc = "";

        # create wizard command
        $wizard = new \Bim\Db\Iblock\IblockCommand($this->getConsole());
        $wizard->createWizard($up_data,$down_data,$desc,$is_full);

        # set
        $temp = ($is_full) ? "up_full" : "up";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->setTemplateMethod('iblock', 'add', $up_data, $temp ),
                $this->setTemplateMethod('iblock', 'add', $down_data, "down"),
                $desc,
                get_current_user()
            ));
    }

    /**
     * createIblockDelete
     * @param array $args
     * @param array $options
     */
    public function createIblockDelete(array $args, array $options = array())
    {
        $this->padding("this createIblockDelete");
    }


    /**
     * createOther
     * @param array $args
     * @param array $options
     */
    public function createOther(array $args, array $options = array())
    {
        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";
        if (!is_string($desc)) {
            $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
            $desc = $dialog->ask('Description:', '', false);
        }

        $up_data = array();
        $down_data = array();

        $name_method = "other";
        # set
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->setTemplateMethod(strtolower($name_method), 'create', $up_data),
                $this->setTemplateMethod(strtolower($name_method), 'create', $down_data, "down"),
                $desc,
                get_current_user()
            ));
    }

}
