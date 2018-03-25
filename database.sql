CREATE DATABASE pimereni;
USE pimereni;

CREATE TABLE `mereni`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `hodnota` DOUBLE NOT NULL,
    `time` INT(4) UNSIGNED,
    PRIMARY KEY(`id`)
) ENGINE = InnoDB;	
