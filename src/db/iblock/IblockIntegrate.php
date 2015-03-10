<?php

namespace Bim\Db\Iblock;
\CModule::IncludeModule("iblock");

/**
 * Class IblockIntegrate
 *
 * Documentation: http://cjp2600.github.io/bim-core/
 * @package Bim\Db\Iblock
 */
class IblockIntegrate
{
    /**
     * Add Iblock
     * @param $arFields
     * @return bool
     * @throws \Exception
     * @internal param bool $isRevert
     */
    public function Add( $arFields )
    {
        if (isset($arFields['SORT'])) {
            if (!is_int($arFields['SORT'])) {
                if (intval($arFields['SORT'])) {
                    $arFields['SORT'] = intval($arFields['SORT']);
                } else {
                    $arFields['SORT'] = 500;
                }
            }
        } else {
            $arFields['SORT'] = 500;
        }
        # default values
        $arDefaultValues = array(
            'ACTIVE' => 'Y',
            'LIST_PAGE_URL' => '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/index.php?ID=#IBLOCK_ID#',
            'SECTION_PAGE_URL' => '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/list.php?SECTION_ID=#ID#',
            'DETAIL_PAGE_URL' => '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/detail.php?ID=#ID#',
            'INDEX_SECTION' => 'Y',
            'INDEX_ELEMENT' => 'Y',
            'PICTURE' => array(
                'del' => null,
                'MODULE_ID' => 'iblock',
            ),
            'DESCRIPTION' => '',
            'DESCRIPTION_TYPE' => 'text',
            'EDIT_FILE_BEFORE' => '',
            'EDIT_FILE_AFTER' => '',
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
            'SECTION_CHOOSER' => 'L',
            'LIST_MODE' => '',
            'FIELDS' => array(),
            'ELEMENTS_NAME' => 'Элементы',
            'ELEMENT_NAME' => 'Элемент',
            'ELEMENT_ADD' => 'Добавить элемент',
            'ELEMENT_EDIT' => 'Изменить элемент',
            'ELEMENT_DELETE' => 'Удалить элемент',
            'SECTIONS_NAME' => 'Разделы',
            'SECTION_NAME' => 'Раздел',
            'SECTION_ADD' => 'Добавить раздел',
            'SECTION_EDIT' => 'Изменить раздел',
            'SECTION_DELETE' => 'Удалить раздел',
            'RIGHTS_MODE' => 'S',
            'GROUP_ID' => array(
                2 => 'R',
                1 => 'X'
            ),
            'VERSION' => 1
        );
        if ( !strlen( $arFields['CODE'] ) ) {
            throw new \Exception('Not found iblock code');
        }
        $iblockDbRes = \CIBlock::GetList( array(), array('CODE' => $arFields['CODE'],'CHECK_PERMISSIONS'=>'N' ) );
        if ( $iblockDbRes !== false && $iblockDbRes->SelectedRowsCount() ) {
            throw new \Exception('Iblock with code = "' . $arFields['CODE'] .'" already exist.');
        }
        foreach ($arDefaultValues as $DefaultName => $DefaultValue) {
            if (!isset($arFields[$DefaultName]) || empty($arFields[$DefaultName])) {
                $arFields[$DefaultName] = $DefaultValue;
            }
        }
        $CIblock = new \CIBlock();
        $ID = $CIblock->Add($arFields);
        if ($ID) {
           return $ID;
        } else {
            throw new \Exception($CIblock->LAST_ERROR);
        }
        return false;
    }

    /**
     * Delete
     * @param $IblockCode
     * @return bool
     * @throws \Exception
     */
    public function Delete($IblockCode)
    {
        $dbIblock = \CIBlock::GetList(array(), array('CODE' => $IblockCode,'CHECK_PERMISSIONS'=>'N'));
        if ($arIblock = $dbIblock->Fetch()) {
            $iblockElDbRes = \CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arIblock['ID']));
            if ($iblockElDbRes !== false && $iblockElDbRes->SelectedRowsCount()) {
                throw new \Exception('Can not delete iblock id = ' . $arIblock['ID'] . ' have elements');
            }
            if (\CIBlock::Delete($arIblock['ID'])) {
                return true;
            } else {
                throw new \Exception('Iblock delete error!');
            }
        } else {
            throw new \Exception('Not find iblock with code ' . $IblockCode);
        }
        return false;
    }

}