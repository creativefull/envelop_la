<?php
    function convertWhere($item) {
        return("'$item'");
    }
    class MoveUser {
        public function bystep($users, $step) {
            global $wpdb;
            $arrayIn = array_map("convertWhere", $users);
            $where = "(" . join(',', $arrayIn) . ")";
            $query = "UPDATE tbl_env_market SET seq='" . $step . "' WHERE id_env_market IN $where";
            // echo $query;
            $wpdb->query($query);
            return array('success' => 'ok');
        }

        public function all($prevStep, $nextStep) {
            global $wpdb;
            $query = "UPDATE tbl_env_market SET seq='" . $nextStep . "' WHERE seq='" . $prevStep . "'";
            $query1 = $wpdb->query($query) or die ("Something wrong");
            return array('success' => true);            
        }
    }
?>