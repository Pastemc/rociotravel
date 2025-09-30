-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.30 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando datos para la tabla rociotravel.cache: ~0 rows (aproximadamente)

-- Volcando datos para la tabla rociotravel.cache_locks: ~0 rows (aproximadamente)

-- Volcando datos para la tabla rociotravel.clientes: ~27 rows (aproximadamente)
INSERT INTO `clientes` (`id`, `numero_documento`, `nombre`, `contacto`, `nacionalidad`, `activo`, `created_at`, `updated_at`) VALUES
	(43, '77564145', 'CHRISTOPHER LEWIS PINEDO PAREDES', '901992834', 'PERUANA', 1, '2025-09-30 18:15:49', '2025-09-30 18:15:49');

-- Volcando datos para la tabla rociotravel.failed_jobs: ~0 rows (aproximadamente)

-- Volcando datos para la tabla rociotravel.historial_ventas: ~0 rows (aproximadamente)
INSERT INTO `historial_ventas` (`id`, `pasaje_id`, `nombre_cliente`, `documento_cliente`, `contacto_cliente`, `nacionalidad_cliente`, `cantidad`, `descripcion`, `precio_unitario`, `subtotal`, `destino`, `ruta`, `embarcacion`, `puerto_embarque`, `hora_embarque`, `hora_salida`, `medio_pago`, `pago_mixto`, `detalles_pago`, `estado`, `nota`, `motivo_anulacion`, `created_at`, `updated_at`) VALUES
	(66, 73, 'CHRISTOPHER LEWIS PINEDO PAREDES', '77564145', '901992834', 'PERUANA', 1, 'PUCALLPA - NAUTA-ALFONSO UGARTE', 100.00, 100.00, 'OTROS', 'PUCALLPA - NAUTA-ALFONSO UGARTE', 'NR', 'LA CASONA DE SAJUTA', '10:00 AM', '07:30 AM', 'efectivo', 0, 'Efectivo: S/ 100.00 - Cliente: S/ 100.00 - Vuelto: S/ 0.00', 'activo', NULL, NULL, '2025-09-30 18:16:44', '2025-09-30 18:16:44');

-- Volcando datos para la tabla rociotravel.jobs: ~0 rows (aproximadamente)

-- Volcando datos para la tabla rociotravel.job_batches: ~0 rows (aproximadamente)

-- Volcando datos para la tabla rociotravel.migrations: ~20 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_09_23_054200_create_sessions_table', 2),
	(5, '2025_09_23_133308_create_clientes_table', 3),
	(6, '2025_09_23_201750_create_clientes_table', 4),
	(7, '2025_09_23_204433_create_detalle_pasajes_table', 5),
	(8, '2025_09_23_205802_create_detalle_pasajes_table', 6),
	(9, '2025_09_24_051340_create_detalle_pasajes_table', 7),
	(10, '2025_09_24_053135_create_detalle_pasajes_table', 8),
	(11, '2025_09_24_061221_create_detalle_pasajes_table', 9),
	(12, '2025_09_24_130202_create_detalles_pasajes_table', 10),
	(13, '2025_09_24_135342_create_detalles_pasaje_table', 11),
	(14, '2025_09_25_065611_create_pasajes_table', 12),
	(15, '2025_09_25_073812_create_pasajes_table', 13),
	(16, '2025_09_25_181624_create_pasajes_table', 14),
	(17, '2025_09_26_165023_create_pasajes_table', 15),
	(18, '2025_09_26_165028_create_numeracion_table', 15),
	(19, '2025_09_26_171426_create_pasajes_table', 16),
	(20, '2025_09_26_171902_create_pasajes_table', 17),
	(21, '2025_09_26_180105_create_nota_ventas_table', 18),
	(22, '2025_09_26_231719_create_historial_ventas_table', 19),
	(23, '2025_09_27_150452_create_detalles_pasaje_table', 20),
	(24, '2025_09_28_004607_create_historial_ventas_table', 21),
	(25, '2025_09_28_034936_create_detalles_pasajes_table', 22),
	(26, '2025_09_28_061529_create_historial_ventas_table', 23),
	(27, '2025_09_30_044646_update_medio_pago_in_pasajes_table', 24);

-- Volcando datos para la tabla rociotravel.numeracion: ~2 rows (aproximadamente)
INSERT INTO `numeracion` (`id`, `tipo_documento`, `ultimo_numero`, `prefijo`, `created_at`, `updated_at`) VALUES
	(1, 'RT', 0, 'RT-', '2025-09-26 21:52:26', '2025-09-26 21:52:26'),
	(2, 'PASAJE', 0, 'PSJ-', '2025-09-26 21:52:26', '2025-09-26 21:52:26');

-- Volcando datos para la tabla rociotravel.pasajes: ~67 rows (aproximadamente)
INSERT INTO `pasajes` (`id`, `cantidad`, `descripcion`, `precio_unitario`, `subtotal`, `destino`, `ruta`, `embarcacion`, `puerto_embarque`, `hora_embarque`, `hora_salida`, `medio_pago`, `pago_mixto`, `detalles_pago`, `nombre_cliente`, `documento_cliente`, `contacto_cliente`, `nacionalidad_cliente`, `nota`, `estado`, `motivo_anulacion`, `created_at`, `updated_at`) VALUES
	(73, 1, 'PUCALLPA - NAUTA-ALFONSO UGARTE', 100.00, 100.00, 'OTROS', 'PUCALLPA - NAUTA-ALFONSO UGARTE', 'NR', 'LA CASONA DE SAJUTA', '10:00 AM', '07:30 AM', 'efectivo', 0, 'Efectivo: S/ 100.00 - Cliente: S/ 100.00 - Vuelto: S/ 0.00', 'CHRISTOPHER LEWIS PINEDO PAREDES', '77564145', '901992834', 'PERUANA', NULL, 'activo', NULL, '2025-09-30 18:16:44', '2025-09-30 18:16:44');

-- Volcando datos para la tabla rociotravel.sessions: ~2 rows (aproximadamente)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('6JYePnll0mfDJ75krWEPpaiC34I0WckR1kLFpegG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWUN3N2pObmRWRkt2akM1NmpMcmowT1lMN05XeVNib1lJQzl4N3kwaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly9yb2Npb3RyYXZlbC50ZXN0L2FwaS9oaXN0b3JpYWwtdmVudGFzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1759243971),
	('kTkJ98HzC0iCQvIB1O8f2rdot6XCpwqFJDH0etUm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMUVHVEhzRnVickdjeVZMS3ZTeWdvSkdPY3NFajh6MFRDNkQ4cDhwUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly9yb2Npb3RyYXZlbC50ZXN0L2RldGFsbGVzLXBhc2FqZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1759218391);

-- Volcando datos para la tabla rociotravel.users: ~0 rows (aproximadamente)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `active`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador Rocío Travel', 'rociotravel@gmail.com', NULL, '$2y$12$m7tCg0b3OlV1hs3Iw2DxsOuKWNoCgmk1zNXaPHrcRWIuPh3OIW8my', 'admin', 1, NULL, '2025-09-23 10:39:26', '2025-09-23 10:39:26');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
