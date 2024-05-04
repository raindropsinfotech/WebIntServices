<?php

namespace App\Nova\Actions;

use App\Helpers\FTPHelpers;
use App\Helpers\RaynaHelpers;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

use function PHPUnit\Framework\isNull;

class ProcessViaApi extends Action
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
        $provider = $fields->provider;

        $user = Auth::user();
        if (is_null($user))
            return ActionResponse::danger('User is null');


        if ($provider == 3)
            return ActionResponse::danger('Function not available.');


        if ($provider == 2)
            return RaynaHelpers::ProcessOrderItems($models, $user);

        return ActionResponse::message('Function not available.');
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
            Select::make('provider')->options(Product::$orderProcessingTypes),
        ];
    }
}
