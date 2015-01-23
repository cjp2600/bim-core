<?php

class ListCommand extends BaseCommand {

    public function execute(array $args, array $options = array())
    {
        $list = $this->getDirectoryTree( $this->getMigrationPath(), "php" );

        echo "<pre>";
        print_r($list);
        echo "</pre>";
        
    }

}
