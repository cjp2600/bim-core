<?php


/**
 * Main class of creating migration is required for the generation of migration files.
 *
 * The following commands create:
 *   - Iblock # Create a migration of information blocks
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
                throw new Exception("Missing command, see help Example: php help create");
            }
        } else {
            throw new Exception("Required command is empty, see help Example: php help create");
        }
    }

    /**
     * createIblock
     * @param array $args
     * @param array $options
     */
    public function createIblock(array $args, array $options = array())
    {
        $up_data = array();
        $down_data = array();

        # set
        $name_migration = $this->getMigrationName($this->fromCamelCase(__METHOD__));
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->setTemplateMethod(strtolower($args[0]),$up_data),
                $this->setTemplateMethod(strtolower($args[0]),$down_data,"down")
        ));

    }

}
