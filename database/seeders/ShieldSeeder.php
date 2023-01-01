<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"admin","guard_name":"web","permissions":["view_account","view_any_account","create_account","update_account","restore_account","restore_any_account","replicate_account","reorder_account","delete_account","delete_any_account","force_delete_account","force_delete_any_account","view_bank::account","view_any_bank::account","create_bank::account","update_bank::account","restore_bank::account","restore_any_bank::account","replicate_bank::account","reorder_bank::account","delete_bank::account","delete_any_bank::account","force_delete_bank::account","force_delete_any_bank::account","view_category","view_any_category","create_category","update_category","restore_category","restore_any_category","replicate_category","reorder_category","delete_category","delete_any_category","force_delete_category","force_delete_any_category","view_category::transaksi","view_any_category::transaksi","create_category::transaksi","update_category::transaksi","restore_category::transaksi","restore_any_category::transaksi","replicate_category::transaksi","reorder_category::transaksi","delete_category::transaksi","delete_any_category::transaksi","force_delete_category::transaksi","force_delete_any_category::transaksi","view_comment","view_any_comment","create_comment","update_comment","restore_comment","restore_any_comment","replicate_comment","reorder_comment","delete_comment","delete_any_comment","force_delete_comment","force_delete_any_comment","view_newsletter","view_any_newsletter","create_newsletter","update_newsletter","restore_newsletter","restore_any_newsletter","replicate_newsletter","reorder_newsletter","delete_newsletter","delete_any_newsletter","force_delete_newsletter","force_delete_any_newsletter","view_post","view_any_post","create_post","update_post","restore_post","restore_any_post","replicate_post","reorder_post","delete_post","delete_any_post","force_delete_post","force_delete_any_post","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_seo::detail","view_any_seo::detail","create_seo::detail","update_seo::detail","restore_seo::detail","restore_any_seo::detail","replicate_seo::detail","reorder_seo::detail","delete_seo::detail","delete_any_seo::detail","force_delete_seo::detail","force_delete_any_seo::detail","view_setting","view_any_setting","create_setting","update_setting","restore_setting","restore_any_setting","replicate_setting","reorder_setting","delete_setting","delete_any_setting","force_delete_setting","force_delete_any_setting","view_share::snippet","view_any_share::snippet","create_share::snippet","update_share::snippet","restore_share::snippet","restore_any_share::snippet","replicate_share::snippet","reorder_share::snippet","delete_share::snippet","delete_any_share::snippet","force_delete_share::snippet","force_delete_any_share::snippet","view_tag","view_any_tag","create_tag","update_tag","restore_tag","restore_any_tag","replicate_tag","reorder_tag","delete_tag","delete_any_tag","force_delete_tag","force_delete_any_tag","view_template::whatsapp","view_any_template::whatsapp","create_template::whatsapp","update_template::whatsapp","restore_template::whatsapp","restore_any_template::whatsapp","replicate_template::whatsapp","reorder_template::whatsapp","delete_template::whatsapp","delete_any_template::whatsapp","force_delete_template::whatsapp","force_delete_any_template::whatsapp","view_transaction::user","view_any_transaction::user","create_transaction::user","update_transaction::user","delete_transaction::user","delete_any_transaction::user","verify_transaction::user","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","page_ManageSetting","page_ManageWhatsapp","page_MyProfilePage","view_announcement","view_any_announcement","create_announcement","update_announcement","restore_announcement","restore_any_announcement","replicate_announcement","reorder_announcement","delete_announcement","delete_any_announcement","force_delete_announcement","force_delete_any_announcement","view_prayer::time","view_any_prayer::time","create_prayer::time","update_prayer::time","restore_prayer::time","restore_any_prayer::time","replicate_prayer::time","reorder_prayer::time","delete_prayer::time","delete_any_prayer::time","force_delete_prayer::time","force_delete_any_prayer::time","view_slider","view_any_slider","create_slider","update_slider","restore_slider","restore_any_slider","replicate_slider","reorder_slider","delete_slider","delete_any_slider","force_delete_slider","force_delete_any_slider","page_ManageAbout","page_Backups","view_activitylog","view_any_activitylog","create_activitylog","update_activitylog","restore_activitylog","restore_any_activitylog","replicate_activitylog","reorder_activitylog","delete_activitylog","delete_any_activitylog","force_delete_activitylog","force_delete_any_activitylog","view_commodity","view_any_commodity","create_commodity","update_commodity","restore_commodity","restore_any_commodity","replicate_commodity","reorder_commodity","delete_commodity","delete_any_commodity","force_delete_commodity","force_delete_any_commodity","view_commodity::acquisition","view_any_commodity::acquisition","create_commodity::acquisition","update_commodity::acquisition","restore_commodity::acquisition","restore_any_commodity::acquisition","replicate_commodity::acquisition","reorder_commodity::acquisition","delete_commodity::acquisition","delete_any_commodity::acquisition","force_delete_commodity::acquisition","force_delete_any_commodity::acquisition","view_commodity::location","view_any_commodity::location","create_commodity::location","update_commodity::location","restore_commodity::location","restore_any_commodity::location","replicate_commodity::location","reorder_commodity::location","delete_commodity::location","delete_any_commodity::location","force_delete_commodity::location","force_delete_any_commodity::location","view_event","view_any_event","create_event","update_event","restore_event","restore_any_event","replicate_event","reorder_event","delete_event","delete_any_event","force_delete_event","force_delete_any_event","view_exception","view_any_exception","create_exception","update_exception","restore_exception","restore_any_exception","replicate_exception","reorder_exception","delete_exception","delete_any_exception","force_delete_exception","force_delete_any_exception","page_DashboardInventaris","view_notification::template","view_any_notification::template","create_notification::template","update_notification::template","restore_notification::template","restore_any_notification::template","replicate_notification::template","reorder_notification::template","delete_notification::template","delete_any_notification::template","force_delete_notification::template","force_delete_any_notification::template","view_transaction","view_any_transaction","create_transaction","update_transaction","restore_transaction","restore_any_transaction","replicate_transaction","reorder_transaction","delete_transaction","delete_any_transaction","force_delete_transaction","force_delete_any_transaction","page_MyProfile","widget_TransactionStats","widget_CalendarWidget"]},{"name":"user","guard_name":"web","permissions":[]},{"name":"imam","guard_name":"web","permissions":[]},{"name":"pengurus","guard_name":"web","permissions":[]},{"name":"jamaah","guard_name":"web","permissions":[]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
