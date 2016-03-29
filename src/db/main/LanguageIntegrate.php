<?php
namespace Bim\Db\Main;

use Bim\Exception\BimException;

\CModule::IncludeModule("main");

/**
 * Class LanguageIntegrate
 * @package Bim\Db\Main
 */
class LanguageIntegrate
{

    /**
     * Добавление языка
     *
     * @param $fields
     * @return array|bool
     * @throws BimException
     */
    public static function Add($fields)
    {
        $arReqFields = array('LID', 'SORT', 'NAME', 'FORMAT_DATE', 'FORMAT_DATETIME', 'CHARSET');
        foreach ($arReqFields as $key) {
            if (empty($fields[$key])) {
                throw new BimException('Field ' . $key . ' is empty.');
            }
        }

        if (!isset($fields['ACTIVE']) || empty($fields['ACTIVE'])) {
            $fields['ACTIVE'] = "N";
        }
        if (!isset($fields['DIRECTION']) || empty($fields['DIRECTION'])) {
            $fields['DIRECTION'] = "Y";
        }
        if (!isset($fields['DEF']) || empty($fields['DEF'])) {
            $fields['DEF'] = "N";
        }
        if (!isset($fields['FORMAT_NAME']) || empty($fields['FORMAT_NAME'])) {
            $fields['FORMAT_NAME'] = "#NOBR##NAME# #LAST_NAME##/NOBR#";
        }

        $obLang = new \CLanguage();
        $ID = $obLang->Add($fields);
        if ($ID) {
            return $ID;
        } else {
            throw new BimException($obLang->LAST_ERROR);
        }
    }

    /**
     * @return bool
     */
    public static function Update()
    {
        return true;
    }

    /**
     * Удаление
     *
     * @param $ID
     * @return array
     * @throws BimException
     */
    public static function Delete($ID)
    {
        $obLang = new \CLanguage();
        if ($ID) {
            $dbLang = $obLang->GetList($by = "lid", $order = "desc", array('ID' => $ID));
            if ($arLang = $dbLang->Fetch()) {

                $res = $obLang->Delete($ID);
                if (!$res) {
                     throw new BimException($obLang->LAST_ERROR);
                }
            }
        } else {
            throw new BimException("Language ID is empty");
        }
    }


}