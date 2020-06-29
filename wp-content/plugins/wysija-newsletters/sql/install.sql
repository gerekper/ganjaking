CREATE TABLE IF NOT EXISTS `user_list` (
  `list_id` INT unsigned NOT NULL,
  `user_id` INT unsigned NOT NULL,
  `sub_date` INT unsigned DEFAULT 0,
  `unsub_date` INT unsigned DEFAULT 0,
  PRIMARY KEY (`list_id`,`user_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` INT unsigned NOT NULL AUTO_INCREMENT,
  `wpuser_id` INT unsigned NOT NULL DEFAULT 0,
  `email` VARCHAR(255) NOT NULL,
  `firstname` VARCHAR(255) NOT NULL DEFAULT '',
  `lastname` VARCHAR(255) NOT NULL DEFAULT '',
  `ip` VARCHAR(100) NOT NULL,
  `confirmed_ip` VARCHAR(100) NOT NULL DEFAULT 0,
  `confirmed_at` INT unsigned NULL,
  `last_opened` INT unsigned NULL,
  `last_clicked` INT unsigned NULL,
  `keyuser` VARCHAR(255) NOT NULL DEFAULT '',
  `created_at` INT unsigned NULL,
  `status` TINYINT  NOT NULL  DEFAULT 0,
  `domain` VARCHAR(255) NULL DEFAULT '',
  `count_confirmations` INT unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `EMAIL_UNIQUE` (`email`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;


-- QUERY ---

CREATE TABLE IF NOT EXISTS `list` (
  `list_id` INT unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(250) NULL,
  `namekey` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `unsub_mail_id` INT unsigned NOT NULL DEFAULT 0,
  `welcome_mail_id` INT unsigned NOT NULL DEFAULT 0,
  `is_enabled` TINYINT unsigned  NOT NULL DEFAULT 0,
  `is_public` TINYINT unsigned  NOT NULL DEFAULT 0,
  `created_at` INT unsigned NULL,
  `ordering` INT unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`list_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `campaign` (
  `campaign_id` INT unsigned AUTO_INCREMENT,
  `name` VARCHAR(250) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`campaign_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `campaign_list` (
  `list_id` INT unsigned NOT NULL,
  `campaign_id` INT unsigned NOT NULL,
  `filter` TEXT NULL,
  PRIMARY KEY (`list_id`,`campaign_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `email` (
  `email_id` INT unsigned AUTO_INCREMENT,
  `campaign_id` INT unsigned NOT NULL DEFAULT 0,
  `subject` VARCHAR(250) NOT NULL DEFAULT '',
  `body` LONGTEXT NULL,
  `created_at` INT unsigned NULL,
  `modified_at` INT unsigned NULL,
  `sent_at` INT unsigned NULL,
  `from_email` VARCHAR(250) NULL,
  `from_name` VARCHAR(250) NULL,
  `replyto_email` VARCHAR(250) NULL,
  `replyto_name` VARCHAR(250) NULL,
  `attachments` TEXT NULL,
  `status` TINYINT NOT NULL DEFAULT 0,
  `type` TINYINT NOT NULL DEFAULT 1,
  `number_sent` INT unsigned NOT NULL DEFAULT 0,
  `number_opened` INT unsigned NOT NULL DEFAULT 0,
  `number_clicked` INT unsigned NOT NULL DEFAULT 0,
  `number_unsub` INT unsigned NOT NULL DEFAULT 0,
  `number_bounce` INT unsigned NOT NULL DEFAULT 0,
  `number_forward` INT unsigned NOT NULL DEFAULT 0,
  `params` TEXT NULL,
  `wj_data` LONGTEXT NULL,
  `wj_styles` LONGTEXT NULL,
  PRIMARY KEY (`email_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `user_field` (
  `field_id` INT unsigned AUTO_INCREMENT,
  `name` VARCHAR(250) NULL,
  `column_name` VARCHAR(250) NOT NULL DEFAULT '',
  `type` TINYINT unsigned DEFAULT 0,
  `values` TEXT NULL,
  `default` VARCHAR(250) NOT NULL DEFAULT '',
  `is_required` TINYINT unsigned NOT NULL DEFAULT 0,
  `error_message` VARCHAR(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`field_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `queue` (
  `user_id` INT unsigned NOT NULL,
  `email_id` INT unsigned NOT NULL,
  `send_at` INT unsigned  NOT NULL DEFAULT 0,
  `priority` TINYINT NOT NULL DEFAULT 0,
  `number_try` TINYINT unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`email_id`),
  KEY `SENT_AT_INDEX` (`send_at`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;


-- QUERY ---

CREATE TABLE IF NOT EXISTS `user_history` (
  `history_id` INT unsigned AUTO_INCREMENT NOT NULL,
  `user_id` INT unsigned NOT NULL,
  `email_id` INT unsigned DEFAULT 0,
  `type` VARCHAR(250) NOT NULL DEFAULT '',
  `details` TEXT NULL,
  `executed_at` INT unsigned NULL,
  `executed_by` INT unsigned NULL,
  `source` TEXT NULL,
  PRIMARY KEY (`history_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `email_user_stat` (
  `user_id` INT unsigned NOT NULL,
  `email_id` INT unsigned NOT NULL,
  `sent_at` INT unsigned NOT NULL,
  `opened_at` INT unsigned NULL,
  `status` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`email_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `url` (
  `url_id` INT unsigned AUTO_INCREMENT,
  `name` VARCHAR(250) NULL,
  `url` TEXT NULL,
  PRIMARY KEY (`url_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `email_user_url` (
  `email_id` INT unsigned NOT NULL,
  `user_id` INT unsigned NOT NULL,
  `url_id` INT unsigned NOT NULL,
  `clicked_at` INT unsigned NULL,
  `number_clicked` INT unsigned NOT NULL DEFAULT 0  ,
  PRIMARY KEY (`user_id`,`email_id`,`url_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `url_mail` (
  `email_id` INT AUTO_INCREMENT NOT NULL,
  `url_id` INT unsigned NOT NULL,
  `unique_clicked` INT unsigned NOT NULL DEFAULT 0,
  `total_clicked` INT unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`email_id`,`url_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `form` (
  `form_id` INT unsigned AUTO_INCREMENT NOT NULL,
  `name` tinytext COLLATE utf8_bin,
  `data` longtext COLLATE utf8_bin,
  `styles` longtext COLLATE utf8_bin,
  `subscribed` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`form_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `custom_field` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `type` tinytext NOT NULL,
  `required` tinyint(1) DEFAULT "0" NOT NULL,
  `settings` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

-- QUERY ---

CREATE TABLE IF NOT EXISTS `subscriber_ips` (
  `ip` varchar(45) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (`created_at`, `ip`),
  KEY ip (`ip`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;
