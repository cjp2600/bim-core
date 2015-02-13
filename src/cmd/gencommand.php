<?php

/**
 * ======================================================
 * --  > BIM GEN | Команда создания миграционных классов.
 *
 * --  Принцип работы:
 *
 * Существует два вида создания миграций:
 *
 * 1) Создание кастомной миграции - Создается шаблон миграционного класса.
 * Структура класса определена интерфейсом Bim/Revision и включает следующие
 * обязательные методы:
 *
 * - - up(); - метод развертывания.
 * - - down(); - метод отката.
 * - - getDescription(); - получения описания.
 * - - getAuthor(); - получение автора.
 *
 * Задача кастомной миграции - развертывание и откат пользовательского
 * кода изменения схем bitrix БД.
 *
 * Пример:
 * > php bim gen
 *
 * 2) Генерация миграция по наличию - Создается код развертывания/отката элемента схемы bitrix БД
 * -- На данный момент доступно генерация по наличию для следующих элементов bitrix БД:
 *
 *
 * 1. IblockType ( php bim gen IblockType:[add|delete] ):
 * ------------------------------------------------------ *
 * Создается Миграционный код "Типа ИБ" включая (UserFields, IBlock, IblockProperty)
 *
 * Пример (запрашивается [IBLOCK_TYPE_ID]):
 *
 * > php bim gen IblockType:add
 *
 * также возможно передать iblock type id опционально:
 *
 * > php bim gen IblockType:add --typeId=catalog
 *
 *
 *
 * 2. Iblock ( php bim gen Iblock:[add|delete] ):
 * ----------------------------------------------
 * Создается Миграционный код "ИБ" включая (IblockProperty)
 *
 * Пример (запрашивается [IBLOCK_CODE]):
 *
 * > php bim gen Iblock:add
 *
 * также возможно передать iblock code опционально:
 *
 * > php bim gen Iblock:add --code=goods
 *
 *
 * ======================================================
 */
class GenCommand extends BaseCommand {

    # generate object
    private $gen_obj = null;
    private $isMulti = false;
    private $multiAddReturn = array();
    private $multiDeleteReturn = array();

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
            }

            # single command generator
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
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk . PHP_EOL . $this->color('Description:', \ConsoleKit\Colors::BLUE), "", false);
        }

        # set
        $autoTag = "add";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateAddCode($iblocktypeId),
                $this->gen_obj->generateDeleteCode($iblocktypeId),
                $desc." #".$autoTag,
                get_current_user()
            ),$autoTag);
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
        $autoTag = "delete";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateDeleteCode($iblocktypeId),
                $this->gen_obj->generateAddCode($iblocktypeId),
                $desc." #".$autoTag,
                get_current_user()
            ),$autoTag);
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

        if (!$this->isMulti()) {

            # get description options
            $desc = (isset($options['d'])) ? $options['d'] : "";
            if (empty($desc)) {
                $desk = "Type Description of migration file. Example: #TASK-124";
                $desc = $dialog->ask($desk . PHP_EOL . $this->color('Description:', \ConsoleKit\Colors::BLUE), "", false);
            }

            # set
            $autoTag = "add";
            $name_migration = $this->getMigrationName();
            $this->saveTemplate($name_migration,
                $this->setTemplate(
                    $name_migration,
                    $this->gen_obj->generateAddCode($code),
                    $this->gen_obj->generateDeleteCode($code),
                    $desc . " #" . $autoTag,
                    get_current_user()
                ), $autoTag);

        } else {

            $this->setMultiAddReturn($this->gen_obj->generateAddCode($code));
            $this->setMultiDeleteReturn($this->gen_obj->generateDeleteCode($code));

        }
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
        $autoTag = "delete";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateDeleteCode($code),
                $this->gen_obj->generateAddCode($code),
                $desc. " #". $autoTag,
                get_current_user()
            ), $autoTag);
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
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk.PHP_EOL.$this->color('Description:',\ConsoleKit\Colors::BLUE), "",false);
        }

        # set
        $autoTag = "add";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateAddCode($params),
                $this->gen_obj->generateDeleteCode($params),
                $desc." #".$autoTag,
                get_current_user()
            ),$autoTag);
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
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk.PHP_EOL.$this->color('Description:',\ConsoleKit\Colors::BLUE), "",false);
        }

        # set
        $autoTag = "delete";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateDeleteCode($params),
                $this->gen_obj->generateAddCode($params),
                $desc . " #".$autoTag,
                get_current_user()
            ),$autoTag);
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
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk.PHP_EOL.$this->color('Description:',\ConsoleKit\Colors::BLUE), "",false);
        }

        # set
        $autoTag = "add";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateAddCode($hlId),
                $this->gen_obj->generateDeleteCode($hlId),
                $desc . " #".$autoTag,
                get_current_user()
            ),$autoTag);
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
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk.PHP_EOL.$this->color('Description:',\ConsoleKit\Colors::BLUE), "",false);
        }

        # set
        $autoTag = "add";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateAddCode($hlId),
                $this->gen_obj->generateDeleteCode($hlId),
                $desc . " #".$autoTag,
                get_current_user()
            ),$autoTag);
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
        if (empty($desc)) {
            $desk = "Type Description of migration file. Example: #TASK-124";
            $desc = $dialog->ask($desk.PHP_EOL.$this->color('Description:',\ConsoleKit\Colors::BLUE), "",false);
        }

        # set
        $autoTag = "add";
        $name_migration = $this->getMigrationName();
        $this->saveTemplate($name_migration,
            $this->setTemplate(
                $name_migration,
                $this->gen_obj->generateAddCode(array("hlblockId" => $hlId,"hlFieldId"=>$hlFieldId)),
                $this->gen_obj->generateDeleteCode(array("hlblockId" => $hlId,"hlFieldId"=>$hlFieldId)),
                $desc . " #".$autoTag,
                get_current_user()
            ),$autoTag);
    }


    /**
     *
     *
     * MultiCommands
     *
     *
     */

    public function multiCommands(array $args, array $options = array())
    {
        $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
        $do = true;
        while ($do) {
            $desk = "Put generation commands:";
            $command = $dialog->ask($desk . " " . $this->color('php bim gen >', \ConsoleKit\Colors::MAGENTA), '', false);
            if (!empty($command)) {

                if ($command != "/") {
                    $this->setMulti(true);
                    $this->execute(array($command));
                } else {
                    $do = false;
                }

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
                $desc." #custom",
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
    public function setMultiAddReturn($multiAddReturn)
    {
        $this->multiAddReturn[] = $multiAddReturn;
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
    public function setMultiDeleteReturn($multiDeleteReturn)
    {
        $this->multiDeleteReturn[] = $multiDeleteReturn;
    }

}
