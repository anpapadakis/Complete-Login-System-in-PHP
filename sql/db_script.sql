CREATE DATABASE AdminPanel;

CREATE TABLE AdminPanel.User (
   ID int(11) NOT NULL AUTO_INCREMENT,
   Username varchar(100) CHARACTER SET utf8 NOT NULL UNIQUE,
   Email varchar(100) CHARACTER SET utf8 NOT NULL UNIQUE,
   Password text CHARACTER SET utf8 NOT NULL,
   DateOfBirth date NOT NULL,
   Photo varchar(255) CHARACTER SET utf8 DEFAULT NULL,
   VerificationCode varchar(255) CHARACTER SET utf8 NOT NULL,
   Verified tinyint(1) NOT NULL DEFAULT 0,
   ResetPasswordHash varchar(255) CHARACTER SET utf8 DEFAULT NULL,
     PRIMARY KEY (ID)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
