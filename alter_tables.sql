ALTER TABLE `room_booking`.`rooms` 
ADD COLUMN `minimum_slot` INT(11) NULL DEFAULT 30 COMMENT '' AFTER `requires_moderation`;