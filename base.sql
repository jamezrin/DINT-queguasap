DROP DATABASE IF EXISTS queguasap;
CREATE DATABASE queguasap;
USE queguasap;

CREATE TABLE usuarios (
    telefono VARCHAR(9) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    contrasena VARCHAR(255) NOT NUll,
    conectado BOOLEAN,
    imagen VARCHAR(100),
    color_fondo VARCHAR(7),
    estado VARCHAR(100),
    CONSTRAINT PK_Usuario PRIMARY KEY (telefono)
);

CREATE TABLE envia_mensaje (
    id INTEGER AUTO_INCREMENT NOT NULL,
    emisor VARCHAR(9) NOT NULL,
    receptor VARCHAR(9) NOT NULL,
    texto VARCHAR(255) NOT NULL,
    momento TIMESTAMP NOT NULL,
    archivo VARCHAR(100),
    CONSTRAINT PK_Mensaje PRIMARY KEY (id),
    FOREIGN KEY (emisor) REFERENCES usuarios(telefono),
    FOREIGN KEY (receptor) REFERENCES usuarios(telefono)
);

/*
La contraseña de estos usuarios es "admin"
bcrypt("admin") = $2y$10$d2.G3WgipvpjqtKn5Lm3N.txF3AwdZiYvSL7qPFxlBbFpRdRknr6W
*/

INSERT INTO usuarios (telefono, nombre, contrasena, conectado, estado) VALUES ("901234536", "Iñaki", "$2y$10$d2.G3WgipvpjqtKn5Lm3N.txF3AwdZiYvSL7qPFxlBbFpRdRknr6W", false, "Hello there I am using queguasap");
INSERT INTO usuarios (telefono, nombre, contrasena, conectado, estado) VALUES ("993726392", "Jaime", "$2y$10$d2.G3WgipvpjqtKn5Lm3N.txF3AwdZiYvSL7qPFxlBbFpRdRknr6W", false, "Hello there I am using queguasap");
INSERT INTO usuarios (telefono, nombre, contrasena, conectado, estado) VALUES ("846378284", "Robert", "$2y$10$d2.G3WgipvpjqtKn5Lm3N.txF3AwdZiYvSL7qPFxlBbFpRdRknr6W", false, "Hello there I am using queguasap");
INSERT INTO usuarios (telefono, nombre, contrasena, conectado, estado) VALUES ("846264826", "Miguel", "$2y$10$d2.G3WgipvpjqtKn5Lm3N.txF3AwdZiYvSL7qPFxlBbFpRdRknr6W", false, "Hello there I am using queguasap");
INSERT INTO usuarios (telefono, nombre, contrasena, conectado, estado) VALUES ("946589467", "Alex", "$2y$10$d2.G3WgipvpjqtKn5Lm3N.txF3AwdZiYvSL7qPFxlBbFpRdRknr6W", false, "Hello there I am using queguasap");
INSERT INTO usuarios (telefono, nombre, contrasena, conectado, estado) VALUES ("957355483", "Hector", "$2y$10$d2.G3WgipvpjqtKn5Lm3N.txF3AwdZiYvSL7qPFxlBbFpRdRknr6W", false, "Hello there I am using queguasap");
INSERT INTO usuarios (telefono, nombre, contrasena, conectado, estado) VALUES ("664739443", "Carlos", "$2y$10$d2.G3WgipvpjqtKn5Lm3N.txF3AwdZiYvSL7qPFxlBbFpRdRknr6W", false, "Hello there I am using queguasap");
INSERT INTO usuarios (telefono, nombre, contrasena, conectado, estado) VALUES ("434525244", "Luis", "$2y$10$d2.G3WgipvpjqtKn5Lm3N.txF3AwdZiYvSL7qPFxlBbFpRdRknr6W", false, "Hello there I am using queguasap");

INSERT INTO envia_mensaje (emisor, receptor, texto, momento) VALUES ("901234536", "993726392", "Hey bro que tal", CURRENT_TIMESTAMP());
INSERT INTO envia_mensaje (emisor, receptor, texto, momento) VALUES ("901234536", "993726392", "Hey bro que tal", CURRENT_TIMESTAMP());
INSERT INTO envia_mensaje (emisor, receptor, texto, momento) VALUES ("901234536", "993726392", "Hey bro que tal", CURRENT_TIMESTAMP());
INSERT INTO envia_mensaje (emisor, receptor, texto, momento) VALUES ("901234536", "993726392", "Hey bro que tal", CURRENT_TIMESTAMP());
INSERT INTO envia_mensaje (emisor, receptor, texto, momento) VALUES ("901234536", "993726392", "Hey bro que tal", CURRENT_TIMESTAMP());
INSERT INTO envia_mensaje (emisor, receptor, texto, momento) VALUES ("846264826", "957355483", "profe no pongas falta", CURRENT_TIMESTAMP());
INSERT INTO envia_mensaje (emisor, receptor, texto, momento) VALUES ("946589467", "901234536", "un rainbow??", CURRENT_TIMESTAMP());
INSERT INTO envia_mensaje (emisor, receptor, texto, momento) VALUES ("957355483", "993726392", "oye porque haces esto asi", CURRENT_TIMESTAMP());
