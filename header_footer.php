<?php
    function getHeader($userid) {
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM tbl_content_env WHERE type='header' AND userid='" . $userid . "' AND step='0'");
        if (is_wp_error($result)) {
            echo $result->get_error_message();
        } else {
            if ($result && count($result) > 0) {
                return $result[0]->content;
            } else {
                return '';
            }
        }
    }
    class HeaderFooter {
        public function initHeader() {
            add_filter('get_header_pdf', 'getHeader', 10, 3);
        }
    }
?>