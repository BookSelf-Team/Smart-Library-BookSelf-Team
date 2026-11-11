<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use App\Policies\BookPolicy;
use App\Policies\FinePolicy;
use App\Policies\LoanPolicy;
use App\Policies\UserPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Book::class, BookPolicy::class);
        Gate::policy(Loan::class, LoanPolicy::class);
        Gate::policy(Fine::class, FinePolicy::class);

        Gate::define('manage-users', fn (User $user) => $user->canManageUsers());
        Gate::define('manage-settings', fn (User $user) => $user->canManageSettings());
        Gate::define('manage-books', fn (User $user) => $user->canManageBooks());
        Gate::define('circulate', fn (User $user) => $user->canCirculate());
        Gate::define('view-reports', fn (User $user) => $user->canViewReports());
        Gate::define('is-staff', fn (User $user) => $user->isStaff());
        Gate::define('is-member', fn (User $user) => $user->isMember());
    }
}
