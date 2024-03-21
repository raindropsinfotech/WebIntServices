<?php

namespace App\Nova;

use App\Nova\Actions\AssignPermissions;
use App\Nova\Actions\AssignRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;


class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\User>
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Gravatar::make()->maxWidth(50),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),

            Boolean::make('allow_nova_access'),
            // BelongsToMany::make('Roles', 'roles', Role::class),

            // Select::make('Roles')->options(\Spatie\Permission\Models\Role::pluck('name', 'name')),
            HasMany::make('permissions'),
            HasMany::make('roles'),
            HasMany::make('audits', 'audits', Audit::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new AssignRole)->canSee(function ($request) {
                return true;
            }),
            (new AssignPermissions)->canSee(function ($request) {
                return true;
            }),

            Actions\RemovePermission::make(),

        ];
    }

    // public function update(Request $request, \App\Models\User $user)
    // {
    //     // update the user

    //     $user->update($request->all());

    //     if ($request->has('roles')) {
    //         $roles = $request->input('roles');
    //         $user->assignRole($roles);
    //     }

    //     return parent::update($request, $user);
    // }

    // public static function afterUpdate(NovaRequest $request, Model $model)
    // {
    //     if ($request->has('roles')) {
    //         $roles = $request->input('roles');
    //         $model->assignRole($roles);
    //     }
    // }
}
