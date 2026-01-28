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

-- Peuplement de la base de données

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

-- PEUPLEMENT DE LA BASE DE DONNÉE

-- 1. PEUPLEMENT DES UTILISATEURS
-- Mots de passe hachés en BCRYPT (mot de passe : "password123")
INSERT INTO users (username, email, password_hash, role) VALUES
('Tony_InCo', 'tony@inco.fr', '$2y$10$qPPQZwoaWEOrR94dMo.PIeLrVpNgce6EpJtEQ.5U65.xH89ShfBGq', 'admin'),
('Paul_Arthure', 'paul@laser.com', '$2y$10$qPPQZwoaWEOrR94dMo.PIeLrVpNgce6EpJtEQ.5U65.xH89ShfBGq', 'user'),
('Maker_Julie', 'julie@fablab.fr', '$2y$10$qPPQZwoaWEOrR94dMo.PIeLrVpNgce6EpJtEQ.5U65.xH89ShfBGq', 'user');

-- 2. PEUPLEMENT DES MACHINES
INSERT INTO machines (name, model, laser_type, is_mopa, brand) VALUES
('B6 MOPA', 'M7', 'FIBRE', TRUE, 'Commarker'),
('Raycus 30W', 'S-Series', 'FIBRE', FALSE, 'Raycus'),
('xTool D1 Pro', 'D1', 'DIODE', FALSE, 'xTool'),
('OMTech 60W', 'CO2-60', 'CO2', FALSE, 'OMTech');

-- 3. PEUPLEMENT DES MATÉRIAUX
INSERT INTO materials (name, category, color, thickness) VALUES
('Inox 304L', 'Métal', 'Gris', 2.00),
('Contreplaqué Bouleau', 'Bois', 'Clair', 3.00),
('Acrylique Noir', 'Plastique', 'Noir', 5.00),
('Aluminium Anodisé', 'Métal', 'Noir', 1.00),
('Cuir Naturel', 'Peau', 'Marron', 2.50);

-- 4. PEUPLEMENT DES TESTS (Fiches visibles sur la Home)
INSERT INTO tests (title, user_id, machine_id, material_id, image, description, status) VALUES
('Marquage Bleu Profond Inox', 1, 1, 1, 'test_65b2a1c3.jpg', 'Rendu obtenu avec une fréquence haute. Très stable sur Inox 304.', 'published'),
('Gravure Photo sur Bois', 2, 4, 2, 'test_88f2e4a1.webp', 'Réglage optimisé pour éviter les brûlures excessives.', 'published'),
('Marquage Profond Aluminium', 1, 2, 4, 'test_22c3d5b2.png', '3 passes pour obtenir un relief tactile.', 'published'),
('Découpe Acrylique Propre', 3, 4, 3, 'test_44d1a9e8.jpg', 'Vitesse lente pour une tranche bien lisse et brillante.', 'published');

-- 5. PEUPLEMENT DES PARAMÈTRES (Liés aux tests ci-dessus)
INSERT INTO parameters (test_id, speed, power, frequency, pulse, z_offset, nb_passes, sweep, hatch, row_interval, wobble) VALUES
(1, 800, 35, 45, 200, -1.00, 1, 'bidirectional', TRUE, 0.0500, FALSE),
(2, 2500, 20, 25, NULL, 0.00, 1, 'bidirectional', FALSE, 0.0800, FALSE),
(3, 400, 80, 20, NULL, 0.00, 3, 'monodirectional', TRUE, 0.0300, TRUE),
(4, 150, 95, 20, NULL, 0.50, 1, 'monodirectional', FALSE, 0.1000, FALSE);

-- 6. PEUPLEMENT DES COMMENTAIRES
INSERT INTO comments (test_id, user_id, content) VALUES
(1, 2, 'Incroyable le bleu ! Je vais tester sur mon Mopa 20W.'),
(1, 3, 'Est-ce que tu as nettoyé la pièce avant ?'),
(2, 1, 'Superbe rendu. Attention à la fumée sur le bouleau.');