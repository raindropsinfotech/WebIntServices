<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;

class Order extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Order>
     */
    public static $model = \App\Models\Order::class;

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
        'id', 'shopOrderNumber',
    ];

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
            ID::make('id', 'Id')->readonly()->sortable(),
            Text::make('shopOrderNumber', 'ShopOrderNumber'),
            Select::make('status', 'Status')->options([
                0 => 'New',
                1 => 'PartiallyCompleted',
                2 => 'Completed',
                3 => 'Cancelled'
            ])->displayUsingLabels()->filterable(),
            Text::make('customerName', 'CustomerName'),
            Text::make('customerEmail', 'CustomerEmail'),
            DateTime::make('created_at', 'CreatedAt')->readonly(),
            DateTime::make('updated_at', 'UpdatedAt')->readonly(),
            Text::make('external_connection_id')->nullable(),
            HasMany::make('Order Items', 'orderItems', OrderItem::class),
            Badge::make('PaymentStatus', 'PaymentStatus')
                ->withIcons()
                ->map([
                    '0' => 'danger',
                    '1' => 'danger',
                    '2' => 'success',
                    '3' => 'warning'
                ]),
            Number::make('total', 'OrderTotal'),
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

    /**
     * Get the menu that should represent the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Nova\Menu\MenuItem
     */
    public function menu(Request $request)
    {
        return parent::menu($request)->withBadge(function () {
            return static::$model::where('PaymentStatus', 2)->where('Status', 0)
                ->orWhere('Status', 1)
                ->count();
        });
    }
}
