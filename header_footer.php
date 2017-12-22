<?php
    function getHeader($userid, $type='header') {
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM tbl_content_env WHERE type='" . $type . "' AND userid='" . $userid . "' AND step='0'");
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
    function getLogo($userid) {
        $logoMeta = get_user_meta($userid, 'companyLogo', true);
        return $logoMeta;
    }
    class HeaderFooter {
        public function initHeader() {
            add_filter('get_header_pdf', 'getHeader', 10, 3);
            add_filter('get_footer_pdf', 'getHeader', 10, 3);
            add_filter('get_logo', 'getLogo', 10, 3);
        }
    }
?>