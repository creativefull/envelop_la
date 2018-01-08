<?php
    $userid = wp_get_current_user()->id;
    $userdata = get_userdata($userid);
    $companyName = get_user_meta($userid, 'company', true);
    $phoneName = get_user_meta($userid, 'phone', true);
    if ($companyName && $phoneName) {
?>
<div class="panel panel-default">
    <div class="panel-heading">
        Upload CSV Data
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <ul class="alert alert-default">
                    <li>Format should be First Name, Last Name, Address 1, Address 2, City, State, Zipcode (<a target="_blank" href="<?= content_url(); ?>/uploads/2018/01/demo_format_la_contact.csv">Download Sample</a>)</li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="hidden" name="step" value="<?= $atts['step']; ?>"/>
                        <label class="label-control" for="file">Select File</label>
                        <input type="file" class="form-control" id="file" name="csv_file"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" value="Upload"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
    } else {
        ?>
        <p class="alert alert-warning">Before upload the data, you need to edit your company name, your phone and your website(if any) on your profile</p>
        <form action="" method="POST">
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="companyName" class="form-control" value="<?= $companyName; ?>" />
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="phone" name="phoneNumber" class="form-control" value="<?= $phoneName; ?>"/>
            </div>
            <div class="form-group">
                <label>Website</label>
                <input type="website" name="websiteURL" class="form-control" value="<?= $userdata->user_url ; ?>" />
            </div>
            <input type="submit" name="submitCompany" value="UPDATE"/>
        </form>
        <?php
    }
?>