<?php

namespace Bim\Db\Generator\Providers;

use Bim\Db\Generator\Code;
use Bim\Exception\BimException;


/**
 * Class SiteGen
 */
class Site extends Code
{
    /**
     * Генерация создания
     *
     * @param $siteId
     * @return string
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
     * Генерация кода обновления
     *
     * generateUpdateCode
     * @param $params
     * @return mixed|void
     */
    public function generateUpdateCode($params)
    {
        // Update
    }

    /**
     * Метод для генерации кода удаления
     *
     * generateDeleteCode
     * @param $siteId
     * @return mixed
     */
    public function generateDeleteCode($siteId)
    {
        $this->checkParams($siteId);

        $siteData = $this->ownerItemDbData;
        return $this->getMethodContent('Bim\Db\Main\SiteIntegrate', 'Delete', array($siteData['LID']));
    }


    /**
     * Абстрактный метод проверки передаваемых параметров
     *
     * checkParams
     * @param array $siteId
     * @return mixed|void
     * @throws \Exception
     */
    public function checkParams($siteId)
    {
        $site = new \CSite();
        if (!isset($siteId) || empty($siteId)) {
            throw new BimException('В параметрах не найден siteId');
        }
        $this->ownerItemDbData = array();
        $siteDbRes = $site->GetList($by = "lid", $order = "desc", array('LID' => $siteId));
        if ($siteDbRes === false || !$siteDbRes->SelectedRowsCount()) {
            throw new BimException('Не найден сайт с id = ' . $siteId);
        }
        $siteData = $siteDbRes->Fetch();
        $this->ownerItemDbData = $siteData;
    }


}