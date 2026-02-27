CREATE DATABASE mensajeria_ci;
USE mensajeria_ci;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    reset_token VARCHAR(255),
    reset_expira DATETIME,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE conversaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario1_id INT NOT NULL,
    usuario2_id INT NOT NULL,
    created_at DATETIME,

    FOREIGN KEY (usuario1_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario2_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversacion_id INT NOT NULL,
    remitente_id INT NOT NULL,
    mensaje TEXT,
    mensaje_cifrado TEXT,
    archivo VARCHAR(255),
    tipo ENUM('texto','imagen','video') DEFAULT 'texto',
    created_at DATETIME,

    FOREIGN KEY (conversacion_id) REFERENCES conversaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE archivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    ruta VARCHAR(255),
    tipo VARCHAR(50),
    tamaño INT,
    created_at DATETIME,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);