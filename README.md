
# <a name="about"></a>Bitrix Migration (BIM)

[![Latest Stable Version](https://poser.pugx.org/cjp2600/bim-core/v/stable.svg)](https://packagist.org/packages/cjp2600/bim-core) [![Total Downloads](https://poser.pugx.org/cjp2600/bim-core/downloads.svg)](https://packagist.org/packages/cjp2600/bim-core) [![Latest Unstable Version](https://poser.pugx.org/cjp2600/bim-core/v/unstable.svg)](https://packagist.org/packages/cjp2600/bim-core) [![License](https://poser.pugx.org/cjp2600/bim-core/license.svg)](https://packagist.org/packages/cjp2600/bim-core)

Версионная миграция структуры БД для **[1С Битрикс CMS](http://bitrix.ru)**

- [Установка](#install)
  * [Автоматическая установка](#auto)
  * [Ручная установка](#hand)
- [Настройка](#prop)
- [Выполнение - bim up](#up)
- [Отмена - bim down](#down)
- [Вывод списка - bim ls](#ls)
- [Создание - bim gen](#gen)
  * [Создание пустой миграции](#gen_empty)
  * [Создание миграционного кода по наличию](#gen_nal)
    * [IblockType](#iblocktype)
    * [Iblock](#iblock)
    * [IblockProperty](#iblockproperty)
    * [Highloadblock](#hlblock)
    * [HighloadblockField](#hlblockfield)
  * [Режим multi - bim gen multi](#multi)
  * [Тегирование миграций](#tag)
- [Информация о проекте - bim info](#info)

# <a name="install"></a>1 Установка

### <a name="auto"></a>1.1 Автоматическая установка 

Для установки и инициализации bim для bitrix проекта необходимо выполнить следующиие действия из корня проекта:

- Установить Composer:
```    
curl -s https://getcomposer.org/installer | php
```
- Выполнить установочный скрипт:

``` bash
php -r "readfile('https://raw.githubusercontent.com/cjp2600/bim/master/install');" | php
```
> Автоматические действия установщика:

> 1. Добавление файла **bim** в корень проекта.
> 2. Инициализация **composer autoloader** в файле **init.php**
> 3. Создание файла **composer.json** в корне проекта со ссылкой на **bim** репозиторий **"require": { "cjp2600/bim-core": "dev-master"}**

### <a name="hand"></a>1.2 Ручная установка 

Для ручной установки bim необходимо:

- Установить Composer:
   
```    
curl -s https://getcomposer.org/installer | php
```

- Создать файл bim (без расширения) в корне bitrix проекта с содержимым:
```php
#!/usr/bin/php
<?php

ini_set('max_execution_time', 36000);

define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = dirname(__FILE__);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

\Bim\Migration::init();

```
- Добавть инициализацию composer (в файл init.php добавить запись):

```bash
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'))
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
```

- Создать в корне сайта файл **composer.json** с содержимым:

```json
{
	"require": {
		"cjp2600/bim-core": "dev-master"
	}
}
```

- В **.gitignore** добавить запись:

```
/vendor
*.lock
```


# 2 <a name="prop"></a>Настройка

Для начала работы обновляем **composer** и создаем миграционную таблицу в БД:

``` bash
php composer.phar update
```
Создаём таблицу миграций : 

```bash
php bim init
```


# 3 <a name="up"></a>Выполнение миграций [BIM UP]

- Общее выполнение:
```bash
php bim up
```
Выполняет полный список не выполненых либо ранее отмененных миграционных классов отсортированых по названию (**timestamp**).

- Еденичное выполнение:
```bash
php bim up 1423660766
```
Выполняет указанную в праметрах миграцию.

- Выполнение по временному периоду:
```bash
php bim up --from="29.01.2015 00:01" --to="29.01.2015 23:55"
```
- Выполнение по тегу:
```bash
php bim up --tag=iws-123
```
Выполняет все миграции где найден указанный тег в описании.

Например:

> Description: add new migration #iws-123


# 4 <a name="down"></a>Отмена выполненых миграций  [BIM DOWN]

- Общая отмена:
```bash
php bim down
```
Отменяет весь список выполненных миграционных классов.

- Еденичная отмена:
``` bash
php bim down 1423660766
```
Отменяет указанную в праметрах миграцию.

- Отмена по временному периоду:
```bash
php bim down --from="29.01.2015 00:01" --to="29.01.2015 23:55"
```
- Отмена по тегу:
```bash
php bim up --tag=iws-123
```
Отменяет все миграции где найден указанный тег в описании.

Например:
> Description: add new migration #iws-123

# 5 <a name="ls"></a>Вывод списка миграций [BIM LS]
- Общей список:
```bash
php bim ls
```
- Список выполненных миграций:
```bash
php bim ls --a
```
- Список отменённых миграций:
```bash
php bim ls --n
```
- Список миграций за определённый период времени:
```bash
php bim ls --from="29.01.2015 00:01" --to="29.01.2015 23:55" 
```
- Список миграций по тегу:
```bash
php bim ls --tag=iws-123
```

# 6 <a name="gen"></a>Создание новых миграций [BIM GEN]

Существует два способа создания миграций:
 
## <a name="gen_empty"></a>1) Создание пустой миграции:
Создается пустой шаблон миграционного класса. Структура класса определена интерфейсом *Bim/Revision* и включает следующие
обязательные методы:
 
  - *up();* - выполнение
  - *down();* - отмена
  - *getDescription();* - получения описания.
  - *getAuthor();* - получение автора.

Дополнительно запрашивается:
- [Description]
 
**Пример:**

``` bash
php bim gen
```
Также возможно передать description опционально:
``` bash  
php bim gen --d="new description #iws-123"
```

> Далее создается файл миграции вида: */[migrations_path]/[timestamp].php

> Например: /migrations/123412434.php
 
## <a name="gen_nal"></a>2) Создание миграционного кода по наличию:

Создается код развертывания/отката существующего элемента схемы bitrix БД.
На данный момент доступно генерация по наличию для следующих элементов bitrix БД:
 
### 2.1 <a name="iblocktype"></a>IblockType *( php bim gen IblockType:[add|delete] )*:

Создается Миграционный код "**Типа ИБ**" включая созданные для него *(UserFields, IBlock, IblockProperty)*
 
Дополнительно запрашивается:
- [IBLOCK_TYPE_ID]
- [Description]
 
**Пример:**

``` bash 
php bim gen IblockType:add
``` 
Также возможно передать iblock type id и description опционально:
``` bash  
php bim gen IblockType:add --typeId=catalog --d="new description #iws-123"
``` 
### <a name="iblock"></a>2.2 Iblock *( php bim gen Iblock:[add|delete] )*:

Создается Миграционный код "**ИБ**" включая созданные для него *(IblockProperty)*

Дополнительно запрашивается:
- [IBLOCK_CODE]
- [Description]

**Пример:**
``` bash  
php bim gen Iblock:add
``` 
Также возможно передать iblock code и description опционально:
``` bash  
php bim gen Iblock:add --code=goods --d="new description #iws-123"
``` 

### <a name="iblockproperty"></a>2.3 IblockProperty *( php bim gen IblockProperty:[add|delete] )*:

Создается Миграционный код "**Свойства ИБ**"

Дополнительно запрашивается:
- [IBLOCK_CODE]
- [PROPERTY_CODE]
- [Description]

**Пример:**
``` bash  
php bim gen IblockProperty:add
``` 
Также возможно передать iblock code, property code и description опционально:
``` bash  
php bim gen IblockProperty:add --code=goods --propertyCode=NEW_ITEM --d="new description #iws-123"
``` 

### <a name="hlblock"></a>2.4 Hlblock *( php bim gen Hlblock:[add|delete] )*:

Создается Миграционный код "**Highloadblock**" включая созданные для него *(UserFields)*

Дополнительно запрашивается:
- [HLBLOCK_ID]
- [Description]

**Пример:**
``` bash  
php bim gen Hlblock:add
``` 
Также возможно передать hlblock id и description опционально:
``` bash  
php bim gen IHlblock:add --id=82 --d="new description #iws-123"
``` 

### <a name="hlblockfield"></a>2.4 HlblockField *( php bim gen HlblockField:[add|delete] )*:

Создается Миграционный код "**HighloadblockField (UserField)**"

Дополнительно запрашивается:
- [HLBLOCK_ID]
- [USER_FIELD_ID]
- [Description]

**Пример:**
``` bash  
php bim gen HlblockField:add
``` 
Также возможно передать hlblock id, hlblock field id и description опционально:
``` bash  
php bim gen IHlblock:add --hlblockid=93 --hlFieldId=582 --d="new description #iws-123"
```


> Обратите внимание!

> что миграционные классы созданные по наличию, выполняются автоматически.


## <a name="multi"></a> Режим multi [BIM GEN MULTI]:

Так же доступен режим массовой генерации по наличию. Данный способ удобен при созданиие миграций по наличию для множества одинаковых элементов.
Например для нескольких UserFields.

**Пример:**

``` bash
php bim gen multi
```

## <a name="tag"></a> Тегирование миграций:

При создании нового миграционного класса существует возможность выставления тега в комментарии к миграции для дальнейшей более удобной отмены либо выполнения группы миграций связанных одним тегом.

**Формат**: #[название]

**Пример:**
Как вариант применения, вставлять тег номера задачи из трекера.

``` bash
[Description]: #IWS-242 Add new Iblock[services]
```


# <a name="info"></a>7 Информация о проекет [BIM INFO]

Информация о текущем bitrix проекте:

- Название проекта
- Версия bitrix
- Редакция bitrix

**Пример:**
``` bash  
php bim info
``` 
 
