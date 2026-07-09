# PROMPT DE DÉVELOPPEMENT — PLATEFORME WEB DE FORMATION & CERTIFICATION EN LIGNE (LMS)

> **Rôle à adopter :** Tu es un **développeur web full-stack senior (12 ans d'expérience)**, expert en applications e-learning sécurisées et scalables. Tu maîtrises parfaitement la stack ci-dessous et tu appliques les bonnes pratiques d'architecture, de sécurité (OWASP), de performance et de maintenabilité. Tu produis un code propre, commenté, testé et documenté.

---

## 1. OBJECTIF DU PROJET

Concevoir et développer une **plateforme web de formation et de certification en ligne (LMS)** pour un cabinet, inspirée de la plateforme de certification de l'UVCI (https://certification.uvci.online/). La plateforme doit permettre de :
- publier un catalogue de formations certifiantes ;
- gérer l'inscription, le paiement en ligne et l'accès aux contenus ;
- assurer le suivi pédagogique et l'évaluation des apprenants ;
- générer et vérifier des certificats ;
- piloter l'ensemble via un back-office complet.

---

## 2. STACK TECHNIQUE IMPOSÉE (À RESPECTER STRICTEMENT)

### Front-end
- **HTML5**, **CSS3**
- **Bootstrap 5** (grille responsive, composants)
- **jQuery** + **Ajax** (interactions dynamiques, requêtes asynchrones, validations, chargement partiel)
- **Police (font-family) : `DM Sans`** (importée depuis Google Fonts) appliquée globalement
- **Font Awesome** pour toutes les icônes
- Approche **mobile-first**, responsive sur mobile / tablette / desktop

### Back-end
- **Laravel 11+** (PHP 8.2+)
- Architecture MVC, **Eloquent ORM**, **migrations & seeders**, **Form Requests** (validation), **Policies/Gates** (autorisations), **Middleware**, **Events/Listeners**, **Queues** (jobs asynchrones : e-mails, génération de certificats), **Notifications** (mail + base de données)
- **Blade** pour le rendu des vues (intégré avec Bootstrap/jQuery/Ajax)
- **Laravel Breeze / Fortify** (ou auth maison) pour l'authentification ; **spatie/laravel-permission** pour les rôles & permissions (RBAC)

### Base de données
- **MySQL 8+**
- Modélisation relationnelle normalisée, clés étrangères, index, contraintes d'intégrité
- Migrations Laravel versionnées

### Paiement en ligne
- **CinetPay** comme agrégateur pour :
  - **Mobile Money : Orange Money, MTN Money, Moov Money, Wave**
  - **Carte bancaire (Visa/Mastercard)**
- Intégration via l'**API CinetPay** : initialisation de paiement, redirection/checkout, **URL de notification (webhook/IPN)** pour confirmer la transaction côté serveur, vérification du statut, gestion des cas succès/échec/annulation, idempotence et rapprochement des transactions.

> ⚠️ Toutes les autres exigences du cahier des charges (fonctionnelles, sécurité/RGPD, performances, intégrations, hébergement, livrables, etc.) sont **maintenues**.

---

## 3. RÔLES & PERMISSIONS (RBAC via spatie/laravel-permission)

- **Visiteur** : catalogue, fiches formation, inscription, vérification de certificat.
- **Apprenant** : achat/inscription, suivi des cours, quiz/examens, certificats, profil.
- **Formateur** : création/édition de cours, ressources, évaluations, correction, suivi.
- **Gestionnaire de scolarité** : inscriptions, sessions, paiements, attestations, support N1.
- **Administrateur** : gestion complète (utilisateurs, rôles, catalogue, tarifs, paramètres, CMS, statistiques).
- **Partenaire/Entreprise (B2B)** : inscription de groupes, suivi des collaborateurs, rapports.

---

## 4. FONCTIONNALITÉS À DÉVELOPPER

### 4.1. Site vitrine (public)
- Page d'accueil : bannière, **certifications mises en avant**, appels à l'action.
- Catalogue : recherche **Ajax**, filtres (catégorie, niveau, prix, durée, langue), tri, pagination.
- Fiche formation : objectifs, programme (modules/leçons), prérequis, durée, formateur, tarif, avis, bouton d'inscription.
- Pages : À propos, Contact (formulaire Ajax), FAQ, Mentions légales, CGU/CGV, Politique de confidentialité, **Tarification**.
- Blog/Actualités (SEO).
- **Vérification publique de certificat** (par n° unique ou QR code).
- Multilingue FR (par défaut) / EN (option), SEO (meta, sitemap, URLs propres/slugs).

### 4.2. Comptes & authentification
- Inscription e-mail/mot de passe, connexion, vérification e-mail, mot de passe oublié, **2FA optionnelle**.
- Connexion sociale (Google/Facebook) en option (Laravel Socialite).
- Profil : infos, photo, historique, certificats.

### 4.3. Gestion des formations (Formateur/Admin)
- Structure : **Formation → Modules → Leçons → Ressources**.
- Multi-formats : vidéo (upload/streaming/embed), PDF, diaporamas, audio, texte enrichi, liens ; **SCORM/xAPI en option**.
- Éditeur WYSIWYG + bibliothèque de médias.
- Sessions/cohortes, dates, capacité, self-paced.
- Prérequis, **déblocage séquentiel** des modules, duplication, versionnage.
- Workflow de validation avant publication.

### 4.4. Espace apprenant
- Tableau de bord : formations en cours, **progression (%)**, échéances, certificats.
- Lecteur de cours avec suivi automatique et **reprise à la dernière position**.
- Notes, marque-pages, téléchargement des ressources autorisées.
- Notifications (e-mail + in-app via Ajax/polling ou broadcasting).
- Forum / Q&R, messagerie avec le formateur.

### 4.5. Évaluations & certification
- Quiz/examens : QCM, vrai/faux, réponses courtes, appariement, questions ouvertes, **banque de questions aléatoires**.
- Paramétrage : durée, tentatives, note de passage, correction auto et/ou manuelle.
- Devoirs (upload) avec feedback.
- **Génération automatique de certificats PDF** (modèle, logo, signature, **n° unique + QR code**) via job en file d'attente.
- Vérification en ligne d'authenticité.

### 4.6. Paiement & commercialisation (CinetPay)
- Panier / tunnel d'achat ; inscription directe pour formations gratuites.
- **Mobile Money (Orange, MTN, Moov, Wave) + carte** via CinetPay.
- Codes promo, tarifs de groupe (B2B), paiement échelonné (option).
- **Webhook/IPN** de confirmation, vérification serveur du statut, sécurisation (signature/token), idempotence.
- Facturation automatique (factures/reçus PDF), historique des transactions, remboursements, gestion des accès selon statut de paiement.

### 4.7. Back-office & pilotage
- CRUD complet : utilisateurs, formations, catégories, tarifs, sessions, paiements, certificats.
- **Tableaux de bord & statistiques** (inscriptions, revenus, complétion, performance par formation/formateur).
- Export CSV/Excel, rapports périodiques.
- CMS du site vitrine (pages, bannières, actualités, FAQ).
- **Journal d'audit** des actions sensibles.

---

## 5. MODÈLE DE DONNÉES (MySQL — indicatif à compléter)

Tables principales à créer via migrations :
- `users`, `roles`, `permissions`, `model_has_roles`, `role_has_permissions`
- `profiles`, `partners` (B2B)
- `categories`, `courses`, `modules`, `lessons`, `resources`
- `sessions_cohorts`, `enrollments`, `progress`
- `quizzes`, `questions`, `answers`, `quiz_attempts`, `assignments`, `submissions`
- `certificates` (n° unique, hash, QR, statut), `certificate_templates`
- `orders`, `order_items`, `payments` (référence CinetPay, statut, canal), `invoices`, `coupons`
- `forum_threads`, `forum_posts`, `messages`, `notifications`
- `pages`, `banners`, `posts` (blog), `faqs`, `audit_logs`, `settings`

Respecter : clés étrangères, **contraintes ON DELETE**, index sur colonnes filtrées/recherchées, timestamps, soft deletes le cas échéant.

---

## 6. EXIGENCES FRONT-END DÉTAILLÉES

- Importer **DM Sans** (`@import`/`<link>` Google Fonts) et l'appliquer sur `body { font-family: 'DM Sans', sans-serif; }`.
- Utiliser **Font Awesome** (CDN ou assets) pour toutes les icônes.
- **Bootstrap 5** pour la grille, les composants (navbar, cards, modals, offcanvas, toasts, accordions).
- **jQuery + Ajax** pour : recherche/filtres du catalogue sans rechargement, validation de formulaires côté client, soumission asynchrone (contact, quiz, panier), notifications toast, chargement paresseux (lazy loading), mise à jour de la progression.
- Design system cohérent : palette de la charte du cabinet, composants réutilisables (partials Blade), états de chargement (spinners), messages d'erreur/succès.
- Accessibilité (WCAG 2.1 AA visée), responsive mobile-first, images optimisées.

---

## 7. SÉCURITÉ & CONFORMITÉ (MAINTENUES)

- HTTPS/TLS, hachage des mots de passe (**bcrypt/argon2**), politique de mots de passe robuste.
- Protection **OWASP Top 10** : CSRF (jetons Laravel), XSS (échappement Blade, sanitation), injection SQL (Eloquent/requêtes préparées), validation via **Form Requests**, rate limiting, protection upload (types/taille/scan).
- **RBAC** (moindre privilège) via Policies/Gates + spatie.
- Sauvegardes automatiques, plan de reprise (PRA).
- **RGPD / ARTCI** : consentement cookies, droits d'accès/rectification/effacement, registre des traitements, politique de confidentialité.
- **Journalisation et traçabilité** (audit logs) des actions sensibles.
- Sécurisation du **webhook CinetPay** (vérification signature/token, idempotence, vérification côté serveur du montant et du statut).

---

## 8. PERFORMANCE & NON-FONCTIONNEL (MAINTENUS)

- Uptime ≥ 99,5 % ; pages < 3 s ; ≥ 1 000 utilisateurs simultanés (évolutif).
- Cache (Laravel cache, Redis en option), eager loading (éviter N+1), pagination, index MySQL.
- Streaming vidéo adaptatif via CDN, stockage média (S3 compatible en option).
- **Queues** pour tâches lourdes (mails, certificats, notifications).
- Sauvegardes quotidiennes (rétention ≥ 30 jours).

---

## 9. INTÉGRATIONS TIERCES (MAINTENUES)

- **Paiement : CinetPay** (Mobile Money + carte) — priorité.
- E-mailing transactionnel/marketing (SMTP, Mailgun/Brevo/Sendgrid).
- SMS/OTP (option), visioconférence (Zoom/Meet/Jitsi — option).
- Analytics (Google Analytics/Matomo), connexion & partage réseaux sociaux.
- **API REST documentée (Swagger/OpenAPI)** pour interconnexion future (CRM/ERP/app mobile).

---

## 10. QUALITÉ, TESTS & LIVRABLES

- **Tests** : unitaires & feature (PHPUnit/Pest), tests des flux critiques (auth, paiement CinetPay, inscription, génération de certificat).
- Code respectant **PSR-12**, commenté ; usage de **.env** pour la configuration (aucun secret en dur).
- **Seeders** de démonstration (rôles, catégories, formations, utilisateurs de test).
- **Documentation** : README d'installation, documentation technique (architecture, API), manuels (admin, formateur, apprenant).
- Dépôt **Git** avec branches, commits clairs, CI/CD (option).
- Environnements : **dev / staging / production**.

---

## 11. ARBORESCENCE PROJET ATTENDUE (Laravel — indicatif)

```
app/
 ├─ Http/Controllers/{Public,Auth,Learner,Instructor,Admin,Payment}
 ├─ Http/Requests/           # Form Requests (validation)
 ├─ Http/Middleware/
 ├─ Models/                  # User, Course, Module, Lesson, Enrollment, Payment, Certificate...
 ├─ Policies/                # Autorisations par rôle
 ├─ Services/                # CinetPayService, CertificateService, EnrollmentService...
 ├─ Jobs/                    # GenerateCertificate, SendNotification...
 └─ Notifications/
database/
 ├─ migrations/  ├─ seeders/  └─ factories/
resources/
 ├─ views/ (Blade + Bootstrap)  ├─ css/  └─ js/ (jQuery/Ajax)
routes/ (web.php, api.php)
tests/ (Feature, Unit)
```

---

## 12. LIVRABLE FINAL ATTENDU DE TOI

En te comportant comme le développeur senior décrit :
1. Propose l'**architecture** et le **schéma de base de données** (migrations MySQL).
2. Génère le **code Laravel 11+** (modèles, migrations, contrôleurs, Form Requests, policies, routes, services, jobs).
3. Génère les **vues Blade** intégrant **Bootstrap 5 + DM Sans + Font Awesome + jQuery/Ajax**.
4. Implémente l'**intégration CinetPay complète** (init paiement, checkout, webhook/IPN, vérification, statuts, factures).
5. Implémente **évaluations + génération de certificats PDF (QR code)**.
6. Fournis **seeders de démo, tests, et documentation d'installation**.
7. Respecte **toutes les exigences de sécurité, RGPD, performance et non-fonctionnelles** du cahier des charges.

> Procède de manière **structurée et itérative** (lot par lot), en expliquant brièvement chaque choix technique, et en produisant du code prêt à l'emploi.
