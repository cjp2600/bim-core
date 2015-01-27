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

        $do = true;
        while ($do) {
            $desk = "Information block type - no default/required";
            $field_val = $dialog->ask($desk . PHP_EOL . $this->color('[IBLOCK_TYPE_ID]:', \ConsoleKit\Colors::YELLOW), '', false);
            $up_data['IBLOCK_TYPE_ID'] = $this->clear($field_val);

            $iblockTypeDbRes = CIBlockType::GetByID($up_data['IBLOCK_TYPE_ID']);
            if (!$iblockTypeDbRes || (!$iblockTypeDbRes->SelectedRowsCount())) {

                $this->error('Iblock type with id = "' . $up_data['IBLOCK_TYPE_ID'] .'" not exist.');
                $field_val = $dialog->ask("Do you want to continue recording a non-existent id? [Y/N]", 'Y');
                if (strtolower($field_val) == "y"){
                    $do = false;
                }

            } else {
                $do = false;
            }
        }

        $desk = "Name of the information block - no default/required";
        $field_val = $dialog->ask($desk.PHP_EOL.$this->color('[NAME]:',\ConsoleKit\Colors::YELLOW), '',false);
        $up_data['NAME'] =  $this->clear($field_val);

        $do = true;
        while ($do) {
            $desk = "Character code information block - no default/required";
            $field_val = $dialog->ask($desk . PHP_EOL . $this->color('[CODE]:', \ConsoleKit\Colors::YELLOW), '', false);
            $up_data['CODE'] = $this->clear($field_val);

            #check on exist
            $iblockDbRes = \CIBlock::GetList(array(), array('CODE' => $up_data['CODE']));
            if (!$iblockDbRes || (!$iblockDbRes->SelectedRowsCount())) {
                $do = false;
            } else {
                $this->error('Iblock with code = "' . $up_data['CODE'] .'" already exist.');
            }
        }

        # send down function var
        $down_data['IBLOCK_CODE'] = $this->clear($up_data['CODE']);

        $desk = "Sites to which the information block - no default/required. Пример: s1";
        $field_val = $dialog->ask($desk.PHP_EOL.$this->color('[LID]:',\ConsoleKit\Colors::YELLOW), "s1");
        $up_data['LID'] = $this->clear($field_val);


        # set
        $name_migration = $this->getMigrationName();
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
        $name_migration = $this->getMigrationName();
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
