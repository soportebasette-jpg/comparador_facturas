-- ============================================================
-- Crea la tabla "lead_comparador_facturas" en tu base de datos
-- "lead" de IONOS. No toca ninguna tabla existente.
-- ============================================================

CREATE TABLE IF NOT EXISTS lead_comparador_facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_origen VARCHAR(45) DEFAULT NULL,
    contactado TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
