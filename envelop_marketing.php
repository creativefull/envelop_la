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
        include_once 'updated_content.php';
        include_once 'views/print_letter.php';
        include_once 'move.php';
        $Move = new MoveUser();
        $Content = new UpdateContent();
        $Letter = new PrintLetter();
        $userid = wp_get_current_user()->id;
        $dataContent = $Content->getTemplate($atts['step']);
        if (@$_GET['move']) {
            $Move->all($atts['step'], $atts['step'] + 1);
        }
        return $Letter->view($dataContent);
    }

    function env_shortcode_func($atts) {
        global $wpdb;
        $userid = wp_get_current_user()->id;

        include_once 'views/tables-user.php';
        include_once 'updated_content.php';
        $Content = new UpdateContent();
        include_once 'move.php';
        $Move = new MoveUser();

        $headers = array('First Name', 'Last Name', 'Address 1', 'Address 2', 'City', 'State', 'Zipcode');
        $theData = $wpdb->get_results("SELECT * FROM tbl_env_market WHERE userid='" . $userid . "' AND seq='" . $atts['step'] . "' ORDER BY created_at DESC");
        $Table = new TableEVList($headers, $theData);

        // IF CONTENT EDITED
        if (@$_POST['editContent']) {
            $updatedContent = $Content->save($_POST['step'], $_POST['editorContent' . $_POST['step']]);
            print_r($updatedContent);
        }

        // IF STUDENT MOVE
        if (@$_POST['submitMove']) {
            $MoveStudent = $Move->bystep($_POST['moveUser'], $_POST['submitMove']);
            ?>
            <p class="alert alert-success">Success move user to step <?php echo $_POST['submitMove']; ?>, please wait to reload this page</p>
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
            'move' => $atts['move'],
            'step' => $atts['step']
        );
        if (count($theData) >= 1) {
            return $Table->view($option);
        } else {
            echo "<p class=\"alert alert-warning\">No Contact Found</p>";
        }
    }

    function env_shortcode_upload_func($atts) {
        global $wpdb;
        include_once 'read_csv.php';
        $CSV = new ReadCSV();
        $userid = wp_get_current_user()->id;
        $seq = 1;

        // UPDATE COMPANY NAME
        if (@$_POST['submitCompany']) {
            update_user_meta($userid, 'company', $_POST['companyName']);
            update_user_meta($userid, 'phone', $_POST['phoneNumber']);
            ?>
            <script>
                setTimeout(function() {
                    window.location.replace(window.location.href)
                }, 100);
            </script>
            <?php
        }

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
            $seq = $_POST['step'];

            foreach($dataCSV as $d) {
                $object = array("('" . $userid . "', '".$seq."','".$d['fname']."','".$d['lname']."', '".$d['address1']."','".$d['address2']."','".$d['city']."','".$d['state']."','".$d['zipcode']."')");
                $dataQuery[] = join(",", $object);
            }

            $dataHasil = (join(",", $dataQuery));
            $querySQL = "INSERT INTO tbl_env_market (userid, seq, fname, lname, address1, address2, city, state, zipcode) VALUES $dataHasil";
            $wpdb->query($querySQL) or die ('Something wrong');
            // echo "<pre>";
            // echo "</pre>";
            echo "<p class=\"alert alert-success\">Success Upload User in Squence " . $atts['step'] . "</p>";
        }
        include_once 'views/upload-view.php';
    }

    envelope_marketing_la();
}
