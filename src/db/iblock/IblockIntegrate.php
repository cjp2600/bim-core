<?php

namespace Bim\Db\Iblock;

class IblockIntegrate
{
    /*
     * Add() - метод добавления инфоблока
     * @param string $arFields['IBLOCK_TYPE_ID'] - тип инфоблока - no default/required
     * @param string $arFields['NAME'] - название инфоблока - no default/required
     * @param string $arFields['CODE'] - символьный код инфоблока - no default/required
     * @param array $arFields['LID'] - сайты, к которым относится инфоблок - no default/required. Пример:
     *      array(
     *          0 => 's1',
     *          1 => 'en'
     *      );
     * @param string $arFields['ACTIVE'] - активность инфоблока - default 'Y'
     * @param string $arFields['LIST_PAGE_URL'] - URL страницы инфоблока - default '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/index.php?ID=#IBLOCK_ID#'
     * @param string $arFields['SECTION_PAGE_URL'] - URL страницы раздела инфоблока - default '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/list.php?SECTION_ID=#ID#'
     * @param string $arFields['DETAIL_PAGE_URL'] - URL страницы детального просмотра - default '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/detail.php?ID=#ID#'
     * @param string $arFields['INDEX_SECTION'] - Индексировать разделы для модуля поиска - default 'Y'
     * @param string $arFields['INDEX_ELEMENT'] - Индексировать элементы для модуля поиска - default 'Y'
     * @param int $arFields['SORT'] - Сортировка - default 500
     * @param array $arFields['PICTURE'] - картинка инфоблока - default:
     *      array (
     *          'del' => NULL,
     *          'MODULE_ID' => 'iblock',
     *      );
     * @param string $arFields['DESCRIPTION'] - текстовое описание инфоблока - default ''
     * @param string $arFields['DESCRIPTION_TYPE'] - тип текстового описания - default 'text'. Возможные значения:
     *      text
     *      html
     * @param string $arFields['EDIT_FILE_BEFORE'] - Файл для редактирования элемента, позволяющий модифицировать поля перед сохранением - default ''
     * @param string $arFields['EDIT_FILE_AFTER'] - Файл с формой редактирования элемента - default ''
     * @param string $arFields['WORKFLOW'] - участвует в документообороте - default 'N'
     * @param string $arFields['BIZPROC'] - участвует в бизнес-процессах - default 'N'
     * @param string $arFields['SECTION_CHOOSER'] - Интерфейс привязки элемента к разделам - default 'L'. Возможные варианты:
     *      L - Список множественного выбора
     *      D - Выпадающие списки
     *      P - Окно поиска
     * @param string $arFields['LIST_MODE'] - Режим просмотра разделов и элементов - default ''. Возможные варианты:
     *      '' - из настроек модуля
     *      'S' - раздельный
     *      'C' - совместный
     *
     ********
     * ПОЛЯ *
     ********
     *
     * @param array $arFields['FIELDS'] - поля элементов инфоблока - default array().
     * Описание полей:
     *      @param array IBLOCK_SECTION - Привязка к разделам:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - всегда ''
     *      @param array ACTIVE - Активность:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default 'Y'
     *      @param array ACTIVE_FROM - Начало активности:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''. Возможные варианты:
     *              '' - Не задано
     *              '=now' - Текущие дата и время
     *              '=today' - Текущая дата
     *      @param array ACTIVE_TO - Окончание активности:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param int DEFAULT_VALUE - значение по умолчанию - default 0
     *      @param array SORT - Сортировка:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param int DEFAULT_VALUE - значение по умолчанию - всегда ''
     *      @param array NAME - Сортировка:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *      @param array PREVIEW_PICTURE - Картинка для анонса:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param array DEFAULT_VALUE:
     *              @param string FROM_DETAIL - Создавать картинку анонса из детальной (если не задана) - default 'N'
     *              @param string DELETE_WITH_DETAIL - Удалять картинку анонса, если удаляется детальная - default 'N'
     *              @param string UPDATE_WITH_DETAIL - Создавать картинку анонса из детальной даже если задана - default 'N'
     *              @param string SCALE - Уменьшать если большая - default 'N'
     *                @param int WIDTH - Максимальная ширина - default 0
     *                @param int HEIGHT - Максимальная высота - default 0
     *                @param string IGNORE_ERRORS - Игнорировать ошибки масштабирования - default 'N'
     *                @param string METHOD - Сохранять качество при масштабировании (требует больше ресурсов на сервере) - default 'Y'
     *                @param int COMPRESSION - Качество (только для JPEG, 1-100, по умолчанию около 75) - default 95
     *              @param string USE_WATERMARK_FILE - Наносить авторский знак в виде изображения - default 'N'
     *                @param string WATERMARK_FILE - Путь к изображению с авторским знаком - default ''
     *                @param int WATERMARK_FILE_ALPHA - Прозрачность авторского знака (%) - default 0
     *                @param string WATERMARK_FILE_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *              @param string USE_WATERMARK_TEXT - Наносить авторский знак в виде текста - default 'N'
     *                @param string WATERMARK_TEXT - Содержание надписи - default ''
     *                @param string WATERMARK_TEXT_FONT - Путь к файлу шрифта - default ''
     *                @param string WATERMARK_TEXT_COLOR - Цвет надписи (без #, например, FF00EE) - default ''
     *                @param int WATERMARK_TEXT_SIZE - Размер (% от размера картинки) - default 0
     *                @param string WATERMARK_TEXT_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *      @param array PREVIEW_TEXT_TYPE - Тип описания для анонса:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default 'text'. Возможные значения:
     *              text
     *              html
     *      @param array PREVIEW_TEXT - Описание для анонса:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *      @param array DETAIL_PICTURE - Картинка для анонса:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param array DEFAULT_VALUE:
     *              @param string SCALE - Уменьшать если большая - default 'N'
     *                @param int WIDTH - Максимальная ширина - default 0
     *                @param int HEIGHT - Максимальная высота - default 0
     *                @param string IGNORE_ERRORS - Игнорировать ошибки масштабирования - default 'N'
     *                @param string METHOD - Сохранять качество при масштабировании (требует больше ресурсов на сервере) - default 'Y'
     *                @param int COMPRESSION - Качество (только для JPEG, 1-100, по умолчанию около 75) - default 95
     *              @param string USE_WATERMARK_FILE - Наносить авторский знак в виде изображения - default 'N'
     *                @param string WATERMARK_FILE - Путь к изображению с авторским знаком - default ''
     *                @param int WATERMARK_FILE_ALPHA - Прозрачность авторского знака (%) - default 0
     *                @param string WATERMARK_FILE_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *              @param string USE_WATERMARK_TEXT - Наносить авторский знак в виде текста - default 'N'
     *                @param string WATERMARK_TEXT - Содержание надписи - default ''
     *                @param string WATERMARK_TEXT_FONT - Путь к файлу шрифта - default ''
     *                @param string WATERMARK_TEXT_COLOR - Цвет надписи (без #, например, FF00EE) - default ''
     *                @param int WATERMARK_TEXT_SIZE - Размер (% от размера картинки) - default 0
     *                @param string WATERMARK_TEXT_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *      @param array DETAIL_TEXT_TYPE - DETAIL_TEXT_TYPE:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default 'text'. Возможные значения:
     *              text
     *              html
     *      @param array DETAIL_TEXT - Детальное описание:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *      @param array XML_ID - Внешний код:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - всегда ''
     *      @param array CODE - Символьный код:
     *          @param string UNIQUE - Если код задан, то проверять на уникальность - default 'N'
     *          @param string TRANSLITERATION - Транслитерировать из названия при добавлении элемента - default 'N'
     *          @param int TRANS_LEN - Максимальная длина результата транслитерации - default 100
     *          @param string TRANS_CASE - Максимальная длина результата транслитерации - default 'L'. Возможные значения:
     *              '' - сохранить
     *              'L' - к нижнему
     *              'U' - к верхнему
     *          @param string TRANS_SPACE - Замена для символа пробела - default '-'
     *          @param string TRANS_OTHER - Замена для прочих символов - default '-'
     *          @param string TRANS_EAT - Удалять лишние символы замены - default 'Y'
     *          @param string USE_GOOGLE - Использовать внешний сервис для перевода - default 'N'
     *      @param array TAGS - Теги:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *
     *****************
     * ПОЛЯ РАЗДЕЛОВ *
     *****************
     *
     *      @param array SECTION_NAME - Название раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *      @param array SECTION_PICTURE - Картинка для анонса раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param array DEFAULT_VALUE:
     *              @param string FROM_DETAIL - Создавать картинку анонса из детальной (если не задана) - default 'N'
     *              @param string DELETE_WITH_DETAIL - Удалять картинку анонса, если удаляется детальная - default 'N'
     *              @param string UPDATE_WITH_DETAIL - Создавать картинку анонса из детальной даже если задана - default 'N'
     *              @param string SCALE - Уменьшать если большая - default 'N'
     *                @param int WIDTH - Максимальная ширина - default 0
     *                @param int HEIGHT - Максимальная высота - default 0
     *                @param string IGNORE_ERRORS - Игнорировать ошибки масштабирования - default 'N'
     *                @param string METHOD - Сохранять качество при масштабировании (требует больше ресурсов на сервере) - default 'Y'
     *                @param int COMPRESSION - Качество (только для JPEG, 1-100, по умолчанию около 75) - default 95
     *              @param string USE_WATERMARK_FILE - Наносить авторский знак в виде изображения - default 'N'
     *                @param string WATERMARK_FILE - Путь к изображению с авторским знаком - default ''
     *                @param int WATERMARK_FILE_ALPHA - Прозрачность авторского знака (%) - default 0
     *                @param string WATERMARK_FILE_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *              @param string USE_WATERMARK_TEXT - Наносить авторский знак в виде текста - default 'N'
     *                @param string WATERMARK_TEXT - Содержание надписи - default ''
     *                @param string WATERMARK_TEXT_FONT - Путь к файлу шрифта - default ''
     *                @param string WATERMARK_TEXT_COLOR - Цвет надписи (без #, например, FF00EE) - default ''
     *                @param int WATERMARK_TEXT_SIZE - Размер (% от размера картинки) - default 0
     *                @param string WATERMARK_TEXT_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *      @param array SECTION_DESCRIPTION_TYPE - Тип описания раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default 'text'. Возможные значения:
     *              text
     *              html
     *      @param array SECTION_DESCRIPTION - Описание раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *      @param array SECTION_DETAIL_PICTURE - Детальная картинка раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param array DEFAULT_VALUE:
     *              @param string SCALE - Уменьшать если большая - default 'N'
     *                @param int WIDTH - Максимальная ширина - default 0
     *                @param int HEIGHT - Максимальная высота - default 0
     *                @param string IGNORE_ERRORS - Игнорировать ошибки масштабирования - default 'N'
     *                @param string METHOD - Сохранять качество при масштабировании (требует больше ресурсов на сервере) - default 'Y'
     *                @param int COMPRESSION - Качество (только для JPEG, 1-100, по умолчанию около 75) - default 95
     *              @param string USE_WATERMARK_FILE - Наносить авторский знак в виде изображения - default 'N'
     *                @param string WATERMARK_FILE - Путь к изображению с авторским знаком - default ''
     *                @param int WATERMARK_FILE_ALPHA - Прозрачность авторского знака (%) - default 0
     *                @param string WATERMARK_FILE_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *              @param string USE_WATERMARK_TEXT - Наносить авторский знак в виде текста - default 'N'
     *                @param string WATERMARK_TEXT - Содержание надписи - default ''
     *                @param string WATERMARK_TEXT_FONT - Путь к файлу шрифта - default ''
     *                @param string WATERMARK_TEXT_COLOR - Цвет надписи (без #, например, FF00EE) - default ''
     *                @param int WATERMARK_TEXT_SIZE - Размер (% от размера картинки) - default 0
     *                @param string WATERMARK_TEXT_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *      @param array SECTION_XML_ID - Внешний код раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - всегда ''
     *      @param array SECTION_CODE - Символьный код раздела:
     *          @param string UNIQUE - Если код задан, то проверять на уникальность - default 'N'
     *          @param string TRANSLITERATION - Транслитерировать из названия при добавлении элемента - default 'N'
     *          @param int TRANS_LEN - Максимальная длина результата транслитерации - default 100
     *          @param string TRANS_CASE - Максимальная длина результата транслитерации - default 'L'. Возможные значения:
     *              '' - сохранить
     *              'L' - к нижнему
     *              'U' - к верхнему
     *          @param string TRANS_SPACE - Замена для символа пробела - default '-'
     *          @param string TRANS_OTHER - Замена для прочих символов - default '-'
     *          @param string TRANS_EAT - Удалять лишние символы замены - default 'Y'
     *          @param string USE_GOOGLE - Использовать внешний сервис для перевода - default 'N'
     *
     ******************
     * ЖУРНАЛ СОБЫТИЙ *
     * ****************
     *
     *      @param array LOG_SECTION_ADD - Записывать добавление раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *      @param array LOG_SECTION_EDIT - Записывать изменение раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *      @param array LOG_SECTION_DELETE - Записывать удаление раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *      @param array LOG_ELEMENT_ADD - Записывать добавление элемента:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *      @param array LOG_ELEMENT_EDIT - Записывать изменение элемента:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *      @param array LOG_ELEMENT_DELETE - Записывать удаление элемента:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *
     ***********
     * ПОДПИСИ *
     ***********
     *
     * @param string $arFields['ELEMENTS_NAME'] - заголовок "Элементы" - default 'Элементы'
     * @param string $arFields['ELEMENT_NAME'] - заголовок "Элемент" - default 'Элемент'
     * @param string $arFields['ELEMENT_ADD'] - заголовок "Добавить элемент" - default 'Добавить элемент'
     * @param string $arFields['ELEMENT_EDIT'] - заголовок "Изменить элемент" - default 'Изменить элемент'
     * @param string $arFields['ELEMENT_DELETE'] - заголовок "Удалить элемент" - default 'Удалить элемент'
     * @param string $arFields['SECTIONS_NAME'] - заголовок "Разделы" - default 'Разделы'
     * @param string $arFields['SECTION_NAME'] - заголовок "Раздел" - default 'Раздел'
     * @param string $arFields['SECTION_ADD'] - заголовок "Добавить раздел" - default 'Добавить раздел'
     * @param string $arFields['SECTION_EDIT'] - заголовок "Изменить раздел" - default 'Изменить раздел'
     * @param string $arFields['SECTION_DELETE'] - заголовок "Удалить раздел" - default 'Удалить раздел'
     *
     * @param string $arFields['RIGHTS_MODE'] - Расширенное управление правами - default 'S'. Возможные варианты:
     *      S - стандартные права
     *      E - расширенное управление правами
     *
     * @param array $arFields['GROUP_ID'] - права на доступ к инфоблоку для групп пользователей:
     *      array (
     *          2 => 'D',   // для всех групп - "Нет доступа"
     *          1 => 'X',   // для админов - "Полный доступ"
     *          3 => '',    // для другой группы - "Наследовать"
     *      );
     *
     * @param int $arFields['VERSION'] - "Инфоблоки (1.0)" или "Инфоблоки+ (2.0)" - default 1. Возможные значения:
     *      1 - Инфоблоки (1.0)
     *      2 - Инфоблоки+ (2.0)
     *
     *
     * Summary:
     * 4 required
     * 30 optional with defaults
     *
     * return array - массив с идентификатором добавленного инфоблока или с текстом возникшей в процессе ошибки
     */
    public function Add($arFields,$isRevert = false)
    {
        global $RESPONSE;
        if (isset($arFields['SORT']))
        {
            if (!is_int($arFields['SORT']))
            {
                if (intval($arFields['SORT']))
                    $arFields['SORT'] = intval($arFields['SORT']);
                else
                    $arFields['SORT'] = 500;
            }
        }
        else
            $arFields['SORT'] = 500;

        $arDefaultValues = array(
            'ACTIVE' => 'Y',
            'LIST_PAGE_URL' => '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/index.php?ID=#IBLOCK_ID#',
            'SECTION_PAGE_URL' => '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/list.php?SECTION_ID=#ID#',
            'DETAIL_PAGE_URL' => '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/detail.php?ID=#ID#',
            'INDEX_SECTION' => 'Y',
            'INDEX_ELEMENT' => 'Y',
            'PICTURE' => array(
                'del' => null,
                'MODULE_ID' => 'iblock',
            ),
            'DESCRIPTION' => '',
            'DESCRIPTION_TYPE' => 'text',
            'EDIT_FILE_BEFORE' => '',
            'EDIT_FILE_AFTER' => '',
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
            'SECTION_CHOOSER' => 'L',
            'LIST_MODE' => '',
            'FIELDS' => array(),
            'ELEMENTS_NAME' => 'Элементы',
            'ELEMENT_NAME' => 'Элемент',
            'ELEMENT_ADD' => 'Добавить элемент',
            'ELEMENT_EDIT' => 'Изменить элемент',
            'ELEMENT_DELETE' => 'Удалить элемент',
            'SECTIONS_NAME' => 'Разделы',
            'SECTION_NAME' => 'Раздел',
            'SECTION_ADD' => 'Добавить раздел',
            'SECTION_EDIT' => 'Изменить раздел',
            'SECTION_DELETE' => 'Удалить раздел',
            'RIGHTS_MODE' => 'S',
            'GROUP_ID' => array(
                2 => 'R', //D
                1 => 'X'
            ),
            'VERSION' => 1
        );

        if ( !strlen( $arFields['CODE'] ) ) {
            return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Not found iblock code');
        }

        $iblockDbRes = CIBlock::GetList( array(), array('CODE' => $arFields['CODE'] ) );
        if ( $iblockDbRes !== false && $iblockDbRes->SelectedRowsCount() ) {
            return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Iblock with code = "' . $arFields['CODE'] .'" already exist.');
        }


        foreach ($arDefaultValues as $DefaultName => $DefaultValue)
        {
            if (!isset($arFields[$DefaultName]) || empty($arFields[$DefaultName]))
                $arFields[$DefaultName] = $DefaultValue;
        }

        $CIblock = new \CIBlock();

        $ID = $CIblock->Add($arFields);

        if ($ID)
        {
            return $RESPONSE[] = array('type' => 'success', 'ID' => $ID);
//            if (!$isRevert) {
//                $IblockRevert = new IblockRevertIntegrate();
//                if ($IblockRevert->Delete($arFields['CODE']))
//                    return $RESPONSE[] = array('type' => 'success', 'ID' => $ID);
//                else
//                    return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Cant create "iblock add revert" operation');
//            }
//            return $RESPONSE[] = array('type' => 'success', 'ID' => $ID);
        }
        else
            return $RESPONSE[] = array('type' => 'error', 'error_text' => $CIblock->LAST_ERROR);
    }

