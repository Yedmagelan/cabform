<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Faq;
use App\Models\Setting;
use App\Models\CertificateTemplate;
use App\Models\Banner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    /**
     * Données de démonstration pour la plateforme CabForm.
     */
    public function run(): void
    {
        // ── Utilisateurs de démonstration ─────────────────────────
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'CabForm',
            'email' => 'admin@cabform.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $admin->assignRole('administrateur');

        $instructor1 = User::create([
            'first_name' => 'Aimé',
            'last_name' => 'Konan',
            'email' => 'formateur@cabform.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $instructor1->assignRole('formateur');

        $instructor2 = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Diallo',
            'email' => 'marie@cabform.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $instructor2->assignRole('formateur');

        $learner = User::create([
            'first_name' => 'Fanta',
            'last_name' => 'Kouamé',
            'email' => 'apprenant@cabform.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $learner->assignRole('apprenant');

        $manager = User::create([
            'first_name' => 'Paul',
            'last_name' => 'Bamba',
            'email' => 'gestionnaire@cabform.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $manager->assignRole('gestionnaire');

        // ── Catégories ───────────────────────────────────────────
        $categories = [
            ['name' => 'Informatique & Digital', 'slug' => 'informatique-digital', 'icon' => 'fa-laptop-code', 'color' => '#4d6bfe', 'description' => 'Développement web, mobile, cloud, cybersécurité et transformation digitale.'],
            ['name' => 'Management & Leadership', 'slug' => 'management-leadership', 'icon' => 'fa-users-cog', 'color' => '#00d97e', 'description' => 'Gestion d\'équipe, leadership, communication et stratégie d\'entreprise.'],
            ['name' => 'Finance & Comptabilité', 'slug' => 'finance-comptabilite', 'icon' => 'fa-chart-line', 'color' => '#f5a623', 'description' => 'Analyse financière, comptabilité, fiscalité et gestion budgétaire.'],
            ['name' => 'Marketing & Communication', 'slug' => 'marketing-communication', 'icon' => 'fa-bullhorn', 'color' => '#e63757', 'description' => 'Marketing digital, réseaux sociaux, branding et communication corporate.'],
            ['name' => 'Droit & Juridique', 'slug' => 'droit-juridique', 'icon' => 'fa-gavel', 'color' => '#39afd1', 'description' => 'Droit des affaires, droit du travail, conformité et RGPD.'],
            ['name' => 'Ressources Humaines', 'slug' => 'ressources-humaines', 'icon' => 'fa-user-tie', 'color' => '#6f42c1', 'description' => 'Recrutement, formation, gestion des talents et bien-être au travail.'],
        ];

        foreach ($categories as $i => $cat) {
            Category::create(array_merge($cat, ['sort_order' => $i, 'is_active' => true]));
        }

        // ── Formations de démonstration ──────────────────────────
        $coursesData = [
            [
                'category_id' => 1, 'instructor_id' => $instructor1->id,
                'title' => 'Développement Web Full-Stack avec Laravel & Bootstrap',
                'slug' => 'developpement-web-fullstack-laravel',
                'description' => 'Maîtrisez le développement web moderne avec PHP/Laravel côté serveur et Bootstrap/jQuery côté client. De la conception à la mise en production.',
                'objectives' => "Maîtriser HTML5, CSS3, JavaScript et jQuery\nDévelopper des applications Laravel complètes\nCréer des interfaces responsive avec Bootstrap 5\nGérer les bases de données avec Eloquent ORM\nDéployer une application en production",
                'level' => 'intermediaire', 'price' => 75000, 'duration_hours' => 60,
                'is_certified' => true, 'status' => 'published', 'published_at' => now(),
                'enrollment_count' => 234, 'rating' => 4.8, 'rating_count' => 89,
            ],
            [
                'category_id' => 2, 'instructor_id' => $instructor2->id,
                'title' => 'Leadership Transformationnel & Gestion d\'Équipe',
                'slug' => 'leadership-transformationnel',
                'description' => 'Développez votre posture de leader, apprenez à motiver et à conduire des équipes performantes dans un environnement en mutation.',
                'objectives' => "Comprendre les styles de leadership\nDévelopper l'intelligence émotionnelle\nGérer les conflits et la communication\nConduire le changement\nÉvaluer et développer les talents",
                'level' => 'avance', 'price' => 50000, 'duration_hours' => 32,
                'is_certified' => true, 'status' => 'published', 'published_at' => now(),
                'enrollment_count' => 178, 'rating' => 4.9, 'rating_count' => 67,
            ],
            [
                'category_id' => 3, 'instructor_id' => $instructor1->id,
                'title' => 'Analyse Financière & Business Intelligence',
                'slug' => 'analyse-financiere-bi',
                'description' => 'Approfondissez vos compétences en analyse financière et découvrez les outils de Business Intelligence pour la prise de décision stratégique.',
                'objectives' => "Analyser les états financiers\nCréer des tableaux de bord BI\nMaîtriser les ratios financiers\nÉlaborer des prévisions budgétaires\nPrendre des décisions éclairées",
                'level' => 'avance', 'price' => 85000, 'duration_hours' => 40,
                'is_certified' => true, 'status' => 'published', 'published_at' => now(),
                'enrollment_count' => 156, 'rating' => 4.7, 'rating_count' => 52,
            ],
            [
                'category_id' => 4, 'instructor_id' => $instructor2->id,
                'title' => 'Marketing Digital & Stratégie Réseaux Sociaux',
                'slug' => 'marketing-digital-reseaux-sociaux',
                'description' => 'Construisez une stratégie de marketing digital complète et maîtrisez les réseaux sociaux pour développer votre présence en ligne.',
                'level' => 'debutant', 'price' => 35000, 'duration_hours' => 24,
                'is_certified' => true, 'status' => 'published', 'published_at' => now(),
                'enrollment_count' => 312, 'rating' => 4.6, 'rating_count' => 98,
            ],
            [
                'category_id' => 1, 'instructor_id' => $instructor1->id,
                'title' => 'Introduction à la Cybersécurité',
                'slug' => 'introduction-cybersecurite',
                'description' => 'Découvrez les fondamentaux de la cybersécurité, les menaces actuelles et les bonnes pratiques pour protéger les systèmes d\'information.',
                'level' => 'debutant', 'price' => 0, 'is_free' => true, 'duration_hours' => 8,
                'is_certified' => false, 'status' => 'published', 'published_at' => now(),
                'enrollment_count' => 523, 'rating' => 4.5, 'rating_count' => 145,
            ],
            [
                'category_id' => 5, 'instructor_id' => $instructor2->id,
                'title' => 'Conformité RGPD & Protection des Données',
                'slug' => 'conformite-rgpd-protection-donnees',
                'description' => 'Mettez votre organisation en conformité avec le RGPD et les réglementations de protection des données personnelles.',
                'level' => 'intermediaire', 'price' => 45000, 'duration_hours' => 20,
                'is_certified' => true, 'status' => 'published', 'published_at' => now(),
                'enrollment_count' => 89, 'rating' => 4.8, 'rating_count' => 34,
            ],
        ];

        foreach ($coursesData as $courseData) {
            $course = Course::create($courseData);

            // Créer 3-4 modules par formation
            $moduleNames = [
                ['Introduction & Fondamentaux', 'Concepts Avancés', 'Pratique & Projets', 'Évaluation Finale'],
                ['Les Bases', 'Approfondissement', 'Cas Pratiques', 'Certification'],
            ];

            $modules = $moduleNames[array_rand($moduleNames)];
            foreach ($modules as $j => $moduleName) {
                $module = Module::create([
                    'course_id' => $course->id,
                    'title' => $moduleName,
                    'slug' => Str::slug($moduleName) . '-' . ($j + 1),
                    'description' => "Module " . ($j + 1) . " de la formation {$course->title}",
                    'sort_order' => $j,
                    'duration_minutes' => rand(60, 180),
                    'is_free_preview' => $j === 0,
                    'is_active' => true,
                ]);

                // 2-4 leçons par module
                $lessonTypes = ['video', 'text', 'pdf', 'video'];
                for ($k = 0; $k < rand(2, 4); $k++) {
                    Lesson::create([
                        'module_id' => $module->id,
                        'title' => "Leçon " . ($k + 1) . " — " . fake()->sentence(4),
                        'slug' => 'lecon-' . ($k + 1) . '-' . Str::random(5),
                        'type' => $lessonTypes[$k % count($lessonTypes)],
                        'duration_minutes' => rand(10, 45),
                        'sort_order' => $k,
                        'is_free_preview' => $j === 0 && $k === 0,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // ── FAQ ──────────────────────────────────────────────────
        $faqs = [
            ['category' => 'Général', 'question' => 'Qu\'est-ce que CabForm ?', 'answer' => 'CabForm est une plateforme de formation et de certification en ligne qui propose des formations professionnelles certifiantes dans divers domaines.'],
            ['category' => 'Général', 'question' => 'Comment m\'inscrire ?', 'answer' => 'Cliquez sur le bouton "Inscription" en haut de la page, remplissez le formulaire avec vos informations et vous recevrez un email de confirmation.'],
            ['category' => 'Formation', 'question' => 'Les formations sont-elles certifiantes ?', 'answer' => 'Oui, la majorité de nos formations délivrent un certificat vérifiable par QR code à l\'issue de la formation et après validation des évaluations.'],
            ['category' => 'Formation', 'question' => 'Puis-je suivre une formation à mon rythme ?', 'answer' => 'Absolument ! Nos formations sont en mode self-paced, vous pouvez les suivre à votre rythme, quand et où vous voulez.'],
            ['category' => 'Paiement', 'question' => 'Quels moyens de paiement acceptez-vous ?', 'answer' => 'Nous acceptons Orange Money, MTN Money, Moov Money, Wave et les cartes bancaires (Visa/Mastercard) via notre partenaire CinetPay.'],
            ['category' => 'Paiement', 'question' => 'Puis-je obtenir un remboursement ?', 'answer' => 'Oui, vous pouvez demander un remboursement dans les 14 jours suivant l\'achat si vous n\'avez pas complété plus de 20% de la formation.'],
            ['category' => 'Certificat', 'question' => 'Comment vérifier l\'authenticité d\'un certificat ?', 'answer' => 'Rendez-vous sur notre page de vérification de certificat et scannez le QR code ou saisissez le numéro unique du certificat.'],
            ['category' => 'Certificat', 'question' => 'Quelle est la durée de validité d\'un certificat ?', 'answer' => 'Nos certificats sont valables 3 ans à compter de leur date de délivrance. Ils sont ensuite renouvelables.'],
        ];

        foreach ($faqs as $i => $faq) {
            Faq::create(array_merge($faq, ['sort_order' => $i, 'is_active' => true]));
        }

        // ── Modèle de certificat par défaut ──────────────────────
        CertificateTemplate::create([
            'name' => 'Certificat Standard CabForm',
            'description' => 'Modèle de certificat par défaut de la plateforme CabForm',
            'logo_path' => 'assets/img/Logo-CabForm.png',
            'signatory_name' => 'Direction CabForm',
            'signatory_title' => 'Directeur de la Formation',
            'is_default' => true,
            'is_active' => true,
        ]);

        // ── Paramètres ───────────────────────────────────────────
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'CabForm', 'type' => 'string'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Excellence en Formation & Certification', 'type' => 'string'],
            ['group' => 'general', 'key' => 'site_email', 'value' => 'contact@cabform.com', 'type' => 'string'],
            ['group' => 'general', 'key' => 'site_phone', 'value' => '+225 XX XX XX XX', 'type' => 'string'],
            ['group' => 'general', 'key' => 'site_address', 'value' => 'Abidjan, Côte d\'Ivoire', 'type' => 'string'],
            ['group' => 'payment', 'key' => 'currency', 'value' => 'XOF', 'type' => 'string'],
            ['group' => 'payment', 'key' => 'tax_rate', 'value' => '0', 'type' => 'integer'],
            ['group' => 'certificate', 'key' => 'validity_years', 'value' => '3', 'type' => 'integer'],
        ];

        foreach ($settings as $setting) {
            Setting::create(array_merge($setting, ['is_public' => true]));
        }

        $this->command->info('✅ Données de démonstration créées avec succès !');
        $this->command->info('   📧 Admin: admin@cabform.com / password');
        $this->command->info('   📧 Formateur: formateur@cabform.com / password');
        $this->command->info('   📧 Apprenant: apprenant@cabform.com / password');
    }
}
