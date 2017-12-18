<?php
/**
	* Plugin Name: Envelop Marketing
	* Plugin URI: http://legendaryagent.com
	* Description: Envelop Marketing
	* Version: 1.0
	* Author: Tangituru
	* Author URI: http://tangituru.com
	* License: Full
*/
if (!function_exists('envelope_marketing_la')) {
    function envelope_marketing_la() {
        require_once('create_table.php');
        initTable();

        add_shortcode('env_marketing_upload', 'env_shortcode_upload_func');
        add_shortcode('env_marketing', 'env_shortcode_func');
        add_shortcode('env_marketing_print', 'env_print_func');
    }

    function env_print_func($atts) {
        include 'updated_content.php';
        include 'views/print_letter.php';
        include 'move.php';
        $Move = new MoveUser();
        $Content = new UpdateContent();
        $Letter = new PrintLetter();
        $dataContent = $Content->getTemplate($atts['step']);
        if (@$_GET['move']) {
            $Move->all($atts['step'], $atts['step'] + 1);
        }
        return $Letter->view($dataContent);
    }

    function env_shortcode_func($atts) {
        global $wpdb;
        $userid = wp_get_current_user()->id;
        include 'views/tables-user.php';
        include 'updated_content.php';
        $Content = new UpdateContent();
        include 'move.php';
        $Move = new MoveUser();

        $headers = array('First Name', 'Last Name', 'Company', 'Address 1', 'Address 2', 'City', 'State', 'Zipcode');
        $theData = $wpdb->get_results("SELECT * FROM tbl_env_market WHERE userid='" . $userid . "' AND seq='" . $atts['step'] . "' ORDER BY created_at DESC");
        $Table = new TableEVList($headers, $theData);

        // IF CONTENT EDITED
        if ($_POST['editContent']) {
            $updatedContent = $Content->save($atts['step'], $_POST['editorContent']);
            print_r($updatedContent);
        }

        // IF STUDENT MOVE
        if ($_POST['submitMove']) {
            extract($_POST);
            $MoveStudent = $Move->bystep($moveUser, $atts['move']);
            ?>
            <p class="alert alert-success">Success move user to step <?= $atts['move']; ?>, please wait to reload this page</p>
            <script>
                setTimeout(function() {
                    window.location.replace(window.location.href)
                }, 500);
            </script>
            <?php
        }
        $option = array(
            'withEditor' => true,
            'editorValue' => $Content->get($atts['step']),
            'print' => $atts['print'],
            'move' => $atts['move']
        );
        return $Table->view($option);
    }

    function env_shortcode_upload_func($atts) {
        global $wpdb;
        include 'read_csv.php';
        $CSV = new ReadCSV();
        $userid = wp_get_current_user()->id;
        $seq = 1;

        if ($_POST['submit']) {

            // INITIAL FILE
            $CSV->initFile($_FILES['csv_file']);
            $dataCSV = $CSV->read();

            $dataQuery = [];
            // GET SQUENCE
            // $resultData = $wpdb->get_results("SELECT * FROM tbl_env_market WHERE userid='$userid' ORDER BY seq DESC LIMIT 0,1", OBJECT);
            // if (count($resultData) >= 1 ) {
            //     $seq = $resultData[0]->seq + 1;
            // }
            $seq = $atts['step'];

            foreach($dataCSV as $d) {
                $object = array("('" . $userid . "', '".$seq."','".$d['fname']."','".$d['lname']."','" . $d['company'] . "', '" .$d['address1']."','".$d['address2']."','".$d['city']."','".$d['state']."','".$d['zipcode']."')");
                $dataQuery[] = join(",", $object);
            }

            $dataHasil = (join(",", $dataQuery));
            $querySQL = "INSERT INTO tbl_env_market (userid, seq, fname, lname, address1, address2, city, state, zipcode) VALUES $dataHasil";
            $wpdb->query($querySQL);
            // echo "<pre>";
            // echo "</pre>";
            echo "<p class=\"alert alert-success\">Success Upload User in Squence " . $atts['step'] . "</p>";
        }
        include 'views/upload-view.php';
    }

    envelope_marketing_la();
}