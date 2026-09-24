<?php

namespace App\Providers;

use App\Enums\Permission;
use App\Models\User;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Types\BooleanType;
use Dedoc\Scramble\Support\Generator\Types\ObjectType;
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
        foreach (Permission::cases() as $permission) {
            Gate::define($permission->value, fn (User $user): bool => $user->hasPermission($permission));
        }

        Scramble::configure()->withDocumentTransformers($this->documentUnifiedErrorShape(...));
    }

    /**
     * Add `success: false` to the documented error responses so the API docs
     * match the unified error shape rendered in bootstrap/app.php.
     */
    private function documentUnifiedErrorShape(OpenApi $openApi): void
    {
        foreach ($openApi->components->responses as $response) {
            $schema = $response->content['application/json'] ?? null;

            if (! $schema instanceof Schema || ! $schema->type instanceof ObjectType) {
                continue;
            }

            $schema->type->properties = ['success' => (new BooleanType)->example(false)] + $schema->type->properties;
            $schema->type->addRequired(['success']);
        }
    }
}
