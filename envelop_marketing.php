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

    /* Wordpress CronJob LA */
    if ( ! wp_next_scheduled( 'my_task_hook' ) ) {
        wp_schedule_event( time(), 'hourly', 'my_task_hook' );
    }
    add_action( 'my_task_hook', 'la_envelope_task' ); 
    
    function la_envelope_task() {
        global $wpdb;
        $subject = "Reminder: It's time to send out the next mailer!";
        $nowdatetime = current_time( 'mysql' );

        $timerdate = $wpdb->get_results("SELECT DISTINCT userid, seq, min(created_at) as created_at FROM tbl_env_market GROUP BY userid , seq ");
        foreach ($timerdate as $datatask)
        {
            $datetimebefore = date('Y-m-d H:i:s', strtotime( $datatask->created_at . ' + 4 day' ));
            $datestop = date('Y-m-d H:i:s', strtotime( $datetimebefore . ' + 1 hour' ));
            
            // Create Date Range between Date +4D and +4D1H, to avoid repeatable mail send because cron is hourly
            if ( strtotime($nowdatetime) >= strtotime($datetimebefore) &&  strtotime($nowdatetime) <= strtotime($datestop) ) {
                $userdata = get_userdata( $datatask->userid );
                $body = "<html><body>Hello,<br><br>
                This is a reminder to login to LegendaryAgent.com and send out Sequence $datatask->seq in the Probate Mailing Center! You DO NOT want to delay on this step because consistent communication is what is going to get us our next deal. On average, you will get a response after you mail the sellers 7-12+ times (don't give up!).  <br><br>
                
                Login and send out the next mailer as soon as possible.  <br><br>
                Let us know if you have any questions,  <br><br><br>
                Legendary Agent</body></html>";
                wp_mail( $userdata->user_email, $subject, $body );
            }
        }
        
    }

    function envelope_marketing_la() {
        require_once('create_table.php');
        require_once('header_footer.php');
        initTable();
        $HeaderFooter = new HeaderFooter();
        $HeaderFooter->initHeader();

        session_start();

        add_shortcode('env_marketing_upload', 'env_shortcode_upload_func');
        add_shortcode('env_marketing', 'env_shortcode_func');
        add_shortcode('env_marketing_content', 'env_shortcode_content_func');
        add_shortcode('env_marketing_print', 'env_print_func');
        add_shortcode('env_marketing_header_footer', 'env_shortcode_hf_pdf');
        add_shortcode('env_content_alert_result', 'env_content_alert_result');

    }
    require('admin_envelop_marketing.php');

    function env_content_alert_result() {
        if (@$_SESSION['status_upload'] == 'error') {
            ?>
            <p class="alert alert-warning"><?= @$_SESSION['message_upload']; ?></p>
            <?php
            unset($_SESSION['status_upload']);
            unset($_SESSION['message_upload']);
        } 
        if (@$_SESSION['status_upload'] == 'ok') {
            ?>
            <p class="alert alert-success"><?= @$_SESSION['message_upload']; ?></p>
            <?php
            unset($_SESSION['status_upload']);
            unset($_SESSION['message_upload']);
        }
    }

    function env_shortcode_content_func($atts) {
        include_once 'updated_content.php';
        include_once 'views/contents.php';
        $Content = new UpdateContent();
        $ContentView = new ENVContent();
        // IF MODIFY CONTENT
        if (@$_POST['submitContent']) {
            $Content->setStrategy($_POST['strategy']);
            $updatedContent = $Content->save($_POST['step'], $_POST['editorContent']);
            print_r($updatedContent);
        }
        if (@$_GET['id']) {
            $data = array(
                "content" => $Content->get($_GET['id'], $_GET['strategy']),
                "step" => $_GET['id'],
                'strategy' => $_GET['strategy']
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
        $userid = wp_get_current_user()->id;
        $ContentHeader = new UpdateContent('header');
        $ContentFooter = new UpdateContent('footer');
        $ContentEnvelope = new UpdateContent('envelope');
        $ContentLogo = new UpdateContent('clogo');
        $ContentView = new ENVContent();

        $userdata = get_userdata($userid);
        $companyName = get_user_meta($userid, 'company', true);
        $phoneName = get_user_meta($userid, 'phoneMarketing', true);

        // IF MODIFY CONTENT
        if (@$_POST['submitHFEL']) {
            $ContentLogo->saveLogo($_POST['companyLogo']);
            $headerContent = $ContentHeader->save(0, $_POST['headerContent']);
            $footerContent = $ContentFooter->save(0, $_POST['footerContent']);
            $envelopeContent = $ContentEnvelope->save(0, $_POST['envelopeContent']);

            // update_user_meta($userid, 'company', $_POST['companyName']);
            update_user_meta($userid, 'phoneMarketing', $_POST['phone']);
            wp_update_user( array( 'ID' => $userid, 'user_url' => $_POST['website'] ) );

            print_r("<p class=\"alert alert-success\">Update successfull</p>");
        }
        $data = array(
            'headerContent' => $ContentHeader->get(0) != "" ? $ContentHeader->get(0) : "<p style=\"font-size: 10px\">Insert Your Company Name Here<br/>Insert your phone here<br/>Insert Your Address Here</p>",
            'footerContent' => $ContentFooter->get(0),
            'envelopeContent' => $ContentEnvelope->get(0) != "" ? $ContentEnvelope->get(0) : "<p style=\"padding-left: 420px;\"><strong>Attn : [fname] [lname]</strong>
            [address1] [address2]
            [city] [state] [zipcode]</p>",
            'companyLogo' => $ContentLogo->getLogo(),
            'website' => $userdata->user_url,
            'phone' => $phoneName
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
        // echo get_option( 'dkpdf_pdf_footer_text' );
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

        $strategy = $atts['strategy'] ? $atts['strategy'] : 0;
        $headers = array('First Name', 'Last Name', 'Address 1', 'Address 2', 'City', 'State', 'Zipcode');
        $theData = $wpdb->get_results("SELECT * FROM tbl_env_market WHERE userid ='" . $userid . "' AND strategy='" . $strategy . "' AND seq ='" . $atts['step'] . "' ORDER BY created_at DESC");
        $timerdate = $wpdb->get_results("SELECT created_at  FROM tbl_env_market WHERE userid ='" . $userid . "' AND seq ='" . $atts['step'] . "' ORDER BY created_at ASC");
        $checkContent = $wpdb->get_results("SELECT tbl_content_env_id FROM tbl_content_env WHERE userid = '" . $userid . "' AND step = '" . $atts['step'] . "' AND type = 'content' ");
        $Table = new TableEVList($headers, $theData, $checkContent, $timerdate);

        // IF CONTENT EDITED
        if (@$_POST['editContent']) {
            $updatedContent = $Content->save($_POST['step'], $_POST['editorContent' . $_POST['step']]);
            print_r($updatedContent);
            $_SESSION['status_upload'] = 'ok';
            $_SESSION['message_upload'] = 'Letter Content updated ';
            ?>
            <script>
                setTimeout(function() {
                    window.location.replace(window.location.href)
                }, 100);
            </script>
            <?php
        }

        // IF STUDENT MOVE
        if (@$_POST['submitMove']) {
            $MoveStudent = $Move->bystep($_POST['moveUser'], $_POST['submitMove']);
            if ($MoveStudent) {
                $_SESSION['status_upload'] = 'ok';
                $_SESSION['message_upload'] = 'Selected user successfully moved to step ' . $_POST['submitMove'];
                ?>
                <script>
                    setTimeout(function() {
                        window.location.replace(window.location.href)
                    }, 100);
                </script>
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
            $nextStep = $atts['step'] + 1;
            $nextData = $wpdb->get_results("SELECT userid FROM tbl_env_market WHERE userid ='" . $userid . "' AND strategy='" . $strategy . "' AND seq ='" . $nextStep . "' ORDER BY created_at DESC LIMIT 0,1");
            if (count($nextData) >= 1) {
                echo "<p class=\"alert alert-warning\">Previous Sent Mailers - View (On progress ...)";
            } else {
                echo "<p class=\"alert alert-warning\">No Contact Found</p>";
            }
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
            update_user_meta($userid, 'phoneMarketing', $_POST['phoneNumber']);
            update_user_meta($userid, 'marketing_fname', $_POST['fname']);
            update_user_meta($userid, 'marketing_lname', $_POST['lname']);
            wp_update_user( array( 'ID' => $userid, 'user_url' => $_POST['websiteURL'] ) );
            ?>
            <script>
                setTimeout(function() {
                    window.location.replace(window.location.href)
                }, 100);
            </script>
            <?php
        }

        if (@$_POST['submit']) {

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
            $strategy = $_POST['strategy'] ? $_POST['strategy'] : 0;

            $skip = 0;
            foreach($dataCSV as $d) {
                // CHECK EXISTING
                $params = array(
                    'userid' => $userid,
                    'fname' => $d['fname'],
                    'lname' => $d['lname'],
                    'address1' => $d['address1'],
                    'strategy' => $strategy
                );
                if ($UserModel->isExists($params)) {
                    $skip++;
                } else {
                    $object = array("('" . $userid . "', '".$seq."','".$d['fname']."','".$d['lname']."', '".$d['address1']."','".$d['address2']."','".$d['city']."','".$d['state']."','".$d['zipcode']."', '" . $strategy . "')");
                    $dataQuery[] = join(",", $object);
                }
            }

            if (count($dataQuery) > 0) {
                $dataHasil = (join(",", $dataQuery));
                $querySQL = "INSERT INTO tbl_env_market (userid, seq, fname, lname, address1, address2, city, state, zipcode, strategy) VALUES $dataHasil";
                $wpdb->query($querySQL) or die ('Something wrong');
                $_SESSION['status_upload'] = 'ok';
                $_SESSION['message_upload'] = 'Data Successfully Uploaded';
                ?>
                <script>
                    window.location.href = location.href
                </script>
                <?php
                // echo "<p class=\"alert alert-success\">Data Successfully Uploaded</p>";
            } else {
                $_SESSION['status_upload'] = 'error';
                $_SESSION['message_upload'] = 'Data Exist, No new data uploaded';
                ?>
                <script>
                    window.location.href = location.href
                </script>
                <?php
                // echo "<p class=\"alert alert-warning\">Nothing new data to insert</p>";
            }
        }
        include_once 'views/upload-view.php';
    }

    envelope_marketing_la();
}
