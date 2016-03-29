<?php

namespace Bim\Db\Main;

use Bim\Exception\BimException;

\CModule::IncludeModule("main");

/**
 * Class GroupIntegrate
 *
 * Documentation: http://cjp2600.github.io/bim-core/
 * @package Bim\Db\Main
 */
class GroupIntegrate
{

    /**
     * Add
     * @param $fields
     * @return array
     * @throws \Exception
     */
    public static function Add($fields)
    {
        $group = new \CGroup();
        unset($fields['ID']);
        if (empty($fields['STRING_ID'])) {
            return array('type' => 'error', 'error_text' => 'Field STRING_ID is required.');
        }
        if (!isset($fields['ACTIVE']) || empty($fields['ACTIVE'])) {
            $fields['ACTIVE'] = "N";
        }
        if (!isset($fields['C_SORT']) || empty($fields['C_SORT'])) {
            $fields['C_SORT'] = 100;
        }
        $groupDbRes = $group->GetList($by = 'sort', $sort = 'asc', array('STRING_ID' => $fields['STRING_ID']));
        if ($groupDbRes !== false && $groupDbRes->SelectedRowsCount()) {
            throw new BimException('Group with STRING_ID = "' . $fields['STRING_ID'] . '" already exist.');
        }
        $group = new \CGroup;
        $ID = $group->Add($fields);
        if ($ID) {
            return $ID;
        } else {
            throw new BimException($group->LAST_ERROR);
        }
    }


    /**
     * Update
     * @param $CODE
     * @param $fields
     * @return bool
     */
    public function Update($CODE, $fields)
    {
        return true;
    }


    /**
     * Delete
     * @param $CODE
     * @return array
     * @throws \Exception
     */
    public static function Delete($CODE)
    {
        $group = new \CGroup();
        $user = new \CUser();
        if (!empty($CODE)) {
            $by = "ID";
            $order = "asc";
            $dbGroup = $group->GetList($by, $order, array('STRING_ID' => $CODE));
            if ($arGroup = $dbGroup->Fetch()) {
                $arReturn = $arGroup;
            }
        } else {
            throw new BimException('Empty group code');
        }

        if (intval($arReturn['ID']) > 0) {
            $arUsers = $group->GetGroupUser($arReturn['ID']);
            foreach ($arUsers as $UserID) {
                $arGroup = $user->GetUserGroup($UserID);
                $arGroup[] = "3";
                $user->SetUserGroup($UserID, $arGroup);
            }
            $res = $group->Delete($arReturn['ID']);

            if (is_object($res)) {
                return $arReturn['ID'];
            } else {
                throw new BimException($group->LAST_ERROR);
            }
        } else {
            throw new BimException('Group not found');
        }
    }


}