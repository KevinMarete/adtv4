<?php

namespace Modules\Tables\Controllers;

class Tables extends \CodeIgniter\Controller {

    public function load_table($columns = array(), $table_data = array(), $options = '', $set_options = 1) {
        $table_data_builder = new \CodeIgniter\View\Table();
        array_unshift($columns, "#");
        $tmpl = array('table_open' => '<table id="dyn_table" class="table table-hover table-bordered table-condensed dataTables">');
        $table_data_builder->setTemplate($tmpl);
        $table_data_builder->setHeading($columns);
        $counter = 1;

        foreach ($table_data as $table) {
            if (is_array($table)) {
                array_unshift($table, $counter);
                if ($set_options == 1) {
                    $table['options'] = $options;
                }
                $table_data_builder->addRow($table);
            } else {
                $new_table = array();
                array_unshift($new_table, $counter);
                $new_table[] = $table;
                if ($set_options == 1) {
                    $new_table[] = $options;
                }
                $table_data_builder->addRow($new_table);
            }
            $counter++;
        }
        return $table_data_builder->generate();
    }

}
