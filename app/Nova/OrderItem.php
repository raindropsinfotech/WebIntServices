<?php

namespace App\Nova;

use App\Nova\Filters\OrderItemStatus;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Number as FieldsNumber;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Number;

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
        'id', 'order.ShopOrderNumber',
    ];

    /**
     * Get the default ordering for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public static function defaultOrder(NovaRequest $request)
    // {
    //     return [
    //         ['Id', 'asc'], // Default sorting by ID in ascending order
    //     ];
    // }

    /**
     * The visual style used for the table. Available options are 'tight' and 'default'.
     *
     * @var string
     */
    public static $tableStyle = 'tight';
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
            Boolean::make('Processed', 'IsProcessed')->filterable(),
            BelongsTo::make('Order', 'order', Order::class)->display('ShopOrderNumber')->readonly(),
            Text::make('externalId', 'ExrenalId')->showOnDetail(),
            Select::make('Product', 'ProductId')
                ->options(\App\Models\Product::where('ProductType', 0)->pluck('FullName', 'Id'))->onlyOnForms(),

            BelongsTo::make('Product', 'product', Product::class)->display('FullName')
                ->exceptOnForms(),
            FieldsNumber::make('Adults', 'Adults'),
            Text::make('Kids', 'Children'),
            Date::make('ServiceDate', 'ServiceDateTime')
                ->displayUsing(function ($value) {
                    return $value->format('d M Y'); // Customize the date format as per your preference
                })
                ->filterable()->required(),
            Boolean::make('Pospond Delivery', 'PostpondDelivery'),
            DateTime::make('created_at', 'CreatedAt')->readonly()->onlyOnDetail(),
            DateTime::make('updated_at', 'UpdatedAt')->readonly()->onlyOnDetail(),
            DateTime::make('ProcessDateTime', 'ProcessDateTime')->filterable()
                ->displayUsing(function ($value) {
                    if ($value)
                        return $value->format('d M Y H m'); // Customize the date format as per your preference
                }),
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
        return [
            // new OrderItemStatus(),
        ];
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

    /**
     * Get the menu that should represent the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Nova\Menu\MenuItem
     */
    public function menu(Request $request)
    {
        return parent::menu($request)->withBadge(function () {
            return static::$model::where('IsProcessed', false)->count();
        });
    }
}
