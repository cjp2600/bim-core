<?php

/**
 * --  > BIM GEN | Command to create the migration classes.
 *
 *  Documentation: http://cjp2600.github.io/bim-core/
 *
 */
class GenCommand extends BaseCommand {

    const END_LOOP_SYMPOL = "";

    private $gen_obj = null;
    private $isMulti = false;
    private $multiAddReturn = array();
    private $multiDeleteReturn = array();
    private $multiHeaders = array();
    private $multiCurrentCommand = null;

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

            #if gen multi command generator
            if (strtolower($args[0]) == "multi"){
                $this->multiCommands($args,$options);
            } else {

                # single command generator
                if (strstr($args[0], ':')) {
                    $ex = explode(":", $args[0]);
                    $this->setGenObj(Bim\Db\Lib\CodeGenerator::buildHandler(ucfirst($ex[0])));
                    $methodName = ucfirst($ex[0]) . ucfirst($ex[1]);
                } else {
                    throw new Exception("Improperly formatted command. Example: php bim gen iblock:add");
                }
                $method = "gen" . $methodName;
                if (method_exists($this, $method)) {
                    $this->{$method}($args, $options);
                } else {
                    throw new Exception("Missing command, see help Example: php bim help gen");
                }

            }

        } else {
            $this->createOther($args,$options);
        }
    }

    /**
     *
     *
     * IblockType
     *
     *
     */

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

        $autoTag = "add";
        $this->_save(
            $this->gen_obj->generateAddCode($iblocktypeId),
            $this->gen_obj->generateDeleteCode($iblocktypeId)
            ,$desc,
            $autoTag
        );

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

        $autoTag = "delete";
        $this->_save(
            $this->gen_obj->generateDeleteCode($iblocktypeId),
            $this->gen_obj->generateAddCode($iblocktypeId)
            ,$desc,
            $autoTag
        );

    }


    /**
     *
     *
     * Iblock
     *
     *
     */

    /**
     * createIblock
     * @param array $args
     * @param array $options
     * @throws Exception
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

        $autoTag = "add";
        $this->_save(
            $this->gen_obj->generateAddCode($code),
            $this->gen_obj->generateDeleteCode($code)
            ,$desc,
            $autoTag
        );

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

        $autoTag = "delete";
        $this->_save(
            $this->gen_obj->generateDeleteCode($code),
            $this->gen_obj->generateAddCode($code)
            ,$desc,
            $autoTag
        );

    }

    /**
     *
     *
     * IblockProperty
     *
     *
     */

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

        $autoTag = "add";
        $this->_save(
            $this->gen_obj->generateAddCode($params),
            $this->gen_obj->generateDeleteCode($params)
            ,$desc,
            $autoTag
        );

    }


    /**
     * genIblockPropertyDelete
     * @param array $args
     * @param array $options
     * @throws Exception
     */
    public function genIblockPropertyDelete (array $args, array $options = array())
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

        $autoTag = "delete";
        $this->_save(
            $this->gen_obj->generateDeleteCode($params),
            $this->gen_obj->generateAddCode($params)
            ,$desc,
            $autoTag
        );

    }

    /**
     *
     *
     * Highloadblock
     *
     *
     */

    /**
     * genHlblockAdd
     * @param array $args
     * @param array $options
     */
    public function genHlblockAdd (array $args, array $options = array())
    {
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $hlId = (isset($options['id'])) ? $options['id'] : false;

        if (!$hlId) {
            $do = true;
            while ($do) {
                $desk = "Put id Highloadblock - no default/required";
                $hlId = $dialog->ask($desk . PHP_EOL . $this->color('[HLBLOCK_ID]:', \ConsoleKit\Colors::YELLOW), '', false);
                $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById( $hlId )->fetch();
                if ( $hlblock ) {
                    $do = false;
                } else {
                    $this->error('Highloadblock with id = "' . $hlId . '" not exist.');
                }
            }
        }

        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";

        $autoTag = "add";
        $this->_save(
            $this->gen_obj->generateAddCode($hlId),
            $this->gen_obj->generateDeleteCode($hlId)
            ,$desc,
            $autoTag
        );

    }

    /**
     * genHlblockDelete
     * @param array $args
     * @param array $options
     */
    public function genHlblockDelete (array $args, array $options = array())
    {
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $hlId = (isset($options['id'])) ? $options['id'] : false;

        if (!$hlId) {
            $do = true;
            while ($do) {
                $desk = "Put id Highloadblock - no default/required";
                $hlId = $dialog->ask($desk . PHP_EOL . $this->color('[HLBLOCK_ID]:', \ConsoleKit\Colors::YELLOW), '', false);
                $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById( $hlId )->fetch();
                if ( $hlblock ) {
                    $do = false;
                } else {
                    $this->error('Highloadblock with id = "' . $hlId . '" not exist.');
                }
            }
        }

        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";

        $autoTag = "delete";
        $this->_save(
            $this->gen_obj->generateDeleteCode($hlId),
            $this->gen_obj->generateAddCode($hlId)
            ,$desc,
            $autoTag
        );

    }

    /**
     * genHlblockFieldAdd
     * @param array $args
     * @param array $options
     */
    public function genHlblockFieldAdd (array $args, array $options = array())
    {
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $hlId = (isset($options['hlblockid'])) ? $options['hlblockid'] : false;

        if (!$hlId) {
            $do = true;
            while ($do) {
                $desk = "Put id Highloadblock - no default/required";
                $hlId = $dialog->ask($desk . PHP_EOL . $this->color('[HLBLOCK_ID]:', \ConsoleKit\Colors::YELLOW), '', false);
                $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById( $hlId )->fetch();
                if ( $hlblock ) {
                    $do = false;
                } else {
                    $this->error('Highloadblock with id = "' . $hlId . '" not exist.');
                }
            }
        }

        $hlFieldId =  (isset($options['hlFieldId'])) ? $options['hlFieldId'] : false;
        if (!$hlFieldId) {
            $do = true;
            while ($do) {
                $desk = "Put id HighloadblockField (UserField) - no default/required";
                $hlFieldId = $dialog->ask($desk . PHP_EOL . $this->color('[USER_FIELD_ID]:', \ConsoleKit\Colors::YELLOW), '', false);
                $userFieldData = \CUserTypeEntity::GetByID($hlFieldId);
                if ($userFieldData === false || empty($userFieldData)) {
                    $this->error('UserField with id = "' . $hlFieldId . '" not exist.');
                } else {
                    $do = false;
                }
            }
        }

        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";

        # set
        $autoTag = "add";
        $this->_save(
            $this->gen_obj->generateAddCode(array("hlblockId" => $hlId,"hlFieldId"=>$hlFieldId)),
            $this->gen_obj->generateDeleteCode(array("hlblockId" => $hlId,"hlFieldId"=>$hlFieldId))
            ,$desc,
            $autoTag
        );
    }


    /**
     * genHlblockFieldDelete
     * @param array $args
     * @param array $options
     */
    public function genHlblockFieldDelete (array $args, array $options = array())
    {
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $hlId = (isset($options['hlblockid'])) ? $options['hlblockid'] : false;

        if (!$hlId) {
            $do = true;
            while ($do) {
                $desk = "Put id Highloadblock - no default/required";
                $hlId = $dialog->ask($desk . PHP_EOL . $this->color('[HLBLOCK_ID]:', \ConsoleKit\Colors::YELLOW), '', false);
                $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById( $hlId )->fetch();
                if ( $hlblock ) {
                    $do = false;
                } else {
                    $this->error('Highloadblock with id = "' . $hlId . '" not exist.');
                }
            }
        }

        $hlFieldId =  (isset($options['hlFieldId'])) ? $options['hlFieldId'] : false;
        if (!$hlFieldId) {
            $do = true;
            while ($do) {
                $desk = "Put id HighloadblockField (UserField) - no default/required";
                $hlFieldId = $dialog->ask($desk . PHP_EOL . $this->color('[USER_FIELD_ID]:', \ConsoleKit\Colors::YELLOW), '', false);
                $userFieldData = \CUserTypeEntity::GetByID($hlFieldId);
                if ($userFieldData === false || empty($userFieldData)) {
                    $this->error('UserField with id = "' . $hlFieldId . '" not exist.');
                } else {
                    $do = false;
                }
            }
        }

        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";

        # set
        $autoTag = "delete";
        $this->_save(
            $this->gen_obj->generateDeleteCode(array("hlblockId" => $hlId,"hlFieldId"=>$hlFieldId)),
            $this->gen_obj->generateAddCode(array("hlblockId" => $hlId,"hlFieldId"=>$hlFieldId))
            ,$desc,
            $autoTag
        );
    }


    /**
     *
     *
     * MultiCommands !
     *
     *
     */

    public function multiCommands(array $args, array $options = array())
    {
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $do = true;
        while ($do) {

            $headers = $this->getMultiHeaders();
            if (!empty($headers)){
                $this->padding(implode(PHP_EOL,$headers));
            }

            $current_command = $this->getMultiCurrentCommand();

            if (is_null($current_command)) {

                $desk = "Put generation commands:";
                $command = $dialog->ask($desk . " " . $this->color('php bim gen >', \ConsoleKit\Colors::MAGENTA), '', false);
                if (!empty($command)) {
                    if ($command != self::END_LOOP_SYMPOL) {
                        $this->setMulti(true);
                        $this->setMultiCurrentCommand($command);
                        $this->execute(array($command));
                    } else {
                        $do = false;
                    }
                } else {
                    $do = false;
                }

            } else {
                $ask = $dialog->ask("You want to repeat command (".$this->color($current_command, \ConsoleKit\Colors::MAGENTA).")", 'Y', true);
                if (strtolower($ask) == "y") {

                    $this->setMulti(true);
                    $this->setMultiCurrentCommand($current_command);
                    $this->execute(array($current_command));

                } else {
                    $this->setMultiCurrentCommand(null);
                }
            }
        }

        $addItems = $this->getMultiAddReturn();
        if (empty($addItems)){
            return true;
        }

        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk . PHP_EOL . $this->color('Description:', \ConsoleKit\Colors::BLUE), "", false);
        }

        $up = $this->getMultiAddReturn();
        $down = $this->getMultiDeleteReturn();

        if (count($up) == count($down)) {

            foreach (array("add","delete") as $it) {

                $i=0;
                foreach ($up[$it] as $row) {
                    # set
                    $autoTag = $it;
                    $desc = $desc . " #" . $autoTag;
                    $name_migration = $this->getMigrationName();
                    $this->saveTemplate($name_migration,
                        $this->setTemplate(
                            $name_migration,
                            $row,
                            $down[$it][$i],
                            $desc,
                            get_current_user()
                        ),$it);

                        sleep(2); # sleep 2 seconds
                $i++;}
            }
        }
    }


    /**
     *
     *
     * Other
     *
     *
     */

    /**
     * createOther
     * @param array $args
     * @param array $options
     */
    public function createOther(array $args, array $options = array())
    {
        # get description options
        $desc = (isset($options['d'])) ? $options['d'] : "";

        $up_data = array();
        $down_data = array();

        $name_method = "other";
        $desc = $desc." #custom";

        $this->_save(
            $this->setTemplateMethod(strtolower($name_method), 'create', $up_data),
            $this->setTemplateMethod(strtolower($name_method), 'create', $down_data, "down")
            ,$desc
        );
    }

    /**
     * _save
     * @param $up_content
     * @param $down_content
     * @param bool $tag
     * @param $desc
     */
    private function _save($up_content,$down_content,$desc,$tag = false)
    {

        if (empty($desc)) {
            $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk.PHP_EOL.$this->color('Description:',\ConsoleKit\Colors::BLUE), "",false);
        }
        if ($tag) {
            $desc = $desc . " #" . $tag;
        }

        if (!$this->isMulti()) {

            $name_migration = $this->getMigrationName();
            $this->saveTemplate($name_migration,
                $this->setTemplate(
                    $name_migration,
                    $up_content,
                    $down_content,
                    $desc,
                    get_current_user()
                ), $tag);

        } else {

            $db = debug_backtrace();
            $this->setMultiHeaders($this->color('>',\ConsoleKit\Colors::YELLOW)." ".$db[1]['function']);
            $this->setMultiAddReturn($up_content,$tag);
            $this->setMultiDeleteReturn($down_content,$tag);

        }
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

    /**
     * @return boolean
     */
    public function isMulti()
    {
        return $this->isMulti;
    }

    /**
     * @param boolean $isMulti
     */
    public function setMulti($isMulti)
    {
        $this->isMulti = $isMulti;
    }

    /**
     * @return array
     */
    public function getMultiAddReturn()
    {
        return (array) $this->multiAddReturn;
    }

    /**
     * @param array $multiAddReturn
     */
    public function setMultiAddReturn($multiAddReturn,$type = "add")
    {
        $this->multiAddReturn[$type][] = $multiAddReturn;
    }

    /**
     * @return array
     */
    public function getMultiDeleteReturn()
    {
        return (array) $this->multiDeleteReturn;
    }

    /**
     * @param array $multiDeleteReturn
     */
    public function setMultiDeleteReturn($multiDeleteReturn,$type = "add")
    {
        $this->multiDeleteReturn[$type][] = $multiDeleteReturn;
    }

    /**
     * @return array
     */
    public function getMultiHeaders()
    {
        return $this->multiHeaders;
    }

    /**
     * @param $multiHeaders
     * @internal param array $multiHeders
     */
    public function setMultiHeaders($multiHeaders)
    {
        $this->multiHeaders[] = $multiHeaders;
    }

    /**
     * @return null
     */
    public function getMultiCurrentCommand()
    {
        return $this->multiCurrentCommand;
    }

    /**
     * @param null $multiCurrentCommand
     */
    public function setMultiCurrentCommand($multiCurrentCommand)
    {
        $this->multiCurrentCommand = $multiCurrentCommand;
    }

}
