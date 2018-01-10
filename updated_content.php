<?php
    class UpdateContent {
        private $type;
        private $stg = 0;
        public function __construct($type = 'content') {
            $this->type = $type;
        }
        public function setStrategy($stg) {
            $this->stg = $stg;
        }
        public function saveLogo($logo) {
            $userid = wp_get_current_user()->id;
            $logoMeta = get_user_meta($userid, 'companyLogo');
            if (count($logoMeta) > 0) {
                update_user_meta($userid, 'companyLogo', $logo);
            } else {
                add_user_meta($userid, 'companyLogo', $logo);
            }
            return 'ok';
        }
        public function getLogo() {
            $userid = wp_get_current_user()->id;
            $logoMeta = get_user_meta($userid, 'companyLogo', true);
            return $logoMeta;
        }
        public function save($step, $content, $uid, $title='') {
            global $wpdb;
            $userid = $uid == 'default' ? '0' : wp_get_current_user()->id;
            $query1 = $wpdb->get_results("SELECT * FROM tbl_content_env WHERE step='$step' AND userid='" . $userid . "' AND strategy='" . $this->stg . "' AND type='" . $this->type . "'");
            if (count($query1) > 0 ) {
                // UPDATE THE CONTENT
                $wpdb->query("UPDATE tbl_content_env SET title='" . $title . "', content='" . $content . "' WHERE step='$step' AND userid='" . $userid . "' AND strategy='" . $this->stg . "' AND type='" . $this->type . "'");
                ?>
                <script>
                    setTimeout(function() {
                        window.location.replace(window.location.href)
                    }, 100);
                </script>
                <?php
            } else {
                $wpdb->query("INSERT INTO tbl_content_env (userid, step, strategy, type, content, title) VALUES ('" . $userid . "', '" . $step . "', '" . $this->stg . "', '" . $this->type . "', '" . $content . "', '" . $title . "')") or die ("error create new template");
                ?>
                <script>
                    setTimeout(function() {
                        window.location.replace(window.location.href)
                    }, 100);
                </script>
                <?php
            }
        }

        public function all($uid) {
            global $wpdb;
            $userInfo = wp_get_current_user();
            $userid = $uid == 'default' ? '0' : $userInfo->id;
            $query = $wpdb->get_results("SELECT * FROM tbl_content_env WHERE userid='" . $userid . "' AND type='" . $this->type . "' ORDER BY step");
            return $query;
        }

        public function get($step, $stg=0) {
            global $wpdb;
            $userInfo = wp_get_current_user();
            $userid = $userInfo->id;
            $query = $wpdb->get_results("SELECT * FROM tbl_content_env WHERE step='$step' AND strategy='$stg' AND userid='" . $userid . "' AND type='" . $this->type . "'");
            $blankquery = $wpdb->get_results("SELECT * FROM tbl_content_env WHERE step='$step' AND userid='0' AND type='" . $this->type . "'");
            if (count($query) > 0) {
                return $query[0]->content;
            } else {
                $html = $blankquery[0]->content;
                    // $wpdb->query("INSERT INTO tbl_content_env (content, userid, step, type) VALUES('" . $html . "','" . $userid . "','" . $step . "','" . $this->type . "')") or die ("something wrong get content");
                return $html;
            }
        }

        public function getTemplate($step) {
            global $wpdb;
            global $current_user;
            get_currentuserinfo();

            $userinfo = wp_get_current_user();
            $name = $current_user->user_firstname . " " . $current_user->user_lastname;
            if (empty ($_GET[envelope])) {
                // This for Letter
                $strx = "SELECT M.*, C.content FROM tbl_env_market as M INNER JOIN tbl_content_env AS C ON (C.userid = M.userid && C.step = M.seq) WHERE M.userid='$userinfo->id' AND M.seq=$step";
            } else {
                // This for Envelope
                $strx = "SELECT M.*, C.content FROM tbl_env_market as M INNER JOIN tbl_content_env AS C ON (C.userid = M.userid) WHERE M.userid='$userinfo->id' AND M.seq=$step AND C.type='envelope'";
            }
            $query = $wpdb->get_results($strx);
            $content = array();
            $company = get_user_meta($userinfo->ID, 'company', true);
            $website = $current_user->user_url;
            $email = $current_user->user_email;
            $phone = get_user_meta($userinfo->ID, 'phone', true);

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
                        '[myemail]'
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
                        $website,
                        $email
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
