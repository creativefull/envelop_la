<?php
    class UserModel {
        public function isExists($params) {
            global $wpdb;
            $query = "SELECT COUNT(id_env_market) AS total FROM tbl_env_market WHERE userid='" . $params['userid'] . "' AND fname='" . $params['fname'] . "' AND lname='" . $params['lname'] . "' AND address1='" . $params['address1'] . "'";
            $theData = $wpdb->get_results($query);
            if ($theData[0]->total > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
?>