DROP TABLE IF EXISTS multimedia;

CREATE TABLE multimedia
(
   idmultimedia      INT            NOT NULL AUTO_INCREMENT,
   nombremultimedia  VARCHAR(200)   NOT NULL,
   urlmultimedia     VARCHAR(200)   NOT NULL,
   extension         CHAR(20)       NOT NULL,
   urlmultimediaget  VARCHAR(200)   NOT NULL,
   idremote          VARCHAR(200)   NOT NULL,
   fechaalta         DATETIME       NOT NULL,
   idusumodifica     INT,
   fechamodifica     DATETIME,
   PRIMARY KEY (idmultimedia)
)
ENGINE=InnoDB;