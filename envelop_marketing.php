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
        require_once('header_footer.php');
        initTable();
        $HeaderFooter = new HeaderFooter();
        $HeaderFooter->initHeader();

        add_shortcode('env_marketing_upload', 'env_shortcode_upload_func');
        add_shortcode('env_marketing', 'env_shortcode_func');
        add_shortcode('env_marketing_content', 'env_shortcode_content_func');
        add_shortcode('env_marketing_print', 'env_print_func');
        add_shortcode('env_marketing_header_footer', 'env_shortcode_hf_pdf');
    }

    function env_shortcode_content_func($atts) {
        include_once 'updated_content.php';
        include_once 'views/contents.php';
        $Content = new UpdateContent();
        $ContentView = new ENVContent();
        // IF MODIFY CONTENT
        if (@$_POST['submitContent']) {
            $updatedContent = $Content->save($_POST['step'], $_POST['editorContent']);
            print_r($updatedContent);
        }
        if (@$_GET['id']) {
            $data = array(
                "content" => $Content->get($_GET['id']),
                "step" => $_GET['id']
            );
            $ContentView->edit($data);
        } else if (@$_GET['type'] == 'add') {
            $ContentView->edit();
        } else {
            $data = $Content->all();
            $ContentView->view($data);
        }
    }

    function env_shortcode_hf_pdf($atts) {
        include_once 'updated_content.php';
        include_once 'views/contents.php';
        $ContentHeader = new UpdateContent('header');
        $ContentFooter = new UpdateContent('footer');
        $ContentView = new ENVContent();
        // IF MODIFY CONTENT
        if (@$_POST['submitContent']) {
            $headerContent = $ContentHeader->save(0, $_POST['headerContent']);
            $footerContent = $ContentFooter->save(0, $_POST['footerContent']);
            print_r("<p class=\"alert alert-success\">Success setting header and footer</p>");
        }
        $data = array(
            'headerContent' => $ContentHeader->get(0),
            'footerContent' => $ContentFooter->get(0)
        );
        $ContentView->headerFooter($data);
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
        echo get_option( 'dkpdf_pdf_footer_text' );
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
            if ($MoveStudent) {
                ?>
                <p class="alert alert-success">Selected user successfully moved to step <?php echo $_POST['submitMove']; ?></p>
                <?php
            }
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
        include_once 'users.php';
        $UserModel = new UserModel();

        // UPDATE COMPANY NAME
        if (@$_POST['submitCompany']) {
            update_user_meta($userid, 'company', $_POST['companyName']);
            update_user_meta($userid, 'phone', $_POST['phoneNumber']);
            wp_update_user( array( 'ID' => $userid, 'user_url' => $_POST['websiteURL'] ) );
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

            $skip = 0;
            foreach($dataCSV as $d) {
                // CHECK EXISTING
                $params = array(
                    'userid' => $userid,
                    'step' => $seq,
                    'fname' => $d['fname'],
                    'lname' => $d['lname'],
                    'address1' => $d['address1']
                );
                if ($UserModel->isExists($params)) {
                    $skip++;
                } else {
                    $object = array("('" . $userid . "', '".$seq."','".$d['fname']."','".$d['lname']."', '".$d['address1']."','".$d['address2']."','".$d['city']."','".$d['state']."','".$d['zipcode']."')");
                    $dataQuery[] = join(",", $object);
                }
            }

            if (count($dataQuery) > 0) {
                $dataHasil = (join(",", $dataQuery));
                $querySQL = "INSERT INTO tbl_env_market (userid, seq, fname, lname, address1, address2, city, state, zipcode) VALUES $dataHasil";
                $wpdb->query($querySQL) or die ('Something wrong');
                echo "<p class=\"alert alert-success\">Success Upload User in Squence " . $atts['step'] . "
                <br/>User already existing in database => " . $skip . "</p>";
            } else {
                echo "<p class=\"alert alert-warning\">Nothing new data to insert</p>";
            }
        }
        include_once 'views/upload-view.php';
    }

    envelope_marketing_la();
}
