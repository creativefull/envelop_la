<?php
    class ENVContent {
        private $type;
        public function __construct($type='content') {
            $this->type = $type;
        }
        public function view($data) {
            global $wp;
            ?>
            <a href="<?=home_url($wp->request);?>?type=add" title="Add Default Content" class="btn btn-primary">
                Add Default Content
            </a>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Step</th>
                        <th>Content</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($data as $d) {
                            ?>
                            <tr>
                                <td><?= $d->step; ?></td>
                                <td><?= $d->content; ?></td>
                                <td>
                                    <a href="<?=home_url($wp->request);?>?id=<?= $d->step; ?>">Edit</a>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                </tbody>
            </table>
            <?php
        }

        public function edit($data) {
            global $wp;
            ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Step</label>
                    <input type="number" placeholder="Step" value="<?= $data['step']; ?>" name="step"/>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <?php
                        $settings = array( 'media_buttons' => false, 'editor_height' => '60' );
                        wp_editor($data['content'], 'editorContent', $settings);
                    ?>
                </div>
                <a href="<?= home_url($wp->request); ?>" title="Back" class="btn btn-default">Back</a>
                <input type="submit" name="submitContent" class="btn btn-primary" value="Modify Content"/>
            </form>
            <?php
        }

        public function headerFooter($data) {
            global $wp;
            ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Company Logo Url</label>
                    <input type="text" name="companyLogo" value="<?= $data['companyLogo']; ?>"/>
                </div>
                <div class="form-group">
                    <label>Header</label>
                    <?php
                        $settings = array( 'media_buttons' => false, 'editor_height' => '60' );
                        wp_editor($data['headerContent'], 'headerContent', $settings);
                    ?>
                </div>
                <!--<div class="form-group">
                    <label>Footer</label>
                    <?php
                        $settings = array( 'media_buttons' => false, 'editor_height' => '60' );
                        wp_editor($data['footerContent'], 'footerContent', $settings);
                    ?>
                </div>-->
                <input type="submit" name="submitContent" class="btn btn-primary" value="Modify Content"/>
            </form>
            <?php            
        }
    }
?>