<?php
    $userid = wp_get_current_user()->id;
    $userdata = get_userdata($userid);
    $companyName = get_user_meta($userid, 'company', true);
    $phoneName = get_user_meta($userid, 'phoneMarketing', true);
    $fname = get_user_meta($userid, 'marketing_fname', true) ? get_user_meta($userid, 'marketing_fname', true) : $userdata->first_name;
    $lname = get_user_meta($userid, 'marketing_lname', true) ? get_user_meta($userid, 'marketing_lname', true) : $userdata->last_name;;
    if ($companyName && $phoneName && get_user_meta($userid, 'marketing_lname', true) && get_user_meta($userid, 'marketing_fname', true)) {
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
                <img src="<?= content_url(); ?>/uploads/2018/01/csv.png" alt="csv image">;
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
        <p class="alert alert-warning">Before you upload the data, you need to edit your company name, phone, and your website(if any) on your profile.</p>
        <form action="" method="POST">
            <div class="form-group" style="display:none">
                <label>Company Name</label>
                <input type="text" name="companyName" class="form-control" value="United Property Buyers" />
            </div>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="fname" class="form-control" value="<?= $fname; ?>"/>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lname" class="form-control" value="<?= $lname; ?>"/>
            </div>
            <div class="form-group">
                <label>Enter Phone Number Here</label>
                <input type="phone" name="phoneNumber" class="form-control" value="<?= $phoneName; ?>"/>
            </div>
            <div class="form-group" style="display:none">
                <label>Website</label>
                <input type="website" name="websiteURL" class="form-control" value="www.UnitedPropertyBuyers.com" />
            </div>
            <input type="submit" name="submitCompany" value="UPDATE"/>
        </form>
        <?php
    }
?>
