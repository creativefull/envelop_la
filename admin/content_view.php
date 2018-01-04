<?php
    class AdminContent {
        private $type;
        public function __construct($type='content') {
            $this->type = $type;
        }

        public function view($data) {
            global $wp;
            ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Step</label>
                    <input type="text" name="step" value="<?= $data['step']; ?>" readonly/>
                </div>
                <div class="form-group">
                    <label>Strategy</label>
                    <input type="text" name="strategy" value="<?= $data['strategy']; ?>" readonly/>
                </div>
                <div class="form-group">
                    <label>Please enter default content</label>
                    <?php
                        $settings = array( 'media_buttons' => false, 'editor_height' => '60' );
                        wp_editor($data['content'], 'defaultContent' . $data['step'] . $data['strategy'], $settings);
                    ?>
                </div>
                <a href="<?= home_url($wp->request); ?>" title="Back" class="btn btn-default">Back</a>
                <input type="submit" name="submitContent" class="btn btn-primary" value="Save Letter"/>
            </form>
            <?php
        }
    }
?>