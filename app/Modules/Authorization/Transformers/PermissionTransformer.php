<?php
/**
 * Created by PhpStorm.
 * User: arthur
 * Date: 29.10.18
 * Time: 12:06.
 */

namespace App\Modules\Authorization\Transformers;

use App\Modules\Authorization\Models\Permission;
use Larapie\Transformer\Transformer;

class PermissionTransformer extends Transformer
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function transform(Permission $permission)
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'created_at' => $permission->created_at,
            'updated_at' => $permission->updated_at,
        ];
    }
}
