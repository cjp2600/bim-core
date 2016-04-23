<?php

use Alchemy\Zippy\Adapter\GNUTar\TarGzGNUTarAdapter;
use Alchemy\Zippy\FileStrategy\TarGzFileStrategy;
use Alchemy\Zippy\Zippy;
use ConsoleKit\Colors;

/**
 * =================================================================================
 * Информация об экспорте [BIM EXPORT]
 * =================================================================================
 *
 * @example php vendor/bin/bim export
 *
 * Documentation: https://github.com/cjp2600/bim-core
 * =================================================================================
 */
class ExportCommand extends BaseCommand
{

    public function execute(array $args, array $options = array())
    {
        if (count($args) > 0) {
            $methodName = ucfirst($args[0]);
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($args);
            } else {
                throw new \Bim\Exception\BimException("command (export " . $args[0] . ") not found");
            }
        } else {
            $this->padding(" call default method " . $this->color(strtoupper("completed"),
                    \ConsoleKit\Colors::GREEN));
        }
    }

    /**
     * @throws \Bim\Exception\BimException
     */
    public function Make()
    {
        $time_start = microtime(true);
        $this->info(" -> Start make export:");
        $this->writeln('');

        $session = new \Bim\Export\Session();
        $session->init();
        $session->packCore();

        $this->padding($session->getDataResponse());

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $this->writeln('');
        $this->info(" -> " . round($time, 2) . "s");
    }

    /**
     * @param bool $return
     * @return array|mixed
     * @throws \Bim\Exception\BimException
     */
    public function Ls($return = false)
    {
        $exportJson = self::getMigrationPath() . \Bim\Export\Session::EXPORT_FOLDER . "/export.json";
        if (file_exists($exportJson)) {
            $data = json_decode(file_get_contents($exportJson), true);
        } else {
            $data = array();
            file_put_contents($exportJson, json_encode($data));
        }

        if ($return === true) {
            return $data;
        }

        if (!empty($data)) {
            if (isset($data['items'])) {
                $list = array();
                foreach ($data['items'] as $key => $item) {
                    $list[] = "[" . $key . "] - " . $this->color(strtoupper($item['id']),
                            \ConsoleKit\Colors::GREEN);
                }
                $this->padding(implode("\n", $list));
            } else {
                throw new \Bim\Exception\BimException("Bad format export.json");
            }
        } else {
            // empty
        }
        return $data;
    }


    public function Install($args)
    {
        global $DB;
        $this->padding("up export databases (Revision № ".$args[1].") ...");
        $session = new \Bim\Export\Session();
        $sqlBatch = $session->getSqlBatchByExport($args, $this->Ls(true));

        $i = 0;
        if ($sqlBatch) {
            foreach ($sqlBatch as $i => $bath) {
                $err_mess = "Line: ";
                $res = $DB->Query($bath, false, $err_mess.__LINE__);
                fwrite(\STDOUT, "   SQL QUERY: ".$i." \r");
            }
        }
        fwrite(\STDOUT, "   SQL QUERY: ".$i." - ".$this->color(strtoupper("completed"),\ConsoleKit\Colors::GREEN)." \n");


        $this->padding("up export core (Revision № ".$args[1].") ...");

    }

}
