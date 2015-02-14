<?php

namespace Bim\Db\Iblock;
\CModule::IncludeModule("iblock");

/**
 * Class IblockTypeIntegrate
 *
 * Documentation: http://cjp2600.github.io/bim-core/
 * @package Bim\Db\Iblock
 */
class IblockTypeIntegrate
{
    /**
     * Add
     * @param $arFields
     * @return bool
     * @throws \Exception
     * @internal param bool $isRevert
     */
    public function Add($arFields)
    {
        if (!isset($arFields['SECTIONS']) || empty($arFields['SECTIONS'])) {
            $arFields['SECTIONS'] = 'Y';
        }
        if (!isset($arFields['IN_RSS']) || empty($arFields['IN_RSS'])) {
            $arFields['IN_RSS'] = 'N';
        }
        if (isset($arFields['SORT']))
        {
            if (!is_int($arFields['SORT']))
            {
                if (intval($arFields['SORT'])) {
                    $arFields['SORT'] = intval($arFields['SORT']);
                } else {
                    $arFields['SORT'] = 500;
                }
            }
        }
        else {
            $arFields['SORT'] = 500;
        }
        if(!isset($arFields['LANG']) || empty($arFields['LANG']))
        {
            $langDefaults = array(
                'ru' => array(
                    'NAME'          => 'Название',
                    'SECTION_NAME'  => 'Разделы',
                    'ELEMENT_NAME'  => 'Элементы',
                ),
                'en' => array(
                    'NAME'          => 'Common',
                    'SECTION_NAME'  => 'Sections',
                    'ELEMENT_NAME'  => 'Elements',
                ),
            );
            $l = \CLanguage::GetList($lby="sort", $lorder="asc");
            while($arIBTLang = $l->GetNext())
            {
                if (array_key_exists($arIBTLang["LID"], $langDefaults)) {
                    $arFields["LANG"][$arIBTLang["LID"]] = $langDefaults[$arIBTLang["LID"]];
                }
            }
        }
        $CIblockType = new \CIBlockType();
        if ($CIblockType->Add($arFields)) {
            return true;
        } else {
            throw new \Exception($CIblockType->LAST_ERROR);
        }
        return false;
    }

    /**
     * Delete
     * @param $IblockTypeCode
     * @return bool
     * @throws \Exception
     */
    public function Delete($IblockTypeCode)
    {
        $Iblock = new \CIBlock();
        $dbIblock = $Iblock->GetList(array(), array('TYPE' => $IblockTypeCode));
        while ($dbRow = $dbIblock->Fetch()) {
            $iblockElDbRes = \CIBlockElement::GetList(array(), array('IBLOCK_ID' => $dbRow['ID']));
            if ($iblockElDbRes !== false && $iblockElDbRes->SelectedRowsCount()) {
                throw new \Exception('Can not delete iblock type: iblock id =' . $dbRow['ID'] . ' have elements');
            }
        }
        if (!\CIBlockType::Delete($IblockTypeCode)) {
            throw new \Exception('Delete iblock type error!');
        }
        return true;
    }

}