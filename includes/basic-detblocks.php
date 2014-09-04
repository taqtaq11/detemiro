<?php
    //Добавление блока
    function add_detblock($par) {
        global $DETDB;

        $custom = array(
            'code' => '',
            'name' => ''
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
                        'PRIMARY KEY'    => "(ID)",
                        "INDEX entry"    => "(code)",
                        "INDEX category" => "(category_ID)"
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
        if(is_numeric($ID) && $DETDB->delete('detblocks_types', $ID) && $DETDB->delete_table("detblocks_content_$ID")) {
            $DETDB->delete('detblocks_fields', null, "WHERE block_ID='$ID'");
            $DETDB->delete('detblocks_categories', null, "WHERE block_ID='$ID'");
            return true;
        }

        return false;
    }

    function update_detblock($ID) {
        global $DETDB;

        if($param = set_ID($ID)) {
            
        }

        return false;
    }
?>