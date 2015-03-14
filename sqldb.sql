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

# DROP TABLE lab1.profile;
CREATE TABLE IF NOT EXISTS lab1.profile (
	profile_id	int NOT NULL AUTO_INCREMENT,
    user_id		int NOT NULL,
	firstname	varchar(128),
	lastname	varchar(128),
    private_key text,
    public_key	text,
	PRIMARY KEY (profile_id),
    FOREIGN KEY (user_id) REFERENCES lab1.user(user_id)
);
DROP TABLE lab1.transfers;
CREATE TABLE IF NOT EXISTS lab1.transfers (
	transfer_id	int NOT NULL AUTO_INCREMENT,
    sender_id	int NOT NULL,
    rcver_email varchar(128),
	#upl_file	text,
    signed_file text,
    #symm_key	text,
    enc_symmkey text,
    enc_file	text,
	PRIMARY KEY (transfer_id),
    FOREIGN KEY (sender_id) REFERENCES lab1.user(user_id)
);

# TESTING
SELECT * FROM lab1.user;
SELECT * FROM lab1.profile;
SELECT * FROM lab1.transfers;

SELECT user.pword FROM user WHERE user.email='neid.1993@gmail.com';

SELECT user.email, user.pword, user.verified FROM lab1.user WHERE email='neid.1993@gmail.com';

INSERT INTO user (email, pword) VALUES ('neid.1993@gmail.com', '$2y$10$y0KaOJbF5MIAB/d6j7MuaeNTAEADPV71DtWyA7TaQQXIm4P8sPb7m');
INSERT INTO lab1.profile (profile_id, user_id) VALUES ('2', '8');
INSERT INTO lab1.transfers (sender_id, signed_file) VALUES ('10', 'JA(����/J�R�嫂�y�Oن�l�,�����������8n��mf��=�6��[}���g��`H� 0*=6��9����M}� B;s��7G\�0��Ĩ��2�r��j������`�ھ�PkL�n�Č');

DELETE user FROM lab1.user WHERE user_id=13;

SELECT * FROM lab1.reset_requests;

SELECT reset_pword, expiry_time FROM lab1.reset_requests WHERE user_email='neid.1993@gmail.com' AND expiry_time>='2015-02-16';

UPDATE lab1.profile SET 
	private_key='-----BEGIN RSA PRIVATE KEY----- MIICXQIBAAKBgQDPXpCDSiqJ544pKUj079iEFPrA+lTOZ22Wq7JTTa5+4zGF0XGc G40/Eql+BvSTvRRjDBNbz8gD01px1fHwxbDQiS/SuecxmM/JmUQpwya1BquizETU pgN2V3Q0vEcjp3eKNr+zl+OOksaP/HiXh89cqpW9fpZe+Ft4rIkHmzCJ4wIDAQAB AoGAfpkSzLRYp/xPk916ht5uZqSQOYQahjAqfVOw+J5yK1D0iOfG3jEL2DfCdgg1 BIToj1dt8h011PARRXIB1KY8PNjD1hpbZyNGdrt0YoWftMn3QOp0+emhE3I9fS7r 7XPmJsL8nVTCabcvfpwIj0yBIAUiP09fFSzjktzXkuvs58ECQQD/4ip3nZCbhACU IapIcXDX6ALs2qLxuKzBXr2tHZeQ6SuwHcjU8KW6/LbgMtvzkaLgwGosBRDdPJnC TTN2CO+xAkEAz3a+AyN4127PXierA5TJERaOD7kzAXNVaJKLcSCjZGbPYuus/tBe MTElKjNcXTMJfDgQEKLOwJqhVYg23ORr0wJBANVxR7FNWpGOs5jc2Bjjn5hJrR77 ZU4ymNAYAioEdChph4q53YtaTTRDlxw+8GAlDHNjrWyYsS+KXEvKb/G2lJECQEY/ b8GCTlWsKL0581coFxkZKQs764BvPBlHnb21jn3drhVRtecmSO6hNHNgpsLMGEce eJoZdqaS9VQP5nvPQI8CQQDNn6mTaiz5yAcBApXqu6p8sBOPvG4PUEvRMCfXnRwM ar9lfj13tmVEu6joQchHSVWdC+eCEILtjORNfzf3Wnwd -----END RSA PRIVATE KEY-----',
    public_key='----BEGIN PUBLIC KEY----- MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDPXpCDSiqJ544pKUj079iEFPrA +lTOZ22Wq7JTTa5+4zGF0XGcG40/Eql+BvSTvRRjDBNbz8gD01px1fHwxbDQiS/S uecxmM/JmUQpwya1BquizETUpgN2V3Q0vEcjp3eKNr+zl+OOksaP/HiXh89cqpW9 fpZe+Ft4rIkHmzCJ4wIDAQAB -----END PUBLIC KEY-----' 
    WHERE profile_id='2';