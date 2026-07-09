<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Création des rôles et permissions RBAC.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ──────────────────────────────────────────
        $permissions = [
            // Cours
            'courses.view', 'courses.create', 'courses.edit', 'courses.delete', 'courses.publish',
            // Modules & Leçons
            'modules.manage', 'lessons.manage', 'resources.manage',
            // Utilisateurs
            'users.view', 'users.create', 'users.edit', 'users.delete', 'users.manage_roles',
            // Inscriptions
            'enrollments.view', 'enrollments.manage', 'enrollments.create',
            // Quiz & Évaluations
            'quizzes.view', 'quizzes.create', 'quizzes.manage', 'quizzes.grade',
            // Certificats
            'certificates.view', 'certificates.generate', 'certificates.revoke',
            // Paiements & Commandes
            'payments.view', 'payments.manage', 'orders.view', 'orders.manage',
            // CMS
            'pages.manage', 'blog.manage', 'banners.manage', 'faqs.manage',
            // Catégories
            'categories.manage',
            // Partenaires
            'partners.view', 'partners.manage',
            // Coupons
            'coupons.manage',
            // Paramètres
            'settings.manage',
            // Audit
            'audit.view',
            // Rapports
            'reports.view', 'reports.export',
            // Forum
            'forum.moderate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Rôles ────────────────────────────────────────────────

        // Administrateur - accès complet
        $admin = Role::firstOrCreate(['name' => 'administrateur']);
        $admin->givePermissionTo(Permission::all());

        // Gestionnaire de scolarité
        $manager = Role::firstOrCreate(['name' => 'gestionnaire']);
        $manager->givePermissionTo([
            'courses.view', 'users.view', 'users.edit',
            'enrollments.view', 'enrollments.manage', 'enrollments.create',
            'payments.view', 'orders.view',
            'certificates.view', 'certificates.generate',
            'reports.view',
        ]);

        // Formateur
        $instructor = Role::firstOrCreate(['name' => 'formateur']);
        $instructor->givePermissionTo([
            'courses.view', 'courses.create', 'courses.edit',
            'modules.manage', 'lessons.manage', 'resources.manage',
            'quizzes.view', 'quizzes.create', 'quizzes.manage', 'quizzes.grade',
            'enrollments.view',
            'forum.moderate',
        ]);

        // Apprenant
        $learner = Role::firstOrCreate(['name' => 'apprenant']);
        $learner->givePermissionTo([
            'courses.view', 'quizzes.view', 'certificates.view',
        ]);

        // Partenaire / Entreprise (B2B)
        $partner = Role::firstOrCreate(['name' => 'partenaire']);
        $partner->givePermissionTo([
            'courses.view', 'enrollments.view', 'enrollments.create',
            'reports.view', 'partners.view',
        ]);

        $this->command->info('✅ Rôles et permissions créés avec succès !');
    }
}
