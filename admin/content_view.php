<?php
    class AdminContent {
        private $type;
        public function __construct($type='content') {
            $this->type = $type;
            wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js');
            wp_enqueue_script( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
        }

        public function view($data) {
            global $wp;
            ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#collapse<?= $data['step'] . $data['strategy']; ?>" data-parent="#accordion" data-toggle="collapse">
                            <?= $data['title'] ? $data['title'] : 'Untitled'; ?>
                        </a>
                    </h4>
                </div>
                <div class="panel-body panel-collapse collapse" id="collapse<?= $data['step'] . $data['strategy']; ?>">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" value="<?= $data['title']; ?>"/>
                        </div>
                        <div class="form-group">
                            <label>Step</label>
                            <input type="text" name="step" value="<?= $data['step']; ?>" readonly/>
                        </div>
                        <div class="form-group">
                            <!--<label>Strategy</label>-->
                            <input type="hidden" name="strategy" value="<?= $data['strategy']; ?>" readonly/>
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
                </div>
            </div>
            <?php
        }
    }
?>