# 1.1 Автоматическая установка BIM

Для установки и инициализации bim для bitrix проекта необходимо выполнить следующиие действия из корня проекта.

``` bash
php -r "readfile('https://raw.githubusercontent.com/cjp2600/bim/master/install');" | php
```
> Автоматические действия установщика:
1) Добавление файла **bim** в корень проекта.
2) Инициализация **composer autoloader** в файле **inint.php**
3) Создание файла **composer.json** в корне проекта со ссылкой на **bim** репозиторий **"require": { "cjp2600/bim-core": "dev-master"}**

## 1.2 Ручная установка BIM

Для ручной установки bim необходимо:

1) Создать файл bim (без расширения) в корне bitrix проекта с содержимым:
```
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
2) Добавть инициализацию composer (в файл init.php добавить запись):

```
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'))
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
```

3) Создать в корне сайта файл **composer.json** с содержимым:

```
{
	"require": {
		"cjp2600/bim-core": "dev-master"
	}
}
```

4) В **.gitignore** добавить запись:

```
/vendor
*.lock
```




# 2 Настройка

Для начала работы обновляем **composer** и создаем миграционную таблицу в БД:

``` bash
php composer.phar update
```
Создаём таблицу миграций : 

```
php bim init
```



# 3 Развертывание [BIM UP]

### 1) Общее применение:
```bash
php bim up
```
Применяет весь список не применённых миграционных классов отсортированых по названию (**timestamp**).

2) Еденичное применение:
``` bash
php bim up 1423660766
```
Применяет указанную в праметрах миграцию.

3) Применение по временному периоду:
```bash
php bim up --from="29.01.2015 00:01" --to="29.01.2015 23:55"
```
4) Применение по тегу:
```bash
php bim up --tag=iws-123
```
Применяет все миграции где найден указанный тег в описании.

Например:

> Description: add new migration #iws-123



# 4 Откат  [BIM DOWN]

### 1) Общей откат:
```bash
php bim down
```
Применяет весь список применённых миграционных классов.

2) Еденичный откат:
``` bash
php bim down 1423660766
```
Откатывает указанную в праметрах миграцию.

3) Откат по временному периоду:
```bash
php bim down --from="29.01.2015 00:01" --to="29.01.2015 23:55"
```
4) Откат по тегу:
```bash
php bim up --tag=iws-123
```
Откатывает все миграции где найден указанный тег в описании.

Например:
> Description: add new migration #iws-123

# 5 Вывод списка миграций [BIM LS]
1) Общей список:
```bash
php bim ls
```
2) Список применённых миграций:
```bash
php bim ls --a
```
3) Список новых миграций:
```bash
php bim ls --n
```
4) Список миграций за определённый период времени:
```bash
php bim ls --from="29.01.2015 00:01" --to="29.01.2015 23:55" 
```
5) Список миграций по тегу:
```bash
php bim ls --tag=iws-123
```

# 6 Создание новых миграций [BIM GEN]

Существует два способа создания миграций:
 
## 1) Создание пустой миграции:
Создается пустой шаблон миграционного класса. Структура класса определена интерфейсом *Bim/Revision* и включает следующие
обязательные методы:
 
  - *up();* - метод развертывания.
  - *down();* - метод отката.
  - *getDescription();* - получения описания.
  - *getAuthor();* - получение автора.
 
Задача - развертывание и откат кастомного кода изменения схем bitrix БД.
 
**Пример:**

``` bash
php bim gen
```
> После выполнения команды создается файл миграции вида: */[migrations_path]/[timestamp].php*
> Например /migrations/123412434.php
 
## 2) Создание миграционного кода по наличию:

Создается код развертывания/отката существующего элемента схемы bitrix БД.
На данный момент доступно генерация по наличию для следующих элементов bitrix БД:
 
### 2.1 IblockType *( php bim gen IblockType:[add|delete] )*:

Создается Миграционный код "**Типа ИБ**" включая созданные для него *(UserFields, IBlock, IblockProperty)*
 
Дополнительно запрашивается:
- [IBLOCK_TYPE_ID]
- [Description]
 
**Пример:**

``` bash 
php bim gen IblockType:add
``` 
Также возможно передать **iblock type id** опционально:
``` bash  
php bim gen IblockType:add --typeId=catalog
``` 
### 2.2 Iblock *( php bim gen Iblock:[add|delete] )*:

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

### 2.2 IblockProperty *( php bim gen IblockProperty:[add|delete] )*:

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


> Обратите внимание! что миграционные классы созданые по наличию, применяются автоматически.
 
