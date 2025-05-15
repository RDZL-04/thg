<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api/helper')
                ->group(base_path('routes/api_helper.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
			
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_hotel.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_facility.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_msystem.php'));
                
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_outlet.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_user.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_role.php'));
			
			Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_permission.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_promo.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_table.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_payment.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_mice.php'));
                
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_hall.php'));
                
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_attraction.php'));
				
			Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/token.php'));
            
                //Route prefix api_user
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_user.php'));
                
           Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_profile.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_reservation.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_hotel.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_facility.php'));
            
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_msystem.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_outlet.php'));
            
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_menu.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_review.php'));
            
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_role.php'));
			
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_near_attraction.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_promo.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_table.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_order_dining.php'));
            
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_log_error.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_allo.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_member.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_mice.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_hall.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_request_proposal.php'));
				
			Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_attraction.php'));


        });
    }

    // Edited oleh Arka.Rangga 20210622
    // Kebutuhan Custom Response, bisa sesuai bahasa di file resource/lang/{lang-id}/message.php

    /**
     * Max Attemps for hit request api
     *
     * Isi value dengan numeric, menandakan jumlah request dalam 1 menit
     *
     * @var numeric
     */
    protected $maxAttempts = 1000;

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            // return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
            return Limit::perMinute($this->maxAttempts)->by(optional($request->user())->id ?: $request->ip())->response(function () {
                return $this->buildResponse();
            });
        });
    }

    /**
     * Create a 'too many attempts' response.
     *
     * @param  string $key
     * @param  int $maxAttempts
     * @return \Illuminate\Http\Response
     */
    protected function buildResponse()
    {
        $message = [
            'status' => false,
            'message' => __('message.throttle-many-attempts'),
            'data' => null,
            'errorCode' => 4029

        ];

        return new Response($message, 429);
    }
}
