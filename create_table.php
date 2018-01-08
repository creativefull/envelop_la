<?php
function initTable() {
    global $wpdb;
    $tablename = 'tbl_env_market';

    if ($wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename) {
        create_table_em();
    }
}

function create_table_em() {
    $tablename = 'tbl_env_market';
    $sqlQuery = "CREATE TABLE $tablename (
    `id_env_market` INT NOT NULL AUTO_INCREMENT,
    `userid` INT NULL,
    `seq` INT NULL,
    `fname` VARCHAR(200) NULL,
    `lname` VARCHAR(200) NULL,
    `address1` VARCHAR(200) NULL,
    `address2` VARCHAR(200) NULL,
    `state` VARCHAR(45) NULL,
    `city` VARCHAR(45) NULL,
    `zipcode` VARCHAR(45) NULL,
    `strategy` INT 0,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id_env_market`));";

    $sqlQuery2 = "CREATE TABLE `wordpress`.`tbl_content_env` (
        `tbl_content_env_id` INT NOT NULL AUTO_INCREMENT,
        `userid` INT NULL,
        `step` INT NULL,
        `type` VARCHAR(20) NULL,
        `strategy` INT 0,
        `content` LONGTEXT NULL,
        `title` VARCHAR(20),
        PRIMARY KEY (`tbl_content_env_id`));";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sqlQuery );
    dbDelta( $sqlQuery2 );
}

initTable();