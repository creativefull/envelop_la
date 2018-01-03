<?php
    class PrintLetter {
        public function view($content) {
            ?>
            <style>
                /*header {
                    visibility : hidden !important;
                    display : none;
                }*/
                .site-content {
                    padding : 0px !important;
                }
                @media print
                {
                    body * {
                        visibility: hidden;
                        margin: 0em;
                    }
                    body p {
                        // padding-bottom : 12px;
                    }
                    .hide-print {
                        visibility: hidden !important;
                        display: none;
                     }
                    .pagebreak  { display:block; page-break-after:always; visibility: visible; }

                    @page {
                        size: auto;   /* auto is the initial value */ 

                        /* this affects the margin in the printer settings */ 
                        margin: 35mm 25mm 25mm 25mm;                        
                    }
                    .pagebreak:last-child {
                        page-break-after: auto;
                   }
                }
            </style>
            <div id="section-to-print">
                <?php
                    foreach($content as $c) {
                        ?>
                        <div class="row pagebreak">
                            <div class="col-md-12">
                                <?= wpautop($c); ?>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </div>
            <?php
        }
    }
?>