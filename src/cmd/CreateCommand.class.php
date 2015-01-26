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
            $method = "create" . ucfirst($args[0]);
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
    public function createIblock(array $args, array $options = array())
    {
        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";
        if (!is_string($desc)) {
            $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
            $desc = $dialog->ask('Description:', '',false);
        }



        # Up Wizard
        $up_data = array();
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $field_val = $dialog->ask('Type required field [IBLOCK_TYPE_ID]:', '',false);
        $up_data['IBLOCK_TYPE_ID'] = $field_val;
        $field_val = $dialog->ask('Type required field [NAME]:', '',false);
        $up_data['NAME'] = $field_val;
        $field_val = $dialog->ask('Type required field [CODE]:', '',false);
        $up_data['CODE'] = $field_val;
        $field_val = $dialog->ask('Type required field [LID]:', "array(0 => 's1',1 => 'en');");
        $up_data['LID'] = $field_val;

        $down_data = array();

        # set
        $name_migration = $this->getMigrationName($this->fromCamelCase(__METHOD__));
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->setTemplateMethod(strtolower($args[0]),$up_data),
                $this->setTemplateMethod(strtolower($args[0]),$down_data,"down"),
                $desc,
                get_current_user()
        ));

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
            $desc = $dialog->ask('Description:', '',false);
        }

        $up_data = array();
        $down_data = array();

        $name_method = "other";
        # set
        $name_migration = $this->getMigrationName($this->fromCamelCase(__METHOD__));
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->setTemplateMethod(strtolower($name_method),$up_data),
                $this->setTemplateMethod(strtolower($name_method),$down_data,"down"),
                $desc,
                get_current_user()
            ));
    }

}
