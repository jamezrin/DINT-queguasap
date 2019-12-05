create database queguasap;

use queguasap;

create table usuarios(telefono varchar(9), nombre varchar(50), contrasena varchar(50), conectado boolean, imagen 
varchar(100), color_fondo varchar(7), estado varchar(20), PRIMARY KEY (telefono);

create table envia_mensaje (emisor varchar(9), receptor varchar(9), texto varchar(255), fecha date, 
archivo varchar(100), CONSTRAINT PK_Mensaje PRIMARY KEY (emisor, receptor), 
FOREIGN KEY (emisor) REFERENCES usuarios(telefono), FOREIGN KEY (receptor) REFERENCES usuarios(telefono));

