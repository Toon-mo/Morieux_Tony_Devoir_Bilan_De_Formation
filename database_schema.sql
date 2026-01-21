-- Database Schema for "Forum Gravure Laser" Application--
-- This schema defines the tables and relationships for managing
-- users, machines, materials, tests, parameters, comments, and results.

-- Tables users
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    avatar VARCHAR(255),
    role ENUM('admin', 'moderator', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tables machines
CREATE TABLE machines (
    machine_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    laser_type ENUM('FIBRE', 'CO2', 'DIODE', 'OTHER') NOT NULL,
    is_mopa BOOLEAN DEFAULT FALSE,
    brand VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tables materials
CREATE TABLE materials (
    material_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL, -- For example: wood, steel, brass, silver...
    color VARCHAR(50), -- Only if applicable
    thickness DECIMAL(5,2), -- Only if applicable (in mm)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tables test
CREATE TABLE tests (
    test_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    machine_id INT NOT NULL,
    material_id INT NOT NULL,
    image VARCHAR(255),
    description TEXT,
    status ENUM('draft', 'published', 'validated') DEFAULT 'draft',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (machine_id) REFERENCES machines(machine_id),
    FOREIGN KEY (material_id) REFERENCES materials(material_id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tables parameters
CREATE TABLE parameters (
    parameter_id INT PRIMARY KEY AUTO_INCREMENT,
    test_id INT NOT NULL,
    speed INT NOT NULL, -- in mm/s
    power INT NOT NULL, -- in percentage
    frequency INT NOT NULL, -- in KHz
    pulse INT, -- Only for mopa (ns)
    z_offset DECIMAL(5,2), -- Only not mopa (mm)
    nb_passes INT NOT NULL,
    sweep ENUM('monodirectional', 'bidirectional') NOT NULL, -- Monodirectional, bidirectional.
    hatch BOOLEAN DEFAULT FALSE, -- TRUE = Quadrillage (Croisé)
    wobble BOOLEAN DEFAULT FALSE,
    row_interval DECIMAL (5,4), -- in(mm)
    FOREIGN KEY (test_id) REFERENCES tests(test_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tables comments
CREATE TABLE comments (
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    test_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    FOREIGN KEY (test_id) REFERENCES tests(test_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);