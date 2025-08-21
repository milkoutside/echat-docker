<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use src\Domain\Interfaces\Clients\IClientsRepository;
use src\Domain\Interfaces\Messages\IMessageRepository;
use src\Domain\Services\Messages\MessageServiceContext;
use src\Infrastructure\Repositories\ClientsRepository\ClientsRepository;
use src\Infrastructure\Repositories\MessagesRepository\MessagesEchatTelegramRepository;
use src\Infrastructure\Repositories\MessagesRepository\MessagesEchatViberRepository;
use src\Infrastructure\Repositories\MessagesRepository\MessagesEchatWhatsAppRepository;
use src\Infrastructure\Repositories\MessagesRepository\MessagesRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MessageServiceContext::class, function () {
            $context = new MessageServiceContext();
            $context->addStrategy('telegram', new MessagesEchatTelegramRepository());
            $context->addStrategy('viber', new MessagesEchatViberRepository());
            $context->addStrategy('whatsapp', new MessagesEchatWhatsAppRepository());
            return $context;
        });
        $this->app->bind(IClientsRepository::class, ClientsRepository::class);
        $this->app->bind(IMessageRepository::class, MessagesRepository::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
