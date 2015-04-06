# CREATE DB
CREATE DATABASE lab1;

SET CHARACTER SET utf8;

# CREATE TABLES
CREATE TABLE IF NOT EXISTS lab1.user (
	user_id		int NOT NULL AUTO_INCREMENT,
	email		varchar(128) NOT NULL UNIQUE,
	pword		varchar(128) NOT NULL,
	verified	bit(1) NOT NULL DEFAULT 0,
	PRIMARY KEY(user_id)
);

CREATE TABLE IF NOT EXISTS lab1.reset_requests (
	reset_id	int NOT NULL AUTO_INCREMENT,
	reset_pword	varchar(128) NOT NULL,
	expiry_time DATETIME NOT NULL,
	user_email	varchar(128) NOT NULL,
	PRIMARY KEY(reset_id)
);

DROP TABLE lab1.transfers;

#DROP TABLE lab1.profile;
CREATE TABLE IF NOT EXISTS lab1.profile (
	profile_id	int NOT NULL AUTO_INCREMENT,
    user_id		int NOT NULL,
	firstname	varchar(128),
	lastname	varchar(128),
    public_key	text,
    age			int,
	PRIMARY KEY (profile_id),
    FOREIGN KEY (user_id) REFERENCES lab1.user(user_id)
);

#DROP TABLE lab1.user_access;
CREATE TABLE IF NOT EXISTS lab1.user_access (
	owner_id	int NOT NULL,
    viewer_id	int NOT NULL,
    FOREIGN KEY (owner_id) REFERENCES lab1.user(user_id),
    FOREIGN KEY (viewer_id) REFERENCES lab1.user(user_id)
);

# TESTING
SELECT * FROM lab1.user;
SELECT * FROM lab1.profile;
SELECT * FROM lab1.transfers;
SELECT * FROM lab1.user_access;

SELECT user.pword FROM user WHERE user.email='neid.1993@gmail.com';

SELECT user.email, user.pword, user.verified FROM lab1.user WHERE email='neid.1993@gmail.com';

INSERT INTO user (email, pword) VALUES ('neid.1993@gmail.com', '$2y$10$y0KaOJbF5MIAB/d6j7MuaeNTAEADPV71DtWyA7TaQQXIm4P8sPb7m');
INSERT INTO lab1.profile (profile_id, user_id) VALUES ('2', '8');
INSERT INTO lab1.transfers (sender_id, signed_file) VALUES ('10', 'JA(����/J�R�嫂�y�Oن�l�,�����������8n��mf��=�6��[}���g��`H� 0*=6��9����M}� B;s��7G\�0��Ĩ��2�r��j������`�ھ�PkL�n�Č');

DELETE FROM lab1.user WHERE user.user_id=14;

SELECT * FROM lab1.reset_requests;

SELECT reset_pword, expiry_time FROM lab1.reset_requests WHERE user_email='neid.1993@gmail.com' AND expiry_time>='2015-02-16';

UPDATE lab1.profile SET 
    public_key='----BEGIN PUBLIC KEY----- MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDPXpCDSiqJ544pKUj079iEFPrA +lTOZ22Wq7JTTa5+4zGF0XGcG40/Eql+BvSTvRRjDBNbz8gD01px1fHwxbDQiS/S uecxmM/JmUQpwya1BquizETUpgN2V3Q0vEcjp3eKNr+zl+OOksaP/HiXh89cqpW9 fpZe+Ft4rIkHmzCJ4wIDAQAB -----END PUBLIC KEY-----' 
    WHERE profile_id='2';