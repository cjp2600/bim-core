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
                'Description',
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
                    $format = $date[0];
                }

                $class_name = $this->camelCase($id);
                include "".$this->getMigrationPath() . $row."";

                $table->addRow(array(
                    $i,
                    date("d.m.y G:h:i",$format),
                    $class_name,
                    $row,
                    $class_name::getDescription(),
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
