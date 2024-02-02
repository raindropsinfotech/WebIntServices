<?php

namespace App\Nova;


use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class OrderItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\OrderItem>
     */
    public static $model = \App\Models\OrderItem::class;

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
            Text::make('externalId', 'ExrenalId'),
            BelongsTo::make('Product', 'product', Product::class)->display('FullName'),
            BelongsTo::make('Order', 'order', Order::class)->display('ShopOrderNumber')->readonly(),
            Text::make('adults', 'Adults'),
            Text::make('children', 'Children'),
            DateTime::make('serviceDateTime', 'ServiceDateTime'),
            Boolean::make('isProcessed', 'IsProcessed'),
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
