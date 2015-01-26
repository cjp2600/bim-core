<?php

use ConsoleKit\Widgets\ProgressBar,ConsoleKit\Colors;

class DownCommand extends BaseCommand
{
    public function execute(array $args, array $options = array())
    {
        $list = $this->getDirectoryTree($this->getMigrationPath(), "php");
        if (!empty($list)) {
            foreach ($list as $id => $data) {
                $row  = $data['file'];
                $name = $data['name'];

                # check in db
                $is_new = (!$this->checkInDb($id));
                $class_name = "Migration".$id;

                if ($is_new) {
                    $return_array_new[$id] = array($class_name,"" . $this->getMigrationPath() . $row . "",$name);
                } else {
                    $return_array_apply[$id] =  array($class_name,"" . $this->getMigrationPath() . $row . "",$name);
                }
            }

            $return = array();
            # filer
            $f_id = false;
            if ((isset($options['id']))) {
                if (is_string($options['id'])) {
                    $f_id = $options['id'];
                } else {
                    $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
                    $f_id  = $dialog->ask('Type migration id:', $f_id);
                }
            } else if (isset($args[0])){
                if (is_string($args[0])) {
                    $f_id  = $args[0];
                }
            }

            if ($f_id){
                if (isset ($return_array_apply[$f_id])) {
                    $return_array_apply = array($f_id => $return_array_apply[$f_id]);
                } else {
                    if (isset ($return_array_apply[$f_id])) {
                        throw new Exception("Migration ".$f_id . " - is already applied");
                    } else {
                        throw new Exception("Migration ".$f_id . " - is not found");
                    }
                }
            } else {
                $dialog = new \ConsoleKit\Widgets\Dialog($this->console);
                $type  = $dialog->ask('Are you sure you want to remove all applied migration (yes/no)?:', 'yes');

                if (strtolower($type) == "no" || strtolower($type) == "n"){
                    return true;
                }
            }

            if (empty($return_array_apply)){
                $this->info("Applied migrations list is empty.");
                return false;
            }

            $time_start = microtime(true);
            $this->info(" <- Start revert migration:");
            $this->writeln('');
            foreach ( $return_array_apply as $id => $mig) {
                include_once "" . $mig[1] . "";
                if ((method_exists($mig[0], "down"))) {
                    if ($do = $mig[0]::down()) {
                        if ($do === true) {
                            $obSelect = Bim\Db\Entity\MigrationsTable::getList(array("filter" => array("id" => $id)));
                            if ($obSelect->fetch()) {
                                $ob = Bim\Db\Entity\MigrationsTable::delete($id);
                                if ($ob->isSuccess()) {
                                    $this->writeln($this->color("     - revert   : " . $mig[2], Colors::GREEN));
                                }
                            }
                        }
                    } else {
                        $this->writeln($this->color("     - error : " . $mig[2], Colors::RED)." ".$this->color("(Method Up return false)",Colors::YELLOW));
                    }
                }
            }

            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $this->writeln('');
            $this->info(" <- ".round($time, 2)."s");
        }
    }
}