-- SQL COMPLETO PARA TESTEAR IA DE RECOMENDACIONES
-- Fecha de generación: 2025-08-04 20:31:45
-- Modificado para incluir 'video_url' y 'codigo_qr'

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de datos: tienda_videojuegos
DROP DATABASE IF EXISTS tienda_videojuegos;
CREATE DATABASE tienda_videojuegos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE tienda_videojuegos;

-- Tabla juegos
CREATE TABLE juegos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT NOT NULL,
  categoria ENUM('Accion','Aventura','Estrategia','Deportes') NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  imagen_url TEXT DEFAULT NULL,
  video_url VARCHAR(255) DEFAULT NULL,
  calificacion_promedio FLOAT DEFAULT 0,
  cantidad_resenas INT(11) DEFAULT 0,
  PRIMARY KEY (id)
);

-- Insertar datos con la nueva columna 'video_url'
INSERT INTO juegos (nombre, descripcion, categoria, precio, imagen_url, video_url) VALUES
('Call of Duty', 'Sumérgete en un épico shooter en primera persona que te transporta a intensos escenarios de guerra. Experimenta combates frenéticos, una narrativa cinematográfica y un multijugador competitivo que te mantendrá al borde del asiento.', 'Accion', 999.00, 'https://cdn2.steamgriddb.com/thumb/abc08456cfdc28e5ae109e8898eabf29.jpg', 'https://www.youtube.com/embed/YUrUDrjOFYY'),
('Call of Duty 2', 'Revive los momentos más icónicos de la Segunda Guerra Mundial en este aclamado shooter. Lucha en los frentes de Europa del Este, África del Norte y el Frente Occidental con gráficos detallados y una IA avanzada que te hará sentir el caos de la batalla.', 'Accion', 213.43, 'https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/2630/header.jpg', 'https://www.youtube.com/embed/FjIe0Lp9w0Y'),
('FIFA 23', 'Siente la emoción del deporte rey con este simulador de fútbol de última generación. Con una jugabilidad hiperrealista y licencias oficiales, crea tu equipo de ensueño, compite en ligas de élite y domina el campo con un control total.', 'Deportes', 899.00, 'https://www.mundodeportivo.com/files/article_gallery_microformat/uploads/2022/07/19/62d6b69ef2d59.jpeg', 'https://www.youtube.com/embed/0F1Rj250328'),
('Minecraft', 'Explora un vasto mundo generado por bloques en este icónico juego de construcción. Desata tu creatividad construyendo estructuras gigantescas, sobrevive a los peligros de la noche y aventúrate a descubrir secretos en un mundo infinito.', 'Aventura', 299.00, 'https://i.pinimg.com/1200x/d2/45/9d/d2459de3246eb8a7d3504903d200f26c.jpg', 'https://www.youtube.com/embed/MmB9b5gknHs'),
('The Witcher 3', 'Embárcate en una aventura épica como Geralt de Rivia, un cazador de monstruos a sueldo. Explora un vasto mundo abierto lleno de misiones complejas, personajes inolvidables y decisiones morales que afectarán el destino del mundo.', 'Aventura', 399.00, 'https://media.vandal.net/t200/89975/the-witcher-3-wild-hunt-20231210553239_1.jpg', 'https://www.youtube.com/embed/c0i88t0Kacs'),
('Elden Ring', 'Enfréntate a un desafío monumental en este juego de rol de acción y fantasía oscura. Descubre los misterios de las Tierras Intermedias, derrota a poderosos jefes y forja tu propio camino en un mundo abierto que castiga y recompensa por igual.', 'Accion', 899.00, 'https://m.media-amazon.com/images/M/MV5BZGQxMjYyOTUtNjYyMC00NzdmLWI4YmYtMDhiODU3Njc5ZDJkXkEyXkFqcGc@._V1_QL75_UX190_CR0,2,190,281_.jpg', 'https://www.youtube.com/embed/K_03kFqWfGs'),
('Civilization VI', 'Forja un imperio que resista el paso del tiempo en este legendario juego de estrategia por turnos. Funda una civilización, desarrolla tecnologías, explora el mundo y derrota a tus rivales para convertirte en el líder supremo.', 'Estrategia', 299.00, 'https://cdn1.epicgames.com/cd14dcaa4f3443f19f7169a980559c62/offer/EGS_SidMeiersCivilizationVI_FiraxisGames_S1-2560x1440-2fcd1c150ac6d8cdc672ae042d2dd179.jpg', 'https://www.youtube.com/embed/5FcI8m3kY3c'),
('Zelda: Breath of the Wild', 'Vive una aventura sin precedentes en un vasto mundo abierto. Explora el reino de Hyrule, descubre sus misterios y utiliza la física y la química para resolver puzles mientras te preparas para enfrentarte a la amenaza de Ganon.', 'Aventura', 799.00, 'https://sm.ign.com/ign_es/screenshot/default/zelda-wii-u-3441758_vngx.jpg', 'https://www.youtube.com/embed/zw47_q9wbP4'),
('Hollow Knight', 'Descubre las profundidades de un reino insecto en decadencia en este desafiante Metroidvania. Domina el combate de precisión, explora laberintos subterráneos y desentraña una historia sombría y bellamente narrada.', 'Aventura', 249.00, 'https://sm.ign.com/ign_latam/blogroll/h/hollow-kni/hollow-knight-sold-over-250000-copies-on-switch-in-its-first_pnze.jpg', 'https://www.youtube.com/embed/pD4X2o-yH2o'),
('Rocket League', 'Una combinación explosiva de fútbol y carreras de coches. Domina la física de los autos cohete, realiza acrobacias aéreas y anota goles espectaculares en partidos multijugador caóticos y llenos de adrenalina.', 'Deportes', 199.00, 'https://www.rocketleague.com/images/keyart/rl_evergreen.jpg', 'https://www.youtube.com/embed/SgJ3kK8Jj1c'),
('Age of Empires IV', 'Recrea la historia en tiempo real. Lidera poderosas civilizaciones, construye vastos imperios y participa en batallas épicas que cambiarán el curso de la historia, desde la Edad Media hasta el Renacimiento.', 'Estrategia', 399.00, 'https://assets.xboxservices.com/assets/2d/c7/2dc7eb1d-3378-42df-95e6-b733df84ab6b.jpg?n=AoE-IV_Gallery-0_1350x759_03.jpg', 'https://www.youtube.com/embed/hN6R4F4W4i0'),
('God of War', 'Emprende un viaje épico a través de la mitología nórdica como Kratos, el dios de la guerra. En una historia profunda y emotiva, descubre un nuevo capítulo en la vida de Kratos, ahora con su hijo Atreus, en un mundo brutal y lleno de criaturas míticas.', 'Accion', 749.00, 'https://upload.wikimedia.org/wikipedia/en/a/a7/God_of_War_4_cover.jpg', 'https://www.youtube.com/embed/K0u_k-P8c0Q'),
('Overwatch 2', 'Únete a un equipo de héroes futuristas y compite en un vibrante shooter de acción. Con una variedad de personajes únicos y habilidades especiales, coordínate con tu equipo para superar al enemigo en modos de juego dinámicos.', 'Accion', 0.00, 'https://www.nintendo.com/eu/media/images/10_share_images/games_15/nintendo_switch_download_software_1/2x1_NSwitchDS_Overwatch2_Season6_image1600w.png', 'https://www.youtube.com/embed/d_K2a8p9U5M'),
('Forza Horizon 5', 'Disfruta de la máxima libertad al volante en un espectacular mundo abierto ambientado en México. Explora paisajes impresionantes, compite en carreras de alta velocidad y personaliza cientos de coches de ensueño.', 'Deportes', 659.00, 'https://image.api.playstation.com/vulcan/ap/rnd/202501/2717/42b3ee6b1b2094212231b0b0a82824f687fc5c4dc9bde31c.png', 'https://www.youtube.com/embed/nNqB8aXvP2Y'),
('Stardew Valley', 'Escapa de la vida de la ciudad para empezar de nuevo en una granja heredada. Cultiva cosechas, cría animales, pesca, explora cuevas misteriosas y hazte amigo de los habitantes del pueblo en este encantador simulador de vida rural.', 'Estrategia', 139.00, 'https://m.media-amazon.com/images/I/81nkc6OQ9TL._UF350,350_QL80_.jpg', 'https://www.youtube.com/embed/otGmtI23JkM');

