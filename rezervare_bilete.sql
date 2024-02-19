-- Crearea bazei de date `rezerva_bilete` (modificată pentru free webhosting)
CREATE DATABASE IF NOT EXISTS id21893952_rezerva_bilete; 
USE id21893952_rezerva_bilete;

-- Crearea tabelului `utilizatori`
CREATE TABLE IF NOT EXISTS utilizatori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nume VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
	tip VARCHAR(255) NOT NULL,
    parola VARCHAR(255) NOT NULL,
    creat_la TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crearea tabelului `evenimente` (sau `bilete`)
CREATE TABLE IF NOT EXISTS evenimente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titlu VARCHAR(255) NOT NULL,
    descriere TEXT,
    data_evenimentului DATE NOT NULL,
    locatie VARCHAR(255) NOT NULL,
    pret DECIMAL(10, 2) NOT NULL,
    cantitate_disponibila INT NOT NULL,
    creat_la TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crearea tabelului `rezervari`
CREATE TABLE IF NOT EXISTS rezervari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilizator INT NOT NULL,
    id_eveniment INT NOT NULL,
    cantitate INT NOT NULL,
    creat_la TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilizator) REFERENCES utilizatori(id),
    FOREIGN KEY (id_eveniment) REFERENCES evenimente(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
