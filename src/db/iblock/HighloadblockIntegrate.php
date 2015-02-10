<?php
namespace Bim\Db\Iblock;

\CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;
/*
 * класс взаимодействия с highload инфоблоками
 */
class HighloadblockIntegrate
{
    /*
     * Add() - метод добавляет highload инфоблок
     * @param string $entityName - Название сущности ( должно начинаться с заглавной буквы и состоять только из латинских букв и цифр )
     * @param string $tableName - Название таблицы в БД ( должно состоять только из строчных латинских букв, цифр и знака подчеркивания )

     *
     * Summary:
     * 2 required
     *
     * return array - массив с идентификатором добавленного типа инфоблоков или с текстом возникшей в процессе добавления ошибки
     */
    public function Add($entityName, $tableName)
    {
        if ( empty( $entityName ) ) {
            throw new \Exception( 'entityName is empty' );
        }
        if ( empty( $tableName ) ) {
            throw new \Exception( 'tableName is empty' );
        }

        $addFields = array(
            'NAME' => trim( $entityName ),
            'TABLE_NAME' => trim( $tableName )
        );

        $addResult = HL\HighloadBlockTable::add( $addFields );
        if ( !$addResult->isSuccess() ) {
            throw new \Exception( $addResult->getErrorMessages() );
        }
    }

    /*
     * Update() - метод обновляет highload инфоблок
     * @param string $entityName - Название сущности ( должно начинаться с заглавной буквы и состоять только из латинских букв и цифр )
     * @param string $tableName - Название таблицы в БД ( должно состоять только из строчных латинских букв, цифр и знака подчеркивания )
     * @param string $filterType - entity | table - значение, по к-ому будет делаться фильтрация для нахождения инфоблока, к-ый нужно обновить
     *
     * Summary:
     * 3 required
     *
     * return array - массив с идентификатором добавленного типа инфоблоков или с текстом возникшей в процессе добавления ошибки
     */
    public function Update($entityName, $tableName, $filterType, $isRevert = false)
    {
        global $RESPONSE;
        $result = array('type' => 'success');
        try{
            if ( !in_array( $filterType, array('entity', 'table') ) ) {
                throw new \Exception('Incorrect filterType param value');
            }

            $filter = array('NAME' => $entityName);
            if ( $filterType == 'table' ) {
                $filter = array('TABLE_NAME' => $tableName);
            }
            $hlBlockDbRes = HL\HighloadBlockTable::getList(array(
                "filter" => $filter
            ));
            if ( !$hlBlockDbRes->getSelectedRowsCount() ) {
                throw new \Exception('Not found highloadBlock for update');
            }
            $hlBlockRow = $hlBlockDbRes->fetch();

            if ( !$isRevert ) {
                $hlBlockRevert = new HighloadblockRevertIntegrate();
                if ( !$hlBlockRevert->Update( $hlBlockRow['NAME'], $hlBlockRow['TABLE_NAME'], $filterType ) ) {
                    throw new \Exception( 'Cant create "highblock update revert" operation' );
                }
            }

            $updateFields = array(
                'NAME' => trim( $entityName ),
                'TABLE_NAME' => trim( $tableName )
            );

            $updateResult = HL\HighloadBlockTable::update( $hlBlockRow['ID'], $updateFields );
            if ( !$updateResult->isSuccess() ) {
                throw new \Exception( $updateResult->getErrorMessages() );
            }

        }catch ( \Exception $e ){
            $result = array('type' => 'error', 'error_text' => $e->getMessage() );

        }

        return $RESPONSE[] = $result;
    }

    /*
     * Delete() - метод удаляет highload инфоблок
     * @param string $entityName - Название сущности - no defaults/required
     */
    public function Delete($entityName)
    {
        if ( !strlen( $entityName ) ) {
            throw new \Exception('Incorrect entityName param value');
        }

        $filter = array('NAME' => $entityName);
        $hlBlockDbRes = HL\HighloadBlockTable::getList(array(
            "filter" => $filter
        ));
        if ( !$hlBlockDbRes->getSelectedRowsCount() ) {
            throw new \Exception('Not found highloadBlock with entityName = ' . $entityName );
        }
        $hlBlockRow = $hlBlockDbRes->fetch();
        $delResult = HL\HighloadBlockTable::delete( $hlBlockRow['ID'] );

        if ( !$delResult->isSuccess() ) {
            throw new \Exception($delResult->getErrorMessages());
        }
    }

}