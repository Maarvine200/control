CREATE DATABASE control;

USE control;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    email VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    rol ENUM('administrador', 'profesor')
);

CREATE TABLE docentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    materia VARCHAR(50)
);

CREATE TABLE alumnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id INT,
    docente_id INT,
    nota TINYINT(1), -- 0 = Reprobó, 1 = Aprobó
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id),
    FOREIGN KEY (docente_id) REFERENCES docentes(id)
);