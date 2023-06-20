CREATE DATABASE IF NOT EXISTS hospitalmanagement;

USE hospitalmanagement;

-- Tabellenstruktur für patient

CREATE TABLE patient (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    street VARCHAR(255),
    zip INT,
    place VARCHAR(255)
);

-- Tabllenstruktur für Befund

CREATE TABLE record (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    patient_id INT,
    description MEDIUMTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patient(id)
);

-- Tabellenstruktur für Termin

CREATE TABLE appointment (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    patient_id INT,
    start DATETIME,
    end DATETIME,
    FOREIGN KEY (patient_id) REFERENCES patient(id)
);
