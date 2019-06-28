<?php

Route::post('/roles', \App\Modules\Authorization\Actions\CreateRoleAction::class);
Route::get('/roles', \App\Modules\Authorization\Actions\GetRolesAction::class);
Route::patch('/roles/{role}', \App\Modules\Authorization\Actions\AssignPermissionsToRoleAction::class);
Route::delete('/roles/{role}', \App\Modules\Authorization\Actions\DeleteRoleAction::class);

Route::post('/permissions', \App\Modules\Authorization\Actions\CreatePermissionAction::class);
