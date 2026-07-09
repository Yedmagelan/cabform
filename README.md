# CabForm LMS — Plateforme de Formation en Ligne & Certifications

CabForm LMS est une plateforme moderne de gestion de l'apprentissage (LMS) conçue avec Laravel, offrant des parcours de formation certifiants avec double authentification, suivi de progression, évaluations en ligne, messagerie privée, forums d'entraide, génération de certificats PDF avec QR Codes et paiements sécurisés via CinetPay.

---

## 🚀 Fonctionnalités Clés

* **Gestion du Catalogue & Cours** : 
  * Hiérarchie complète : Catégories, Cours, Modules, Leçons, Ressources.
  * Déblocage séquentiel obligatoire des leçons pour guider l'apprenant.
  * Duplication de cours en cascade et gestion des versions.
* **Module d'Évaluation & Certification** :
  * Questionnaires QCM configurables avec tirage de questions aléatoires (question bank).
  * Génération automatique de certificats PDF à la réussite d'un examen final.
  * QR Code d'authenticité unique rendu sur chaque certificat PDF.
* **Espace Apprenant Interactif** :
  * Dashboard de progression globale et temps passé sur la plateforme.
  * Reprise de lecture automatique à la dernière seconde vue pour les leçons vidéo.
  * Forum d'entraide communautaire par formation et messagerie privée avec les formateurs.
  * Système de marque-pages et notes personnelles sur les leçons.
* **Sécurité & Transactions** :
  * Double authentification (2FA) par code OTP e-mail pour tous les profils.
  * Intégration de la passerelle CinetPay v2 avec signature cryptographique `x-token` (HMAC-SHA256) et contrôle d'idempotence contre le double-traitement de notifications.
  * Middleware d'injection d'en-têtes HTTP de sécurité recommandés par l'OWASP.

---

## 🛠️ Stack Technique

* **Framework** : Laravel 11.x & PHP 8.2+
* **Style & UI** : Bootstrap 5, FontAwesome 6, JQuery 3.7 & CSS personnalisé (Thème Glassmorphism)
* **Permissions & Rôles** : Spatie Laravel Permission (Rôles : `apprenant`, `formateur`, `gestionnaire`, `administrateur`)
* **Paiements** : API Checkout CinetPay v2 (Mobile Money & Cartes bancaires)
* **Génération PDF** : Barryvdh Laravel DomPDF
* **Base de données** : SQLite (Tests en mémoire) / MySQL (Production)

---

## ⚙️ Installation & Configuration

1. **Cloner le projet** :
   ```bash
   git clone <repository_url>
   cd CabForm
   ```

2. **Installer les dépendances Composer** :
   ```bash
   composer install
   ```

3. **Configurer l'environnement** :
   * Copier le fichier `.env.example` en `.env` :
     ```bash
     cp .env.example .env
     ```
   * Renseigner la clé de chiffrement Laravel :
     ```bash
     php artisan key:generate
     ```
   * Configurer vos clés d'API **CinetPay** dans le fichier `.env` :
     ```env
     CINETPAY_API_KEY=votre_cle_api
     CINETPAY_SITE_ID=votre_site_id
     CINETPAY_SECRET_KEY=votre_cle_secrete
     ```

4. **Lancer les migrations et remplir la base de données** :
   ```bash
   php artisan migrate --seed
   ```

---

## 🧪 Lancement des Tests

L'application contient une suite de tests automatisés exhaustive validant la double authentification, le déblocage séquentiel, le versionnage des cours, les calculs de progression, la modération, la sécurisation des webhooks CinetPay et les exports d'administration.

Pour lancer tous les tests unitaires et de fonctionnalités :
```bash
.\vendor\bin\phpunit
```

---

## 📁 Architecture des répertoires clés

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/             # Authentification (login, 2FA, OTP)
│   │   ├── Learner/          # Espace Apprenant (Player, Messagerie, Forum, Quiz)
│   │   ├── Instructor/       # Espace Formateur (Création de cours, duplication)
│   │   └── Admin/            # Back-office (CRUD, Logs, Exports, Rapports)
│   └── Middleware/
│       └── AddSecurityHeaders.php  # Injection d'en-têtes HTTP de sécurité
├── Models/                   # Modèles Eloquent (Course, Lesson, Progress, Bookmark, etc.)
├── Services/                 # Services métier (QuizService, CertificateService, ProgressService)
└── Jobs/                     # File d'attente (Génération PDF asynchrone)
```
