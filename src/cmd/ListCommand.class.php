<?php

use ConsoleKit\Colors;

/**
 * .
 * That command displays a list of migration.
 * ======================================================================================
 *
 * 1) Common list:
 *              Example: php bim ls
 *
 * 2) Displays a list of already Applied on migration:
 *              Example: php bim ls --a
 *
 * 3) Displays a list of new migrations:
 *              Example: php bim ls --n
 *
 * 4) Displays a list of migrations for a certain period of time:
 *              Example:  php bim ls --from="29.01.2015 00:01" --to="29.01.2015 23:55"
 *
 * 5) Display list of migrations with tag:
 *              Example: php bim ls --tag=iws-123
 *
 * .
 */
class ListCommand extends BaseCommand
{
    public function execute(array $args, array $options = array())
    {
        $list = $this->getDirectoryTree($this->getMigrationPath(), "php");

        # get filename
        $file = (isset($options['f'])) ? true : false;
        $filter_apply = (isset($options['a'])) ? $options['a'] : false;
        $filter_new = (isset($options['n'])) ? $options['n'] : false;
        $filter_from = (isset($options['from'])) ? $options['from'] : false;
        $filter_to = (isset($options['to'])) ? $options['to'] : false;
        $filter_from = ($filter_from) ? strtotime($filter_from) : false;
        $filter_to = ($filter_to) ? strtotime($filter_to) : false;

        #check tag list
        $filer_tag = (isset($options['tag'])) ? $options['tag'] : false;

        if (!empty($list)) {

            $headers = array('№', 'id', 'Author', 'Date');
            if ($file) {
                $headers[] = 'File';
            }

            $headers[] = 'Description';
            $headers[] = 'Status';

            $table = new \cli\Table();
            $table->setHeaders($headers);

            $count = 0;
            $applied = 0;
            $new = 0;
            $i = 1;
            $return_array_new = array();
            $return_array_apply = array();

            #filter
            $is_filter = false;
            $this->prepareFilter($list,$filter_from,$filter_to,$filer_tag,$options,$is_filter);

            foreach ($list as $id => $data) {
                $count++;

                $row = $data['file'];
                $name = $data['name'];

                # check in db
                $is_new = (!$this->checkInDb($id));
                $class_name = "Migration" . $id;
                include_once "" . $this->getMigrationPath() . $row . "";

                $color = ConsoleKit\Colors::GREEN;
                $status = ConsoleKit\Colors::colorize('apply', Colors::GREEN);

                # check in db
                if ($is_new) {
                    $new++;
                    $color = ConsoleKit\Colors::RED;
                    $status = ConsoleKit\Colors::colorize('new', Colors::RED);
                } else {
                    $applied++;
                }

                $rowArray = array(
                    ConsoleKit\Colors::colorize($i, $color),
                    ConsoleKit\Colors::colorize($id, $color),
                    $data['author'],
                    date("d.m.y G:h", $data['date'])
                );
                if ($file) {
                    $rowArray[] = $row;
                }
                $rowArray[] = $data['description'];
                $rowArray[] = $status;

                if ($is_new) {
                    $return_array_new[] = $rowArray;
                } else {
                    $return_array_apply[] = $rowArray;
                }

                $i++;
            }

            if ($filter_new) {
                $table->setRows($return_array_new);
            } else if ($filter_apply) {
                $table->setRows($return_array_apply);
            } else {
                $table->setRows(array_merge($return_array_apply, $return_array_new));
            }

            $displayArray=$table->getDisplayLines();
            if (!empty($displayArray)) {
                $table->display();
            }

            if (!$is_filter) {
                # count info
                $return[] = Colors::colorize('New:', Colors::RED) . " " . $new;
                $return[] = Colors::colorize('Applied:', Colors::GREEN) . " " . $applied;
                $return[] = "Count: " . $count;
            }

            # display
            $this->padding(implode(PHP_EOL, $return));

        } else {
            $this->info('Empty list');
        }
    }

    /**
     * prepareFilter
     * @param $list
     * @param $filter_from
     * @param $filter_to
     * @param $filer_tag
     * @param $options
     * @param $is_filter
     */
    public function prepareFilter(&$list,$filter_from,$filter_to,$filer_tag,$options,&$is_filter)
    {
        if ($filter_from || $filter_to) {
            $this->padding("Filter to the list:" . $this->color(PHP_EOL . "from: " . $options['from'] . PHP_EOL . "to: " . $options['to'], Colors::YELLOW));
            $newArrayList = array();
            foreach ($list as $id => $data) {
                if ($filter_from && $filter_to) {
                    if ($id >= $filter_from && $id <= $filter_to) {
                        $newArrayList[$id] = $data;
                    }
                } else if ($filter_from && !$filter_to) {
                    if ($id >= $filter_from) {
                        $newArrayList[$id] = $data;
                    }
                } else if (!$filter_from && $filter_to) {
                    if ($id <= $filter_to) {
                        $newArrayList[$id] = $data;
                    }
                }
            }
            if (!empty($newArrayList)) {
                $is_filter = true;
                $list = $newArrayList;
            } else {
                $list = array();
            }
        }
        # check to tag list
        if ($filer_tag) {
            $newArrayList = array();
            foreach ($list as $id => $data) {
                if (!empty($data['tags'])) {
                    if (in_array(strtolower($filer_tag), $data['tags'])) {
                        $newArrayList[$id] = $data;
                    }
                }
            }
            if (!empty($newArrayList)) {
                $is_filter = true;
                $list = $newArrayList;
            } else {
                $list = array();
            }
        }
    }


}
