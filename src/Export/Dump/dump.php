<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/backup.php");


function haveTime()
{
    return true;
}

function IntOption($name, $def = 0)
{
    static $CACHE;
    if (!$CACHE[$name]) {
        $CACHE[$name] = COption::GetOptionInt("main", $name, $def);
    }
    return $CACHE[$name];
}

function makeLocalDump($filename) {
    $state = false;
    return (new \CBackup())->MakeDump($filename, $state);
}