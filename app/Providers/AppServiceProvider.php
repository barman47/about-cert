<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use PhpOffice\PhpWord\Settings;
use App\Repositories\DocumentSignatureRepository;
use App\SignatureGateway\SignatureGatewayAbstractClass;
use App\Observers\AlertObserver;
use App\Alert;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);
        Settings::setPdfRendererPath(base_path() . "/vendor/dompdf/dompdf");

        Alert::observe(AlertObserver::class);

        $this->app->bind(DocumentSignatureRepository::class, function($app){
            return new DocumentSignatureRepository(SignatureGatewayAbstractClass::getGatewayInstance());
        });
    }
}
