<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Permission\Models\Role;

use function PHPUnit\Framework\isNan;
use function PHPUnit\Framework\isNull;

class RemoveRole extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if ($models->count() > 1) {
            return Action::danger('Please run this action on only one user resource');
        }

        $user = $models->first();
        if (isset($fields->role)) {
            $roleToRemove = Role::findById($fields->role);
            $user->removeRole($roleToRemove->name);

            return ActionResponse::message('Role(' . $roleToRemove->name . ') removed from User(' . $models->first()->name . ').');
        }
        // foreach ($models as $user) {
        //     $user->removeRole($fields->role->name);
        // }

        // $user->givePermissionTo('edit articles');

        return ActionResponse::message('nothing happened!');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Role')->options($request->user()->roles()->pluck('name', 'id')),
        ];
    }
}
