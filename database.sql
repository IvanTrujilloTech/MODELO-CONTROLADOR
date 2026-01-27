USE finanzas_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    tipo ENUM('ingreso', 'gasto'),
    categoria VARCHAR(50),
    monto DECIMAL(10,2),
    descripcion TEXT,
    fecha DATE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE inversiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    empresa VARCHAR(100),
    cantidad INT,
    precio_compra DECIMAL(10,2),
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    message TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES usuarios(id),
    FOREIGN KEY (receiver_id) REFERENCES usuarios(id)
);

CREATE TABLE transfers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    recipient_id INT,
    amount DECIMAL(10,2),
    description TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES usuarios(id),
    FOREIGN KEY (recipient_id) REFERENCES usuarios(id)
);