<?php

namespace Database\Seeders;

use App\Modules\UserType\Enums\UserTypeIdEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date = now();

        DB::table('permissions')->insertOrIgnore([
            [ 'id' => 1, 'name' => 'user', 'description' => 'Acesso ao módulo de usuários', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 2, 'name' => 'user-type', 'description' => 'Acesso ao módulo de tipos de usuário', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 3, 'name' => 'user-type.options', 'description' => 'Acesso à opções de tipos de usuário', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 4, 'name' => 'audit-log', 'description' => 'Acesso ao módulo de auditoria', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 5, 'name' => 'state', 'description' => 'Acesso ao módulo de Estados', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 6, 'name' => 'state.options', 'description' => 'Acesso à opções de Estados', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 7, 'name' => 'city', 'description' => 'Acesso ao módulo de Cidades', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 8, 'name' => 'city.options', 'description' => 'Acesso à opções de Cidades', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 9, 'name' => 'address', 'description' => 'Acesso ao módulo de Endereços', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 10, 'name' => 'document-type', 'description' => 'Acesso ao módulo de Tipos de Documentos', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 11, 'name' => 'document-type.options', 'description' => 'Acesso à opções de Tipos de Documentos', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 12, 'name' => 'report', 'description' => 'Acesso ao módulo de Exportações', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 13, 'name' => 'permission', 'description' => 'Acesso ao módulo de permissões', 'created_at' => $date, 'updated_at' => $date ],
            [ 'id' => 14, 'name' => 'permission.all', 'description' => 'Lista com todas as permissões', 'created_at' => $date, 'updated_at' => $date ],
        ]);

        DB::table('permission_user_type')->insertOrIgnore([
            // ADMINISTRATOR PERMISSIONS
            [ 'permission_id' => 1, 'user_type_id' => UserTypeIdEnum::ADMINISTRATOR ],
            [ 'permission_id' => 3, 'user_type_id' => UserTypeIdEnum::ADMINISTRATOR ],
            [ 'permission_id' => 6, 'user_type_id' => UserTypeIdEnum::ADMINISTRATOR ],
            [ 'permission_id' => 8, 'user_type_id' => UserTypeIdEnum::ADMINISTRATOR ],
            [ 'permission_id' => 9, 'user_type_id' => UserTypeIdEnum::ADMINISTRATOR ],
            [ 'permission_id' => 11, 'user_type_id' => UserTypeIdEnum::ADMINISTRATOR ],
            [ 'permission_id' => 12, 'user_type_id' => UserTypeIdEnum::ADMINISTRATOR ],
        ]);
    }
}
