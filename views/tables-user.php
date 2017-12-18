<?php

class TableEVList {
    private $data;
    private $header;

    public function __construct($header, $arrayData) {
        $this->data = $arrayData;
        $this->header = $header;
    }

    public function view($options) {
        if ($options['move']) {
            ?>
            <form action="" method="POST">
            <?php
        }
        ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <?php
                        if ($options['move']) {
                            ?>
                            <td>

                            </td>
                            <?php
                        }
                        foreach($this->header as $column) {
                            ?>
                            <td><?= $column; ?></td>
                            <?php
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (count($this->data) >= 1) {
                        foreach($this->data as $theData) {
                            ?>
                            <tr>
                                <?php
                                    if ($options['move']) {
                                        ?>
                                        <td>
                                            <input type="checkbox" name="moveUser[]" value="<?= $theData->id_env_market; ?>"/>
                                        </td>
                                        <?php
                                    }
                                ?>
                                <td><?= $theData->fname; ?></td>
                                <td><?= $theData->lname; ?></td>
                                <td><?= $theData->address1; ?></td>
                                <td><?= $theData->address2; ?></td>
                                <td><?= $theData->city; ?></td>
                                <td><?= $theData->state; ?></td>
                                <td><?= $theData->zipcode; ?></td>
                            </tr>
                            <?php
                        }
                    }
                ?>
            </tbody>
            <?php
                if (count($this->data) >= 1 && $options['print']) {
                    ?>
                        <tfoot>
                            <tr>
                                <td colspan="6"></td>
                                <td>
                                    <a class="btn btn-default" href="<?php echo get_permalink($options['print']); ?>">Print All Letters</a>
                                </td>
                                <?php
                                    if ($options['move']) {
                                        ?>
                                        <td>
                                            <button type="submit" value="<?= $options['move']; ?>" name="submitMove" id="btnMove" class="btn btn-primary">Move to step <?= $options['move']; ?></button>
                                        </td>
                                        <?php
                                    }
                                ?>
                            </tr>
                        </tfoot>
                    <?php
                }
            ?>
        </table>
        <?php
        if ($options['move']) {
            ?>
            </form>
            <?php
        }

        if ($options['withEditor']) {
            ?>
            <form action="" method="POST">
            <?php
            wp_editor($options['editorValue'], 'editorContent');
            ?>
            <br/>
            <input type="hidden" name="editContent" value="yes"/>
            <input type="submit" class="btn btn-primary" name="submitContent" value="SAVE CONTENT"/>
            </form>
            <?php
        }
    }
}