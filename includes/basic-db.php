<?php
    //А здесь будет класс для операций с БД, чтобы не писать всякую фигню
    class db_actions {
        //На всякий случай инфо-поля
        public $host = DB_HOST;
        public $name = DB_NAME;
        public $user = DB_USER;
        public $prefix = DB_PREFIX;
        //Указатель
        protected $connect;
        function __construct() {
            global $BLOCK;
            if(!($this->connect = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME))) {
                $BLOCK = true;
                die();
            }
        }
        function __destruct() {
            $this->connect->close();
        }

        //Действия

        //Кастомное действие с БД по SQL запросу
        public function action($query) {
            return $this->connect->query($query);
        }

        //Профессиональная выборка
        public function select($par, $cols='*', $one=false, $cond='') {
            $work = true;

            //Формирование запроса
            $custom = array(
                'table'      => '',
                'cols'       => '*',
                'cond'       => '',
                'order_by'   => 'ID',
                'order_type' => 'ASC',
                'limit'      => 100,
                'offset'     => 0,
                'one'        => false
            );

            if(!is_assoc_array($par)) {
                if($cols) {
                    $custom['table'] = $par;
                    $custom['cols']  = $cols;
                    $custom['cond']  = $cond;
                    $custom['one']   = $one;
                }
                else {
                    $work = false;
                }
            }
            else {
                $custom = set_merge($custom, $par);
            }

            if(!is_numeric($custom['offset']) || !is_numeric($custom['limit']) || !in_array($custom['order_type'], array('ASC', 'DESC'))) {
                $work = false;
            }

            if($work) {
                $cond = ($res = $this->custom_where($custom['cond'])) ? ' ' . $res : '';

                $cond .= " ORDER BY {$custom['order_by']} {$custom['order_type']}";
                $cond .= " LIMIT {$custom['offset']},{$custom['limit']}";
                
                if(is_object($custom['cols'])) $custom['cols'] = (array) $custom['cols'];
                if(is_array($custom['cols'])) $custom['cols'] = implode(',', $custom['cols']);

                $query = "SELECT {$custom['cols']} FROM " . $this->prefix . "{$custom['table']}$cond";

                //Формирование данных
                if($res = $this->action($query)) {
                    if(mysqli_num_rows($res) > 0) {
                        $arr = array();
                        while($row = $res->fetch_object()) {
                            $arr[] = $row;
                        }
                        mysqli_free_result($res);
                        if(!$custom['one'] && count($arr) > 0) {
                            return $arr;
                        }
                        elseif($custom['one'] && count($arr) == 1) {
                            return $arr[0];
                        }
                    }
                }
            }

            return null;
        }

        //Удаление по ID/коду, или условию (тогда второй параметр не учитывается)
        public function delete($table, $ID, $cond='') {
            if($cond == '') {
                $param = set_ID($ID);
                if($param) {
                    return ($this->action("DELETE FROM " . $this->prefix . "$table WHERE $param='$ID'"));
                }
            }
            else {
                return ($this->action("DELETE FROM " . $this->prefix . "$table $cond"));
            }
            return false;
        }

        //Вставка(таблица, строка столбцов, массив значений)
        public function insert($table, $par1, $par2 = null) { //table, cols, values OR table, assoc
            $query = '';

            if(is_assoc_array($par1)) {
                $query = string_values($par1);
            }
            elseif($par2) {
                $query = string_values($par1, $par2);
            }

            if($query) {
                $query = "INSERT INTO " . $this->prefix . "$table SET $query";
                return ($this->action($query));
            }
            else return false;
        }

        //Обновление
        public function update($table, $par1, $par2, $cond = null) { //table, cols, values, cond OR table, assoc, cond
            $mode = (is_assoc_array($par1) && $cond == null) ? true : false;

            if($mode) {
                $values = string_values($par1);
                $cond = $par2;
            }
            else {
                $values = string_values($par1, $par2);
            }

            $cond = $this->custom_where($cond);

            if($cond && $values) {
                $query = "UPDATE " . $this->prefix . "$table SET $values $cond";

                return ($this->action($query));
            }
            else return false;
        }

        //Кол-во строк
        public function count($table, $cond='') {
            if($cond) $cond = ' ' . $cond;
            
            if($res = $this->action("SELECT COUNT(*) FROM " . $this->prefix . "$table$cond")) {
                $res = $res->fetch_row();
                return $res[0];
            }
            else {
                return null;
            }
        }

        //Создание таблицы
        public function create_table($table, $params) {
            if($this->count($table) != null) {
                return false;
            }

            $query = "CREATE TABLE " . $this->prefix . "$table\n(\n";

            $query .= string_values($params, array(
                        'separ' => ",\n",
                        'body'  => '',
                        'middle' => ' '
                      ));

            $query .= "\n)\nENGINE = MYISAM";

            return ($this->action($query));
        }

        //Удаление таблицы
        public function delete_table($table) {
            return ($this->action("DROP TABLE " . $this->prefix . "$table"));
        }

        //Существует элемент
        public function isset_cell($table, $ID) {
            if($param = set_ID($ID)) {
                $param = $this->select($table, 'ID', false, "WHERE $param='$ID'");
                if($param && count($param)) {
                    return true;
                }
            }
            return false;
        }

        //Действия со столбцами по добавлению
        public function add_alter($table, $par) {
            return ($this->action("ALTER TABLE " . $this->prefix . "$table ADD $par"));
        }

        //Действия со столбцами по удалению
        public function delete_alter($table, $par) {
            return ($this->action("ALTER TABLE " . $this->prefix . "$table DROP $par"));
        }
        
        //Добавить столбец
        public function add_column($table, $col) {
            return ($this->action("ALTER TABLE " . $this->prefix . "$table ADD COLUMN $col"));
        }

        //Удалить столбец
        public function delete_column($table, $col) {
            return ($this->action("ALTER TABLE " . $this->prefix . "$table DROP COLUMN $col"));
        }

        //Экранирование символов
        public function escape_string($str) {
            return $this->connect->real_escape_string($str);
        }

        //Умное получение данных в SQL
        private function custom_where($param) {
            $cond = '';

            if(is_array($param)) {
                $i = 0;
                if(is_assoc_array($param)) {
                    $param = array($param);
                }

                $custom = array(
                    'log'      => 'AND', //AND, OR
                    'relation' => '=',   //=,>=,<=,>,<,!=, IS NOT
                    'param'    => '',    //column
                    'value'    => ''     //value of column or 'NULL'
                );

                foreach($param as $sub_cond) {
                    if(isset($sub_cond['param'])) {
                        $temp = set_merge($custom, $sub_cond);

                        $temp['value'] = $this->escape_string($temp['value']);

                        if($i == 0) {
                            $cond = ' WHERE ';
                        }
                        if($i > 0 && $temp['log']) {
                            $cond .= " {$temp['log']} ";
                        }
                        if($i == 0 || $temp['log']) {
                            $cond .= "{$temp['param']} {$temp['relation']} '{$temp['value']}'";
                        }

                        $i++;
                    }
                }
            }
            else {
                if($param) $cond = (preg_match('/^(WHERE)/i', $param)) ? $param : "WHERE $param";
            }

            return $cond;
        }
    }
    $DETDB = new db_actions();
?>