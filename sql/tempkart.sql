DROP TABLE IF EXISTS products;
CREATE TABLE products
(
  id 			SERIAL           PRIMARY KEY,
  title         VARCHAR(255)   	NOT NULL,
  price   		DECIMAL  		NOT NULL,
  list_price    DECIMAL			NOT NULL,
  brand   		INT,
  categories	INT,
  image			VARCHAR(255),
  description   VARCHAR(255),
  featured		INT     NOT NULL DEFAULT '0',
  sizes			VARCHAR(40),
  deleted		INT   NOT NULL DEFAULT '0'
);

DROP TABLE IF EXISTS brand;
CREATE TABLE brand
(
  id 			SERIAL          PRIMARY KEY ,
  brand         VARCHAR(255)   	NOT NULL
);

DROP TABLE IF EXISTS categories;
CREATE TABLE categories
(
  id 			SERIAL       	 PRIMARY KEY,
  category      VARCHAR(255)   	NOT NULL,
  parent		INT 
);

DROP TABLE IF EXISTS users;
CREATE TABLE users
(
   id		    SERIAL     PRIMARY KEY,
   full_name	VARCHAR(255)	NOT NULL,
   email		VARCHAR(255)	NOT NULL,
   password		VARCHAR(255)	NOT NULL,
   join_date	TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
   last_login	TIMESTAMP		NOT NULL,
   permission	VARCHAR(255)    NOT NULL	
);

DROP TABLE IF EXISTS cart;
CREATE TABLE cart
(
	id			SERIAL     PRIMARY KEY,
	items		VARCHAR(255)  NOT NULL,
	expire_date	TIMESTAMP	  NOT NULL,
	paid		INT			DEFAULT 0

);

DROP TABLE IF EXISTS transactions;
CREATE TABLE transactions
(
	id			SERIAL     PRIMARY KEY,
	charge_id	VARCHAR(255)  NOT NULL,
	cart_id		INT	  NOT NULL,
	full_name	VARCHAR(255)	NOT NULL,
	email		VARCHAR(255)	NOT NULL,
	street	    VARCHAR(255)	NOT NULL,
	street2 	VARCHAR(255)	NOT NULL,
    city		VARCHAR(175)	NOT NULL,
	state		VARCHAR(175)	NOT NULL,
	zip_code	VARCHAR(50)		NOT NULL,
    country		VARCHAR(175)	NOT NULL,
	sub_total	DECIMAL(10,2)	NOT NULL,
	tax			DECIMAL(10,2)	NOT NULL,
	grand_total	DECIMAL(10,2)	NOT NULL,
	description VARCHAR(255)	NOT NULL,
	txn_type	VARCHAR(255)	NOT NULL,
	txn_date	TIMESTAMP	DEFAULT CURRENT_TIMESTAMP NOT NULL,
	shipped 	SMALLINT     DEFAULT 0

);