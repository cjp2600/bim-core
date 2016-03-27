<?php


/**
 * Class AdvTypeGen
 * класс для генерации кода изменений типа баннеров
 *
 * @package Bitrix\Adv_Preset\Saletype
 */
class AdvTypeGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('advertising');
    }

    /**
     * метод для генерации кода добавления типа баннеров
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем тип баннеров */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $typeData) {
            $addFields = $typeData;

            $code = $code . $this->buildCode('ADVTypeIntegrate', 'Set', array($addFields, '', 'N')) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления типа баннеров
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем тип баннеров */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $typeData) {
            $updateFields = $typeData;


            $code = $code . $this->buildCode('ADVTypeIntegrate', 'Set',
                    array($updateFields, $updateFields['SID'], 'N')) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  налога
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  тип баннеров   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $typeData) {
            $code = $code . $this->buildCode('ADVTypeIntegrate', 'Delete', array($typeData['SID'], 'N'));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * typeId => id типа баннеров
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['typeId']) || empty($params['typeId'])) {
            throw new \Exception('В параметрах не найден typeId');
        }

        foreach ($params['typeId'] as $typeId) {

            $typeDbRes = \CAdvType::GetByID($typeId);
            $typeData = $typeDbRes->Fetch();

            $this->ownerItemDbData[] = $typeData;
        }


    }


}

?>
