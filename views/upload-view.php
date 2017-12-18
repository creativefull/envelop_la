<?php
    $companyName = get_user_meta($userid, 'company', true);
    if ($companyName) {
?>
<div class="panel panel-default">
    <div class="panel-heading">
        Upload CSV Data
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <ul class="alert alert-default">
                    <li>Format should be First Name, Last Name, Company, Address 1, Address 2, City, State, Zipcode</li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
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
        <p class="alert alert-warning">Before upload the data, you need to edit your company name on your profile</p>
        <?php
    }
?>