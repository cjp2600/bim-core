<?php

namespace Bim\Db\Lib;
use Bim\Db\Lib\CodeGenerator;

/**
 * Class GroupGen
 * @package Bim\Db\Lib
 */
class GroupGen extends CodeGenerator
{

    /**
     * generateAddCode
     * @param array $groupId
     * @return string
     * @throws \Exception
     * @internal param array $params
     */
    public function generateAddCode( $groupId )
    {
        $this->checkParams( $groupId );
        if ( $groupData = $this->ownerItemDbData ) {
            unset( $groupData['ID'] );
            $addFields = $groupData;
           return $this->getMethodContent('Bim\Db\Main\GroupIntegrate', 'Add', array($addFields));
        }
        return true;
    }


    /**
     * generateUpdateCode
     * @param array $params
     * @return mixed|void
     */
    public function generateUpdateCode( $params )
    {
        // Update generate
    }


    /**
     * generateDeleteCode
     * @param array $groupId
     * @return string
     * @throws \Exception
     * @internal param array $params
     */
    public function generateDeleteCode( $groupId )
    {
        $this->checkParams( $groupId );

        if ( $groupData = $this->ownerItemDbData ) {
            unset( $groupData['ID'] );
            return  $this->getMethodContent('Bim\Db\Main\GroupIntegrate', 'Delete', array( $groupData['STRING_ID'] ));
        }
        return false;
    }

    /**
     * checkParams
     * @param array $groupId
     * @return mixed|void
     * @throws \Exception
     * @internal param array $params
     */
    public function checkParams( $groupId )
    {
        if (!isset( $groupId ) || empty( $groupId )) {
            throw new \Exception('empty groupId param');
        }
        $this->ownerItemDbData = array();
        $groupDbRes = \CGroup::GetList($by = 'id', $order = 'desc', array('ID' => $groupId));
        if ($groupDbRes === false || !$groupDbRes->SelectedRowsCount()) {
            throw new \Exception('Group with id = ' . $groupId .' not exists');
        }
        $groupData = $groupDbRes->Fetch();
        if (!strlen($groupData['STRING_ID'])) {
            throw new \Exception('Group with id = ' . $groupId . ' have empty STRING_ID!');
        }
        $this->ownerItemDbData = $groupData;
    }


}
?>
