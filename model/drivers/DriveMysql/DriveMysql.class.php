<?php

class DriveMysql
{

    public $base = 'default';
    private $configfile = 'server.config.json';

    function connect($base = 'global')
    {
        $config = json_decode(file_get_contents(__DIR__ . '/' . $this->configfile), true);
        $mysqli = new mysqli($config['host'], $config['username'], $config['password'], $base);
        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        } else {
            return $mysqli;
        }
    }

    function config()
    {
        return json_decode(file_get_contents(__DIR__ . '/driverconfig.json'), true);
    }

    function select($table, $where = null, $data = null, $limit = 100, $orderby = null, $orderby_type = null)
    {
        $mysqli = $mysqli = $this->connect($this->base);
        if (!is_array($table)) {
            if (!$data) {
                $sql = 'SELECT * FROM ' . $table;
            } else {
                $sql = 'SELECT ';
                if (is_array($data)) {
                    foreach ($data as $columns) {
                        $sql .= $columns . ", ";
                    }
                    $sql = substr($sql, 0, -2);
                    $sql .= ' FROM ' . $table;
                }
            }
        } else {
            die(print_r($table));
        }
        if ($where == null) {
            $sql .= '';
        } else {
            $sql .= ' WHERE ' . $where;
        }
        if ($orderby) {
            $sql .= ' ORDER BY ' . $orderby . ' ' . $orderby_type;
        }
        if ($limit != 'all') {
            $sql .= " LIMIT " . $limit;
        }
        $result = [];

        //toconsole('Query:' . PHP_EOL . json_encode($sql));
        $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('select', mysqli_error($mysqli), $sql, $table)));
        while ($row = $res->fetch_assoc()) {
            if (multidimensionalisjson($row)) {
                $row = array_merge($row, multidimensionalisjson($row, true));
            } else {
                $row = array_map('utf8_encode', $row);
            }
            $result[] = convert_value_to_number($row);
        }
        return $result;
    }

    function count($table, $where = null)
    {
        $mysqli = $mysqli = $this->connect($this->base);
        $sql = 'SELECT COUNT(*) FROM ' . $table;
        if ($where == null) {
            $sql .= '';
        } else {
            $sql .= ' WHERE ' . $where;
        }
        $result = [];
        $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('select', mysqli_error($mysqli), $sql, $table)));

        while ($row = $res->fetch_assoc()) {
            $result = array_map('utf8_encode', $row);
        }
        return end($result);
    }

    function select_distinct($table, $col, $where = null)
    {

        $mysqli = $mysqli = $this->connect($this->base);
        $sql = 'SELECT * FROM ' . $table . '';
        if ($where == null) {
            $sql .= '';
        } else {
            $sql .= ' WHERE ' . $where;
        }
        $sql .= ' GROUP BY ' . $col . '';
        $result = [];
        $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('select', mysqli_error($mysqli), $sql, $table)));

        while ($row = $res->fetch_assoc()) {
            $result[] = array_map('utf8_encode', $row);
        }
        return $result;
    }

    function select_inner($table1, $table2, $on, $where = null)
    {

        $mysqli = $mysqli = $this->connect($this->base);
        $sql = 'SELECT * FROM ' . $table1 . ' INNER JOIN ' . $table2 . ' ON ' . $on;
        if ($where == null) {
            $sql .= '';
        } else {
            $sql .= ' WHERE ' . $where;
        }
        $result = [];
        $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('select', mysqli_error($mysqli), $sql, $table)));

        while ($row = $res->fetch_assoc()) {
            $result[] = array_map('utf8_encode', $row);
        }
        return $result;
    }

    function select_columns($table)
    {
        $mysqli = $mysqli = $this->connect($this->base);
        $sql = 'DESCRIBE ' . $table;
        $res = mysqli_query($mysqli, $sql);
        if ($res) {
            if (mysqli_num_rows($res) > 0) {
                while ($row = $res->fetch_assoc()) {
                    $col[] = $row['Field'];
                }
                return $col;
            }
        }
        return 'erro';
    }

    function insert($table, $data)
    {
        $mysqli = $mysqli = $this->connect($this->base);
        if (isset($data)) {
            if (!$this->isAssoc($data)) {
                $sql = 'INSERT  INTO ' . $table . ' VALUES (';
                for ($i = 0; $i < count($data); $i++) {
                    if (($i + 1) == count($data)) {
                        $sql .= " '" . str_replace("'", '´', utf8_decode($data[$i])) . "'";
                    } elseif ($i == 0) {
                        $sql .= "'" . str_replace("'", '´', utf8_decode($data[$i])) . "'" . ", ";
                    } else {

                        $sql .= " '" . str_replace("'", '´', utf8_decode($data[$i])) . "'" . ", ";
                    }
                }
                $sql .= ')';
            } else {
                $sql = 'INSERT INTO ' . $table . ' ';
                $cols = '';
                $valus = '';
                foreach ($data as $key => $value) {
                    if ($value != "") {
                        $cols .= $key . ", ";
                        $valus .= "'$value', ";
                    }
                }
                $cols = substr($cols, 0, -2);
                $valus = substr($valus, 0, -2);
                $sql = $sql . '(' . $cols . ') VALUES (' . $valus . ')';
            }
            $result = [];
            $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('insert', mysqli_error($mysqli), $sql, $table)));
            if ($res) {
                return ['status' => TRUE, 'RegistredID' => mysqli_insert_id($mysqli)];
            } else {
                return FALSE;
            }
        } else {
            return ['error' => 'Not Data'];
        }
    }

    function update($table, $data, $where)
    {
        $mysqli = $this->connect($this->base);
        $b = 0;
        $vars = array();
        $sql = "";
        if (!$this->isAssoc($data)) {
            $result = mysqli_query($mysqli, "SELECT * FROM $table LIMIT 1");
            for ($i = 0; $i < mysqli_num_fields($result); $i++) {
                $vars[] = mysqli_fetch_field_direct($result, $b)->name;
                $b++;
            }

            for ($i = 0; $i < count($data); $i++) {
                $sql .= $vars[$i] . "='" . str_replace("'", '´', utf8_decode($data[$i])) . "'";
            }
        } else {
            $result = $this->select($table, $where);
            if (isset($result[0])) {
                foreach ($result[0] as $key => $value) {
                    if (isset($data[$key])) {
                        if ($value != $data[$key]) {
                            $sql .= $key . "='" . $data[$key] . "', ";
                        }
                    }
                }
            }
        }
        if ($sql != '') {
            $sql = substr($sql, 0, -2);
            $query = "UPDATE $table SET $sql WHERE $where";
            //toconsole($query);
            $alteracao = mysqli_query($mysqli, $query);
            if ($alteracao) {
                return TRUE;
            } else {
                $erromysql = mysqli_error($mysqli);
                if ($erromysql) {
                    return $erromysql;
                } else {
                    return 'erro não encontrado';
                }
            }
        } else {
            return TRUE;
        }
    }

    function delete($table, $where)
    {
        $mysqli = $this->connect($this->base);
        $sql = 'DELETE FROM ' . $table . ' WHERE ' . $where;
        $delete = $this->select($table, $where);
        if ($delete) {
            $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('delete', mysqli_error($mysqli), $sql, $table, null, $where)));
            if ($res) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function check_table($table)
    {
        $mysqli = $this->connect($this->base);
        $sql = 'SHOW TABLES LIKE "' . $table . '";';
        $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('Error cheking table', mysqli_error($mysqli), $sql, $table)));
        if ($res) {
            return $res;
        } else {
            return FALSE;
        }
    }

    function show_table($database)
    {
        $mysqli = $this->connect($database);
        $sql = 'SHOW TABLES;';
        $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('Error cheking table', mysqli_error($mysqli), $sql)));
        while ($cRow = mysqli_fetch_array($res)) {
            $tableList[] = $cRow[0];
        }
        if ($res) {
            return $tableList;
        } else {
            return FALSE;
        }
    }



    function get_table_structure($database, $table)
    {
        $mysqli = $this->connect($database);
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'" . $table . "';";
        $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('count', mysqli_error($mysqli), $sql, $table)));
        while ($row = $res->fetch_assoc()) {
            $result[] = array_map('utf8_encode', $row);
        }
        return $result;
    }

    function show_table_columns($database, $table)
    {
        $mysqli = $this->connect($database);
        $sql = "SHOW COLUMNS FROM " . $table;
        $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('count', mysqli_error($mysqli), $sql, $table)));
        while ($cRow = mysqli_fetch_array($res)) {
            $columns[] = $cRow[0];
        }
        /*
        $infos = $this->get_table_structure($database, $table);
        foreach ($infos as $info) {
            $columns[] = $info['COLUMN_NAME'];
        }
        */
        return $columns;
    }

    function check_table_coluns($table, $column)
    {
        $mysqli = $this->connect($this->base);
        $sql = "SHOW COLUMNS FROM `$table` LIKE '$column'";
        $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('count', mysqli_error($mysqli), $sql, $table)));
        return (mysqli_num_rows($res)) ? TRUE : FALSE;
    }


    function create_table($table, $coluns)
    {
        if (!$this->check_table($table)) {
            $mysqli = $this->connect($this->base);
            $sql = 'CREATE TABLE `' . $table . '` ';
            $sql .= '(`id` int(11) NOT NULL AUTO_INCREMENT, ';
            foreach ($coluns as $colun) {
                $sql .= '`' . $colun . '` varchar(255) DEFAULT NULL, ';
            }
            $sql .= 'PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;';

            $res = mysqli_query($mysqli, $sql) or die(json_encode($this->error_report('Error creating table', mysqli_error($mysqli), $sql, $table)));
            if ($res) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function update_table($table, $coluns)
    {
        if ($this->check_table($table)) {
            $old = $this->select_columns($table);
            $mysqli = $this->connect($this->base);
            $sql = 'ALTER TABLE `' . $table . '` ';
            $n = 0;
            for ($i = 0; $i < count($coluns); $i++) {
                $nova = $coluns[$i];
                if (isset($old[$i + 1])) {
                    if ($old[$i + 1] != $nova) {
                        if ($i == 0) {
                            $ant = 'id';
                        } elseif ($old[$i + 1] == $nova) {
                            $ant = $old[$i];
                        } else {
                            $ant = $coluns[$i - 1];
                        }

                        $sql .= "CHANGE COLUMN `" . $old[$i + 1] . "` `" . $nova . "`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `" . $ant . "`";

                        if ($nova == end($coluns)) {
                            $sql .= "; ";
                        } else {
                            $sql .= ", ";
                        }
                    }
                } else {
                    $ant = $coluns[$i - 1];
                    $sql .= "ADD COLUMN `" . $nova . "`  varchar(255) NULL AFTER `" . $ant . "`";
                    if ($nova == end($coluns)) {
                        $sql .= "; ";
                    } else {
                        $sql .= ", ";
                    }
                }
            }
            $res = mysqli_query($mysqli, $query) or die(json_encode($this->error_report('Error creating table', mysqli_error($mysqli), $sql, $table)));
            if ($res) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function custom($table, $return = null)
    {
        $mysqli = $this->connect($this->base);
        $sql = $table;
        $res = mysqli_query($mysqli, $sql);
        if ($return == 'array') {
            while ($row = $res->fetch_assoc()) {
                $result[] = array_map('utf8_encode', $row);
            }
            return $result;
        } else {
            return $res;
        }
    }

    function solutions($mysql_error)
    {
        $error[] = array('erro' => "Column count doesn't match value count at row 1", 'solution' => 'check table structure');
        $error[] = array('erro' => "Not Found your Where request", 'solution' => 'where used not found in DataBase');

        for ($i = 0; $i < count($error); $i++) {
            if ($mysql_error == $error[$i]['erro']) {
                return $error[$i]['solution'];
            } else {
                return 'not found solution';
            }
        }
    }

    function error_report($origin, $mysql_error, $sql, $tables, $data = null, $where = null)
    {
        $mysqli = $this->connect($this->base);
        $error_report['erro'] = 'no_results';
        $error_report['syntax'] = $sql;
        $error_report['mysql_erro'] = $mysql_error;
        $error_report['solution'][] = $this->solutions(mysqli_error($mysqli));

        if ($origin == 'select') {

        } elseif ($origin == 'insert') {
            $error_report['column'] = $this->select_columns($tables);
            if (count($data) < count($error_report['column'])) {
                $error_report['solution'][] = 'Missing values on your request';
            } else if (count($data) > count($error_report['column'])) {
                $error_report['solution'][] = 'So much values on your request';
            }
        } elseif ($origin == 'update') {

        }
        $error_report['post'] = $_POST;
        return $error_report;
    }

    function fala($msg)
    {
        echo $msg;
    }

    function isAssoc(array $arr)
    {
        if (is_array($arr)) {
            if (array() === $arr) return false;
            return array_keys($arr) !== range(0, count($arr) - 1);
        } else {
            return false;
        }
    }

    function showdatabases($return = 'array')
    {
        $mysqli = $this->connect($this->base);
        $select = mysqli_query($mysqli, 'SHOW DATABASES');
        if ($return == 'array') {
            while ($row = $select->fetch_assoc()) {
                $result[] = array_map('utf8_encode', $row);
            }
            return $result;
        } else {
            return $select;
        }
    }
}
