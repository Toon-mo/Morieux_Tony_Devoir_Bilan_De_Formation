# 🛠️ InCô Laser Community - Forum de partage de paramètres laser

**InCô Laser Community** est une plateforme communautaire dédiée aux professionnels et passionnés de la gravure laser. Elle permet de partager des résultats de tests réels, d'échanger des conseils techniques et de diffuser des bibliothèques de paramètres machine (Vitesse, Puissance, Fréquence) en fonction des matériaux utilisés.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)
![Vue.js](https://img.shields.io/badge/Vue.js-3.0-4FC08D?logo=vue.js)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)

---

## 🚀 Fonctionnalités

### 🔍 Consultation et Recherche

- **Galerie de tests** : Affichage des rendus laser avec fiches techniques détaillées
- **Filtres avancés** : Recherche croisée par type de machine (Fibre, CO2, Diode) et par catégorie de matériau (Bois, Métal, Plastique)
- **Recherche textuelle** : Recherche dans les titres, machines et matériaux
- **Page de détail** : Vue complète de chaque test avec tous les paramètres laser

### 📤 Partage et Contribution

- **Formulaire structuré** : Publication de tests avec upload d'image
- **Paramètres détaillés** : Vitesse, Puissance, Fréquence, Pulse, Z-Offset, Nombre de passes, etc.
- **Sélection dynamique** : Choix de la machine et du matériau depuis la base de données

### 👥 Communauté et Authentification

- **Système d'authentification** : Inscription et connexion sécurisées avec gestion des rôles
- **Profil utilisateur** : Dashboard personnel avec liste des tests publiés
- **Attribution** : Chaque test est lié à son auteur
- **Gestion des rôles** : Système de permissions (Utilisateur, Modérateur, Administrateur)
- **Pages informatives** : Catalogues de machines, matériaux et page astuces

### 🔐 Administration et Modération

- **Panel administrateur** : Gestion complète des utilisateurs, machines et matériaux
- **Modération des contenus** : Validation, modification et suppression des tests
- **Gestion des utilisateurs** : Attribution des rôles, suspension de comptes
- **Statistiques** : Dashboard avec métriques clés (nombre de tests, utilisateurs actifs, etc.)
- **Gestion du catalogue** : Ajout/modification/suppression de machines et matériaux

### 🎨 Interface Utilisateur

- **Page d'accueil** : Aperçu des 2 derniers tests publiés
- **Design moderne** : Interface cyan/noir/blanc cohérente avec la charte InCô
- **Responsive** : Adapté mobile, tablette et desktop
- **Notifications** : Toasts pour feedback utilisateur

---

## 🏗️ Architecture Technique

Le projet utilise une architecture **MVC Découplée** (API REST Backend + Frontend SPA).

### Backend (API REST)

#### Stack Technique

- **Langage** : PHP 8.3 (POO / MVC)
- **Base de données** : MySQL 8.0 via phpMyAdmin
- **Serveur** : Apache (WAMP)
- **Gestionnaire de dépendances** : Composer (Autoloading PSR-4)

#### Structure du Backend

```
Backend/
├── api/
│   ├── comments.php              # Endpoints commentaires
│   ├── image.php                 # Gestion images
│   ├── login.php                 # Endpoints login
│   ├── machines.php              # Endpoints machines
│   ├── materials.php             # Endpoints matériaux
│   ├── register.php              # Endpoints register
│   ├── tests.php                 # Endpoints tests
│   └── users.php                 # Endpoints utilisateurs/auth
├── public/
│   └── uploads/
│       └── tests/                # Stockage images des tests
├── src/
│   ├── Config/
│   │   ├── CORS.php              # Configuration CORS
│   │   └── Database.php          # Connexion PDO
│   ├── Controllers/
│   │   ├── CommentController.php # Gestion des commentaires
│   │   ├── MachineController.php # Gestion des machines
│   │   ├── MaterialController.php# Gestion des matériaux
│   │   ├── TestController.php    # Gestion des tests
│   │   └── UserController.php    # Authentification et utilisateurs
│   └── Models/
│       ├── CommentModel.php      # Logique métier commentaires
│       ├── MachineModel.php      # Logique métier machines
│       ├── MaterialModel.php     # Logique métier matériaux
│       ├── TestModel.php         # Logique métier tests
│       └── UserModel.php         # Logique métier utilisateurs
├── vendor/                       # Dépendances Composer
├── .env                          # Variables d'environnement
├── .env.example                  # Exemple de configuration
├── composer.json                 # Configuration Composer
├── composer.lock                 # Versions des dépendances
└── index.php                     # Point d'entrée API
```

#### Fonctionnalités Backend

- **Sécurité** :
  - Hachage de mot de passe (BCRYPT)
  - Requêtes préparées PDO (protection SQL Injection)
  - Validation des données côté serveur
  - Gestion des CORS pour API REST
  - Upload sécurisé d'images (5 Mo max, JPEG/PNG/WEBP)
- **API REST** :
  - Endpoints JSON bien structurés
  - Codes HTTP appropriés (200, 201, 400, 404, 500)
  - Support `JSON_UNESCAPED_UNICODE` pour les accents
  - Logging détaillé avec `error_log()`

- **Base de données** :
  - Relations normalisées (tests, users, machines, materials, parameters)
  - Colonne `material_category` pour filtrage granulaire
  - Colonne `laser_type` ENUM (FIBRE, CO2, DIODE, OTHER)
  - Index optimisés pour performances

---

### Frontend (SPA Vue.js)

#### Stack Technique

- **Framework** : Vue.js 3 (Composition API)
- **Build Tool** : Vite
- **Routing** : Vue Router 4
- **HTTP Client** : Axios
- **Notifications** : Vue3-Toastify
- **CSS** : Scoped Styles + Tailwind-inspired utilities

#### Structure du Frontend

```
Frontend/
├── public/                   # Assets statiques
├── src/
│   ├── components/
│   │   ├── HomeCard.vue      # Carte page d'accueil
│   │   └── TestCard.vue      # Carte galerie
│   ├── router/
│   │   └── index.js          # Configuration routes
│   ├── views/
│   │   ├── AstucesView.vue
│   │   ├── CreateTestView.vue
│   │   ├── DashboardView.vue
│   │   ├── HomeView.vue      # Page d'accueil (2 tests)
│   │   ├── LoginView.vue
│   │   ├── MachinesView.vue
│   │   ├── MaterialsView.vue
│   │   ├── RegisterView.vue
│   │   ├── TestDetailView.vue
│   │   ├── TestsView.vue     # Galerie complète + filtres
│   ├── App.vue               # Composant racine
│   └── main.js               # Point d'entrée
├── vite.config.js            # Config Vite
└── package.json              # Dépendances NPM
```

#### Fonctionnalités Frontend

- **Routing** :
  - 10 routes configurées
  - Protection des routes authentifiées
  - Navigation fluide SPA
  - URLs dynamiques (/test/:id)

- **Gestion d'état** :
  - LocalStorage pour session utilisateur
  - Reactive data avec `ref()` et `computed()`
  - Gestion des états de chargement

- **Filtrage avancé** :
  - Filtrage par `material_category` (Bois, Métal, Plastique)
  - Filtrage par `laser_type` (CO2, Diode, Fibre)
  - Recherche textuelle combinée
  - Filtrage insensible à la casse

- **UX/UI** :
  - Design cohérent cyan (#60C4E6) / noir (#1E1E1E)
  - Loaders animés pendant chargement
  - Toasts pour notifications
  - Effets hover élégants
  - Responsive design (4 cols → 2 → 1)

---

## 🛠️ Installation du projet

### Prérequis

- **Serveur local** : WAMP, XAMPP ou MAMP
- **PHP** : >= 8.1
- **MySQL** : >= 8.0
- **Node.js** : >= 16.x
- **NPM** : >= 8.x
- **Composer** : Dernière version

---

### Installation du Backend

#### 1. Cloner le dépôt

```bash
git clone https://github.com/Toon-mo/Morieux_Tony_Devoir_Bilan_De_Formation.git
cd Morieux_Tony_Devoir_Bilan_De_Formation
```

#### 2. Installer les dépendances PHP

```bash
cd Backend
composer install
```

#### 3. Configurer la base de données

**Créer la base de données :**

```sql
CREATE DATABASE forum_gravure_laser CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Importer le schéma :**

```bash
# Via phpMyAdmin : Importer database_schema.sql
# OU via ligne de commande :
mysql -u root -p forum_gravure_laser < database/database_schema.sql
```

**Configurer l'accès :**

```php
// Backend/Config/Database.php
private $host = "localhost";
private $db_name = "forum_gravure_laser";
private $username = "root";
private $password = ""; // Votre mot de passe MySQL
```

#### 4. Configurer les permissions

```bash
# Créer le dossier uploads si nécessaire
mkdir -p Backend/public/uploads/tests
chmod 755 Backend/public/uploads/tests
```

#### 5. Démarrer le serveur

```bash
# Démarrer WAMP/XAMPP/MAMP
# L'API sera accessible sur :
# http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api/
```

---

### Installation du Frontend

#### 1. Accéder au dossier Frontend

```bash
cd Frontend
```

#### 2. Installer les dépendances NPM

```bash
npm install
```

#### 3. Configurer l'URL de l'API

Vérifier que les URLs correspondent à votre configuration dans tous les fichiers Vue :

```javascript
// Exemple dans HomeView.vue
const API_BASE =
  "http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api";
```

#### 4. Lancer le serveur de développement

```bash
npm run dev
```

L'application sera accessible sur : **http://localhost:5173**

#### 5. Build pour production

```bash
npm run build
```

Les fichiers de production seront dans `Frontend/dist/`

---

## 📖 Documentation API

### Endpoints Principaux

#### Tests

- **GET** `/api/tests.php` - Liste tous les tests
- **GET** `/api/tests.php?id=X` - Détail d'un test
- **POST** `/api/tests.php` - Créer un test (auth requise)
- **PUT** `/api/tests.php?id=X` - Modifier un test (auth requise)
- **DELETE** `/api/tests.php?id=X` - Supprimer un test (auth requise)

#### Utilisateurs

- **POST** `/api/users.php?action=register` - Inscription
- **POST** `/api/users.php?action=login` - Connexion
- **GET** `/api/users.php` - Liste utilisateurs (admin)
- **PUT** `/api/users.php?id=X` - Modifier utilisateur (admin)
- **DELETE** `/api/users.php?id=X` - Supprimer utilisateur (admin)

#### Machines

- **GET** `/api/machines.php` - Liste des machines
- **POST** `/api/machines.php` - Créer une machine (admin)
- **PUT** `/api/machines.php?id=X` - Modifier une machine (admin)
- **DELETE** `/api/machines.php?id=X` - Supprimer une machine (admin)

#### Matériaux

- **GET** `/api/materials.php` - Liste des matériaux
- **POST** `/api/materials.php` - Créer un matériau (admin)
- **PUT** `/api/materials.php?id=X` - Modifier un matériau (admin)
- **DELETE** `/api/materials.php?id=X` - Supprimer un matériau (admin)

#### Administration

- **GET** `/api/admin.php?action=stats` - Statistiques globales (admin)
- **GET** `/api/admin.php?action=pending` - Tests en attente de modération (moderator)
- **POST** `/api/admin.php?action=approve&id=X` - Approuver un test (moderator)
- **POST** `/api/admin.php?action=reject&id=X` - Rejeter un test (moderator)

#### Images

- **GET** `/api/image.php?name=XXX` - Récupérer une image

### Exemple de requête

```javascript
// --- Soumission ---
const handleSubmit = async () => {
  if (!formData.value.title || !selectedFile.value) {
    toast.error("Le titre et la photo sont obligatoires !");
    return;
  }

  try {
    const data = new FormData();
    data.append("image", selectedFile.value);

    Object.keys(formData.value).forEach((key) => {
      if (formData.value[key] !== null) data.append(key, formData.value[key]);
    });

    const res = await axios.post(`${API_BASE}/tests.php`, data, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    if (res.status === 201 || res.data.success) {
      toast.success("Test publié avec succès !");
      setTimeout(() => router.push("/"), 1500);
    }
  } catch (err) {
    toast.error("Erreur lors de la publication");
    console.error(err);
  }
};
```

### Documentation Postman

📄 [Documentation Postman](https://documenter.getpostman.com/view/45989406/2sBXVmfUD4)

---

## 📐 Conception UI/UX

### Maquettes Figma

Les maquettes Haute Fidélité et Wireframes ont été réalisés sur **Figma** en suivant l'identité visuelle de l'Atelier InCô.

🎨 [Lien vers les maquettes Figma](https://www.figma.com/design/bKnqqi1HGARGGnYOFramL9/Laser-Community-Forum?node-id=2-3&t=flPVKmkKChfdhN6n-1)

### Design System

#### Palette de couleurs

- **Cyan primaire** : `#60C4E6` - Actions, boutons, bordures
- **Anthracite** : `#1E1E1E` - Textes, fond de navigation
- **Gris clair** : `#E8E8E8` - Fond de page
- **Blanc** : `#FFFFFF` - Cartes, conteneurs

#### Typographie

- **Titres** : Segoe UI, sans-serif, 600
- **Corps** : Segoe UI, sans-serif, 400
- **Code/Données techniques** : Monospace (JetBrains Mono)

#### Composants

- **Cartes** : Bordure 2px cyan, ombres douces, coins arrondis 15px
- **Boutons** : Cyan avec effet hover (translation -3px)
- **Badges** : Coins arrondis 20-25px
- **Inputs** : Bordure 2px cyan au focus

---

## 🔒 Sécurité

### Backend

- ✅ Hachage BCRYPT pour mots de passe
- ✅ Requêtes préparées PDO (anti SQL Injection)
- ✅ Validation des données serveur
- ✅ Upload sécurisé (types MIME, taille max)
- ✅ CORS configuré
- ✅ Headers de sécurité HTTP
- ✅ Système de rôles (user, moderator, admin)
- ✅ Middleware d'authentification et autorisation
- ✅ Protection des routes admin/moderator

### Frontend

- ✅ Protection des routes (requiresAuth, requiresAdmin)
- ✅ Validation côté client
- ✅ Gestion sécurisée du localStorage
- ✅ Échappement des données affichées
- ✅ Pas de secrets en dur dans le code
- ✅ Vérification des permissions côté client
- ✅ Redirection selon le rôle utilisateur

---

## 🧪 Tests

### Tests manuels effectués

- ✅ Inscription / Connexion / Déconnexion
- ✅ Création de test avec upload d'image
- ✅ Filtrage par catégorie (Bois, Métal, Plastique)
- ✅ Filtrage par laser (CO2, Diode, Fibre)
- ✅ Recherche textuelle
- ✅ Navigation entre pages
- ✅ Responsive mobile/tablette/desktop
- ✅ Gestion des erreurs (404, 500)
- ✅ Gestion des rôles (user, moderator, admin)
- ✅ Accès restreints aux pages admin
- ✅ Modération des tests
- ✅ CRUD machines et matériaux (admin)

### Tests à venir

- [ ] Tests unitaires Backend (PHPUnit)
- [ ] Tests unitaires Frontend (Vitest)
- [ ] Tests E2E (Cypress)
- [ ] Tests de charge (JMeter)
- [ ] Tests de permissions et rôles
- [ ] Tests d'intrusion (OWASP)

---

## 📊 Base de données

### Schéma relationnel

```
users (user_id, username, email, password_hash, role)
  │
  └─► tests (test_id, title, image, user_id, machine_id, material_id, status)
         │
         ├─► machines (machine_id, name, model, laser_type, brand)
         │
         ├─► materials (material_id, name, category, color, thickness)
         │
         └─► parameters (parameter_id, test_id, speed, power, frequency, pulse, ...)
```

### Tables principales

- **users** : Utilisateurs de la plateforme (avec colonne `role`: 'user', 'moderator', 'admin')
- **tests** : Tests publiés (avec colonne `status`: 'pending', 'approved', 'rejected')
- **machines** : Catalogue de machines laser
- **materials** : Catalogue de matériaux
- **parameters** : Paramètres détaillés de chaque test

---

## 🚀 Méthodologie de développement

### Git Workflow

Le projet utilise **GitHub Flow** avec branches feature :

```
main (branche principale)
  │
  ├───► feature/config (Fondations : PDO, Dotenv, CORS)
  │
  ├───► feature/user-auth (Authentification : Login, Register, BCRYPT)
  │
  ├───► feature/materials-management (Gestion du référentiel matériaux)
  │
  ├───► feature/test-management (Cœur métier : CRUD Tests & Transactions)
  │
  ├───► feature/test-images-params (Refactor : Gestion upload & paramètres avancés)
  │
  ├───► feature/comment-system (Interaction : CRUD Commentaires & Jointures)
  │
  └───► feature/frontend-complete (Interface Vue.js 3, Vite, Axios)
         │
         ├─ feat: initial setup
         ├─ feat: add components
         ├─ feat: create views
         └─ ... (9 commits au total)
```

### Commits sémantiques

```
feat(frontend): nouvelle fonctionnalité
fix(backend): correction de bug
refactor: refactorisation
style: modifications CSS
chore: configuration, dépendances
docs: documentation
```

---

## 📚 Ressources et Références

### Technologies utilisées

- [Vue.js 3 Documentation](https://vuejs.org/)
- [Vite Documentation](https://vitejs.dev/)
- [PHP 8 Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)

### Librairies

- [Axios](https://axios-http.com/)
- [Vue Router](https://router.vuejs.org/)
- [Vue3-Toastify](https://vue3-toastify.js-bridge.com/get-started/introduction.html)

---

## 🎯 Roadmap

### Version 1.0 (Actuelle)

- ✅ Système d'authentification
- ✅ CRUD complet des tests
- ✅ Filtrage avancé par catégorie et laser
- ✅ Upload et gestion d'images
- ✅ Dashboard utilisateur
- ✅ Catalogues machines et matériaux
- ✅ Panel d'administration

### Version 1.1 (En développement)

- 🔄 CRUD à terminer (Machines, Material, Comment)
- 🔄 Gestion des rôles (User, Moderator, Admin)
- 🔄 Modération des contenus
- 🔄 Système de commentaires sous les tests
- 🔄 Système de likes/favoris
- 🔄 Notifications en temps réel (modération)
- 🔄 Historique des modifications (audit trail)
- 🔄 Export des statistiques en CSV

### Version 1.2 (À venir)

- [ ] Recherche avancée multi-critères
- [ ] Export de paramètres en PDF
- [ ] Système de signalement
- [ ] Gestion des bannissements temporaires
- [ ] Dashboard analytics avancé

### Version 2.0 (Future)

- [ ] API GraphQL
- [ ] Application mobile (React Native)
- [ ] Mode hors-ligne (PWA)
- [ ] Webhooks pour intégrations tierces
- [ ] Multi-langue (i18n)

---

## 🤝 Contribution

Les contributions sont les bienvenues !

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'feat: Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

---

## 📝 Licence

Ce projet est sous licence - voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 👨‍💻 Auteur

**Tony Morieux**  
_Développeur Fullstack - Spécialisation Web_

- 🐙 GitHub : [@Toon-mo](https://github.com/Toon-mo)
- 📧 Email : tony.morieux@example.com

---

## 🙏 Remerciements

- **L'Atelier InCô** pour l'inspiration et le contexte professionnel
- **La communauté Vue.js** pour les ressources et la documentation

---

## 📞 Support

Pour toute question ou problème :

- 🐛 [Ouvrir une issue](https://github.com/Toon-mo/Morieux_Tony_Devoir_Bilan_De_Formation/issues)
- 💬 [Discussions](https://github.com/Toon-mo/Morieux_Tony_Devoir_Bilan_De_Formation/discussions)

---
