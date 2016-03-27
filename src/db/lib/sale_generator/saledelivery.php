<?php

/**
 * Class SaleDeliveryGen
 * класс для генерации кода изменений в данных службах доставки
 *
 * @package Bitrix\Adv_Preset\SaleDeliveryGen
 */
class SaleDeliveryGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления новой службы доставки
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем новую службу доставки */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $deliveryData) {
            $addFields = $deliveryData;
            unset($addFields['ID']);

            $code = $code . $this->buildCode('SaleDeliveryIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления службы доставки
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем службу доставки */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $deliveryData) {
            $updateFields = $deliveryData;
            unset($updateFields['ID']);

            $code = $code . $this->buildCode('SaleDeliveryIntegrate', 'Update',
                    array($updateFields['NAME'], $updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  службы доставки
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  службу доставки   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $deliveryData) {
            $code = $code . $this->buildCode('SaleDeliveryIntegrate', 'Delete', array($deliveryData['NAME']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * deliveryId => id службы доставки
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['deliveryId']) || empty($params['deliveryId'])) {
            throw new \Exception('В параметрах не найден deliveryId');
        }

        foreach ($params['deliveryId'] as $deliveryId) {
            $deliveryDbRes = \CSaleDelivery::GetList(array(), array('ID' => $deliveryId));
            if ($deliveryDbRes === false || !$deliveryDbRes->SelectedRowsCount()) {
                throw new \Exception('В системе не найдена служба доставки с id = ' . $deliveryId);
            }

            $deliveryData = $deliveryDbRes->Fetch();
            $locationDbRes = CSaleDelivery::GetLocationList(array('DELIVERY_ID' => $deliveryId));
            while ($locationData = $locationDbRes->Fetch()) {
                unset($locationData['DELIVERY_ID']);
                $deliveryData['LOCATIONS'][] = $locationData;
            }

            $this->ownerItemDbData[] = $deliveryData;
        }


    }


}

?>
