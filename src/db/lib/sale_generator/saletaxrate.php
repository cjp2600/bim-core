<?php


/**
 * Class SaleTaxRateGen
 * класс для генерации кода изменений ставки налога
 *
 * @package Bitrix\Adv_Preset\Salerate
 */
class SaleTaxRateGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('sale');
    }

    /**
     * метод для генерации кода добавления ставки налога
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем ставку налога */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $rateData) {
            $addFields = $rateData;

            $code = $code . $this->buildCode('SaleTaxRateIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления ставки налога
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем ставку налога */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $rateData) {
            $updateFields = $rateData;


            $code = $code . $this->buildCode('SaleTaxRateIntegrate', 'Update',
                    array($updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  ставки налога
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  ставку налога   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $rateData) {
            $code = $code . $this->buildCode('SaleTaxRateIntegrate', 'Delete', array($rateData));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * rateId => id ставки налога
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['rateId']) || empty($params['rateId'])) {
            throw new \Exception('В параметрах не найден rateId');
        }

        foreach ($params['rateId'] as $rateId) {
            $rateData = \CSaleTaxRate::GetByID($rateId);
            $taxData = \CSaleTax::GetByID($rateData['TAX_ID']);
            if (!strlen($taxData['CODE'])) {
                throw new \Exception('У налога с id = "' . $rateData['TAX_ID'] . '" не указан CODE');
            }
            unset($rateData['ID']);
            unset($rateData['TAX_ID']);
            $rateData['TAX_CODE'] = $taxData['CODE'];

            $personTypeData = CSalePersonType::GetById($rateData['PERSON_TYPE_ID']);
            $rateData['PERSON_TYPE_NAME'] = $personTypeData['NAME'];
            $rateData['LID'] = $personTypeData['LID'];
            $rateData['LANG'] = LANGUAGE_ID;
            unset($rateData['PERSON_TYPE_ID']);

            $saleTaxRateDbRes = CSaleTaxRate::GetLocationList(array('TAX_RATE_ID' => $rateId));
            while ($saleTaxRateRow = $saleTaxRateDbRes->Fetch()) {
                $rateLocation = array();
                $rateLocation['LOCATION_TYPE'] = $saleTaxRateRow['LOCATION_TYPE'];
                if ($saleTaxRateRow['LOCATION_TYPE'] == 'G') {
                    $locationGroupData = CSaleLocationGroup::GetById($saleTaxRateRow['LOCATION_ID']);
                    $rateLocation['LOCATION_NAME'] = $locationGroupData['NAME'];

                } else {
                    $locationData = CSaleLocation::GetById($saleTaxRateRow['LOCATION_ID']);
                    $rateLocation['LOCATION_COUNTRY_NAME'] = $locationData['COUNTRY_NAME_ORIG'];

                }
                $rateData['TAX_LOCATION'][] = $rateLocation;
            }


            $this->ownerItemDbData[] = $rateData;
        }


    }


}

?>
