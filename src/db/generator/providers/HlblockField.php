<?php

namespace Bim\Db\Generator\Providers;

use Bim\Db\Generator\Code;
use Bim\Exception\BimException;
use \Bitrix\Highloadblock as HL;
use CIBlock;
use CUserTypeEntity;

/**
 * Class HlblockField
 * @package Bim\Db\Generator
 */
class HlblockField extends Code
{
    /**
     * @var \CUserTypeEntity|null
     */
    private $userType = null;

    /**
     * @var \CIBlock|null
     */
    private $iblock = null;

    /**
     * HlblockField constructor.
     */
    public function __construct()
    {
        # Требует обязательного подключения модуля
        # highloadblock

        \CModule::IncludeModule("highloadblock");

        $this->iblock = new CIBlock();
        $this->userType = new CUserTypeEntity();
    }

    /**
     * Генерация создания
     *
     * generateAddCode
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);
        $return = "";
        $hlBlockData = $this->ownerItemDbData['hlblockData'];
        if ($hlFieldData = $this->ownerItemDbData['hlFieldData']) {
            unset($hlFieldData['ID']);
            unset($hlFieldData['ENTITY_ID']);

            # add iblock code to
            if (($hlFieldData['USER_TYPE_ID'] == "iblock_element" || $hlFieldData['USER_TYPE_ID'] == "iblock_section") && (isset($hlFieldData['SETTINGS']['IBLOCK_ID']))) {
                if (!empty($hlFieldData['SETTINGS']['IBLOCK_ID'])) {
                    $iblockId = $hlFieldData['SETTINGS']['IBLOCK_ID'];
                    unset($hlFieldData['SETTINGS']['IBLOCK_ID']);
                    $rsIBlock = $this->iblock->GetList(array(), array('ID' => $iblockId, 'CHECK_PERMISSIONS' => 'N'));
                    if ($arIBlock = $rsIBlock->Fetch()) {
                        $hlFieldData['SETTINGS']['IBLOCK_CODE'] = $arIBlock['CODE'];
                    } else {
                        throw new BimException(' Not found iblock with id ' . $iblockId);
                    }
                }
            }

            $return = $this->getMethodContent('Bim\Db\Iblock\HighloadblockFieldIntegrate', 'Add',
                array($hlBlockData['NAME'], $hlFieldData));
        }
        return $return;
    }

    /**
     *  Генерация кода обновления
     *
     * generateUpdateCode
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function generateUpdateCode($params)
    {
        // UPDATE
    }

    /**
     * метод для генерации кода удаления
     *
     * generateDeleteCode
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);
        $return = "";
        $hlBlockData = $this->ownerItemDbData['hlblockData'];
        if ($hlFieldData = $this->ownerItemDbData['hlFieldData']) {
            $return = $this->getMethodContent('Bim\Db\Iblock\HighloadblockFieldIntegrate', 'Delete',
                array($hlBlockData['NAME'], $hlFieldData['FIELD_NAME']));
        }
        return $return;
    }

    /**
     * Абстрактный метод проверки передаваемых параметров
     *
     * checkParams
     * @param array $params
     * @return mixed|void
     * @throws \Exception
     */
    public function checkParams($params)
    {
        if (!isset($params['hlblockId']) || empty($params['hlblockId'])) {
            throw new BimException('В параметрах не найден hlblockId');
        }
        if (!isset($params['hlFieldId']) || empty($params['hlFieldId'])) {
            throw new BimException('В параметрах не найден hlFieldId');
        }
        $hlBlock = HL\HighloadBlockTable::getById($params['hlblockId'])->fetch();
        if (!$hlBlock) {
            throw new BimException('В системе не найден highload инфоблок с id = ' . $params['hlblockId']);
        }
        $this->ownerItemDbData['hlblockData'] = $hlBlock;
        if ($params['hlFieldId']) {
            $userFieldData = $this->userType->GetByID($params['hlFieldId']);
            if ($userFieldData === false || empty($userFieldData)) {
                throw new BimException('Не найдено пользовательское поле с id = ' . $params['hlFieldId']);
            }
            $this->ownerItemDbData['hlFieldData'] = $userFieldData;
        }
    }

}