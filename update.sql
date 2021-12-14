
ALTER TABLE `resources`
ADD COLUMN `image` VARCHAR(255) NULL DEFAULT NULL COMMENT '' AFTER `name`;

ALTER TABLE `form_customization`
ADD COLUMN `field_desc` VARCHAR(1000) NULL DEFAULT NULL COMMENT '' AFTER `field_type`;

ALTER TABLE `rooms` 
ADD COLUMN `minimum_slot` INT(11) NULL DEFAULT 30 AFTER `requires_moderation`;


CREATE TABLE `settings` (
  `property` VARCHAR(100) NOT NULL COMMENT '',
  `value` VARCHAR(1000) NULL COMMENT '',
  INDEX `property_index` (`property` ASC)  COMMENT '');
  
INSERT INTO settings values ('site_title', '');
INSERT INTO settings values ('site_logo', '');
INSERT INTO settings values ('debug_mode', '0');
INSERT INTO settings values ('advance_start', '0');
INSERT INTO settings VALUES ('allow_booking_overlap', '1');
INSERT INTO settings VALUES ('global_message', '');
