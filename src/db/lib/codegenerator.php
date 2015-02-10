<?php

namespace Bim\Db\Lib;
/**
 * Class CodeGenerator
 * абстрактный класс для генерацияя кода изменений в модулях:
 * iblock, main, sale, subscribe, form, advertising, catalog, currency
 *
 */
abstract class CodeGenerator
{
    /* данные объекта из базы, для к-го делается генерация */
    protected $ownerItemDbData = null;

    /**
     * "фабричный метод" используется для инстанцирования обработчика генерации  кода
     * @param $type
     * @return CurrencyRatesGen|PostingGen|RubricGen
     * @throws \Exception
     */
    public static function  buildHandler( $type )
    {
        switch( $type ){
            // модуль iblock
            case 'IblockType':
                return new IblockTypeGen();
            break;

            case 'Iblock':
                return new IblockGen();
            break;

            case 'IblockProperty':
                return new IblockPropertyGen();
            break;

            case 'Hlblock':
                return new HighloadblockGen();
            break;

            case 'HighloadblockField':
                return new HighloadblockFieldGen();
            break;

            // модуль - main
            case 'EventType':
                return new EventTypeGen();
            break;

            case 'EventMessage':
                return new EventMessageGen();
            break;

            case 'Group':
                return new GroupGen();
            break;

            case 'Language':
                return new LanguageGen();
            break;

            case 'Site':
                return new SiteGen();
            break;

            case 'UserField':
                return new UserFieldGen();
            break;

            case 'UserFieldEnum':
                return new UserFieldEnumGen();
            break;
            // модуль - form
            case 'Form':
                return new FormGen();
            break;

            case 'FormAnswer':
                return new FormAnswerGen();
            break;

            case 'FormField':
                return new FormFieldGen();
            break;
            // модуль - catalog
            case 'Catalog':
                return new CatalogGen();
            break;

            case 'CatalogStore':
                return new CatalogStoreGen();
            break;

            case 'CatalogDiscount':
                return new CatalogDiscountGen();
            break;

            case 'CatalogGroup':
                return new CatalogGroupGen();
            break;

            case 'Extra':
                return new ExtraGen();
            break;

            case 'CatalogVat':
                return new CatalogVatGen();
                break;
            // модуль - sale
            case 'SaleDelivery':
                return new SaleDeliveryGen();
                break;

            case 'SaleDiscount':
                return new SaleDiscountGen();
                break;

            case 'SaleLocationCity':
                return new SaleLocationCityGen();
                break;

            case 'SaleLocationRegion':
                return new SaleLocationRegionGen();
                break;

            case 'SaleLocationCountry':
                return new SaleLocationCountryGen();
                break;

            case 'SaleLocationGroup':
                return new SaleLocationGroupGen();
                break;

            case 'SaleLocation':
                return new SaleLocationGen();
                break;

            case 'SaleOrderPropsGroup':
                return new SaleOrderPropsGroupGen();
                break;

            case 'SaleOrderProps':
                return new SaleOrderPropsGen();
                break;

            case 'SalePaySystem':
                return new SalePaySystemGen();
                break;

            case 'SalePaySystemAction':
                return new SalePaySystemActionGen();
                break;

            case 'SaleTax':
                return new SaleTaxGen();
                break;

            case 'SaleTaxRate':
                return new SaleTaxRateGen();
                break;

            case 'SaleStatus':
                return new SaleStatusGen();
                break;

            case 'SalePersonType':
                return new SalePersonTypeGen();
                break;

            case 'SaleUserAccount':
                return new SaleUserAccountGen();
                break;

            //advertising
            case 'AdvType':
                return new AdvTypeGen();
                break;

            case 'AdvContract':
                return new AdvContractGen();
                break;

            //currency
            case 'Currency':
                return new CurrencyGen();
                break;

            case 'CurrencyLang':
                return new CurrencyLangGen();

                break;

            case 'CurrencyRates':
                return new CurrencyRatesGen();
                break;


            //subscribe
            case 'Rubric':
                return new RubricGen();
                break;

            case 'Posting':
                return new PostingGen();
                break;
            default:
                throw new \Exception( 'Передан неизвестный type' );

        }

    }

    /**
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
        $arParamsToString[] = 'true';
        $paramsToString = implode(', ', $arParamsToString);
        return $className . '::' . $methodName . '(' . $paramsToString . ');' . PHP_EOL;
    }

    /**
     * абстрактный метод для генерации кода добавления
     * @param $params array
     * @return mixed
     */
    abstract public function generateAddCode( $params );

    /**
     * абстрактный метод для генерации кода обновления
     * @param $params array
     * @return mixed
     */
    abstract public function generateUpdateCode( $params );

    /**
     * абстрактный метод для генерации кода удаления
     * @param $params array
     * @return mixed
     */
    abstract public function generateDeleteCode( $params );

    /**
     * абстрактный метод проверки передаваемых параметров
     * @param $params array
     * @return mixed
     */
    abstract public function checkParams( $params  );
}

?>
