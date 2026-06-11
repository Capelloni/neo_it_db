# NEO Industry IT Management

Application web de gestion du parc informatique — NEO Industry.

## Fonctionnalités

- Gestion des employés et départements
- Gestion des équipements IT
- Suivi des maintenances
- Historique des attributions

## Installation

### 1. Cloner le projet
```bash
git clone https://github.com/ton-username/neo_it_db.git
cd neo_it_db
```

### 2. Configurer la base de données
```bash
# Copier le fichier de configuration
cp config/config.example.php config/config.php

# Éditer config/config.php avec tes propres identifiants
```

### 3. Importer la base de données
```bash
mysql -u root -p < neo_it_db.sql
```

### 4. Lancer le projet
Placer le dossier dans ton serveur local (XAMPP, WAMP, Laragon...) et accéder via :
```
http://localhost/neo_it_db/
```

## Structure du projet

```
neo_it_db/
├── config/
│   ├── config.php          ← tes identifiants (ignoré par Git)
│   └── config.example.php  ← modèle vide (suivi par Git)
├── index.php               ← interface principale
├── api.php                 ← API REST PHP
├── neo_it_db.sql           ← structure de la base de données
└── .gitignore
```

## API

L'API REST est accessible via `api.php?entity=...&action=...`

| Entity | Actions disponibles |
|---|---|
| `employees` | get_all, get, create, update, delete |
| `departments` | get_all |
| `equipment` | get_all, get, create, update, delete |
| `equipment_types` | get_all, create, delete |
| `maintenance` | get_all, create, update, delete |
| `assignments` | get_all, create |
