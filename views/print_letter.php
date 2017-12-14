<?php
    class PrintLetter {
        public function view($content) {
            ?>
            <style>
                @media print
                {
                    body * {
                        visibility: hidden;
                    }
                    body p {
                        padding-bottom : 12px;
                    }
                    .page-break  { display:block; page-break-before:always; visibility: visible; margin-bottom: 4em}
                    #section-to-print, #section-to-print * {
                        visibility: visible;
                    }
                }
            </style>
            <div class="container" id="section-to-print">
                <?php
                    foreach($content as $c) {
                        ?>
                        <div class="row">
                            <div class="col-md-12">
                                <?= wpautop($c); ?>
                            </div>
                            <div class="page-break"></div>
                        </div>
                        <?php
                    }
                ?>
            </div>
            <script>
                jQuery(function() {
                    window.print();
                })
            </script>
            <?php
        }
    }
?>