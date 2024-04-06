<?php

namespace App\Providers;

use App\Models\City;
use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Faq;
use App\Models\Language;
use App\Models\Order;
use App\Models\Role;
use App\Models\Template;
use App\Models\TranslationDirection;
use App\Models\User;
use App\Policies\CityPolicy;
use App\Policies\CountryPolicy;
use App\Policies\DocumentTypePolicy;
use App\Policies\FaqPolicy;
use App\Policies\LanguagePolicy;
use App\Policies\OrderPolicy;
use App\Policies\RolePolicy;
use App\Policies\TemplatePolicy;
use App\Policies\TranslationDirectionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        City::class => CityPolicy::class,
        Country::class => CountryPolicy::class,
        Faq::class => FaqPolicy::class,
        Language::class => LanguagePolicy::class,
        Role::class => RolePolicy::class,
        TranslationDirection::class => TranslationDirectionPolicy::class,
        Order::class => OrderPolicy::class,
        Template::class => TemplatePolicy::class,
        DocumentType::class => DocumentTypePolicy::class
    ];

    public function boot()
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super-Admin') ? true : null;
        });
    }
}
