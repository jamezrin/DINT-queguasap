DROP DATABASE IF EXISTS queguasap;
CREATE DATABASE queguasap;
USE queguasap;

CREATE TABLE usuarios (
    telefono VARCHAR(9),
    nombre VARCHAR(50),
    contrasena VARCHAR(50),
    conectado BOOLEAN,
    imagen VARCHAR(100),
    color_fondo VARCHAR(7),
    estado VARCHAR(100),
    PRIMARY KEY (telefono)
);

CREATE TABLE envia_mensaje (
    emisor VARCHAR(9),
    receptor VARCHAR(9),
    texto VARCHAR(255),
    fecha DATE,
    archivo VARCHAR(100),
    CONSTRAINT PK_Mensaje PRIMARY KEY (emisor, receptor),
    FOREIGN KEY (emisor) REFERENCES usuarios(telefono),
    FOREIGN KEY (receptor) REFERENCES usuarios(telefono)
);

INSERT INTO usuarios VALUES ("901234536", "Iñaki", NULL, false, NULL, "#0000FF", "Hello there I am using queguasap");
INSERT INTO usuarios VALUES ("993726392", "Jaime", NULL, false, NULL, "#317F43", "Hello there I am using queguasap");
INSERT INTO usuarios VALUES ("846378284", "Robert", NULL, true, NULL, "#317F43", "Hello there I am using queguasap");
INSERT INTO usuarios VALUES ("846264826", "Miguel", NULL, false, NULL, "#317F43", "Hello there I am using queguasap");
INSERT INTO usuarios VALUES ("946589467", "Alex", NULL, false, NULL, "#317F43", "Hello there I am using queguasap");
INSERT INTO usuarios VALUES ("957355483", "Hector", NULL, true, NULL, "#317F43", "Hello there I am using queguasap");
INSERT INTO usuarios VALUES ("664739443", "Carlos", NULL, false, NULL, "#317F43", "Hello there I am using queguasap");
INSERT INTO usuarios VALUES ("434525244", "Luis", NULL, true, NULL, "#FF0000", "Hello there I am using queguasap");

INSERT INTO envia_mensaje VALUES ("901234536", "993726392", "Hey bro que tal", CURDATE(), NULL);
INSERT INTO envia_mensaje VALUES ("846264826", "957355483", "profe no pongas falta", CURDATE(), NULL);
INSERT INTO envia_mensaje VALUES ("946589467", "901234536", "un rainbow??", CURDATE(), NULL);
INSERT INTO envia_mensaje VALUES ("957355483", "993726392", "oye porque haces esto asi", CURDATE(), NULL);
