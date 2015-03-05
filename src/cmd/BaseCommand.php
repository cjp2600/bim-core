<?php

use ConsoleKit\Command,
    ConsoleKit\Colors;

abstract class BaseCommand extends Command {

    /**
     * info
     * @param $text
     */
    public function info($text)
    {
        $this->writeln($text, Colors::YELLOW);
    }

    /**
     * success
     * @param $text
     */
    public function success($text)
    {
        $this->writeln($text, Colors::GREEN);
    }

    /**
     * padding
     * @param $text
     * @throws \ConsoleKit\ConsoleException
     */
    public function padding($text)
    {
        $box = new ConsoleKit\Widgets\Box($this->console, $text, '');
        $box->write();
        $this->writeln('');
    }

    /**
     * info
     * @param $text
     */
    public function error($text)
    {
        $this->writeln($text, Colors::RED);
    }

    /**
     * setTemplate
     * @param $name of create
     * @param $method
     * @param array $data
     * @param string $type up|down
     * @return mixed|string
     */
    public function setTemplateMethod($name, $method, $data = array(),$type = "up")
    {
        $template = file_get_contents(__DIR__.'/../db/template/'.$name.'/'.$method.'/'.$type.'.txt');
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $template = str_replace("#".$key."#", $val, $template);
            }
        }
        return $template;
    }

    /**
     * setTemplate
     * @param $class_name
     * @param $up_content
     * @param $down_content
     * @param string $desc_content
     * @param string $author
     * @return mixed|string
     */
    public function setTemplate($class_name, $up_content, $down_content, $desc_content = "",$author = "")
    {
        $template = file_get_contents(__DIR__.'/../db/template/main.txt');
        $template = str_replace(array("#CLASS_NAME#", "#UP_CONTENT#", "#DOWN_CONTENT#","#DESC_CONTENT#","#AUTHOR_CONTENT#"), array($class_name, $up_content, $down_content,$desc_content,$author), $template);
        return $template;
    }

    /**
     * saveTemplate
     * @param $filename
     * @param $template
     * @param bool $needUp
     * @throws Exception
     */
    public function saveTemplate($filename,$template,$needUp = false)
    {
        $migration_path = $this->getMigrationPath();
        if (!file_exists($migration_path)){
            mkdir($migration_path, 0777);
        }
        if (!is_writable($migration_path)){
            throw new Exception("No permission to create a migration file in the folder ".$migration_path);
        }

        $save_file = $migration_path.$filename.'.php';
        $newFile = fopen($save_file, 'w');
        fwrite($newFile, $template);
        fclose($newFile);

        if (file_exists($save_file)) {

            #if need up
            if ($needUp){
                $this->autoUpMethod($needUp,$save_file,$filename);
            }

            # output
            $this->writeln("Create new migration file: ");
            $this->success($save_file);

        } else {
            throw new Exception("Failed to create the migration file ".$save_file);
        }

    }

    /**
     * autoUpMethod
     * @param $needUp
     * @param $save_file
     * @param $migration
     * @throws Exception
     * @throws \Bim\Db\Entity\Exception
     */
    public function autoUpMethod($needUp,$save_file,$migration)
    {
        $time_start = microtime(true);
        $this->info(" -> Start auto applying migration:");
        $this->writeln('');
        if ($needUp != "add") {
            include_once "" . $save_file . "";
            $migrationClass = "Migration" . $migration;
            # check bim migration.
            if ((method_exists($migrationClass, "up"))) {
                try {
                    # call up function
                    if (false !== $migrationClass::up()) {

                        if (!Bim\Db\Entity\MigrationsTable::isExistsInTable($migration)) {
                            if (Bim\Db\Entity\MigrationsTable::add($migration)) {
                                $this->writeln($this->color("     - applied   : " . $migration, Colors::GREEN));
                            } else {
                                throw new Exception("add in migration table error");
                            }
                        }
                    } else {
                        $this->writeln(Colors::colorize("     - error : " . $migration, Colors::RED) . " " . Colors::colorize("(Method Up return false)", Colors::YELLOW));
                    }
                } catch (Exception $e) {
                    $this->writeln(Colors::colorize("     - error : " . $migration, Colors::RED) . " " . Colors::colorize("(" . $e->getMessage() . ")", Colors::YELLOW));
                }
            }
        } else {

            if (!Bim\Db\Entity\MigrationsTable::isExistsInTable($migration)) {
                if (Bim\Db\Entity\MigrationsTable::add($migration)) {
                    $this->writeln($this->color("     - applied   : " . $migration, Colors::GREEN));
                } else {
                    throw new Exception("add in migration table error");
                }
            }

        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $this->writeln('');
        $this->info(" -> ".round($time, 2)."s");
    }

    /**
     * getMigrationPath
     * @param bool $full
     * @return mixed|string
     */
    public function getMigrationPath($full = true)
    {
        $conf = new \Noodlehaus\Config(__DIR__."/../config/bim.json");
        $migration_path = $conf->get("migration_path");
        return ($full) ? $_SERVER["DOCUMENT_ROOT"] . "/".$migration_path."/" : $migration_path;
    }

    /**
     * clear
     * @param $text
     * @return mixed
     */
    public function clear($text)
    {
        return trim($text);
    }

    /**
     * getMigrationName
     * @return string
     */
    public function getMigrationName()
    {
        return time();
    }

    /**
     * fromCamelCase
     * @param $input
     * @return string
     */
    public function fromCamelCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * camelCase
     * @param $str
     * @param array $noStrip
     * @return mixed|string
     */
    public function camelCase($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = str_replace(":", "", $str);
        $str = preg_replace('/[0-9]+/', '', $str);
        $str = lcfirst($str);

        return $str;
    }

    /**
     * getDirectoryTree
     * @param $outerDir
     * @param $x
     * @return array
     */
    public function getDirectoryTree($outerDir, $x)
    {
        $dirs = array_diff(scandir($outerDir), Array(".", ".."));
        $dir_array = Array();
        foreach ($dirs as $d) {
            if (is_dir($outerDir . "/" . $d)) {
                $dir_array[$d] = $this->getDirectoryTree($outerDir . "/" . $d, $x);
            } else {
                if (($x) ? ereg($x . '$', $d) : 1)
                    $dir_array[str_replace("." . $x, "", $d)] = $d;
            }
        }

        $return = array();
        foreach ($dir_array as $key => $val) {
            # include migration file.
            include_once "" . $this->getMigrationPath() . $val . "";
            $class_name = "Migration" . $key;
            # check instance of Revision interface.
            if (new $class_name() instanceof Bim\Revision) {
                # get description
                $description = (method_exists($class_name, "getDescription")) ? $class_name::getDescription() : "";
                # set translit description (because its cli)
                $description = $this->translit($description);
                # colorize
                $description = $this->color_tg($description);
                # get tags
                $tags = (!empty($description)) ? $this->getHashTags($description) : array();
                # get author
                $author = (method_exists($class_name, "getAuthor")) ? $class_name::getAuthor() : "";

                $return[$key] = array(
                    "name" => $key,
                    "file" => $val,
                    "date" => $key,
                    "author" => $author,
                    "description" => $description,
                    "tags" => $tags
                );
            }
        }
        return $return;
    }

    /**
     * logging
     *
     * @param array $input_array
     * @param string $file_name
     * @param string $type
     */
    public function logging( $input_array = array(), $type = "up", $file_name = 'bim.log' )
    {
        $conf = new \Noodlehaus\Config(__DIR__ . "/../config/bim.json");
        $logging_path = $conf->get("logging_path");
        $return_message = " >> php bim " . $type . " \n";
        $return_message .= date('d.m.Y H:i:s') . "\n";
        $return_message .= print_r($input_array, true) . "\n";
        $return_message .= "\n----------\n\n";
        $file_name = empty($file_name) ? 'bim.log' : $file_name;
        $log_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $logging_path . '/' . date("Y") . "/" . date("m") . "/" . date("d");
        if (!file_exists($log_path)) {
            mkdir($log_path, 0777, true);
        }
        file_put_contents($log_path . '/' . $file_name, $return_message, FILE_APPEND);
        $this->writeln("Put info to log file > ".Colors::colorize($log_path . '/' . $file_name,Colors::GREEN));
    }

    /**
     * translit
     *
     * Analog bitrix Utils::translit
     * Unfortunately, the standard method does not work as it is necessary for us.
     * (because its bitrix baby!)
     *
     * @param $str
     * @return string
     */
    public function translit($str)
    {
        $params = array(
            "max_len" => "200",
            "change_case" => "L",
            "replace_space" => " ",
            "replace_other" => " ",
            "delete_repeat_replace" => "true",
            "safe_chars" => "#_-[]()"
        );
        $russian = array(
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
            'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
            'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $latin = array(
            'A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
            'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i',
            'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e',
            'yu', 'ya');
        $str = str_replace($russian, $latin, $str);
        $len = strlen($str);
        $str_new = '';
        $last_chr_new = '';
        for ($i = 0; $i < $len; $i++) {
            $chr = substr($str, $i, 1);
            if (preg_match("/[a-zA-Z0-9]/" . BX_UTF_PCRE_MODIFIER, $chr) || strpos($params["safe_chars"], $chr) !== false) {
                $chr_new = $chr;
            } elseif (preg_match("/\\s/" . BX_UTF_PCRE_MODIFIER, $chr)) {
                if (
                    !$params["delete_repeat_replace"]
                    ||
                    ($i > 0 && $last_chr_new != $params["replace_space"])
                ) {
                    $chr_new = $params["replace_space"];
                } else {
                    $chr_new = '';
                }
            } else {
                if (
                    !$params["delete_repeat_replace"]
                    ||
                    ($i > 0 && $i != $len - 1 && $last_chr_new != $params["replace_other"])
                ) {
                    $chr_new = $params["replace_other"];
                } else {
                    $chr_new = '';
                }
            }
            if (strlen($chr_new)) {
                if ($params["change_case"] == "L" || $params["change_case"] == "l") {
                    $chr_new = ToLower($chr_new);
                } elseif ($params["change_case"] == "U" || $params["change_case"] == "u") {
                    $chr_new = ToUpper($chr_new);
                }
                $str_new .= $chr_new;
                $last_chr_new = $chr_new;
            }
            if (strlen($str_new) >= $params["max_len"]) {
                break;
            }
        }
        return $str_new;
    }

    /**
     * getHashTags
     * @param $text
     * @return array
     */
    public function getHashTags($text)
    {
        $hashtags = array();
        preg_match_all("/#([\w-]+)/i", $text, $matches);
        if( !empty($matches[0]) ){
            foreach( $matches[0] as $hashtag ){
                $hashtag = strtolower( str_replace('#', '', $hashtag) );
                $hashtags[] = $hashtag;
            }
        }
        if (!empty($hashtags)) {
            $hashtags = array_unique($hashtags);
        }
        return $hashtags;
    }

    /**
     * color_tg
     * @param $text
     * @return mixed
     */
    public function color_tg($text)
    {
        return preg_replace("/#([\w-]+)/i", Colors::colorize("$0",Colors::BLUE), $text);
    }

    /**
     * green
     * @param $text
     * @return string
     */
    public function color($text,$color)
    {
        return Colors::colorize($text, $color);
    }

    /**
     * checkInDb
     * @param $migration_id
     * @return bool
     */
    public function checkInDb($migration_id)
    {
        # check migration table
        return Bim\Db\Entity\MigrationsTable::isExistsInTable($migration_id);
    }


}
