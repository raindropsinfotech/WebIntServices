<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class ExternalConnectionMapping extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ExternalConnectionMapping>
     */
    public static $model = \App\Models\ExternalConnectionMapping::class;

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
            ID::make()->sortable(),
            Select::make('External Connection', 'external_connection_id')
                ->options(\App\Models\ExternalConnection::pluck('name', 'id'))
                ->searchable()
                ->sortable()
                ->hideFromIndex(),

            Select::make('Shop Crednetial', 'shop_credential_id')
                ->options(\App\Models\Credential::where('Active', true)->pluck('name', 'Id'))
                ->searchable()
                ->sortable()->hideFromIndex(),

            Select::make('Payment Provider Crednetial', 'payment_provider_credential_id')
                ->options(\App\Models\Credential::where('Active', true)->pluck('name', 'Id'))
                ->searchable()
                ->sortable()->hideFromIndex(),

            Select::make('Mail Setting', 'mail_setting_id')
                ->options(\App\Models\MailSetting::pluck('Username', 'Id'))
                ->searchable()
                ->sortable()->hideFromIndex(),


            BelongsTo::make('External Connection', 'externalConnection', ExternalConnection::class)->searchable()->sortable()->display('name')->hideWhenCreating()->hideWhenUpdating(),
            BelongsTo::make('Shop Credential', 'shopCredential', Credential::class)->searchable()->display('Name')->readonly()->hideWhenCreating()->hideWhenUpdating(),
            BelongsTo::make('Payment Provider Credential', 'paymentProviderCredential', Credential::class)->searchable()->display('Name')->readonly()->hideWhenCreating()->hideWhenUpdating(),
            BelongsTo::make('Mail setting', 'mailSetting', MailSetting::class)->searchable()->display('Username')->readonly()->hideWhenCreating()->hideWhenUpdating(),

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
