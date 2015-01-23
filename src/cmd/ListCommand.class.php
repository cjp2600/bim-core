<?php

class ListCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        $list = $this->getDirectoryTree( $this->getMigrationPath(), "php" );

        if (!empty($list)) {

            $headers = array(
                '№',
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
                $table->addRow(array(
                    $i,
                    $this->$this->camelCase($id),
                    $row,
                    ""
                ));
                $table->addRow(array('','','',''));
            $i++;}
            $progress->stop();
            $table->display();

        } else {
            $this->writeln('');
            $this->writeln('Пусто',Colors::YELLOW);
            $this->writeln('');
        }

        
    }

}
