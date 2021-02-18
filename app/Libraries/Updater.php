<?php

/**
 * Updater Class
 *
 * This class is only for self-updating WebADT based on a library I found at PHPClasses:
 * @author		William Nguru
 * @link		http://bitbucket.org/Codeklerk
 * @license
 * @version     1.0.0
 */
class Updater
{

    private $ADT_file = 'ADT.zip';

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @access    Public
     * @param     string
     * @return    none
     */
    function __construct()
    {
        // log_message('debug', '');
    }

    // --------------------------------------------------------------------


    /*
      Function for checking if connection to the internet exists
     */
    function check_connection()
    {
        $connected = fopen("http://www.google.com:80/", "r");
        if ($connected) {
            return true;
        } else {
            return false;
        }
    }

    /*
      Function for reading latest ADT release details
     */

    function check_ADTrelease()
    {
        // if new release exists then download release file first then keep checking locally
        // if release already installed delete release file 

        $result = null;
        $host = 'http://adt-test.nascop.org/updateinfo.txt';
        try {
            $result = file_get_contents($host);
        } catch (\Exception $e) {
            $result = null;
        }

        return $result;
    }

    function download_ADTRelease()
    {
        $rs = $this->check_ADTrelease();
        $rs = (json_decode($rs));
        $returnable = false;

        if (file_put_contents(FCPATH . $this->ADT_file, fopen($rs->releaseURL, 'r'))) {
            $returnable = true;
        }

        return $returnable;
    }

    function check_ADTRelease_downloaded()
    {
        $rs = $this->check_ADTrelease();
        $rs = (json_decode($rs));
        $returnable = true;

        if (empty($rs)) {
            return $returnable;
        }
        if (@md5_file(FCPATH . $this->ADT_file) == $rs->releaseChecksum && filesize(FCPATH . $this->ADT_file) == $rs->releaseSize) {
            $returnable = true;
        }
        return $returnable;
    }

    function update_ADT()
    {
        $zip = new ZipArchive;
        $res = $zip->open($this->ADT_file);
        if ($res === TRUE) {
            // extract it to the path we determined above
            $zip->extractTo(ROOTPATH);
            $zip->close();
            echo "Success: $this->ADT_file extracted";
            unlink(FCPATH . $this->ADT_file);
        } else {
            echo "Error! I couldn't open the file";
        }
    }

    // function to inform remote site of new update activity
}

/* End of file Updater.php */
/* Location: ./system/libraries/Updater.php */
