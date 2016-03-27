<?php


/**
 * Class SaleOrderPropsGen
 * класс для генерации кода изменений  свойств заказа
 *
 * @package Bitrix\Adv_Preset\SaleOrderPropsGen
 */
class SaleOrderPropsGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления нового свойства заказа
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем свойство заказа */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $propData) {
            $addFields = $propData;

            $code = $code . $this->buildCode('SaleOrderPropsIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления свойство заказа
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем свойство заказа */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $propData) {
            $updateFields = $propData;

            $code = $code . $this->buildCode('SaleOrderPropsIntegrate', 'Update',
                    array($updateFields['CODE'], $updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления свойство заказа
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем свойство заказа   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $propData) {
            $code = $code . $this->buildCode('SaleOrderPropsIntegrate', 'Delete', array($propData['CODE']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * propId => id свойства
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['propId']) || empty($params['propId'])) {
            throw new \Exception('В параметрах не найден propId');
        }


        foreach ($params['propId'] as $propId) {

            $propData = \CSaleOrderProps::GetByID($propId);
            if (!$propData) {
                throw new \Exception('В системе не найден св-во с id = "' . $propId . '"');
            }
            if (!strlen($propData['CODE'])) {
                throw new \Exception('У св-ва с id = "' . $propId . '" не указан символьный код');
            }

            $propGroupData = CSaleOrderPropsGroup::GetByID($propData['PROPS_GROUP_ID']);
            unset($propData['ID']);
            unset($propData['PROPS_GROUP_ID']);
            $propData['PROPS_GROUP_NAME'] = $propGroupData['NAME'];


            $this->ownerItemDbData[] = $propData;
        }


    }


}

?>
