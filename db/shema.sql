CREATE TABLE IF NOT EXISTS `phrases` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT NOT NULL,
  `foreign_phrase` VARCHAR(255) NOT NULL,
  `native_phrase` VARCHAR(255) NOT NULL,
  `pronunciation` VARCHAR(255) NOT NULL,
  `hint` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`foreign_phrase`, `native_phrase`)
) ENGINE = InnoDB, CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `tests` (
  `test_type` ENUM('FN', 'NF'),
  `phrase_id` INT(11) UNSIGNED NOT NULL,
  `passages_cnt` INT(11) UNSIGNED DEFAULT 0 NOT NULL,
  `first_passage` datetime DEFAULT '1970-01-01 00:00:00' NOT NULL,
  `last_passage` datetime DEFAULT '1970-01-01 00:00:00' NOT NULL,
  PRIMARY KEY (`test_type`, `phrase_id`)
) ENGINE = InnoDB, CHARACTER SET = utf8;
