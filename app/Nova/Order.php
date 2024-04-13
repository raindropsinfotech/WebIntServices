<?php

namespace App\Nova;

use App\Nova\Actions\CheckPayment;
use App\Nova\Actions\ReviewRequest;
use App\Nova\Actions\UpdateOrderStatusOnShop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\MorphMany;
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
        'id', 'shopOrderNumber', 'CustomerEmail'
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
            ID::make('Id', 'Id')->readonly()->sortable(),
            Text::make('Shop Order Number', 'ShopOrderNumber')->required(),
            Select::make('Status', 'Status')->options([
                0 => 'New',
                1 => 'PartiallyCompleted',
                2 => 'Completed',
                3 => 'Cancelled'
            ])->displayUsingLabels()->filterable()->hideWhenCreating()->default(0),
            Text::make('Customer Name', 'CustomerName')->required(),
            Email::make('Customer Email', 'CustomerEmail')->required(),
            Select::make('ExternalConnection', 'external_connection_id')->options(\App\Models\ExternalConnection::pluck('name', 'id'))->required()->onlyOnForms(),
            // BelongsTo::make('external_connection_id', 'externalConnection', \App\Models\ExternalConnection::class)->display('name')->exceptOnForms(),
            BelongsTo::make('ExternalConnection', 'externalConnection')->display('name')->exceptOnForms()->filterable(),
            HasMany::make('Order Items', 'orderItems', OrderItem::class),
            Badge::make('PaymentStatus', 'PaymentStatus')
                ->labels([
                    0 => 'Unpaid',
                    1 => 'PartiallyPaid',
                    2 => 'Paid',
                    3 => 'Refunded'
                ])
                ->withIcons()
                ->map([
                    '0' => 'danger',
                    '1' => 'danger',
                    '2' => 'success',
                    '3' => 'warning'
                ])->default(0)

                ->filterable(),
            Number::make('Order Total', 'OrderTotal'),
            Select::make('Payment Status', 'PaymentStatus')
                ->options([
                    0 => 'Unpaid',
                    1 => 'PartiallyPaid',
                    2 => 'Paid',
                    3 => 'Refunded'
                ])
                ->default(0)
                ->onlyOnForms()
                ->filterable()
                ->hideWhenCreating(),
            Date::make('Order Time', 'OrderDateTime')
                ->default(Carbon::today()),
            HasMany::make('Audits', 'audits', Audit::class),
            Text::make('PaymentReference', 'PaymentReference')->readonly(),
            MorphMany::make('Communications'),
            DateTime::make('Created at', 'CreatedAt')->readonly()->onlyOnDetail(),
            DateTime::make('Updated at', 'UpdatedAt')->readonly()->onlyOnDetail(),
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
            CheckPayment::make(),
            UpdateOrderStatusOnShop::make(),
            ReviewRequest::make(),
        ];
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
