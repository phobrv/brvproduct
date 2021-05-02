<?php

namespace Phobrv\BrvProduct;

use Illuminate\Support\ServiceProvider;

class BrvProductServiceProvider extends ServiceProvider {
	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot(): void{
		// $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'phobrv');
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'phobrv');
		// $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
		$this->loadRoutesFrom(__DIR__ . '/routes.php');

		// Publishing is only necessary when using the CLI.
		if ($this->app->runningInConsole()) {
			$this->bootForConsole();
		}
	}

	/**
	 * Register any package services.
	 *
	 * @return void
	 */
	public function register(): void{
		$this->mergeConfigFrom(__DIR__ . '/../config/brvproduct.php', 'brvproduct');

		// Register the service the package provides.
		$this->app->singleton('brvproduct', function ($app) {
			return new BrvProduct;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return ['brvproduct'];
	}

	/**
	 * Console-specific booting.
	 *
	 * @return void
	 */
	protected function bootForConsole(): void{
		// Publishing the configuration file.
		// $this->publishes([
		// 	__DIR__ . '/../config/brvproduct.php' => config_path('brvproduct.php'),
		// ], 'brvproduct.config');

		// Publishing the views.
		$this->publishes([
			__DIR__ . '/../resources/views' => base_path('resources/views/vendor/phobrv'),
		], 'brvproduct.views');

		// Publishing assets.
		/*$this->publishes([
		__DIR__.'/../resources/assets' => public_path('vendor/phobrv'),
		], 'brvproduct.views');*/

		// Publishing the translation files.
		/*$this->publishes([
		__DIR__.'/../resources/lang' => resource_path('lang/vendor/phobrv'),
		], 'brvproduct.views');*/

		// Registering package commands.
		// $this->commands([]);
	}
}
