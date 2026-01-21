# 🛠️ InCô Laser Community - Forum de partage de paramètres laser

**InCô Laser Community** est une plateforme communautaire dédiée aux professionnels et passionnés de la gravure laser. Elle permet de partager des résultats de tests réels, d'échanger des conseils techniques et de diffuser des bibliothèques de paramètres machine (Vitesse, Puissance, Fréquence) en fonction des matériaux utilisés.

---

## 🚀 Fonctionnalités

- **Consultation de tests** : Galerie de rendus avec fiches techniques détaillées.
- **Partage technique** : Formulaire structuré pour l'envoi de réglages (Vitesse, Puissance, Fréquence, Passes, etc.).
- **Communauté** : Système d'authentification sécurisé et espace de commentaires sous chaque test.
- **Filtres avancés** : Recherche croisée par type de machine (Fibre, CO2, Diode) et par matériau.

---

## 🏗️ Architecture Technique

Le projet utilise une architecture **MVC Découplée** (API REST Backend + Frontend SPA).

### Backend (API REST)

- **Langage** : PHP 8.3 (POO / MVC)
- **Base de données** : MySQL via phpMyAdmin
- **Gestionnaire de dépendances** : Composer (Autoloading PSR-4)
- **Sécurité** : Hachage de mot de passe (BCRYPT), Requêtes préparées PDO, gestion des CORS.
- **Documentation** : Postman Collection.

### Frontend

- **Framework** : Vue.js 3 (Vite)
- **Communication** : Axios (Consommation de l'API JSON)
- **Design** : Maquettes personnalisées InCô réalisées sur Figma.

---

## 🛠️ Installation du projet

### Prérequis

- Serveur local (WAMP, XAMPP ou MAMP)
- PHP >= 8.1
- Node.js & NPM
- Composer

### Installation du Backend

1. Clonez le dépôt : `git clone https://github.com/Toon-mo/Morieux_Tony_Devoir_Bilan_De_Formation.git`
2. Accédez au dossier : `cd Backend`
3. Installez les dépendances : `composer install`
4. Importez le fichier `database_schema.sql` dans votre instance MySQL.
5. Configurez vos accès dans `Backend/Config/Database.php`.

### Installation du Frontend

1. Accédez au dossier : `cd Frontend`
   ---A venir

---

## 📖 Documentation API

L'API est testable et documentée via **Postman**.

- [Lien vers la documentation Postman](A venir)

---

## 📐 Conception UI/UX

Les maquettes Haute Fidélité et les Wireframes ont été réalisés sur **Figma** en suivant l'identité visuelle de l'Atelier InCô.

- **Couleurs principales** : Bleu Cyan (#60C4E6), Anthracite (#1E1E1E).
- **Typographie** : Monospace pour les données techniques.

---

## 👨‍💻 Auteur

- **Tony Morieux** - _Développement Fullstack_ - [GitHub](https://github.com/Toon-mo)
