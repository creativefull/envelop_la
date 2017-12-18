<?php

class ReadCSV {
    private $csv_file;
    private $err_msg;

    public function __construct() {

    }

    public function initFile($file) {
        if ($file['type'] == 'text/csv') {
            $this->csv_file = $file;
        } else {
            $this->err_msg = 'File Not Valid';
        }
    }

    public function read() {
        if ($this->err_msg) {
            return $this->err_msg;
        }

        if ($this->csv_file) {
            $csvArray = array();

            if (($handle = fopen($this->csv_file['tmp_name'], "r")) !== FALSE) {
                set_time_limit(0);

                $row = 0;
                while(($data = fgetcsv($handle, 10000, ',')) !== FALSE) {
                    if ($row > 0) {
                        $csvArray[$row]['fname'] = $data[0];
                        $csvArray[$row]['lname'] = $data[1];
                        $csvArray[$row]['company'] = $data[2];
                        $csvArray[$row]['address1'] = $data[3];
                        $csvArray[$row]['address2'] = $data[4];
                        $csvArray[$row]['state'] = $data[5];
                        $csvArray[$row]['city'] = $data[6];
                        $csvArray[$row]['zipcode'] = $data[7];
                    }
                    $row++;
                }
                fclose($handle);
                return $csvArray;
            } else {
                return "File can't read";
            }
        } else {
            return "File not init";
        }
    }
}