<?php

namespace Bim\Db\Generator;
use Bim\Exception\BimException;

/**
 * Клас-фабрика получение объекта генерации
 *
 * Class CodeGenerator
 * @package Bim\Db\Lib
 */
abstract class Code
{
    protected $ownerItemDbData = null;

    /**
     * Получение объекта генерации
     *
     * @param $type
     * @return mixed
     * @throws BimException
     */
    public static function buildHandler($type)
    {
        if ($type) {
            $provider = '\\Bim\\Db\\Generator\\Providers\\' . ucfirst($type);
            if (class_exists($provider)) {
                return new $provider();
            }
        }
        throw new BimException("Неизвестный провайдер");
    }

    /**
     * Получение сгенерированого метода
     * 
     * getMethodContent
     * @param $className
     * @param $methodName
     * @param $arParams
     * @return string
     * @throws \Exception
     */
    public function getMethodContent($className, $methodName, $arParams)
    {
        $arParamsToString = array();
        foreach ($arParams as $param) {
            $arParamsToString[] = var_export($param, true);
        }

        $paramsToString = implode(', ', $arParamsToString);
        return $className . '::' . $methodName . '(' . $paramsToString . ');' . PHP_EOL;
    }

    /**
     * Абстрактный метод для генерации кода добавления
     * 
     * @param $params array
     * @return mixed
     */
    abstract public function generateAddCode($params);

    /**
     * Абстрактный метод для генерации кода обновления
     * 
     * @param $params array
     * @return mixed
     */
    abstract public function generateUpdateCode($params);

    /**
     * Абстрактный метод для генерации кода удаления
     * 
     * @param $params array
     * @return mixed
     */
    abstract public function generateDeleteCode($params);

    /**
     * Абстрактный метод проверки передаваемых параметров
     * 
     * @param $params array
     * @return mixed
     */
    abstract public function checkParams($params);
}