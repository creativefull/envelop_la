<?php
    if (!function_exists('envelope_marketing_la_admin')) {
        function envelope_marketing_la_admin() {
            add_shortcode('env_content_admin', 'env_content_admin');
            add_filter('get_default_content', 'getDefaultContent', 10, 3);
        }

        function env_content_admin($atts) {
            include_once 'admin/content_view.php';
            include_once 'updated_content.php';
            $Content = new UpdateContent();
            $ContentView = new AdminContent();
            // IF FORM SUBMIT
            if (@$_POST['submitContent']) {
                return $Content->save(0, $_POST['defaultContent']);
            }

            // GET DEFAULT CONTENT
            $theData = $Content->get(0, 0);
            return $ContentView->view(array(
                'content' => $theData
            ));
        }

        // HOOK TO GET DEFAULT CONTENT
        function getDefaultContent() {
            include_once 'updated_content.php';
            $Content = new UpdateContent();
            return $Content->get(0,0);
        }
        envelope_marketing_la_admin();
    }
?>