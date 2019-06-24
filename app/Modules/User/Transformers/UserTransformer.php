<?php
/**
 * Created by PhpStorm.
 * User: arthur
 * Date: 29.10.18
 * Time: 12:06.
 */

namespace App\Modules\User\Transformers;

use App\Modules\User\Models\User;
use Larapie\Transformer\Transformer;

class UserTransformer extends Transformer
{
    public $include = [

    ];

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
