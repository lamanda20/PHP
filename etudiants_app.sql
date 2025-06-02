-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 30 mai 2025 à 19:22
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `etudiants_app`
--

-- --------------------------------------------------------

--
-- Structure de la table `absences`
--

CREATE TABLE `absences` (
  `id_absence` int(11) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `date_absence` date NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `justifiee` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `administrateurs`
--

CREATE TABLE `administrateurs` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrateurs`
--

INSERT INTO `administrateurs` (`id`, `email`, `password`, `nom`) VALUES
(16, 'admin1@gmail.com', '$2y$10$U2R4BPMJ7naR6PveKQv9WeKGnOvXAM0svYVYh8IicO1o2lSllOXey', 'Admin1'),
(17, 'admin2@gmail.com', '$2y$10$U2R4BPMJ7naR6PveKQv9WeKGnOvXAM0svYVYh8IicO1o2lSllOXey', 'Admin2');

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `apogee` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `code_activation` varchar(8) NOT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `est_active` tinyint(1) DEFAULT 0,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `id_filiere` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom`, `prenom`, `apogee`, `email`, `code_activation`, `mot_de_passe`, `est_active`, `date_inscription`, `id_filiere`) VALUES
(3, 'GHADI', 'Taha', '1111', 'tahaghadi3@gmail.com', '33012366', '$2y$10$rhwEHcyOmskXmPCXu2RnVuBccEzoY1G0TVy9WVChVhtfhHqEJRBtS', 1, '2025-05-14 09:54:01', 1),
(4, 'Adloune', 'Malak', '0000', 'malakadloune@gmail.com', '65298099', '$2y$10$5pTHprMQn5gzU0rC92a3muOduELN1tdzHCsnnvESmpYIzHVfA3XsK', 1, '2025-05-14 11:29:23', 1);

-- --------------------------------------------------------

--
-- Structure de la table `fichiers`
--

CREATE TABLE `fichiers` (
  `id` int(11) NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `type_mime` varchar(100) NOT NULL,
  `taille` int(11) NOT NULL,
  `date_upload` datetime DEFAULT current_timestamp(),
  `etudiant_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fichiers`
--

INSERT INTO `fichiers` (`id`, `nom_fichier`, `type_mime`, `taille`, `date_upload`, `etudiant_id`) VALUES
(1, '68245b2ae1f9b.pdf', '', 0, '2025-05-14 09:58:18', 3),
(2, '682471251818e.pdf', '', 0, '2025-05-14 11:32:05', 4);

-- --------------------------------------------------------

--
-- Structure de la table `filieres`
--

CREATE TABLE `filieres` (
  `id_filiere` int(11) NOT NULL,
  `code_filiere` varchar(10) NOT NULL,
  `nom_filiere` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `filieres`
--

INSERT INTO `filieres` (`id_filiere`, `code_filiere`, `nom_filiere`) VALUES
(1, 'GI', 'Génie Informatique'),
(2, 'GE', 'Génie Électrique'),
(3, 'RSSP', 'Réseau');

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions_modules`
--

CREATE TABLE `inscriptions_modules` (
  `id_inscription` int(11) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_module` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `modules`
--

CREATE TABLE `modules` (
  `id_module` int(11) NOT NULL,
  `code_module` varchar(20) NOT NULL,
  `nom_module` varchar(100) NOT NULL,
  `semestre` enum('S1','S2') NOT NULL,
  `id_filiere` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `modules`
--

INSERT INTO `modules` (`id_module`, `code_module`, `nom_module`, `semestre`, `id_filiere`, `id_responsable`) VALUES
(1, 'PHP_MySQL', 'PHP', 'S1', 1, 1),
(2, 'BD', 'Base de données', 'S2', 1, 2),
(3, 'Crypto', 'CS', 'S1', 3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `responsables`
--

CREATE TABLE `responsables` (
  `id_responsable` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `responsables`
--

INSERT INTO `responsables` (`id_responsable`, `nom`, `prenom`, `email`) VALUES
(1, 'BOUARIFI', 'WALID', 'bouarifi@gmail.com'),
(2, 'MOUTAWAKIL', 'AHMED', 'moutawakil3@gmail.com'),
(3, 'ELOUMARI', 'Mustapha', 'eloumari1@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `table_attendance`
--

CREATE TABLE `table_attendance` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `time_in` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `table_attendance`
--

INSERT INTO `table_attendance` (`id`, `student_id`, `time_in`) VALUES
(5, 'Taha GHADI', '2025-05-29 01:42:47');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `absences`
--
ALTER TABLE `absences`
  ADD PRIMARY KEY (`id_absence`),
  ADD KEY `id_etudiant` (`id_etudiant`),
  ADD KEY `id_module` (`id_module`);

--
-- Index pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `apogee` (`apogee`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_etudiants_filiere` (`id_filiere`);

--
-- Index pour la table `fichiers`
--
ALTER TABLE `fichiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etudiant_id` (`etudiant_id`);

--
-- Index pour la table `filieres`
--
ALTER TABLE `filieres`
  ADD PRIMARY KEY (`id_filiere`),
  ADD UNIQUE KEY `code_filiere` (`code_filiere`);

--
-- Index pour la table `inscriptions_modules`
--
ALTER TABLE `inscriptions_modules`
  ADD PRIMARY KEY (`id_inscription`),
  ADD UNIQUE KEY `unique_inscription` (`id_etudiant`,`id_module`),
  ADD KEY `id_module` (`id_module`);

--
-- Index pour la table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id_module`),
  ADD UNIQUE KEY `code_module` (`code_module`),
  ADD KEY `id_filiere` (`id_filiere`),
  ADD KEY `id_responsable` (`id_responsable`);

--
-- Index pour la table `responsables`
--
ALTER TABLE `responsables`
  ADD PRIMARY KEY (`id_responsable`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `table_attendance`
--
ALTER TABLE `table_attendance`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `absences`
--
ALTER TABLE `absences`
  MODIFY `id_absence` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `fichiers`
--
ALTER TABLE `fichiers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `filieres`
--
ALTER TABLE `filieres`
  MODIFY `id_filiere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `inscriptions_modules`
--
ALTER TABLE `inscriptions_modules`
  MODIFY `id_inscription` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `modules`
--
ALTER TABLE `modules`
  MODIFY `id_module` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `responsables`
--
ALTER TABLE `responsables`
  MODIFY `id_responsable` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `table_attendance`
--
ALTER TABLE `table_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `absences`
--
ALTER TABLE `absences`
  ADD CONSTRAINT `absences_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absences_ibfk_2` FOREIGN KEY (`id_module`) REFERENCES `modules` (`id_module`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD CONSTRAINT `fk_etudiants_filiere` FOREIGN KEY (`id_filiere`) REFERENCES `filieres` (`id_filiere`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fichiers`
--
ALTER TABLE `fichiers`
  ADD CONSTRAINT `fichiers_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inscriptions_modules`
--
ALTER TABLE `inscriptions_modules`
  ADD CONSTRAINT `inscriptions_modules_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_modules_ibfk_2` FOREIGN KEY (`id_module`) REFERENCES `modules` (`id_module`) ON DELETE CASCADE;

--
-- Contraintes pour la table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`id_filiere`) REFERENCES `filieres` (`id_filiere`) ON DELETE CASCADE,
  ADD CONSTRAINT `modules_ibfk_2` FOREIGN KEY (`id_responsable`) REFERENCES `responsables` (`id_responsable`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
