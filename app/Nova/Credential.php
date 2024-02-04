<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;


class Credential extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Credential>
     */
    public static $model = \App\Models\Credential::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
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
            ID::make('id', 'Id')->readonly()->sortable(),
            Text::make('name', 'Name'),
            Select::make('credentialType', 'CredentialType')->options([
                0 => 'Ecwid',
                1 => 'Bokun',
                3 => 'Rayna',
                4 => 'Stripe',
                5 => 'Razorpay',
                6 => 'Twilio',
                7 => 'CurrencyConverter'
            ])->displayUsingLabels()->filterable(),
            Boolean::make('active', 'Active'),
            Text::make('baseUrl', 'BaseUrl'),
            Text::make('username', 'Username'),
            Password::make('password', 'Password'),
            Markdown::make('comment', 'Comment'),
            DateTime::make('created_at', 'CreatedAt')->readonly(),
            DateTime::make('updated_at', 'UpdatedAt')->readonly(),
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
        return [];
    }
}