    /*
     * Update() - метод изменения инфоблока
     *
     * В метод можно передать любое количество параметров. Если какие-то значения не указаны - они не будут изменены
     *
     * @param string $IblockCode - код изменяемого инфоблока - no default/required
     * @param array $arFields - набор параметров изменяемого свойства:
     * @param string $arFields['IBLOCK_TYPE_ID'] - тип инфоблока - no default/required
     * @param string $arFields['NAME'] - название инфоблока - no default/required
     * @param string $arFields['CODE'] - символьный код инфоблока - no default/required
     * @param array $arFields['LID'] - сайты, к которым относится инфоблок - no default/required. Пример:
     *      array(
     *          0 => 's1',
     *          1 => 'en'
     *      );
     * @param string $arFields['ACTIVE'] - активность инфоблока - default 'Y'
     * @param string $arFields['LIST_PAGE_URL'] - URL страницы инфоблока - default '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/index.php?ID=#IBLOCK_ID#'
     * @param string $arFields['SECTION_PAGE_URL'] - URL страницы раздела инфоблока - default '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/list.php?SECTION_ID=#ID#'
     * @param string $arFields['DETAIL_PAGE_URL'] - URL страницы детального просмотра - default '#SITE_DIR#/'.$arFields['IBLOCK_TYPE_ID'].'/detail.php?ID=#ID#'
     * @param string $arFields['INDEX_SECTION'] - Индексировать разделы для модуля поиска - default 'Y'
     * @param string $arFields['INDEX_ELEMENT'] - Индексировать элементы для модуля поиска - default 'Y'
     * @param int $arFields['SORT'] - Сортировка - default 500
     * @param array $arFields['PICTURE'] - картинка инфоблока - default:
     *      array (
     *          'del' => NULL,
     *          'MODULE_ID' => 'iblock',
     *      );
     * @param string $arFields['DESCRIPTION'] - текстовое описание инфоблока - default ''
     * @param string $arFields['DESCRIPTION_TYPE'] - тип текстового описания - default 'text'. Возможные значения:
     *      text
     *      html
     * @param string $arFields['EDIT_FILE_BEFORE'] - Файл для редактирования элемента, позволяющий модифицировать поля перед сохранением - default ''
     * @param string $arFields['EDIT_FILE_AFTER'] - Файл с формой редактирования элемента - default ''
     * @param string $arFields['WORKFLOW'] - участвует в документообороте - default 'N'
     * @param string $arFields['BIZPROC'] - участвует в бизнес-процессах - default 'N'
     * @param string $arFields['SECTION_CHOOSER'] - Интерфейс привязки элемента к разделам - default 'L'. Возможные варианты:
     *      L - Список множественного выбора
     *      D - Выпадающие списки
     *      P - Окно поиска
     * @param string $arFields['LIST_MODE'] - Режим просмотра разделов и элементов - default ''. Возможные варианты:
     *      '' - из настроек модуля
     *      'S' - раздельный
     *      'C' - совместный
     *
     ********
     * ПОЛЯ *
     ********
     *
     * @param array $arFields['FIELDS'] - поля элементов инфоблока - default array().
     * Описание полей:
     *      @param array IBLOCK_SECTION - Привязка к разделам:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - всегда ''
     *      @param array ACTIVE - Активность:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default 'Y'
     *      @param array ACTIVE_FROM - Начало активности:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''. Возможные варианты:
     *              '' - Не задано
     *              '=now' - Текущие дата и время
     *              '=today' - Текущая дата
     *      @param array ACTIVE_TO - Окончание активности:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param int DEFAULT_VALUE - значение по умолчанию - default 0
     *      @param array SORT - Сортировка:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param int DEFAULT_VALUE - значение по умолчанию - всегда ''
     *      @param array NAME - Сортировка:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *      @param array PREVIEW_PICTURE - Картинка для анонса:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param array DEFAULT_VALUE:
     *              @param string FROM_DETAIL - Создавать картинку анонса из детальной (если не задана) - default 'N'
     *              @param string DELETE_WITH_DETAIL - Удалять картинку анонса, если удаляется детальная - default 'N'
     *              @param string UPDATE_WITH_DETAIL - Создавать картинку анонса из детальной даже если задана - default 'N'
     *              @param string SCALE - Уменьшать если большая - default 'N'
     *                @param int WIDTH - Максимальная ширина - default 0
     *                @param int HEIGHT - Максимальная высота - default 0
     *                @param string IGNORE_ERRORS - Игнорировать ошибки масштабирования - default 'N'
     *                @param string METHOD - Сохранять качество при масштабировании (требует больше ресурсов на сервере) - default 'Y'
     *                @param int COMPRESSION - Качество (только для JPEG, 1-100, по умолчанию около 75) - default 95
     *              @param string USE_WATERMARK_FILE - Наносить авторский знак в виде изображения - default 'N'
     *                @param string WATERMARK_FILE - Путь к изображению с авторским знаком - default ''
     *                @param int WATERMARK_FILE_ALPHA - Прозрачность авторского знака (%) - default 0
     *                @param string WATERMARK_FILE_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *              @param string USE_WATERMARK_TEXT - Наносить авторский знак в виде текста - default 'N'
     *                @param string WATERMARK_TEXT - Содержание надписи - default ''
     *                @param string WATERMARK_TEXT_FONT - Путь к файлу шрифта - default ''
     *                @param string WATERMARK_TEXT_COLOR - Цвет надписи (без #, например, FF00EE) - default ''
     *                @param int WATERMARK_TEXT_SIZE - Размер (% от размера картинки) - default 0
     *                @param string WATERMARK_TEXT_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *      @param array PREVIEW_TEXT_TYPE - Тип описания для анонса:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default 'text'. Возможные значения:
     *              text
     *              html
     *      @param array PREVIEW_TEXT - Описание для анонса:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *      @param array DETAIL_PICTURE - Картинка для анонса:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param array DEFAULT_VALUE:
     *              @param string SCALE - Уменьшать если большая - default 'N'
     *                @param int WIDTH - Максимальная ширина - default 0
     *                @param int HEIGHT - Максимальная высота - default 0
     *                @param string IGNORE_ERRORS - Игнорировать ошибки масштабирования - default 'N'
     *                @param string METHOD - Сохранять качество при масштабировании (требует больше ресурсов на сервере) - default 'Y'
     *                @param int COMPRESSION - Качество (только для JPEG, 1-100, по умолчанию около 75) - default 95
     *              @param string USE_WATERMARK_FILE - Наносить авторский знак в виде изображения - default 'N'
     *                @param string WATERMARK_FILE - Путь к изображению с авторским знаком - default ''
     *                @param int WATERMARK_FILE_ALPHA - Прозрачность авторского знака (%) - default 0
     *                @param string WATERMARK_FILE_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *              @param string USE_WATERMARK_TEXT - Наносить авторский знак в виде текста - default 'N'
     *                @param string WATERMARK_TEXT - Содержание надписи - default ''
     *                @param string WATERMARK_TEXT_FONT - Путь к файлу шрифта - default ''
     *                @param string WATERMARK_TEXT_COLOR - Цвет надписи (без #, например, FF00EE) - default ''
     *                @param int WATERMARK_TEXT_SIZE - Размер (% от размера картинки) - default 0
     *                @param string WATERMARK_TEXT_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *      @param array DETAIL_TEXT_TYPE - DETAIL_TEXT_TYPE:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default 'text'. Возможные значения:
     *              text
     *              html
     *      @param array DETAIL_TEXT - Детальное описание:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *      @param array XML_ID - Внешний код:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - всегда ''
     *      @param array CODE - Символьный код:
     *          @param string UNIQUE - Если код задан, то проверять на уникальность - default 'N'
     *          @param string TRANSLITERATION - Транслитерировать из названия при добавлении элемента - default 'N'
     *          @param int TRANS_LEN - Максимальная длина результата транслитерации - default 100
     *          @param string TRANS_CASE - Максимальная длина результата транслитерации - default 'L'. Возможные значения:
     *              '' - сохранить
     *              'L' - к нижнему
     *              'U' - к верхнему
     *          @param string TRANS_SPACE - Замена для символа пробела - default '-'
     *          @param string TRANS_OTHER - Замена для прочих символов - default '-'
     *          @param string TRANS_EAT - Удалять лишние символы замены - default 'Y'
     *          @param string USE_GOOGLE - Использовать внешний сервис для перевода - default 'N'
     *      @param array TAGS - Теги:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *
     *****************
     * ПОЛЯ РАЗДЕЛОВ *
     *****************
     *
     *      @param array SECTION_NAME - Название раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *      @param array SECTION_PICTURE - Картинка для анонса раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param array DEFAULT_VALUE:
     *              @param string FROM_DETAIL - Создавать картинку анонса из детальной (если не задана) - default 'N'
     *              @param string DELETE_WITH_DETAIL - Удалять картинку анонса, если удаляется детальная - default 'N'
     *              @param string UPDATE_WITH_DETAIL - Создавать картинку анонса из детальной даже если задана - default 'N'
     *              @param string SCALE - Уменьшать если большая - default 'N'
     *                @param int WIDTH - Максимальная ширина - default 0
     *                @param int HEIGHT - Максимальная высота - default 0
     *                @param string IGNORE_ERRORS - Игнорировать ошибки масштабирования - default 'N'
     *                @param string METHOD - Сохранять качество при масштабировании (требует больше ресурсов на сервере) - default 'Y'
     *                @param int COMPRESSION - Качество (только для JPEG, 1-100, по умолчанию около 75) - default 95
     *              @param string USE_WATERMARK_FILE - Наносить авторский знак в виде изображения - default 'N'
     *                @param string WATERMARK_FILE - Путь к изображению с авторским знаком - default ''
     *                @param int WATERMARK_FILE_ALPHA - Прозрачность авторского знака (%) - default 0
     *                @param string WATERMARK_FILE_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *              @param string USE_WATERMARK_TEXT - Наносить авторский знак в виде текста - default 'N'
     *                @param string WATERMARK_TEXT - Содержание надписи - default ''
     *                @param string WATERMARK_TEXT_FONT - Путь к файлу шрифта - default ''
     *                @param string WATERMARK_TEXT_COLOR - Цвет надписи (без #, например, FF00EE) - default ''
     *                @param int WATERMARK_TEXT_SIZE - Размер (% от размера картинки) - default 0
     *                @param string WATERMARK_TEXT_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *      @param array SECTION_DESCRIPTION_TYPE - Тип описания раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - всегда 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default 'text'. Возможные значения:
     *              text
     *              html
     *      @param array SECTION_DESCRIPTION - Описание раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - default ''
     *      @param array SECTION_DETAIL_PICTURE - Детальная картинка раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param array DEFAULT_VALUE:
     *              @param string SCALE - Уменьшать если большая - default 'N'
     *                @param int WIDTH - Максимальная ширина - default 0
     *                @param int HEIGHT - Максимальная высота - default 0
     *                @param string IGNORE_ERRORS - Игнорировать ошибки масштабирования - default 'N'
     *                @param string METHOD - Сохранять качество при масштабировании (требует больше ресурсов на сервере) - default 'Y'
     *                @param int COMPRESSION - Качество (только для JPEG, 1-100, по умолчанию около 75) - default 95
     *              @param string USE_WATERMARK_FILE - Наносить авторский знак в виде изображения - default 'N'
     *                @param string WATERMARK_FILE - Путь к изображению с авторским знаком - default ''
     *                @param int WATERMARK_FILE_ALPHA - Прозрачность авторского знака (%) - default 0
     *                @param string WATERMARK_FILE_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *              @param string USE_WATERMARK_TEXT - Наносить авторский знак в виде текста - default 'N'
     *                @param string WATERMARK_TEXT - Содержание надписи - default ''
     *                @param string WATERMARK_TEXT_FONT - Путь к файлу шрифта - default ''
     *                @param string WATERMARK_TEXT_COLOR - Цвет надписи (без #, например, FF00EE) - default ''
     *                @param int WATERMARK_TEXT_SIZE - Размер (% от размера картинки) - default 0
     *                @param string WATERMARK_TEXT_POSITION - Позиция размещения авторского знака - default 'tl'. Возможные варианты:
     *                  tl - Сверху слева
     *                  tc - Сверху по центру
     *                  tr - Сверху справа
     *                  ml - Cлева
     *                  mc - По центру
     *                  mr - Cправа
     *                  bl - Снизу cлева
     *                  bc - Снизу по центру
     *                  br - Снизу cправа
     *      @param array SECTION_XML_ID - Внешний код раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *          @param string DEFAULT_VALUE - значение по умолчанию - всегда ''
     *      @param array SECTION_CODE - Символьный код раздела:
     *          @param string UNIQUE - Если код задан, то проверять на уникальность - default 'N'
     *          @param string TRANSLITERATION - Транслитерировать из названия при добавлении элемента - default 'N'
     *          @param int TRANS_LEN - Максимальная длина результата транслитерации - default 100
     *          @param string TRANS_CASE - Максимальная длина результата транслитерации - default 'L'. Возможные значения:
     *              '' - сохранить
     *              'L' - к нижнему
     *              'U' - к верхнему
     *          @param string TRANS_SPACE - Замена для символа пробела - default '-'
     *          @param string TRANS_OTHER - Замена для прочих символов - default '-'
     *          @param string TRANS_EAT - Удалять лишние символы замены - default 'Y'
     *          @param string USE_GOOGLE - Использовать внешний сервис для перевода - default 'N'
     *
     ******************
     * ЖУРНАЛ СОБЫТИЙ *
     * ****************
     *
     *      @param array LOG_SECTION_ADD - Записывать добавление раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *      @param array LOG_SECTION_EDIT - Записывать изменение раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *      @param array LOG_SECTION_DELETE - Записывать удаление раздела:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *      @param array LOG_ELEMENT_ADD - Записывать добавление элемента:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *      @param array LOG_ELEMENT_EDIT - Записывать изменение элемента:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *      @param array LOG_ELEMENT_DELETE - Записывать удаление элемента:
     *          @param string IS_REQUIRED - обязательность заполнения поля - default 'N'
     *
     ***********
     * ПОДПИСИ *
     ***********
     *
     * @param string $arFields['ELEMENTS_NAME'] - заголовок "Элементы" - default 'Элементы'
     * @param string $arFields['ELEMENT_NAME'] - заголовок "Элемент" - default 'Элемент'
     * @param string $arFields['ELEMENT_ADD'] - заголовок "Добавить элемент" - default 'Добавить элемент'
     * @param string $arFields['ELEMENT_EDIT'] - заголовок "Изменить элемент" - default 'Изменить элемент'
     * @param string $arFields['ELEMENT_DELETE'] - заголовок "Удалить элемент" - default 'Удалить элемент'
     * @param string $arFields['SECTIONS_NAME'] - заголовок "Разделы" - default 'Разделы'
     * @param string $arFields['SECTION_NAME'] - заголовок "Раздел" - default 'Раздел'
     * @param string $arFields['SECTION_ADD'] - заголовок "Добавить раздел" - default 'Добавить раздел'
     * @param string $arFields['SECTION_EDIT'] - заголовок "Изменить раздел" - default 'Изменить раздел'
     * @param string $arFields['SECTION_DELETE'] - заголовок "Удалить раздел" - default 'Удалить раздел'
     *
     * @param string $arFields['RIGHTS_MODE'] - Расширенное управление правами - default 'S'. Возможные варианты:
     *      S - стандартные права
     *      E - расширенное управление правами
     *
     * @param array $arFields['GROUP_ID'] - права на доступ к инфоблоку для групп пользователей:
     *      array (
     *          2 => 'D',   // для всех групп - "Нет доступа"
     *          1 => 'X',   // для админов - "Полный доступ"
     *          3 => '',    // для другой группы - "Наследовать"
     *      );
     *
     * @param int $arFields['VERSION'] - "Инфоблоки (1.0)" или "Инфоблоки+ (2.0)" - default 1. Возможные значения:
     *      1 - Инфоблоки (1.0)
     *      2 - Инфоблоки+ (2.0)
     *
     *
     * Summary:
     * 1 required
     * 35 optional with defaults
     *
     * return array - массив с флагом успешности изменения инфоблока или с текстом возникшей в процессе ошибки
     */
    public function Update($IblockCode, $arFields,$isRevert = false)
    {
        global $RESPONSE;

        unset($arFields['ID']);
        $dbIblock = CIBlock::GetList(array(), array('CODE' => $IblockCode));
        if ($arIblock = $dbIblock->Fetch())
        {
            $NewArFields = array_merge($arIblock, $arFields);

            foreach ($NewArFields as $fieldKey => $field)
                if ($field === null)
                    $NewArFields[$fieldKey] = false;

            if (!$isRevert) {

                $IblockRevert = new IblockRevertIntegrate();
                if ($IblockRevert->Update($IblockCode))
                {
                    $Iblock = new \CIBlock();
                    if ($Iblock->Update($NewArFields['ID'], $NewArFields))
                        return $RESPONSE[] = array('type' => 'success');
                    else
                        return $RESPONSE[] = array('type' => 'error', 'error_text' => $Iblock->LAST_ERROR);
                }
                else {
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Cant complete "iblock update revert" operation');
                }

            } else {
                $Iblock = new \CIBlock();
                if ($Iblock->Update($NewArFields['ID'], $NewArFields))
                    return $RESPONSE[] = array('type' => 'success');
                else
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => $Iblock->LAST_ERROR);
            }
        }
        else
            return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Not find iblock with code '.$IblockCode);
    }

    /*
     * Delete() - метод удаления инфоблока
     * @param string $IblockCode - код инфоблока
     */
    public function Delete($IblockCode, $isRevert = false)
    {
        global $RESPONSE;
        $dbIblock = CIBlock::GetList(array(), array('CODE' => $IblockCode));
        if ($arIblock = $dbIblock->Fetch())
        {
            $iblockElDbRes = CIBlockElement::GetList( array(), array('IBLOCK_ID' => $arIblock['ID'] ) );
            if ( $iblockElDbRes !== false && $iblockElDbRes->SelectedRowsCount() ) {
                return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Can not delete iblock id = ' . $arIblock['ID'] . ' have elements');
            }
            if (!$isRevert) {


                $IblockRevert = new IblockRevertIntegrate();

                if ($IblockRevert->Add($IblockCode))
                {
                    if (CIBlock::Delete($arIblock['ID'])) {
                        return $RESPONSE[] = array('type' => 'success');
                    } else {
                        return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Iblock delete error!');
                    }
                }
                else {
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Cant complete "iblock type delete revert" operation');
                }

            } else {

                if (CIBlock::Delete($arIblock['ID'])) {
                    return $RESPONSE[] = array('type' => 'success');
                } else {
                    return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Iblock delete error!');
                }

            }
        }
        else {
            return $RESPONSE[] = array('type' => 'error', 'error_text' => 'Not find iblock with code '.$IblockCode);
        }
    }
}