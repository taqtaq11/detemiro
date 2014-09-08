<?php
    //Добавление блока
    function add_detblock($par) {
        global $DETDB;

        $custom = array(
            'code'        => '',
            'name'        => '',
            'description' => ''
        );
        $custom = set_merge($custom, $par);

        $custom['code'] = canone_code($custom['code']);

        if(validate_code($custom['code'])) {
            if($DETDB->insert('detblocks_types', $custom)) {
                $ID = $DETDB->select('detblocks_types', 'ID', true, "WHERE code='{$custom['code']}'");
                if(isset($ID->ID)) {
                    $ID = $ID->ID;
                    if($DETDB->create_table("detblocks_content_$ID", array(
                        'ID'             => 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT',
                        'code'           => 'VARCHAR(60) NOT NULL',
                        'block_ID'       => 'BIGINT UNSIGNED NOT NULL',
                        'category_ID'    => 'BIGINT UNSIGNED NULL',
                        'PRIMARY KEY'    => '(ID)',
                        "INDEX entry"    => '(code)',
                        "INDEX category" => '(category_ID)'
                    ))) {
                        return $ID;
                    }
                }
            }
        }

        return false;
    }

    function delete_detblock($ID) {
        global $DETDB;
        if(is_numeric($ID)) {
            if($DETDB->delete('detblocks_types', $ID)) {
                if(!$DETDB->delete_table("detblocks_content_$ID")) {
                    push_output_message(array(
                        'text'  => "Ошибка удаления таблицы {$DETDB->prefix}_detblocks_content_$ID",
                        'title' => 'Ошибка!',
                        'class' => 'alert alert-danger',
                        'type'  => 'error'
                    ));
                }
                if(!$DETDB->delete('detblocks_categories', null, "WHERE block_ID=$ID")) {
                    push_output_message(array(
                        'text'  => 'Ошибка удаления категорий, связанных с DET-блоком (возможно их не было)',
                        'title' => 'Ошибка!',
                        'class' => 'alert alert-warning',
                        'type'  => 'error'
                    ));
                }
                if(!$DETDB->delete('detblocks_fields', null, "WHERE block_ID=$ID")) {
                    push_output_message(array(
                        'text'  => 'Ошибка удаления полей, связанных с DET-блоком (возможно их не было)',
                        'title' => 'Ошибка!',
                        'class' => 'alert alert-warning',
                        'type'  => 'error'
                    ));
                }

                return true;
            }
            else {
                push_output_message(array(
                    'text'  => 'Ошибка удаления типа DET-блока',
                    'title' => 'Ошибка!',
                    'class' => 'alert alert-danger',
                    'type'  => 'error'
                ));
            }
        }

        return false;
    }

    function get_detblock_type($ID) {
        global $DETDB;

        if($par = set_ID($ID)) {
            return $DETDB->select('detblocks_types', '*', true, "WHERE $par='$ID'");
        }
        return null;
    }

    function update_detblock($ID, $par) {
        global $DETDB;

        if(is_merged($par) && is_numeric($ID) && $obj = $DETDB->isset_cell('detblocks_types', $ID)) {
            $par = (array) $par;
            $custom = array();

            if(isset($par['code']) && $par['code']) {
                $par['code'] = canone_code($par['code']);
                if(!validate_code($par['code'])) {
                    return false;
                }
                else {
                    $custom['code'] = $par['code'];
                }
            }
            if(isset($par['name'])) {
                $custom['name'] = $par['name'];
            }
            if(isset($par['description'])) {
                $custom['description'] = $par['description'];
            }

            return $DETDB->update('detblocks_types', $custom, "WHERE ID=$ID");
        }

        return false;
    }//Блок со справкой по движку Detemiro.
?>