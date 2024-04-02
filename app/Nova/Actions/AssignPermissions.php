<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class AssignPermissions extends Action
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
        // if ($models->count() > 1) {
        //     return Action::danger('Please run this action on only one user resource');
        // }
        // Get all input data from the request

        foreach ($models as $user) {
            $user->givePermissionTo($fields->permission);
        }

        // $user->givePermissionTo('edit articles');

        return ActionResponse::message('Permissions(' . $fields->permission . ') assinged to ' . $models->count() . ' users.');
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

            Select::make('Permission')->options(\Spatie\Permission\Models\Permission::pluck('name', 'name')),
        ];
    }
}
