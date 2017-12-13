<?php

class TableEVList {
    private $data;
    private $header;

    public function __construct($header, $arrayData) {
        $this->data = $arrayData;
        $this->header = $header;
    }

    public function view($options) {
        ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <?php
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
                            </tr>
                        </tfoot>
                    <?php
                }
            ?>
        </table>
        <?php

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