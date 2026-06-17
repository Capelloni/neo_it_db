-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mer. 04 mars 2026 à 11:27
-- Version du serveur : 10.11.11-MariaDB
-- Version de PHP : 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `neo_it_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `app_cache`
--

CREATE TABLE `app_cache` (
  `cache_key` varchar(100) NOT NULL,
  `cache_value` text DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `asset_audit`
--

CREATE TABLE `asset_audit` (
  `id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `audit_date` date NOT NULL,
  `found` tinyint(1) DEFAULT 1,
  `condition` varchar(50) DEFAULT NULL,
  `audit_notes` text DEFAULT NULL,
  `auditor_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'DEPARTEMENT INFORMATIQUE', 'Direction des Systèmes d\'Information', '2026-03-04 10:24:31'),
(2, 'SERVICE COMPTABILITE', 'Comptabilité et Finances', '2026-03-04 10:24:31'),
(3, 'DIRECTION GENERALE', 'Direction Générale', '2026-03-04 10:24:31'),
(4, 'RESSOURCES HUMAINES', 'Gestion des ressources humaines', '2026-03-04 10:24:31'),
(5, 'VENTES', 'Service commercial', '2026-03-04 10:24:31'),
(6, 'LOGISTIQUE', 'Service logistique', '2026-03-04 10:24:31'),
(7, 'PRODUCTION', 'Service production', '2026-03-04 10:24:31');

-- --------------------------------------------------------

--
-- Structure de la table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `service` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `employees`
--

INSERT INTO `employees` (`id`, `firstname`, `lastname`, `email`, `phone`, `department_id`, `service`, `position`, `hire_date`, `active`, `created_at`, `updated_at`) VALUES
(1, 'TCHIEUGANG', 'DUMAR', 'dumar@neo.sa', '+237697890123', 1, 'DSI', 'Chef DSI', '2020-03-15', 1, '2026-03-04 10:24:31', '2026-03-04 10:24:31'),
(2, 'NGOUDJI', 'FELIX', 'felix@neo.sa', '+237691234567', 2, 'COMPTABILITE', 'Comptable Principal', '2021-06-20', 1, '2026-03-04 10:24:31', '2026-03-04 10:24:31'),
(3, 'IDRISSOU', 'MOUCHILI', 'dg@neo.sa', '+237690123456', 3, 'DG', 'Directeur Général', '2018-01-10', 1, '2026-03-04 10:24:31', '2026-03-04 10:24:31'),
(4, 'MBOUMBA', 'JEAN', 'jean.mboumba@neo.sa', '+237692345678', 4, 'RH', 'Responsable RH', '2019-11-05', 1, '2026-03-04 10:24:31', '2026-03-04 10:24:31'),
(5, 'NENKAM', 'LOUISE', 'louise@neo.sa', '+237693456789', 2, 'COMPTABILITE', 'Comptable', '2022-02-14', 1, '2026-03-04 10:24:31', '2026-03-04 10:24:31');

-- --------------------------------------------------------

--
-- Structure de la table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `identifier` varchar(50) DEFAULT NULL,
  `model` varchar(100) NOT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `status` enum('stock','assigned','maintenance','repair','discarded','lost') DEFAULT 'stock',
  `warranty_until` date DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `equipment`
--

INSERT INTO `equipment` (`id`, `type_id`, `identifier`, `model`, `serial`, `purchase_date`, `purchase_price`, `employee_id`, `status`, `warranty_until`, `location`, `notes`, `photo`, `created_at`, `updated_at`) VALUES
(1, 1, 'L.G02E55', 'HP ProBook G4', 'CE253622', '2024-06-15', 1200.00, 3, 'maintenance', '2025-06-15', 'Bureau DG', 'Écran criqué', NULL, '2026-03-04 10:24:32', '2026-03-04 10:24:32'),
(2, 1, 'L.G02E56', 'Dell Inspiron 15', 'NB123456', '2024-07-20', 950.00, 1, 'assigned', '2025-07-20', 'Bureau DSI', NULL, NULL, '2026-03-04 10:24:32', '2026-03-04 10:24:32'),
(3, 2, 'D.G02E01', 'HP ProDesk 400', 'DE789123', '2023-05-10', 1500.00, 4, 'assigned', '2024-05-10', 'Bureau RH', NULL, NULL, '2026-03-04 10:24:32', '2026-03-04 10:24:32'),
(4, 1, 'L.G02E57', 'Lenovo ThinkPad', 'LN456789', '2024-08-01', 1100.00, NULL, 'stock', '2025-08-01', 'Stock IT', 'Stock', NULL, '2026-03-04 10:24:32', '2026-03-04 10:24:32'),
(5, 3, 'IMP.G02E01', 'HP LaserJet M507', 'HP123456', '2023-03-12', 800.00, NULL, 'maintenance', '2024-03-12', 'Imprimerie', 'En panne depuis 3 mois', NULL, '2026-03-04 10:24:32', '2026-03-04 10:24:32'),
(6, 4, 'SW.G02E01', 'Cisco Catalyst 2960', 'CS111222', '2022-02-14', 3000.00, NULL, 'assigned', '2025-02-14', 'Salle serveur', NULL, NULL, '2026-03-04 10:24:32', '2026-03-04 10:24:32'),
(7, 5, 'TEL.G02E01', 'Alcatel 4018', 'ALC001', '2021-01-20', 150.00, 1, 'assigned', '2024-01-20', 'Bureau DSI', NULL, NULL, '2026-03-04 10:24:32', '2026-03-04 10:24:32');

-- --------------------------------------------------------

--
-- Structure de la table `equipment_assignments`
--

CREATE TABLE `equipment_assignments` (
  `id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `assignment_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `assignment_reason` enum('new_equipment','replacement','upgrade','repair_return','other') DEFAULT 'new_equipment',
  `replaced_equipment_id` int(11) DEFAULT NULL COMMENT 'ID de l''équipement remplacé',
  `replacement_reason` enum('broken','wear','upgrade','reassigned','lost','discarded','other') DEFAULT NULL COMMENT 'Raison du remplacement de l''ancien équipement',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `equipment_assignments`
--

INSERT INTO `equipment_assignments` (`id`, `equipment_id`, `employee_id`, `assignment_date`, `return_date`, `assignment_reason`, `replaced_equipment_id`, `replacement_reason`, `notes`, `created_at`, `created_by`) VALUES
(1, 1, 3, '2024-06-15', NULL, 'new_equipment', NULL, NULL, 'Attribution initiale', '2026-03-04 10:24:32', 'ADMIN'),
(2, 2, 1, '2024-07-20', NULL, 'new_equipment', NULL, NULL, 'Nouvelle acquisition', '2026-03-04 10:24:32', 'ADMIN'),
(3, 3, 4, '2023-05-10', NULL, 'new_equipment', NULL, NULL, 'Attribution initiale', '2026-03-04 10:24:32', 'ADMIN'),
(4, 4, NULL, '2024-08-01', NULL, 'new_equipment', NULL, NULL, 'Achat en stock', '2026-03-04 10:24:32', 'ADMIN'),
(5, 5, NULL, '2023-03-12', NULL, 'new_equipment', NULL, NULL, 'Équipement partagé', '2026-03-04 10:24:32', 'ADMIN'),
(6, 6, NULL, '2022-02-14', NULL, 'new_equipment', NULL, NULL, 'Infrastructure réseau', '2026-03-04 10:24:32', 'ADMIN'),
(7, 7, 1, '2021-01-20', NULL, 'new_equipment', NULL, NULL, 'Attribution initiale', '2026-03-04 10:24:32', 'ADMIN');

-- --------------------------------------------------------

--
-- Structure de la table `equipment_types`
--

CREATE TABLE `equipment_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `expected_lifespan` int(11) DEFAULT NULL COMMENT 'Durée de vie attendue en mois',
  `maintenance_interval` int(11) DEFAULT NULL COMMENT 'Intervalle de maintenance en mois',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `equipment_types`
