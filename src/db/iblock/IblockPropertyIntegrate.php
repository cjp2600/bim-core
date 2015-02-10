<?php
namespace Bim\Db\Iblock;

/*
 * класс взаимодействия со свойствами инфоблоков
 */
class IblockPropertyIntegrate {
    /**
     * Добавляет свойство инфоблока
     * @param array $arFields ['VALUES'] - значения списка для типа свойств "Список" (L):
     *      array(
     *          'n#(int)INDEX#' => array(
     * @param bool $isRevert
     * @return array - массив с идентификатором добавленного свойства или с текстом возникшей в процессе добавления ошибки
     * @throws \Exception
     * @internal param height $int - высота окна редактора (в px) - default 200
     *
     *  - для типа свойства "Привязка к элементам в виде списка" (E:EList)
     * @internal param size $int - высота списка - default 1
     * @internal param width $int - ограничить по ширине (0 - не ограничивать, в px) - default 0
     * @internal param group $string - группировать по разделам - default 'N'
     * @internal param multiple $string - отображать в виде списка множественного выбора - default 'N'
     *
     *  - для типа свойства "Счетчик" (N:Sequence)
     * @internal param write $string - разрешается изменять значения - default 'N'
     * @internal param current_value $int - текущее значение счетчика - default 0
     *
     *  - для типов свойств "Привязка к элементам с автозаполнением" (E:EAutocomplete), "Привязка к товарам (SKU)" (E:SKU)
     * @internal param VIEW $string - Интерфейс показа - default 'A'. Возможные варианты:
     *          A - Строка с автозаполнением
     *          T - Строка с автозаполнением и выбор из списка
     *          E - Строка с автозаполнением и выбор из окна поиска
     * @internal param SHOW_ADD $string - показывать кнопку добавления элементов - default 'N'
     * @internal param IBLOCK_MESS $string - брать название кнопки добавления из настроек связанного инфоблока - default 'N'
     * @internal param MAX_WIDTH $int - максимальная ширина поля ввода в пикселах (0 - не ограничивать) - default 0
     * @internal param MIN_HEIGHT $int - минимальная высота поля ввода в пикселах, если свойство множественное - default 24
     * @internal param MAX_HEIGHT $int - максимальная высота поля ввода в пикселах, если свойство множественное - default 1000
     * @internal param BAN_SYM $string - заменяемые при показе символы - default ',;'
     * @internal param REP_SYM $string - символ, который заменит при показе запрещенные символы - default ' '
     *
     *  - для типа свойства "Видео" (S:video)
     * @internal param BUFFER_LENGTH $int - размер буфера в секундах - default 10
     * @internal param CONTROLBAR $string - расположение панели управления - default 'bottom'. Возможные варианты:
     *          bottom - Внизу
     *          none - Не показывать
     * @internal param AUTOSTART $string - автоматически начать проигрывать - default 'N'
     * @internal param VOLUME $int - уровень громкости в процентах от максимального - default 90
     * @internal param SKIN $string - скин - default ''
     * @internal param FLASHVARS $string - дополнительные переменные Flashvars - default ''
     * @internal param WMODE_FLV $string - режим окна (WMode) - default 'transparent'. Возможные варианты:
     *          window - Обычный
     *          opaque - Непрозрачный
     *          transparent - Прозрачный
     * @internal param BGCOLOR $string - цвет фона панели управления - default 'FFFFFF'
     * @internal param COLOR $string - цвет элементов управления - default '000000'
     * @internal param OVER_COLOR $string - цвет элементов управления при наведении указателя мыши - default '000000'
     * @internal param SCREEN_COLOR $string - цвет экрана - default '000000'
     * @internal param SILVERVARS $string - дополнительные переменные Silverlight - default ''
     * @internal param WMODE_WMV $string - режим окна - default 'windowless'. Возможные варианты:
     *          window - Обычный
     *          windowless - Прозрачный
     *
     * @internal param ID $string 'n#(int)INDEX#' - начиная с: n0, n1, n2,...
     * @internal param VALUE $string - значение элемента списка
     * @internal param XML_ID $string - код элемента списка
     * @internal param SORT $int - сортировка элемента списка
     * @internal param DEF $string - значение по умолчанию - default 'N'
     *          )
     *      );
     *
     * Summary:
     * 3 required
     * 19 optional with defaults
     * 2 optional without defaults
     *
     */
	public function Add($arFields,$isRevert = false)
    {

		if (isset($arFields['SORT'])) {
			if (!is_int($arFields['SORT'])) {
				if (intval($arFields['SORT']))
					$arFields['SORT'] = intval($arFields['SORT']);
				else
					$arFields['SORT'] = 500;
			}
		} else
			$arFields['SORT'] = 500;

		$arDefaultValues = array(
			'MULTIPLE' => false,
			'IS_REQUIRED' => false,
			'ACTIVE' => true,
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => false,
			'FILE_TYPE' => '',
			'LIST_TYPE' => 'L',
			'ROW_COUNT' => 1,
			'COL_COUNT' => 30,
			'LINK_IBLOCK_ID' => null,
			'DEFAULT_VALUE' => null,
			'WITH_DESCRIPTION' => 'N',
			'SEARCHABLE' => 'N',
			'FILTRABLE' => 'N',
			'MULTIPLE_CNT' => 5,
			'HINT' => '',
			'SECTION_PROPERTY' => 'Y',
			'SMART_FILTER' => 'N',
			'USER_TYPE_SETTINGS' => array(),
			'VALUES' => array()
		);


		if ($arFields['IBLOCK_CODE']) {
			unset($arFields['IBLOCK_ID']);
			$rsIBlock = \CIBlock::GetList(array(), array('CODE' => $arFields['IBLOCK_CODE']));
			if ($arIBlock = $rsIBlock->Fetch()) {
				$arFields['IBLOCK_ID'] = $arIBlock['ID'];
			} else{
                throw new \Exception(__CLASS__.'::'.__METHOD__.' Not found iblock with code ' . $arFields['IBLOCK_CODE']);
                return false;
            }
		}

        if ( !strlen( $arFields['CODE'] ) ) {
            throw new \Exception(__CLASS__.'::'.__METHOD__.' Not found property code');
            return false;
        }

        $iblockPropDbRes = \CIBlockProperty::GetList( array(), array('IBLOCK_ID' => $arFields['IBLOCK_ID'], 'CODE' => $arFields['CODE'] ) );
        if ( $iblockPropDbRes !== false && $iblockPropDbRes->SelectedRowsCount() ) {

            throw new \Exception(__CLASS__.'::'.__METHOD__.'Property with code = "' . $arFields['CODE'] .'" ');
            return false;

        }

        if ($arFields['LINK_IBLOCK_CODE']) {
            unset($arFields['LINK_IBLOCK_ID']);
            $rsIBlock = \CIBlock::GetList(array(), array('CODE' => $arFields['LINK_IBLOCK_CODE']));
            if ($arIBlock = $rsIBlock->Fetch()) {
                $arFields['LINK_IBLOCK_ID'] = $arIBlock['ID'];
            }
        }

		foreach ($arDefaultValues as $DefaultName => $DefaultValue) {
			if (!isset($arFields[$DefaultName]) || empty($arFields[$DefaultName]))
				$arFields[$DefaultName] = $DefaultValue;
		}

		$objCIBlockProperty = new \CIBlockProperty();
        unset($arFields['ID']);
		$iId = $objCIBlockProperty->Add($arFields);

        if ($iId) {
            return $iId;
        } else {
            throw new \Exception(__CLASS__ . '::' . __METHOD__ . ' ' . $objCIBlockProperty->LAST_ERROR);
            return false;
        }
    }

