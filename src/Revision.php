<?php

namespace Bim;
/**
 * Bitrix Migration (BIM)
 * Documentation: http://cjp2600.github.io/bim-core/
 */
interface Revision {

    public static function up();
    public static function down();
    public static function getDescription();
    public static function getAuthor();

}