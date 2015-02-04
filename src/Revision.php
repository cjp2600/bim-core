<?php
/**
 * Created for the project "bim-core"
 *
 * @author: Stanislav Semenov (CJP2600)
 * @email: cjp2600@ya.ru
 *
 * @date: 23.01.2015
 * @time: 8:05
 */

namespace Bim;

interface Revision {

    public static function up();
    public static function down();
    public static function getDescription();
    public static function getAuthor();

}