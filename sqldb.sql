# CREATE DB
CREATE DATABASE lab1;

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

CREATE TABLE IF NOT EXISTS lab1.profile (
	profile_id	int NOT NULL AUTO_INCREMENT,
    user_id		int NOT NULL,
	firstname	varchar(128) NOT NULL default '',
	lastname	varchar(128) NOT NULL default '',
    private_key varchar(128) NOT NULL default '',
    public_key	varchar(128) NOT NULL default '',
	PRIMARY KEY (profile_id),
    FOREIGN KEY (user_id) REFERENCES lab1.user(user_id)
);

# DROPS
#DROP TABLE lab1.profile;

# TESTING
SELECT * FROM lab1.user;
SELECT * FROM lab1.profile;

SELECT user.pword FROM user WHERE user.email='neid.1993@gmail.com';

SELECT user.email, user.pword, user.verified FROM lab1.user WHERE email='neid.1993@gmail.com';

INSERT INTO user (email, pword) VALUES ('neid.1993@gmail.com', '$2y$10$y0KaOJbF5MIAB/d6j7MuaeNTAEADPV71DtWyA7TaQQXIm4P8sPb7m');
INSERT INTO lab1.profile (user_id) VALUES ('7');

DELETE user FROM lab1.user WHERE user_id=13;

SELECT * FROM lab1.reset_requests;

SELECT reset_pword, expiry_time FROM lab1.reset_requests WHERE user_email='neid.1993@gmail.com' AND expiry_time>='2015-02-16';