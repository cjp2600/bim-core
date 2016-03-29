<?php

namespace Bim\Db\Generator\Providers;

use Bim\Db\Generator\Code;
use Bim\Util\Helper;

/**
 * Класс генерации кода Информационных блоков
 *
 * Class IblockGenerator
 * @package Bim\Db\Generator\Providers
 */
class Iblock extends Code
{
    /**
     * @var \CIBlock|null
     */
    private $iblock = null;

    /**
     * IblockGenerator constructor.
     */
    public function __construct()
    {
        # Требует обязательного подключения модуля 
        # Iblock
        \CModule::IncludeModule('iblock');

        $this->iblock = new \CIBlock();
    }

    /**
     * Генерация создания Информационного блока
     *
     * generateAddCode
     * @param array $IblockCode
     * @return bool|string
     */
    public function generateAddCode($IblockCode)
    {
        $return = array();
        $iblockObject = $this->iblock->GetList(array(), array('CODE' => $IblockCode, 'CHECK_PERMISSIONS' => 'N'));

        if ($item = $iblockObject->Fetch()) {

            # Установка групп пользователей
            $this->setUserGroupId($item['ID'], $item);

            $item['FIELDS'] = \CIBlock::GetFields($item['ID']);
            Helper::unsetFields(array('ID'),$item);

            if ($return[] = $this->getMethodContent('Bim\Db\Iblock\IblockIntegrate', 'Add', array($item))) {

                $IblockProperty = new \CIBlockProperty();
                $iblockPropertyQuery = $IblockProperty->GetList(array(), array('IBLOCK_CODE' => $item['CODE']));
                while ($iblockProperty = $iblockPropertyQuery->Fetch()) {
                    Helper::unsetFields(array('ID'),$iblockProperty);

                    $iblockProperty['IBLOCK_CODE'] = $item['CODE'];
                    $propertyQuery = \CIBlockPropertyEnum::GetList(
                        array(),
                        array("IBLOCK_ID" => $iblockProperty['IBLOCK_ID'], "CODE" => $iblockProperty['CODE']));
                    while ($propertyValues = $propertyQuery->Fetch()) {

                        Helper::unsetFields(array('ID','PROPERTY_ID'),$propertyValues);
                        $iblockProperty['VALUES'][] = $propertyValues;

                    }
                    if (!is_null($iblockProperty['LINK_IBLOCK_ID'])) {
                        $linkedIBlock = $this->iblock->GetList(array(),
                            array('ID' => $iblockProperty['LINK_IBLOCK_ID'], 'CHECK_PERMISSIONS' => 'N'))->Fetch();
                        $iblockProperty['LINK_IBLOCK_CODE'] = $linkedIBlock['CODE'];
                    }
                    $return[] = $this->getMethodContent('Bim\Db\Iblock\IblockPropertyIntegrate', 'Add',
                        array($iblockProperty));
                }

                return implode(PHP_EOL, $return);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Генерация кода обновления инфоблока
     *
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);
        $code = false;
        foreach ($this->ownerItemDbData as $iblockData) {
            $updateFields = $iblockData;

            Helper::unsetFields(array('ID'),$updateFields);

            $updateFields['FIELDS'] = \CIBlock::GetFields($iblockData['ID']);
            $this->setUserGroupId($iblockData['ID'], $iblockData);

            $code = $code . $this->getMethodContent('Bim\Db\Iblock\IblockIntegrate', 'Update',
                    array($updateFields['CODE'], $updateFields)) . PHP_EOL . PHP_EOL;
        }
        return $code;
    }

    /**
     * Метод для генерации кода удаления  инфоблока
     * 
     * @param array $iblockCode
     * @return mixed
     * @internal param array $params
     */
    public function generateDeleteCode($iblockCode)
    {
        return $this->getMethodContent('Bim\Db\Iblock\IblockIntegrate', 'Delete', array($iblockCode));
    }

    /**
     * Абстрактный метод проверки передаваемых параметров
     * 
     * @param $params array
     * @return mixed
     */
    public function checkParams($params)
    {
        // TODO: Implement checkParams() method.
    }

    /**
     * Установка групп пользователей
     *
     * @param $id : ID Информационного блока
     * @param $item : Формируемый массив
     */
    private function setUserGroupId($id, &$item)
    {
        $item['GROUP_ID'] = $this->iblock->GetGroupPermissions($id);
        $arGroups = Helper::getUserGroups();
        foreach ($item['GROUP_ID'] as $groupId => $right) {
            $groupCode = Helper::getUserGroupCode($groupId, $arGroups);
            if ($groupCode != null && strlen($groupCode) > 0) {
                $item['GROUP_ID'][$groupCode] = $item['GROUP_ID'][$groupId];
                unset($item['GROUP_ID'][$groupId]);
            }
        }
    }

}