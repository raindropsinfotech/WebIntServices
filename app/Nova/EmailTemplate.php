<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class EmailTemplate extends Resource
{

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\EmailTemplate>
     */
    public static $model = \App\Models\EmailTemplate::class;

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
            ID::make('id', 'Id')->sortable(),
            Text::make('name', 'Name')->sortable(),
            Text::make('path', 'Path')->sortable(),
            Code::make('content', 'Content')->sortable(),
            Boolean::make('isActive', 'IsActive')->sortable(),
            Text::make('subject', 'Subject')->sortable(),
            // Text::make('emailTemplateType', 'EmailTemplateType')->sortable(),
            Select::make('emailTemplateType', 'EmailTemplateType')->options([
                0 => 'NA',
                1 => 'TicketsAsAttachment',
                2 => 'TicketAsDownloadLink',
                3 => 'PaymentLink',
                40 => 'ThnakYou',
                41 => 'ReviewRequest',
                51 => 'InformationRequired',
                52 => 'Delay',
                100 => 'Newsletter'
            ])->displayUsingLabels()->filterable(),
            BelongsTo::make('External Connection', 'externalConnection', ExternalConnection::class)->nullable()->display('name')
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
