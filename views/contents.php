<?php
    class ENVContent {
        public function view($data) {
            global $wp;
            ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Step</th>
                        </th>Content</th>
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
                    <label>Step</label>
                    <?php
                        $settings = array( 'media_buttons' => false, 'editor_height' => '60' );
                        wp_editor($data['content'], 'editorContent' . $data['step'], $settings);
                    ?>
                </div>
                <a href="<?= home_url($wp->request); ?>" title="Back" class="btn btn-default">Back</a>
                <input type="submit" name="submitContent" class="btn btn-primary" value="Modify Content"/>
            </form>
            <?php
        }
    }
?>