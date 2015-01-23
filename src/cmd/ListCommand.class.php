<?php

use ConsoleKit\Widgets\ProgressBar;

class ListCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        $list = $this->getDirectoryTree( $this->getMigrationPath(), "php" );

        if (!empty($list)) {

            $headers = array(
                '№',
                'Date',
                'Class',
                'File',
                'Status'
            );

            $table = new \cli\Table();
            $table->setHeaders($headers);

            $i = 1;
            $progress = new ProgressBar($this->console, count($list));
            foreach ($list as $id => $row) {
                $progress->incr();

                $date = explode("_",$id);
                if (isset($date[0])){
                    $format = strtotime($date[0]);
                }

                $table->addRow(array(
                    $i,
                    date("d.m.y G:h:i",$format),
                    $this->camelCase($id),
                    $row,
                    ""
                ));

                //$table->addRow(array('','','',''));

            $i++;}
            $progress->stop();
            $table->display();

        } else {
            $this->writeln('');
            $this->info('Пусто');
            $this->writeln('');
        }

        
    }

}
