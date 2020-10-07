<?php

namespace Modules\Recover\Controllers;

use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;

class Recover extends \CodeIgniter\Controller {

    var $backup_dir = "./backup_db";

    public function index() {
        $this->recovery_tasks();
        $data['backup_files'] = $this->checkdir();
        $data['active_menu'] = 1;
        $data['content_view'] = "\Modules\Recover\Views\\recovery_v";
        $data['title'] = "Dashboard | System Recovery";
        $db = \Config\Database::connect();
        $data['sys_hostname'] = explode(':', $db->hostname)[0];
        $data['sys_hostport'] = (isset($db->port)) ? $db->port : 3306;
        $data['sys_username'] = $db->username;
        $data['sys_password'] = $db->password;


        $this->template($data);
    }

    function post($variable) {
        return $_POST[$variable];
    }

    public function check_server() {
        $session = session();
        $host_name = ($this->post("inputHost") != null) ? $this->post("inputHost") : 'localhost';
        $host_user = $this->post("inputUser");
        $host_password = $this->post("inputPassword");
        $host_port = $this->post("inputPort");

        $link = @mysqli_connect($host_name . ':' . $host_port, $host_user, $host_password);
        if ($link == false) {
            $status = 0;
        } else {
            $status = 1;
            $session->set("db_host", $host_name);
            $session->set("db_user", $host_user);
            $session->set("db_pass", $host_password);
            $session->set("db_port", $host_port);
        }
        echo $status;
    }

    public function check_database() {
        $session = session();
        $host_name = $session->db_host;
        $host_user = $session->db_user;
        $host_password = $session->db_pass;
        $database_port = $session->db_port;
        $database_name = $this->post('inputDb');


        $link = mysqli_connect($host_name . ':' . $database_port, $host_user, $host_password);

        /* check connection */
        if (mysqli_connect_errno()) {
            $status = "\nCould not connect to the database!";
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }



        /* change db to world db */
        $db_selected = mysqli_select_db($link, $database_name);
        if ($db_selected) {
            $status = "\nConnection Success!\nDatabase Exists!";
            $session->set("db_name", $database_name);
        } else {
            $status = "\nConnection Success!\nDatabase does not exist!";
            $sql = "CREATE DATABASE IF NOT EXISTS $database_name";
            if (mysqli_query($link, $sql)) {
                $status = "\nDatabase created successfully!";
                $session->set("db_name", $database_name);
            } else {
                $status = $sql . " \nCould not create database! " . mysqli_error($link);
            }
        }

        echo $status;

        mysqli_close($link);
    }

    public function start_database() {
        error_reporting(E_ALL | E_STRICT);
        $this->load->library('UploadHandler');
        $upload_handler = new UploadHandler();
    }

    public function checkdir() {
        $dir = $this->backup_dir;
        $backup_files = array();
        $backup_headings = array('Filename', 'Options');
        $options = '<button class="btn btn-primary btn-sm recover" >Recover</button>';

        if (is_dir($dir)) {
            $files = scandir($dir, 1);
            foreach ($files as $object) {
                if ($object != "." && $object != ".." && $object != ".gitkeep" && $object != "downloads") {
                    $backup_files[] = $object;
                }
            }
        } else {
            mkdir($dir);
        }
        $table = new Tables();
        //$this->load->module('tables');

        return $table->load_table($backup_headings, $backup_files, $options);
    }

    public function showdir() {
        $dir = $this->backup_dir;
        $backup_files = array();
        $backup_headings = array('Filename', 'Options');
        $options = '<button class="btn btn-primary btn-sm recover" >Recover</button>';

        if (is_dir($dir)) {
            $files = scandir($dir, 1);
            foreach ($files as $object) {
                if ($object != "." && $object != ".." && $object != ".gitkeep" && $object != "downloads") {
                    $backup_files[] = $object;
                }
            }
        } else {
            mkdir($dir);
        }
        $this->load->module('tables');
        echo $this->tables->load_table($backup_headings, $backup_files, $options);
    }

    public function start_recovery() {
        ini_set('memory_limit', '-1');
        $file_name = $_POST['file_name'];
        $file_path = FCPATH . 'backup_db/' . $file_name;
        $unzip = $this->uncompress_zip($file_path);
        $file_path = str_replace(".zip", "", $file_path);
        $file_path = (strpos($file_path, '.sql') !== false) ? $file_path : $file_path . '.sql';
        $file_path = '"' . realpath($file_path) . '"';

        $session = session();


        $hostname = $session->db_host;
        $port = $session->db_port;
        $username = $session->db_user;
        $password = $session->db_pass;
        $current_db = 'testadt';
        $recovery_status = false;


        $link = @mysqli_connect($hostname, $username, $password);
        $sql = "SHOW TABLES FROM $current_db";
        $result = @mysqli_query($sql, $link);
        $count = mysqli_num_rows($result);
        if ($count == 0) {
            $mysql_home = realpath($_SERVER['MYSQL_HOME']) . "\mysql";
            $mysql_bin = str_replace("\\", "\\\\", $mysql_home);
            //$mysql_con = $mysql_bin . ' -u ' . $username . ' -p' . $password . ' -P' . $port . ' -h ' . $hostname . ' ' . $current_db . ' < ' . $file_path;
            $mysql_con = $mysql_bin . ' -u ' . $username . ' -P' . $port . ' -h ' . $hostname . ' ' . $current_db . ' < ' . $file_path;
            exec($mysql_con);
            $recovery_status = true;

            $db_config_file = str_replace('\tools', '', FCPATH) . 'application/config/db_conf.php';

            if (file_exists($db_config_file)) {
                $file = fopen($db_config_file, "w");
                fwrite($file, "" . "\r\n");
                fwrite($file, "<?php " . "\r\n");
                fwrite($file, "\$db['default']['hostname'] = '$hostname';" . "\r\n");
                fwrite($file, "\$db['default']['username'] = '$username';" . "\r\n");
                fwrite($file, "\$db['default']['password'] = '$password';" . "\r\n");
                fwrite($file, "\$db['default']['database'] = '$current_db';" . "\r\n");
                fwrite($file, "\$db['default']['port'] = $port;" . "\r\n");
                fclose($file);
            }
        }

        // $recovery_status = $this->delete_file($file_path);
        echo $recovery_status;
    }

    public function recovery_tasks() {
        // find sql files on root folder, zip & save zipped files to backup_db
        $files = scandir(FCPATH . 'backup_db');
        foreach ($files as $key => $f) {
            if (!(strpos($f, '.sql'))) {
                continue;
            }
            if ((strpos($f, '.sql.zip'))) {
                continue;
            }
            $this->delete_file('backup_db/' . $f);
        }
    }

    public function delete_file($file_path) {
        if (unlink($file_path)) {
            return true;
        } else {
            return false;
        }
    }

    public function uncompress_zip($file_path = null) {
        $zip = new \Zip();
        $return_status = FALSE;
        $destination_path = realpath($file_path);
        $zip = new ZipArchive;
        if ($zip->open($destination_path) === TRUE) {
            $zip->extractTo($this->backup_dir);
            $zip->close();
            $this->deleteDirectory($this->backup_dir . '/xampp');
            $return_status = TRUE;
        }
        return $return_status;
    }

    function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    public function template($data) {
        $data['show_menu'] = 0;
        $data['show_sidemenu'] = 0;
        $template = new Template();
        $template->index($data);
    }

}
