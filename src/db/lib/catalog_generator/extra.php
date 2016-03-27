<?php

/**
 * Class ExtraGen
 * класс для генерации кода изменений в данных наценок
 *
 * @package Bitrix\Adv_Preset\ExtraGen
 */
class ExtraGen extends CodeGenerator
{


    public function __construct()
    {
        \CModule::IncludeModule('catalog');
    }

    /**
     * метод для генерации кода добавления новой наценки
     * @param $params array
     * @return mixed
     */
    public function generateAddCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Добавляем новую наценку */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $extraData) {
            $addFields = $extraData;
            unset($addFields['ID']);

            $code = $code . $this->buildCode('ExtraIntegrate', 'Add', array($addFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода обновления наценки
     * @param $params array
     * @return mixed
     */
    public function generateUpdateCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Обновляем наценку */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $extraData) {
            $updateFields = $extraData;
            unset($updateFields['ID']);

            $code = $code . $this->buildCode('ExtraIntegrate', 'Update',
                    array($updateFields['NAME'], $updateFields)) . PHP_EOL . PHP_EOL;
        }


        return $code;

    }

    /**
     * метод для генерации кода удаления  наценки
     * @param $params array
     * @return mixed
     */
    public function generateDeleteCode($params)
    {
        $this->checkParams($params);

        $code = '<?php' . PHP_EOL . '/*  Удаляем  наценку   */' . PHP_EOL . PHP_EOL;
        foreach ($this->ownerItemDbData as $extraData) {
            $code = $code . $this->buildCode('ExtraIntegrate', 'Delete', array($extraData['NAME']));
        }

        return $code;

    }


    /**
     * метод проверки передаваемых параметров
     * @param $params array(
     * extraId => id наценки
     * )
     * @return mixed
     */
    public function checkParams($params)
    {

        if (!isset($params['extraId']) || empty($params['extraId'])) {
            throw new \Exception('В параметрах не найден extraId');
        }

        foreach ($params['extraId'] as $extraId) {
            $extraDbRes = \CExtra::GetList(array(), array('ID' => $extraId));
            if ($extraDbRes === false || !$extraDbRes->SelectedRowsCount()) {
                throw new \Exception('В системе не найдена наценка с id = ' . $extraId);
            }

            $extraData = $extraDbRes->Fetch();
            $this->ownerItemDbData[] = $extraData;
        }


    }


}

?>
