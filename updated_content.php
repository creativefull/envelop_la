<?php
    class UpdateContent {
        public function save($step, $content) {
            global $wpdb;
            $userid = wp_get_current_user()->id;
            $query1 = $wpdb->get_results("SELECT * FROM tbl_content_env WHERE step='$step' AND userid='" . $userid . "'");
            if (count($query1) > 0 ) {
                // UPDATE THE CONTENT
                $wpdb->query("UPDATE tbl_content_env SET content='" . $content . "' WHERE step='$step' AND userid='" . $userid . "'");
                ?>
                <script>
                    setTimeout(function() {
                        window.location.replace(window.location.href)
                    }, 100);
                </script>
                <?php
            } else {
                $wpdb->query("INSERT INTO tbl_content_env (userid, step, content) VALUES ('" . $userid . "', '" . $step . "', '" . $content . "')") or die ("error create new template");
                ?>
                <script>
                    setTimeout(function() {
                        window.location.replace(window.location.href)
                    }, 100);
                </script>
                <?php
            }
        }

        public function get($step) {
            global $wpdb;
            $userInfo = wp_get_current_user();
            $userid = $userInfo->id;
            $query = $wpdb->get_results("SELECT * FROM tbl_content_env WHERE step='$step' AND userid='" . $userid . "'");
            if (count($query) > 0) {
                return $query[0]->content;
            } else {
                $html = "
                Dear Mr. [fname] [lname], 
                Do you own property that you would like to sell? Are you looking to sell quickly without having to pay a realtor a huge commission? If so please call me, as I am interested in purchasing your property for cash and with a quick closing if necessary. 

                Going through the process of selling a property can be extremely stressful especially when you have to spend large amounts of time on marketing, dealing with realtors and contract issues, meeting with buyers that aren’t really serious about the property and just hoping that some offer might come through. When you consider the average number of days a property will sit on the market before it sells in this county is 150 days it may take even longer to sell your property depending on various factors. But I can provide you an immediate solution to these headaches. 

                My name is [myname] and I buy properties of all kinds throughout the area and I would be interested in speaking with you about the possible sale of your property. A brief phone conversation with me could save you a lot of time and energy while resulting in a fair offer for your house. 
                We have been working with sellers like you in the area for years and make the entire process very painless. 

                CALL US TODAY at 555-111-4444, and I will be happy to speak with you about the sale of your property. Of course everything is completely confidential and absolutely free. If you prefer, you can also fill out a form on our website www.sellhouseorhome.com. I look forward to working with you and I wish you and your family the very best during these difficult times. 
                Sincerely, 
                
                
                
                ([myname])
                        ";
                $wpdb->query("INSERT INTO tbl_content_env (content, userid, step) VALUES('" . $html . "','" . $userid . "','" . $step . "')") or die ("something wrong get content");
                return $html;
            }
        }

        public function getTemplate($step) {
            global $wpdb;
            global $current_user;
            get_currentuserinfo();

            $userinfo = wp_get_current_user();
            $name = $current_user->user_firstname . " " . $current_user->user_lastname;

            $strx = "SELECT M.*, C.content FROM tbl_env_market as M INNER JOIN tbl_content_env AS C ON (C.userid = M.userid && C.step = M.seq) WHERE M.userid='$userinfo->id' AND M.seq=$step";
            $query = $wpdb->get_results($strx);
            $content = array();
            $companyName = get_user_meta($userid, 'company', true);
            $websiteName = get_user_meta($userid, 'website', true);
            $phone = get_user_meta($userid, 'phone', true);

            if (count($query) > 0) {
                foreach($query as $q) {
                    $searchFormat = array(
                        '[fname]',
                        '[lname]',
                        '[mycompany]',
                        '[address1]',
                        '[address2]',
                        '[state]',
                        '[city]',
                        '[zipcode]',
                        '[myname]',
                        '[myphone]',
                        '[mywebsite]',
                    );
                    $replaceFormat = array(
                        $q->fname,
                        $q->lname,
                        $company,
                        $q->address1,
                        $q->address2,
                        $q->state,
                        $q->city,
                        $q->zipcode,
                        $name,
                        $phone,
                        $websiteName
                    );
                    $content[] = str_replace($searchFormat, $replaceFormat, $q->content);
                }
                return $content;
            } else {
                return "No template to print";
            }
        }
    }
?>
