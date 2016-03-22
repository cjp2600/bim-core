<?php

namespace Bim\Db\Main;

\CModule::IncludeModule("main");

/**
 * Class SiteIntegrate
 * @package Bim\Db\Main
 */
class SiteIntegrate
{
    /**
     * Add
     * @param $fields
     * @return array
     * @throws \Exception
     * @internal param bool $isRevert
     */
    public function Add($fields)
    {
        $arReqFields = array('LID', 'FORMAT_DATE', 'FORMAT_DATETIME', 'CHARSET', 'DIR');
        foreach ($arReqFields as $key) {
            if (empty($fields[$key])) {
                throw new \Exception('Field ' . $key . ' is empty.');
            }
        }
        if (isset($fields['FORMAT_NAME']) && empty($fields['FORMAT_NAME'])) {
            $fields['FORMAT_NAME'] = "#NOBR##NAME# #LAST_NAME##/NOBR#";
        }
        if (!isset($fields['ACTIVE']) || empty($fields['ACTIVE'])) {
            $fields['ACTIVE'] = "N";
        }
        if (!isset($fields['DEF']) || empty($fields['DEF'])) {
            $fields['DEF'] = "N";
        }

        $obSite = new \CSite;
        $ID = $obSite->Add($fields);
        if ($ID) {
            return $ID;
        } else {
            throw new \Exception($obSite->LAST_ERROR);
        }
    }

    /**
     * Update
     * @param $ID
     * @param $fields
     */
    public function Update($ID, $fields)
    {
        // Update
    }

    /**
     * Delete
     * @param $ID
     * @return mixed
     * @throws \Exception
     */
    public function Delete($ID)
    {
        $obSite = new \CSite;
        $dbSite = $obSite->GetList($by = "sort", $order = "desc", array('ID' => $ID));
        if ($arSite = $dbSite->Fetch()) {
            $res = $obSite->Delete($ID);
            if ($res) {
                return $ID;
            } else {
                throw new \Exception($obSite->LAST_ERROR);
            }
        }
    }


}