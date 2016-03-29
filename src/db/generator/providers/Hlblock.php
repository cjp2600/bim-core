<?php

namespace Bim\Db\Generator\Providers;

use Bim\Db\Generator\Code;
use Bim\Exception\BimException;
use \Bitrix\Highloadblock as HL;
use CIBlock;
use CUserTypeEntity;

/**
 * Класс генерации кода Highload блоков
 *
 * Class Highloadblock
 * @package Bim\Db\Generator\Providers;
 */
class Hlblock extends Code
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
     * Highloadblock constructor.
     */
    public function __construct()
    {
        # Требует обязательного подключения модуля
        # Iblock, highloadblock

        \CModule::IncludeModule("highloadblock");
        \CModule::IncludeModule("iblock");

        $this->userType = new CUserTypeEntity();
        $this->iblock = new CIBlock();
    }


    /**
     * Генерация создания
     *
     * generateAddCode
     * @param array $hlBlockId
     * @return string
     * @throws \Exception
     */
    public function generateAddCode($hlBlockId)
    {
        $return = array();
        $hlBlock = HL\HighloadBlockTable::getById($hlBlockId)->fetch();
        if (!$hlBlock) {
            throw new BimException('Not found highload block with id = ' . $hlBlockId);
        }
        $return[] = $this->getMethodContent('Bim\Db\Iblock\HighloadblockIntegrate', 'Add',
            array($hlBlock['NAME'], $hlBlock['TABLE_NAME']));

        $hlQuery = $this->userType->GetList(array(), array("ENTITY_ID" => "HLBLOCK_" . $hlBlockId));
        while ($hlData = $hlQuery->Fetch()) {

            $fullData = $this->userType->GetByID($hlData['ID']);
            unset($fullData['ID']);
            unset($fullData['ENTITY_ID']);

            if (($fullData['USER_TYPE_ID'] == "iblock_element" || $fullData['USER_TYPE_ID'] == "iblock_section") && (isset($fullData['SETTINGS']['IBLOCK_ID']))) {
                if (!empty($fullData['SETTINGS']['IBLOCK_ID'])) {
                    $iblockId = $fullData['SETTINGS']['IBLOCK_ID'];
                    unset($fullData['SETTINGS']['IBLOCK_ID']);
                    $blockQuery = $this->iblock->GetList(array(), array('ID' => $iblockId, 'CHECK_PERMISSIONS' => 'N'));
                    if ($iBlockData = $blockQuery->Fetch()) {
                        $fullData['SETTINGS']['IBLOCK_CODE'] = $iBlockData['CODE'];
                    } else {
                        throw new BimException(' Not found iblock with id ' . $iblockId);
                    }
                }
            }

            $return[] = $this->getMethodContent('Bim\Db\Iblock\HighloadblockFieldIntegrate', 'Add',
                array($hlBlock['NAME'], $fullData));
        }
        return implode(PHP_EOL, $return);
    }


    /**
     *  Генерация кода обновления
     *
     * generateUpdateCode
     * @param array $params
     * @return mixed|void
     */
    public function generateUpdateCode($params)
    {
        // update
    }


    /**
     * метод для генерации кода удаления
     *
     * generateDeleteCode
     * @param array $hlBlockId
     * @return string
     * @throws \Exception
     */
    public function generateDeleteCode($hlBlockId)
    {
        $hlBlock = HL\HighloadBlockTable::getById($hlBlockId)->fetch();
        if (!$hlBlock) {
            throw new BimException('В системе не найден highload инфоблок с id = ' . $hlBlockId);
        }

        return $this->getMethodContent('Bim\Db\Iblock\HighloadblockIntegrate', 'Delete', array($hlBlock['NAME']));
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
        foreach ($params['hlblockId'] as $hlblockId) {
            $hlBlock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
            if (!$hlBlock) {
                throw new BimException('В системе не найден highload инфоблок с id = ' . $hlblockId);
            }
            $this->ownerItemDbData[] = $hlBlock;
        }
    }


}