--

INSERT INTO `equipment_types` (`id`, `name`, `description`, `expected_lifespan`, `maintenance_interval`, `created_at`) VALUES
(1, 'LAPTOP', 'Ordinateur portable', 48, 6, '2026-03-04 10:24:29'),
(2, 'DESKTOP', 'Ordinateur de bureau', 60, 12, '2026-03-04 10:24:29'),
(3, 'IMPRIMANTE', 'Imprimante réseau/locale', 60, 6, '2026-03-04 10:24:29'),
(4, 'SWITCH', 'Commutateur réseau', 120, 12, '2026-03-04 10:24:29'),
(5, 'TELEPHONE_FIXE', 'Téléphone de bureau', 60, 12, '2026-03-04 10:24:29'),
(6, 'SERVEUR', 'Serveur informatique', 120, 3, '2026-03-04 10:24:29'),
(7, 'ROUTEUR', 'Routeur réseau', 60, 6, '2026-03-04 10:24:29'),
(8, 'SCANNER', 'Scanner de documents', 60, 12, '2026-03-04 10:24:29'),
(9, 'VIDEOPROJECTEUR', 'Vidéoprojecteur', 60, 12, '2026-03-04 10:24:29'),
(10, 'TABLEAU_BLANC_INTERACTIF', 'Tableau blanc interactif', 120, 12, '2026-03-04 10:24:29');

-- --------------------------------------------------------

--
-- Structure de la table `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `maintenance_type` enum('preventive','corrective','emergency') DEFAULT 'corrective',
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `technician_name` varchar(100) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `action` varchar(100) DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `old_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_value`)),
  `new_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_value`)),
  `user_name` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `asset_audit`
--
ALTER TABLE `asset_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_equipment` (`equipment_id`),
  ADD KEY `idx_audit_date` (`audit_date`);

--
-- Index pour la table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_department` (`department_id`),
  ADD KEY `idx_active` (`active`);

--
-- Index pour la table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identifier` (`identifier`),
  ADD UNIQUE KEY `serial` (`serial`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_type` (`type_id`);

--
-- Index pour la table `equipment_assignments`
--
ALTER TABLE `equipment_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `replaced_equipment_id` (`replaced_equipment_id`),
  ADD KEY `idx_equipment` (`equipment_id`),
  ADD KEY `idx_employee` (`employee_id`),
  ADD KEY `idx_dates` (`assignment_date`,`return_date`);

--
-- Index pour la table `equipment_types`
--
ALTER TABLE `equipment_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_equipment` (`equipment_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Index pour la table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `asset_audit`
--
ALTER TABLE `asset_audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `equipment_assignments`
--
ALTER TABLE `equipment_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `equipment_types`
--
ALTER TABLE `equipment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `asset_audit`
--
ALTER TABLE `asset_audit`
  ADD CONSTRAINT `asset_audit_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `equipment_types` (`id`),
  ADD CONSTRAINT `equipment_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `equipment_assignments`
--
ALTER TABLE `equipment_assignments`
  ADD CONSTRAINT `equipment_assignments_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipment_assignments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipment_assignments_ibfk_3` FOREIGN KEY (`replaced_equipment_id`) REFERENCES `equipment` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD CONSTRAINT `maintenance_logs_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
