<?php
/**
 * Created by PhpStorm.
 * User: arthur
 * Date: 29.10.18
 * Time: 12:06.
 */

namespace App\Modules\Authorization\Transformers;

use App\Modules\Authorization\Models\Role;
use Larapie\Transformer\Transformer;

class RoleTransformer extends Transformer
{
    public $include = [
        'permissions',
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ];
    }

    protected function includePermissions($permissions)
    {
        return $permissions->pluck('name');
    }
}
