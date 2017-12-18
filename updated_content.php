<?php
    class UpdateContent {
        public function save($step, $content) {
            global $wpdb;
            $userid = wp_get_current_user()->id;
            $query1 = $wpdb->get_results("SELECT * FROM tbl_content_env WHERE step='$step' AND userid='" . $userid . "'");
            if (count($query1) > 0 ) {
                // UPDATE THE CONTENT
                $wpdb->query("UPDATE tbl_content_env SET content='" . $content . "' WHERE step='$step' AND userid='" . $userid . "'");
                return 'done';
            } else {
                $wpdb->query("INSERT INTO tbl_content_env (userid, step, content) VALUES ('" . $userid . "', '" . $step . "', '" . $content . "')") or die ("error create new template");
                return 'done';
            }
        }

        public function get($step) {
            global $wpdb;
            $userid = wp_get_current_user()->id;
            $query = $wpdb->get_results("SELECT * FROM tbl_content_env WHERE step='$step' AND userid='" . $userid . "'");
            if (count($query) > 0) {
                return $query[0]->content;
            } else {
                $html = "<p>What about where it says:</p><br/>
<p>
Hello, my name is [name] with [company]
</p>
<br/>
<p>
Can we have an area where the student puts:
</p>

<br/>
<p>Name: Kevin George</p><br/>
<p>Company: XYZ Solutions</p>";
                return $html;
            }
        }

        public function getTemplate($step) {
            global $wpdb;
            $userinfo = wp_get_current_user();
            $strx = "SELECT M.*, C.content FROM tbl_env_market as M INNER JOIN tbl_content_env AS C ON (C.userid = M.userid && C.step = M.seq) WHERE M.userid='$userinfo->id' AND M.seq=$step";
            $query = $wpdb->get_results($strx);
            $content = array();
            if (count($query) > 0) {
                foreach($query as $q) {
                    $searchFormat = array(
                        '[fname]',
                        '[lname]',
                        '[company]',
                        '[address1]',
                        '[address2]',
                        '[state]',
                        '[city]',
                        '[zipcode]',
                        '[myname]'
                    );
                    $replaceFormat = array(
                        $q->fname,
                        $q->lname,
                        $q->company,
                        $q->address1,
                        $q->address2,
                        $q->state,
                        $q->city,
                        $q->zipcode,
                        $userinfo->display_name
                    );
                    $content[] = str_replace($searchFormat, $replaceFormat, $q->content);
                }
                return $content;
            }
        }
    }
?>