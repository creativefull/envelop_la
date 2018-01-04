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
                $Content->setStrategy($_POST['strategy']);
                return $Content->save($_POST['step'], $_POST['defaultContent' . $_POST['step'] . $_POST['strategy']], 'default');
            }

            // GET DEFAULT CONTENT
            $allContent = $Content->all('default');

            foreach($allContent as $data) {
                // $theData = $Content->get(0, 0);
                $ContentView->view(array(
                    'content' => $data->content,
                    'step' => $data->step,
                    'strategy' => $data->strategy
                ));
            }
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