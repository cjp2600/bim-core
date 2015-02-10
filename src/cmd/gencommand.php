<?php

/**
 * Main class of creating migration is required for the generation of migration files.
 *
 * The following commands create:
 *   - iblock
 *   - other
 *
 */
class GenCommand extends BaseCommand {

    # generate object
    private $gen_obj = null;

    /**
     * execute
     * @param array $args
     * @param array $options
     * @return mixed|void
     * @throws Exception
     */
    public function execute(array $args, array $options = array())
    {
        if (isset($args[0])) {
            #chemethod
            if (strstr($args[0], ':')) {
                $ex = explode(":",$args[0]);
                $this->setGenObj(Bim\Db\Lib\CodeGenerator::buildHandler(ucfirst($ex[0])));
                $methodName = ucfirst($ex[0]).ucfirst($ex[1]);
            } else {
                throw new Exception("Improperly formatted command. Example: php bim gen iblock:add");
            }
            $method = "gen" . $methodName;
            if (method_exists($this,$method)) {
                $this->{$method}($args, $options);
            } else {
                throw new Exception("Missing command, see help Example: php bim help gen");
            }
        } else {
            $this->createOther($args,$options);
        }
    }

    /**
     * genIblockTypeAdd
     * @param array $args
     * @param array $options
     */
    public function genIblocktypeAdd( array $args, array $options = array() )
    {
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $iblocktypeId = (isset($options['typeId'])) ? $options['typeId'] : false;

        if (!$iblocktypeId) {
            $do = true;
            while ($do) {
                $desk = "Put block type id - no default/required";
                $iblocktypeId = $dialog->ask($desk . PHP_EOL . $this->color('[IBLOCK_TYPE_ID]:', \ConsoleKit\Colors::YELLOW), '', false);
                $iblockDbRes = \CIBlockType::GetByID($iblocktypeId);
                if ($iblockDbRes->SelectedRowsCount()) {
                    $do = false;
                } else {
                    $this->error('Iblock with id = "' . $iblocktypeId . '" not exist.');
                }
            }
        }

        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk . PHP_EOL . $this->color('Description:', \ConsoleKit\Colors::BLUE), "", false);
        }

        # set
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateAddCode($iblocktypeId),
                $this->gen_obj->generateDeleteCode($iblocktypeId),
                $desc,
                get_current_user()
            ));
    }

    /**
     * genIblocktypeDelete
     * @param array $args
     * @param array $options
     */
    public function genIblocktypeDelete( array $args, array $options = array() )
    {
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $iblocktypeId = (isset($options['typeId'])) ? $options['typeId'] : false;

        if (!$iblocktypeId) {
            $do = true;
            while ($do) {
                $desk = "Put block type id - no default/required";
                $iblocktypeId = $dialog->ask($desk . PHP_EOL . $this->color('[IBLOCK_TYPE_ID]:', \ConsoleKit\Colors::YELLOW), '', false);
                $iblockDbRes = \CIBlockType::GetByID($iblocktypeId);
                if ($iblockDbRes->SelectedRowsCount()) {
                    $do = false;
                } else {
                    $this->error('Iblock with id = "' . $iblocktypeId . '" not exist.');
                }
            }
        }

        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk . PHP_EOL . $this->color('Description:', \ConsoleKit\Colors::BLUE), "", false);
        }

        # set
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateDeleteCode($iblocktypeId),
                $this->gen_obj->generateAddCode($iblocktypeId),
                $desc,
                get_current_user()
            ));
    }

    /**
     * createIblock
     * @param array $args
     * @param array $options
     */
    public function genIblockAdd(array $args, array $options = array())
    {
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $code = (isset($options['code'])) ? $options['code'] : false;

        if (!$code) {
            $do = true;
            while ($do) {
                $desk = "Put code information block - no default/required";
                $code = $dialog->ask($desk . PHP_EOL . $this->color('[IBLOCK_CODE]:', \ConsoleKit\Colors::YELLOW), '', false);
                $iblockDbRes = \CIBlock::GetList(array(), array('CODE' => $code));
                if ($iblockDbRes->SelectedRowsCount()) {
                    $do = false;
                } else {
                    $this->error('Iblock with code = "' . $code . '" not exist.');
                }
            }
        }

        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk . PHP_EOL . $this->color('Description:', \ConsoleKit\Colors::BLUE), "", false);
        }

        # set
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateAddCode($code),
                $this->gen_obj->generateDeleteCode($code),
                $desc,
                get_current_user()
            ));
    }

    /**
     * createIblockDelete
     * @param array $args
     * @param array $options
     */
    public function genIblockDelete(array $args, array $options = array())
    {

        $dialog  = new \ConsoleKit\Widgets\Dialog($this->console);
        $code    = (isset($options['code'])) ? $options['code'] : false;

        if ( !$code ) {
            $do = true;
            while ($do) {
                $desk = "Put code information block - no default/required";
                $code = $dialog->ask($desk . PHP_EOL . $this->color('[IBLOCK_CODE]:', \ConsoleKit\Colors::YELLOW), '', false);
                $iblockDbRes = \CIBlock::GetList(array(), array('CODE' => $code));
                if ($iblockDbRes->SelectedRowsCount()) {
                    $do = false;
                } else {
                    $this->error('Iblock with code = "' . $code . '" not exist.');
                }
            }
        }

        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk.PHP_EOL.$this->color('Description:',\ConsoleKit\Colors::BLUE), "",false);
        }

        # set
        $temp =  "up";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateDeleteCode($code),
                $this->gen_obj->generateAddCode($code),
                $desc,
                get_current_user()
            ));
    }


    /**
     * genIblockPropertyAdd
     * @param array $args
     * @param array $options
     * @throws Exception
     */
    public function genIblockPropertyAdd (array $args, array $options = array())
    {
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $code = (isset($options['code'])) ? $options['code'] : false;

        if (!$code) {
            $do = true;
            while ($do) {
                $desk = "Put code information block - no default/required";
                $code = $dialog->ask($desk . PHP_EOL . $this->color('[IBLOCK_CODE]:', \ConsoleKit\Colors::YELLOW), '', false);
                $iblockDbRes = \CIBlock::GetList(array(), array('CODE' => $code));
                if ($iblockDbRes->SelectedRowsCount()) {
                    $do = false;
                } else {
                    $this->error('Iblock with code = "' . $code . '" not exist.');
                }
            }
        }

        $propertyCode = (isset($options['propertyCode'])) ? $options['propertyCode'] : false;
        if (!$propertyCode) {
            $do = true;
            while ($do) {
                $desk = "Put property code - no default/required";
                $propertyCode = $dialog->ask($desk . PHP_EOL . $this->color('[PROPERTY_CODE]:', \ConsoleKit\Colors::YELLOW), '', false);
                $IblockProperty = new \CIBlockProperty();
                $dbIblockProperty = $IblockProperty->GetList(array(), array('IBLOCK_CODE' =>  $code, 'CODE' => $propertyCode ));
                if ($arIblockProperty = $dbIblockProperty->Fetch())
                {
                    $do = false;
                } else {
                    $this->error('Property with code = "' . $propertyCode . '" not exist.');
                }
            }
        }

        if (!empty($code) && !empty($propertyCode)) {
            $params['iblockCode'] = $code;
            $params['propertyCode'] = $propertyCode;
        } else {
            throw new Exception("Ошибка генерации params");
        }
        
        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk.PHP_EOL.$this->color('Description:',\ConsoleKit\Colors::BLUE), "",false);
        }

        # set
        $temp =  "up";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateAddCode($params),
                "# delete",
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
        if (empty($desc)) {
            $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk.PHP_EOL.$this->color('Description:',\ConsoleKit\Colors::BLUE), "",false);
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

    /**
     * @return null
     */
    public function getGenObj()
    {
        return $this->gen_obj;
    }

    /**
     * @param null $gen_obj
     */
    public function setGenObj($gen_obj)
    {
        $this->gen_obj = $gen_obj;
    }

}