-- Tabla usuarios
CREATE TABLE usuarios (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nombre_completo VARCHAR(255) NULL,
  fecha_nacimiento DATE NULL,
  direccion VARCHAR(255) NULL,
  PRIMARY KEY (id)
);

INSERT INTO usuarios (nombre, email, contrasena) VALUES
('Lozano', 'jona2@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Admin', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Lara', 'lara@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Mario', 'mario@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jonathan', 'jona@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Sofía', 'sofia@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Juan', 'juan@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Ana', 'ana@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Carlos', 'carlos@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');


-- Comando para crear la tabla desde cero
CREATE TABLE tickets_soporte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    motivo VARCHAR(50) NOT NULL,
    mensaje TEXT NOT NULL,
    usuario_id INT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);


-- Si deseas agregar algunos datos de prueba para los tickets
INSERT INTO tickets_soporte (nombre, email, motivo, mensaje) VALUES
('Juan Pérez', 'juan.perez@email.com', 'problema_compra', 'Tuve un problema con el código de mi última compra de Call of Duty.'),
('Ana Gómez', 'ana.gomez@email.com', 'sugerencia', 'Me gustaría ver más juegos de terror en la tienda, como Resident Evil.'),
('Carlos López', 'carlos.lopez@email.com', 'error_juego', 'El juego Minecraft se congela cada vez que lo inicio.');


