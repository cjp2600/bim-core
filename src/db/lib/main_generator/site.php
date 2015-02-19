<?php

namespace Bim\Db\Lib;
use Bim\Db\Lib\CodeGenerator;

/**
 * Class SiteGen
 */
class SiteGen extends CodeGenerator
{
    /**
     * @param $siteId
     * @return string
     * @throws Exception
     * @internal param array $params
     */
    public function generateAddCode($siteId)
    {
        $this->checkParams($siteId);
        $siteData = $this->ownerItemDbData;
        unset($siteData['ID']);
        $addFields = $siteData;
        return $this->getMethodContent('Bim\Db\Main\SiteIntegrate', 'Add', array($addFields));
    }

    /**
     * generateUpdateCode
     * @param $params
     * @return mixed|void
     */
    public function generateUpdateCode( $params )
    {
        // Update
    }

    /**
     * generateDeleteCode
     * @param $siteId
     * @return mixed
     * @throws Exception
     */
    public function generateDeleteCode( $siteId )
    {
        $this->checkParams( $siteId );

        $siteData = $this->ownerItemDbData;
        return $this->getMethodContent('Bim\Db\Main\SiteIntegrate', 'Delete', array( $siteData['LID'] ));
    }


    /**
     * checkParams
     * @param array $siteId
     * @return mixed|void
     * @throws \Exception
     */
    public function checkParams( $siteId  )
    {
        if ( !isset( $siteId ) || empty( $siteId ) ) {
            throw new \Exception( 'В параметрах не найден siteId' );
        }
        $this->ownerItemDbData = array();
        $siteDbRes = \CSite::GetList( $by="lid", $order="desc", array('LID' => $siteId )  );
        if ( $siteDbRes === false || !$siteDbRes->SelectedRowsCount() ) {
            throw new \Exception( 'Не найден сайт с id = ' . $siteId );
        }
        $siteData = $siteDbRes->Fetch();
        $this->ownerItemDbData = $siteData;
    }



}