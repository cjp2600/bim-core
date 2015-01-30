<?php

use ConsoleKit\Widgets\ProgressBar,ConsoleKit\Colors;

class UpdateCommand extends BaseCommand
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
            # if type desctiption
            } else if (isset($options['d'])){
                if (is_string($options['d'])) {
                    $f_id = $this->getIdByDescription($list,$options['d']);
                }
            }

            if ($f_id){
                if (is_array($f_id)){
                        $new_array = array();
                    foreach ($f_id as $rid){
                        if (isset ($return_array_new[$rid])) {
                            $new_array[$rid] = $return_array_new[$rid];
                        }
                    }
                    if (!empty($new_array)) {
                        $return_array_new = $new_array;
                    }
                }else if (isset ($return_array_new[$f_id])) {
                    $return_array_new = array($f_id => $return_array_new[$f_id]);
                } else {
                    if (isset ($return_array_apply[$f_id])) {
                        throw new Exception("Migration ".$f_id . " - is already applied");
                    } else {
                        throw new Exception("Migration ".$f_id . " - is not found in new migrations list");
                    }
                }
            }

            if (empty($return_array_new)){
                $this->info("New migrations list is empty.");
                return false;
            }

            $time_start = microtime(true);
            $this->info(" -> Start applying migration:");
            $this->writeln('');
            foreach ( $return_array_new as $id => $mig) {
                include_once "" . $mig[1] . "";
                if ((method_exists($mig[0], "up"))) {
                    if ($do = $mig[0]::up()) {
                        if ($do === true) {
                            $obSelect = Bim\Db\Entity\MigrationsTable::getList(array("filter" => array("id" => $id)));
                            if (!$obSelect->fetch()) {
                                $content = base64_encode(file_get_contents($mig[1]));
                                $ob = Bim\Db\Entity\MigrationsTable::add(array(
                                    "id" => $id
                                ));
                                if ($ob->isSuccess()) {
                                    $this->writeln($this->color("     - applied   : " . $mig[2], Colors::GREEN));
                                }
                            }
                        }
                    }
                }
            }
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            $this->writeln('');
            $this->info(" -> ".round($time, 2)."s");
        }
    }


}