	/**
	 * изменяет свойство инфоблока
	 *
	 * В метод можно передать любое количество параметров. Если какие-то значения не указаны - они не будут изменены
	 *
	 * @param string $IblockCode - код инфоблока изменяемого свойства
	 * @param string $PropertyCode - код изменяемого свойства
	 * @param array $arFields - набор параметров изменяемого свойства:
	 * @param string $arFields['NAME'] - Название свойства - no default
	 * @param string $arFields['SORT'] - Сортировка - default 500
	 * @param string $arFields['CODE'] - Код свойства - no default
	 * @param string $arFields['MULTIPLE'] - Флаг множественности - default false
	 * @param string $arFields['IS_REQUIRED'] - Флаг обязательности - default false
	 * @param string $arFields['ACTIVE'] - Флаг активности - default true
	 * @param string $arFields['PROPERTY_TYPE'] - Тип свойства - default 'S'. Возможные типы:
	 *      S - строка
	 *      N - число
	 *      L - список
	 *      F - файл
	 *      G - привязка к разделам
	 *      E - привязка к элементам
	 *      S:DateTime - дата/время
	 *      S:ElementXmlID - привязка к элементам по XML_ID
	 *      S:FileMan - привязка к файлу (на сервере)
	 *      S:HTML - HTML/текст
	 *      E:EList - привязка к элементам в виде списка
	 *      N:Sequence - счетчик
	 *      E:EAutocomplete - привязка к элементам с автозаполнением
	 *      E:SKU - привязка к товарам (SKU)
	 *      S:UserID - привязка к пользователю
	 *      S:map_google - привязка к карте Google Maps
	 *      S:map_yandex - привязка к Яндекс.Карте
	 *      S:TopicID - привязка к теме форума
	 *      S:video - видео
	 *
	 * @param string $arFields['USER_TYPE'] - пользовательский тип свойства - default false
	 * @param int $arFields['IBLOCK_ID'] - идентификатор инфоблока - no default
	 * @param string $arFields['FILE_TYPE'] - типы загружаемых файлов (расширения через запятую) - default ''
	 * @param string $arFields['LIST_TYPE'] - вид списка - default 'L'. Возможные типы:
	 *      L - список
	 *      C - флажки
	 *
	 * @param int $arFields['ROW_COUNT'] - количество строк (для внешнего вида "список") - default 1
	 * @param int $arFields['COL_COUNT'] - Размер поля для ввода значения (столбцов) - default 30
	 * @param int $arFields['LINK_IBLOCK_ID'] - идентификатор инфоблока для типов с привязкой к разделам или секциям - default NULL
	 * @param mixed $arFields['DEFAULT_VALUE'] - дефолтное значение - default NULL
	 * @param string $arFields['WITH_DESCRIPTION'] - флаг, выводить ли поле для описания значения - default 'N'
	 * @param string $arFields['SEARCHABLE'] - флаг, участвуют ли значения свойства в поиске - default 'N'
	 * @param string $arFields['FILTRABLE'] - флаг, выводить ли на странице списка элементов поле для фильтрации по этому свойству - default 'N'
	 * @param int $arFields['MULTIPLE_CNT'] - количество полей для ввода новых множественных значений - default 5
	 * @param string $arFields['HINT'] - подсказка - default ''
	 * @param string $arFields['SECTION_PROPERTY'] - флаг, показывать ли на странице редактирования элемента - default 'Y'
	 * @param string $arFields['SMART_FILTER'] - флаг, показывать ли в умном фильтре - default 'N'
	 * @param array $arFields['USER_TYPE_SETTINGS'] - массив дополнительных свойств для разных типов свойств. Детально по типам:
	 *  - для типа свойства "HTML/текст" (S:HTML)
	 *      @param int height - высота окна редактора (в px) - default 200
	 *
	 *  - для типа свойства "Привязка к элементам в виде списка" (E:EList)
	 *      @param int size - высота списка - default 1
	 *      @param int width - ограничить по ширине (0 - не ограничивать, в px) - default 0
	 *      @param string group - группировать по разделам - default 'N'
	 *      @param string multiple - отображать в виде списка множественного выбора - default 'N'
	 *
	 *  - для типа свойства "Счетчик" (N:Sequence)
	 *      @param string write - разрешается изменять значения - default 'N'
	 *      @param int current_value - текущее значение счетчика - default 0
	 *
	 *  - для типов свойств "Привязка к элементам с автозаполнением" (E:EAutocomplete), "Привязка к товарам (SKU)" (E:SKU)
	 *      @param string VIEW - Интерфейс показа - default 'A'. Возможные варианты:
	 *          A - Строка с автозаполнением
	 *          T - Строка с автозаполнением и выбор из списка
	 *          E - Строка с автозаполнением и выбор из окна поиска
	 *      @param string SHOW_ADD - показывать кнопку добавления элементов - default 'N'
	 *      @param string IBLOCK_MESS - брать название кнопки добавления из настроек связанного инфоблока - default 'N'
	 *      @param int MAX_WIDTH - максимальная ширина поля ввода в пикселах (0 - не ограничивать) - default 0
	 *      @param int MIN_HEIGHT - минимальная высота поля ввода в пикселах, если свойство множественное - default 24
	 *      @param int MAX_HEIGHT - максимальная высота поля ввода в пикселах, если свойство множественное - default 1000
	 *      @param string BAN_SYM - заменяемые при показе символы - default ',;'
	 *      @param string REP_SYM - символ, который заменит при показе запрещенные символы - default ' '
	 *
	 *  - для типа свойства "Видео" (S:video)
	 *      @param int BUFFER_LENGTH - размер буфера в секундах - default 10
	 *      @param string CONTROLBAR - расположение панели управления - default 'bottom'. Возможные варианты:
	 *          bottom - Внизу
	 *          none - Не показывать
	 *      @param string AUTOSTART - автоматически начать проигрывать - default 'N'
	 *      @param int VOLUME - уровень громкости в процентах от максимального - default 90
	 *      @param string SKIN - скин - default ''
	 *      @param string FLASHVARS - дополнительные переменные Flashvars - default ''
	 *      @param string WMODE_FLV - режим окна (WMode) - default 'transparent'. Возможные варианты:
	 *          window - Обычный
	 *          opaque - Непрозрачный
	 *          transparent - Прозрачный
	 *      @param string BGCOLOR - цвет фона панели управления - default 'FFFFFF'
	 *      @param string COLOR - цвет элементов управления - default '000000'
	 *      @param string OVER_COLOR - цвет элементов управления при наведении указателя мыши - default '000000'
	 *      @param string SCREEN_COLOR - цвет экрана - default '000000'
	 *      @param string SILVERVARS - дополнительные переменные Silverlight - default ''
	 *      @param string WMODE_WMV - режим окна - default 'windowless'. Возможные варианты:
	 *          window - Обычный
	 *          windowless - Прозрачный
	 *
	 * @param array $arFields['VALUES'] - значения списка для типа свойств "Список" (L):
	 *      array(
	 *          'n#(int)INDEX#' => array(
	 *              @param string ID 'n#(int)INDEX#' - начиная с: n0, n1, n2,...
	 *              @param string VALUE - значение элемента списка
	 *              @param string XML_ID - код элемента списка
	 *              @param int SORT - сортировка элемента списка
	 *              @param string DEF - значение по умолчанию - default 'N'
	 *          )
	 *      );
     *
     * @param array $arFields['ADD_VALUES'] - Дополняет существующий списк для типа свойств "Список" (L):
	 *	   array(
     *          'n#(int)INDEX#' => array(
     *              @param string ID 'n#(int)INDEX#' - начиная с: n0, n1, n2,...
     *              @param string VALUE - значение элемента списка
     *              @param string XML_ID - код элемента списка
     *              @param int SORT - сортировка элемента списка
     *              @param string DEF - значение по умолчанию - default 'N'
     *          )
	 * Summary:
	 * 19 optional with defaults
	 * 5 optional without defaults
	 *
	 * @return array - массив с идентификатором измененного свойства или с текстом возникшей в процессе изменения ошибки
	 */
	public function Update($IblockCode, $PropertyCode, $arFields, $isRevert = false)
	{
        global $RESPONSE;
		unset($arFields['ID']);
		unset($arFields['IBLOCK_ID']);
		$rsProperty = CIBlockProperty::GetList(array(), array('IBLOCK_CODE' => $IblockCode, 'CODE' => $PropertyCode));
		if ($arProperty = $rsProperty->Fetch()) {
			$NewArFields = array_merge($arProperty, $arFields);

            foreach ($NewArFields as $fieldKey => $field) {
                if ($field === null) {
                    $NewArFields[$fieldKey] = false;
                }
            }

            if ($NewArFields['LINK_IBLOCK_CODE']) {
                unset($NewArFields['LINK_IBLOCK_ID']);
                $rsIBlock = CIBlock::GetList(array(), array('CODE' => $NewArFields['LINK_IBLOCK_CODE']));
                if ($arIBlock = $rsIBlock->Fetch()) {
                    $NewArFields['LINK_IBLOCK_ID'] = $arIBlock['ID'];
                }
            }

            /*
             * Если дополняем список св-ва
             */
            if ($NewArFields['ADD_VALUES']){
                if (is_array($NewArFields['ADD_VALUES'])) {
                $dbPropertyValues = CIBlockPropertyEnum::GetList(array(), array("IBLOCK_ID" => $NewArFields['IBLOCK_ID'], "CODE" => $PropertyCode));
                while($arPropertyValues = $dbPropertyValues->Fetch()) {
                    $writeProperyValues = $arPropertyValues;
                    unset($writeProperyValues['ID']);
                    unset($writeProperyValues['PROPERTY_ID']);
                    $NewArFields['VALUES'][$arPropertyValues['ID']] = $writeProperyValues;
                }
                    foreach ($NewArFields['ADD_VALUES'] as $row){
                        array_push($NewArFields['VALUES'], $row);
                    }
                }
                unset($NewArFields['ADD_VALUES']);
            }

            if (!$isRevert)  {

                $objIBlockPropertyRevert = new IblockPropertyRevertIntegrate();
                if ($objIBlockPropertyRevert->Update($IblockCode, $PropertyCode)) {
                    $objIBlockProperty = new CIBlockProperty();
                    if ($objIBlockProperty->Update($NewArFields['ID'], $NewArFields)) {
                        return $RESPONSE[] = array('type' => 'success');
                    } else {
                        return $RESPONSE[] = array('type' => 'error', 'error_text' => $objIBlockProperty->LAST_ERROR);
                    }
                } else {
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Cant complete "iblock property update revert" operation');
                }

            } else {
                $objIBlockProperty = new CIBlockProperty();
                if ($objIBlockProperty->Update($NewArFields['ID'], $NewArFields)) {
                    return $RESPONSE[] = array('type' => 'success');
                } else {
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => $objIBlockProperty->LAST_ERROR);
                }
            }

        } else {
            return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Not find property with code ' . $PropertyCode);
        }
    }

	/**
	 * Метод удаления свойства инфоблока
	 * @param string $IblockCode - код инфоблока
	 * @param string $PropertyCode - код свойства
	 * @return array
	 */
	public function Delete($sIBlockCode, $sPropertyCode,$isRevert = false)
	{
        global $RESPONSE;
		$rsProperty = CIBlockProperty::GetList(array(), array('IBLOCK_CODE' => $sIBlockCode, 'CODE' => $sPropertyCode));
		if ($arProperty = $rsProperty->Fetch()) {
            if (!$isRevert) {
                $objIBlockPropertyRevert = new IblockPropertyRevertIntegrate();
                if ($objIBlockPropertyRevert->Add($sIBlockCode, $sPropertyCode)) {
                    if (CIBlockProperty::Delete($arProperty['ID'])) {
                        return $RESPONSE[] = array('type' => 'success');
                    } else {
                        return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Iblock property delete error!');
                    }
                } else {
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Cant complete "iblock property delete revert" operation');
                }
            } else {
                if (CIBlockProperty::Delete($arProperty['ID'])) {
                    return $RESPONSE[] = array('type' => 'success');
                } else {
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Iblock property delete error!');
                }
            }
		} else {
            return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Not find property with code ' . $sPropertyCode);
        }
	}
}