<?php

class UsersSetRolesController extends ZurmoBulkController
{

    public $title = 'Обновление ролей';

    public function actionProcessStep($step, $id, $roleId)
    {
        if (!$id) {
            throw new InvalidArgumentException('Отсутствует ID пользователя');
        }

        if (!$roleId) {
            return;
        }

        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        $role = Role::findOne($roleId);
        if (!$role) {
            throw new NotFoundHttpException('Роль не найдена');
        }

        ReadPermissionsOptimizationUtil::userBeingRemovedFromRole($user, $role, $step);
        ReadPermissionsOptimizationUtil::userAddedToRole($user, $step);
    }

    public function actionAfterProcess()
    {
        ReadPermissionsSubscriptionUtil::userAddedToRole();
        RightsCache::invalidateAll();
        PoliciesCache::invalidateAll();
    }
}
