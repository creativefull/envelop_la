<?php

class TableEVList {
    private $data;
    private $header;
    private $checkcontent;
    private $timerdate;

    public function __construct($header, $arrayData, $checkContent, $timerdate) {
        $this->data = $arrayData;
        $this->header = $header;
        $this->checkcontent = $checkContent;
        $this->timerdate = $timerdate;
    }

    private function CountDown($id,$datetime){
        if ($id != "1") {
            ?>
            <p id="demo<?= $id; ?>" class="alert alert-danger"></p>
            <script>
                var datetime<?= $id; ?>= "<?= date('Y-m-d H:i:s', strtotime( $datetime . ' + 4 day' )) ?>"; 
                // Set the date we're counting down to
                var countDownDate<?= $id; ?> = new Date(datetime<?= $id; ?>).getTime();

                // Update the count down every 1 second
                var x<?= $id ?> = setInterval(function() {

                    // Get todays date and time
                    var now = new Date().getTime();

                    // Find the distance between now an the count down date
                    var distance<?= $id; ?> = countDownDate<?= $id; ?> - now;

                    // Time calculations for days, hours, minutes and seconds
                    var days = Math.floor(distance<?= $id; ?> / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance<?= $id; ?> % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance<?= $id; ?> % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance<?= $id; ?> % (1000 * 60)) / 1000);

                    // Display the result in the element with id="demo"
                    document.getElementById("demo<?= $id; ?>").innerHTML = "Send email alert in " +days + " days, " + hours + " hours, "
                    + minutes + " minutes, " + seconds + " seconds ";
                        console.log( days + " " + hours + " " + minutes + " " + seconds)
                                

                    // If the count down is finished, write some text 
                    if (distance<?= $id; ?> < 0) {
                        clearInterval(x<?= $id ?>);
                        document.getElementById("demo<?= $id; ?>").innerHTML = "Send email alert in 00 days, 00 hours, 00 minutes, 00 seconds ";
                        
                    }
                }, 1000);

            

            </script>
            <?php
        }
        return ;
    }

    public function view($options) {
        global $current_user;
        get_currentuserinfo();
        $userid = wp_get_current_user()->id;
        if ($options['move']) {
            ?>
            <form action="" method="POST">
            <?php
        }
        ?>

        <?php $this->CountDown($options['step'], $this->timerdate[0]->created_at ); ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <?php
                        if ($options['move']) {
                            ?>
                            <th>

                            </th>
                            <?php
                        }
                        foreach($this->header as $column) {
                            ?>
                            <th><?= $column; ?></th>
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
                                <td colspan="<?= $options['move'] ? 4 : 3; ?>">
                                     <!-- Trigger the modal with a button -->
                                        <?php
                                    
                                        if (count($this->checkcontent) >= 1) { ?>
                                            <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#myletter<?= $options['step']; ?>">Edit Letter</button>
                                        <?php } else { ?>
                                            <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#myletter<?= $options['step']; ?>">Create Letter</button>
                                        <?php } ?>

                                </td>
                                <td>
                                    <?php
                                    
                                    if (count($this->checkcontent) >= 1) { ?>
                                        <a class="btn btn-primary" target="_blank" href="<?php echo get_permalink($options['print']); ?>?pdf=<?= $options['print']; ?>">Letter Preview</a>
                                        
                                        <a class="btn btn-primary" target="_blank" href="<?php echo get_permalink($options['print']); ?>?pdf=<?= $options['print']; ?>&envelope=true">Print Envelope</a>
                                    <?php } else {
                                        echo "<p> Please CREATE LETTER before PRINT</p>";
                                    }?> 
                                </td>
                                <?php
                                    if ($options['move']) {
                                        ?>
                                        <td>
                                        <?php if (count($this->checkcontent) >= 1) { ?>
                                            <a class="btn btn-primary printmove" target="_blank" href="<?php echo get_permalink($options['print']); ?>?move=<?= $options['move']; ?>&pdf=<?= $options['print']; ?>" onclick="javascript:return confirm('Are You Sure You Would Like to Print and Move These Contacts to the Next Sequence?')">Print & Move</a>
                                        <?php } ?>
                                        </td>

                                        <td colspan="2">
                                            <button type="submit" value="<?= $options['move']; ?>" name="submitMove" id="btnMove" class="btn btn-primary">Move selected list to step <?= $options['move']; ?></button>
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

                    <!-- Modal -->
                    <div id="myModal<?= $options['step']; ?>" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Shortcode helper</h4>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th> SHORTCODE </th>
                                            <th> TEXT REPLACEMENT </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>[fname]</td>
                                            <td>Client First Name</td>
                                        </tr>
                                        <tr>
                                            <td>[lname]</td>
                                            <td>Client Last Name</td>
                                        </tr>
                                        <tr>
                                            <td>[address1]</td>
                                            <td>Client Address 1</td>
                                        </tr>
                                        <tr>
                                            <td>[address2]</td>
                                            <td>Client Address 2</td>
                                        </tr>
                                        <tr>
                                            <td>[state]</td>
                                            <td>Client State</td>
                                        </tr>
                                        <tr>
                                            <td>[city]</td>
                                            <td>Client City</td>
                                        </tr>
                                        <tr>
                                            <td>[zipcode]</td>
                                            <td>Client ZipCode</td>
                                        </tr>
                                        <tr>
                                            <td>[myname]</td>
                                            <td>Your Name (<?= $current_user->user_firstname . " " . $current_user->user_lastname; ?>)</td>
                                        </tr>
                                        <tr>
                                            <td>[myphone]</td>
                                            <td>Your Phone (<?= get_user_meta($userid, 'phone', true); ?>)</td>
                                        </tr>
                                        <tr>
                                            <td>[mywebsite]</td>
                                            <td>Your Website (<?= $current_user->user_url; ?>)</td>
                                        </tr>
                                        <tr>
                                            <td>[mycompany]</td>
                                            <td>Your Company (<?= get_user_meta($userid, 'company', true); ?>)</td>
                                        </tr>
                                        <tr>
                                            <td>[myemail]</td>
                                            <td>Your Email (<?= $current_user->user_email; ?>)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                            </div>

                        </div>
                    </div>

                <div id="myletter<?= $options['step']; ?>" class="collapse">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal<?= $options['step']; ?>">Shortcode Helper</button>
                    <form action="" method="POST">
                        <?php
                            $settings = array( 'media_buttons' => false, 'editor_height' => '300' );
                            wp_editor($options['editorValue'], 'editorContent' . $options['step'], $settings);
                        ?>
                        <br/>
                        <input type="hidden" name="editContent" value="yes"/>
                        <input type="hidden" name="step" value="<?= $options['step']; ?>"/>
                        <input type="submit" class="btn btn-primary" name="submitContent" value="MODIFY LETTER"/>
                    </form>
                </div>
            <?php
        }
    }
}