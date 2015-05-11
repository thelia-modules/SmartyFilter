
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- smarty_filter
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `smarty_filter`;

CREATE TABLE `smarty_filter`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `active` TINYINT DEFAULT 0 NOT NULL,
    `filtertype` VARCHAR(255) NOT NULL,
    `code` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- smarty_filter_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `smarty_filter_i18n`;

CREATE TABLE `smarty_filter_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `smarty_filter_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `smarty_filter` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
