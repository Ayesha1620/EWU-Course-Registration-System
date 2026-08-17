-- ============================================================
-- Login system migration
-- Original dump (ewu_course_registration) import করার পর এটা run করবে।
--  1) student ও faculty table-এ Password column add হয়
--  2) auth_tokens table তৈরি হয় (login token save করার জন্য)
-- ============================================================

USE `ewu_course_registration`;

ALTER TABLE `student`
    ADD COLUMN `Password` VARCHAR(255) DEFAULT NULL AFTER `Email`;

ALTER TABLE `faculty`
    ADD COLUMN `Password` VARCHAR(255) DEFAULT NULL AFTER `Email`;

CREATE TABLE IF NOT EXISTS `auth_tokens` (
    `TokenID`      int(11)                  NOT NULL AUTO_INCREMENT,
    `token`        varchar(64)              NOT NULL,
    `user_type`    enum('student','faculty') NOT NULL,
    `reference_id` int(11)                  NOT NULL,
    `created_at`   datetime                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at`   datetime                 NOT NULL,
    PRIMARY KEY (`TokenID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;