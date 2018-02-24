# crudBasic

To get started run the following SQL commands:

CREATE DATABASE misc;
GRANT ALL ON misc.* TO 'fred'@'localhost' IDENTIFIED BY 'zap';
GRANT ALL ON misc.* TO 'fred'@'127.0.0.1' IDENTIFIED BY 'zap';

USE misc; (Or select misc in phpMyAdmin)

CREATE TABLE users (
   user_id INTEGER NOT NULL
     AUTO_INCREMENT PRIMARY KEY,
   name VARCHAR(128),
   email VARCHAR(128),
   password VARCHAR(128),
   INDEX(email)
) ENGINE=InnoDB CHARSET=utf8;


ALTER TABLE users ADD INDEX(password);

INSERT INTO users (name,email,password)
    VALUES ('Elvis','epresley@musicland.edu','1a52e17fa899cf40fb04cfc42e6352f1');

INSERT INTO users (name,email,password)
        VALUES ('Marilyn','mmonroe@whitehouse.gov','1a52e17fa899cf40fb04cfc42e6352f1');

INSERT INTO users (name,email,password)
                VALUES ('UMIS','umsi@umich.edu','1a52e17fa899cf40fb04cfc42e6352f1');

INSERT INTO users (name,email,password)
                VALUES ('guest','guest@mycompany.com','1a52e17fa899cf40fb04cfc42e6352f1');

CREATE TABLE Profile (
       profile_id INTEGER NOT NULL AUTO_INCREMENT,
       user_id INTEGER NOT NULL,
       first_name Text,
       last_name Text,
       email Text,
       headline Text,
       summary Text,

       PRIMARY KEY(profile_id),

       CONSTRAINT profile_ibfk_2
       FOREIGN KEY (user_id) REFERENCES users(user_id)
       ON DELETE CASCADE ON UPDATE CASCADE
       ) ENGINE=InnoDB CHARSET=utf8;

INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
    VALUES (1, 'Elvis', 'Presley', 'epresley@musicland.com', 'great singer', 'Changed America') ;

INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
            VALUES (1, 'Marilyn', 'Monroe', 'mmonroe@hollyland.com', 'great actress', 'America Icon, Changed the world.') ;

INSERT INTO Profiles (user_id, first_name, last_name, email, headline, summary)
                            VALUES (3, 'U', 'MSI', 'umsi@umich.edu', 'great coach', 'Inspiration to students') ;


CREATE TABLE Position (
position_id INTEGER NOT NULL AUTO_INCREMENT,
profile_id INTEGER,
rank INTEGER,
year INTEGER,
description TEXT,
PRIMARY KEY(position_id),

CONSTRAINT position_ibfk_1
FOREIGN KEY (profile_id)
REFERENCES Profile (profile_id)
ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE Institution (
institution_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
name VARCHAR(255),
UNIQUE(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



Many-to-Many table:

CREATE TABLE Education (
profile_id INTEGER,
institution_id INTEGER,
rank INTEGER,
year INTEGER,

CONSTRAINT education_ibfk_1
FOREIGN KEY (profile_id)
REFERENCES Profile (profile_id)
ON DELETE CASCADE ON UPDATE CASCADE,

CONSTRAINT education_ibfk_2
FOREIGN KEY (institution_id)
REFERENCES Institution (institution_id)
ON DELETE CASCADE ON UPDATE CASCADE,

PRIMARY KEY(profile_id, institution_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SQL Command Library:
UPDATE users SET password = 'eaa52980acd0bcfd0937ee5110c74817' WHERE user_id=1;
UPDATE users SET password = 'dc5317823d0034b07f17f19182523c76' WHERE user_id=2;

ALTER TABLE Profiles RENAME TO Profile;
