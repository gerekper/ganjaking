<?php
namespace WeDevs\PM_Pro\Core\Database;

use WeDevs\PM_Pro\Core\Database\Migration_Model;
use Create_PM_Pro_Migrations_Table;

class Migrater {
    public function create_migrations_table() {
        $migration = new Create_PM_Pro_Migrations_Table();

        $this->migrate( $migration );
    }

    public function build_schema() {
        $contents = pm_pro_load_schema();

        foreach ( $contents as $class ) {
            $this->migrate( new $class );
        }
    }

    protected function get_classes( $content ) {
        $classes = [];
        $tokens  = token_get_all( $content );
        $count   = count( $tokens );

        for ( $i = 2; $i < $count; $i++ ) {
            if (   $tokens[$i - 2][0] == T_CLASS
                && $tokens[$i - 1][0] == T_WHITESPACE
                && $tokens[$i][0] == T_STRING ) {

                $class_name = $tokens[$i][1];
                $classes[]  = $class_name;
            }
        }

        return $classes;
    }

    protected function migrate( $migration ) {
        $class_name = get_class( $migration );
        
        if ( $class_name === 'Create_PM_Pro_Migrations_Table' ) {
            $migration->run();
        }

        if ( !Migration_Model::where('migration', $class_name )->first() ) {
            $migration->run();

            Migration_Model::create([
                'migration' => $class_name
            ]);
        }
    }
}