-- Tabla resenas
CREATE TABLE resenas (
  id INT(11) NOT NULL AUTO_INCREMENT,
  juego_id INT(11) NOT NULL,
  usuario_id INT(11) NOT NULL,
  calificacion INT(11) DEFAULT NULL CHECK (calificacion BETWEEN 1 AND 5),
  comentario TEXT DEFAULT NULL,
  fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

INSERT INTO resenas (juego_id, usuario_id, calificacion, comentario) VALUES
(1, 1, 5, 'Excelente shooter, gráficos increíbles'),
(3, 1, 4, 'Buen gameplay pero repetitivo'),
(4, 1, 3, 'Creativo pero se vuelve monótono'),
(7, 1, 2, 'Demasiado lento para mí'),
(11, 1, 5, 'Me encanta la estrategia clásica'),
(2, 2, 4, 'Buena historia bélica'),
(5, 2, 5, 'Uno de los mejores RPGs'),
(8, 2, 5, 'Maravillosa experiencia'),
(12, 2, 5, 'Kratos nunca decepciona'),
(1, 3, 3, 'Demasiado común'),
(6, 3, 5, 'Muy desafiante, excelente'),
(10, 3, 4, 'Divertido pero frustrante a veces'),
(14, 3, 4, 'La conducción es muy realista'),
(5, 4, 5, 'Narrativa y jugabilidad perfectas'),
(9, 4, 4, 'Bonito arte y buen desafío'),
(15, 4, 5, 'Relajante y adictivo'),
(3, 5, 2, 'Muy repetitivo'),
(10, 5, 5, 'Competitivo y divertido'),
(13, 5, 3, 'No me enganchó'),
(4, 6, 5, 'Creatividad total'),
(8, 6, 5, 'Exploración fascinante'),
(15, 6, 4, 'Ideal para relajarse'),
(6, 7, 5, 'Obra maestra de acción'),
(12, 7, 5, 'Brutalidad y narrativa perfectas'),
(1, 7, 4, 'Buen online'),
(7, 8, 5, 'Estrategia pura'),
(11, 8, 5, 'Clásico de la historia'),
(13, 8, 4, 'Entretenido con amigos'),
(2, 9, 2, 'Se siente viejo'),
(3, 9, 3, 'Normalito'),
(14, 9, 5, 'Amante de la velocidad'),
(9, 9, 5, 'Pequeña gran joya');

-- Tabla logros
CREATE TABLE logros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    requisito VARCHAR(255) NOT NULL UNIQUE,
    imagen_url VARCHAR(255) NULL
);

-- Tabla usuario_logros
CREATE TABLE usuario_logros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    logro_id INT NOT NULL,
    fecha_obtenido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (logro_id) REFERENCES logros(id) ON DELETE CASCADE,
    UNIQUE (usuario_id, logro_id)
);

-- Inserción de los seis logros
INSERT INTO logros (nombre, descripcion, requisito, imagen_url) VALUES 
('Comprador novato', 'Realiza tu primera compra en la tienda.', 'primera_compra', '../img/primera_compra.png'),
('Coleccionista Amateur', 'Adquiere 5 juegos en la tienda.', 'cinco_compras', '../img/coleccionista.png'),
('Voz de la Comunidad', 'Escribe 3 reseñas en la tienda.', 'tres_resenas', '../img/voz_comunidad.png'),
('El Crítico Maestro', 'Escribe 10 reseñas.', 'diez_resenas', '../img/critico_maestro.png'),
('Explorador de Géneros', 'Compra un juego de 3 géneros diferentes.', 'tres_generos', '../img/explorador_generos.png'),
('Cliente Fiel', 'Realiza compras en 3 meses diferentes.', 'cliente_fiel', '../img/cliente_fiel.png');


-- Tabla recomendaciones
CREATE TABLE recomendaciones (
  id_usuario INT NOT NULL,
  id_juego INT NOT NULL,
  PRIMARY KEY (id_usuario, id_juego),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (id_juego) REFERENCES juegos(id) ON DELETE CASCADE
);

-- Tabla compras
CREATE TABLE compras (
  id INT(11) NOT NULL AUTO_INCREMENT,
  usuario_id INT(11) NOT NULL,
  juego_id INT(11) NOT NULL,
  fecha_compra TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  codigo_qr VARCHAR(255) NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE
);

-- Insertar algunos datos de ejemplo para el historial de compras
INSERT INTO compras (usuario_id, juego_id, codigo_qr) VALUES
(1, 1, 'QR-COD-1-JUEGO-1'),
(1, 3, 'QR-COD-1-JUEGO-3'),
(2, 5, 'QR-COD-2-JUEGO-5'),
(3, 6, 'QR-COD-3-JUEGO-6'),
(3, 10, 'QR-COD-3-JUEGO-10'),
(4, 9, 'QR-COD-4-JUEGO-9'),
(5, 13, 'QR-COD-5-JUEGO-13'),
(6, 4, 'QR-COD-6-JUEGO-4'),
(6, 8, 'QR-COD-6-JUEGO-8'),
(7, 12, 'QR-COD-7-JUEGO-12');

COMMIT;