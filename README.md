# 🎧 Podcast Platform API (Laravel)

## 📌 Description du projet

Ce projet consiste à développer une **API RESTful avec Laravel** pour une plateforme de découverte, d’écoute et de gestion de podcasts.

L’API permet :

* Aux **utilisateurs** de découvrir, rechercher et consulter des podcasts et épisodes.
* Aux **animateurs** de créer et gérer leurs podcasts et épisodes.
* Aux **administrateurs** de gérer l’ensemble de la plateforme (utilisateurs, animateurs, podcasts et épisodes).

Le projet est sécurisé par une authentification basée sur **Laravel Sanctum** avec une gestion des rôles (**user, animateur, admin**).

---

## 🛠️ Technologies utilisées

* **Laravel 10** (API Backend)
* **Laravel Sanctum** (Authentification & tokens)
* **MySQL** (Base de données)
* **Swagger / OpenAPI** (Documentation API)
* **PHPUnit** (Tests automatisés)

---

## 👥 Rôles et permissions

### Utilisateur

* Création de compte et authentification
* Consultation de la liste des podcasts
* Recherche de podcasts et d’épisodes
* Consultation des détails des podcasts et épisodes

### Animateur

* Création et gestion de ses propres podcasts
* Ajout, modification et suppression de ses épisodes
* Consultation de ses podcasts et épisodes publiés

### Administrateur

* Gestion complète des podcasts, épisodes et animateurs
* Gestion des utilisateurs (création, suppression, modification des rôles)

---

## 🔐 Authentification

* Authentification via **Laravel Sanctum**
* Accès sécurisé selon le rôle de l’utilisateur
* Utilisation de **Form Requests** pour la validation des données

---

## 📚 Endpoints API

### 🔑 Authentification

| Méthode | Endpoint      | Description                             |
| ------- | ------------- | --------------------------------------- |
| POST    | /api/register | Créer un compte utilisateur             |
| POST    | /api/login    | Authentification et génération du token |
| POST    | /api/logout   | Déconnexion                             |

---

### 🎙️ Podcasts

**Admin & Animateur**

| Méthode | Endpoint           | Description          |
| ------- | ------------------ | -------------------- |
| POST    | /api/podcasts      | Créer un podcast     |
| PUT     | /api/podcasts/{id} | Modifier un podcast  |
| DELETE  | /api/podcasts/{id} | Supprimer un podcast |

**Tous les utilisateurs**

| Méthode | Endpoint           | Description          |
| ------- | ------------------ | -------------------- |
| GET     | /api/podcasts      | Lister les podcasts  |
| GET     | /api/podcasts/{id} | Détails d’un podcast |

---

### 🎧 Épisodes

**Admin & Animateur**

| Méthode | Endpoint                            | Description          |
| ------- | ----------------------------------- | -------------------- |
| POST    | /api/podcasts/{podcast_id}/episodes | Créer un épisode     |
| PUT     | /api/episodes/{id}                  | Modifier un épisode  |
| DELETE  | /api/episodes/{id}                  | Supprimer un épisode |

**Tous les utilisateurs**

| Méthode | Endpoint                            | Description          |
| ------- | ----------------------------------- | -------------------- |
| GET     | /api/podcasts/{podcast_id}/episodes | Lister les épisodes  |
| GET     | /api/episodes/{id}                  | Détails d’un épisode |

---

### 👤 Animateurs

| Méthode | Endpoint        | Description            |
| ------- | --------------- | ---------------------- |
| GET     | /api/hosts      | Liste des animateurs   |
| GET     | /api/hosts/{id} | Détails d’un animateur |

---

### 🔎 Recherche

| Méthode | Endpoint             | Description                                 |
| ------- | -------------------- | ------------------------------------------- |
| GET     | /api/search/podcasts | Recherche par titre, catégorie ou animateur |
| GET     | /api/search/episodes | Recherche par titre, podcast ou date        |

---

### 👨‍💼 Gestion des utilisateurs (Admin)

| Méthode | Endpoint        | Description              |
| ------- | --------------- | ------------------------ |
| GET     | /api/users      | Liste des utilisateurs   |
| POST    | /api/users      | Créer un utilisateur     |
| PUT     | /api/users/{id} | Modifier un utilisateur  |
| DELETE  | /api/users/{id} | Supprimer un utilisateur |

---

## 📄 Documentation API

La documentation est générée avec **Swagger** et permet de :

* Visualiser tous les endpoints
* Tester les requêtes directement depuis l’interface

📍 Accès : `/api/documentation`

---

## 🧪 Tests

* Tests unitaires et fonctionnels avec **PHPUnit**
* Vérification de l’authentification, des rôles et des endpoints critiques

---

## 🚀 Installation du projet

1. Cloner le projet
2. Installer les dépendances : `composer install`
3. Configurer le fichier `.env`
4. Lancer les migrations : `php artisan migrate`
5. Lancer le serveur : `php artisan serve`

---

## 👩‍💻 Auteur

**Khadija Araja**
Développeuse Web – Backend Laravel

---

## 📌 Statut du projet

✅ API fonctionnelle
🔄 Améliorations possibles : notifications, favoris, statistiques d’écoute
