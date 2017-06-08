<?php

namespace Commtrv\Communication;

use Commtrv\Communication\Models\CommunicationMessage;
use Commtrv\Communication\Models\CommunicationModels;
use Commtrv\Communication\Models\CommunicationParticipant;
use Commtrv\Communication\Models\CommunicationThread;
use Illuminate\Support\ServiceProvider;

class CommunicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            base_path('vendor/commtrv/communication/src/config/config.php') => config_path('communication.php'),
            base_path('vendor/commtrv/communication/src/migrations') => base_path('database/migrations'),
        ]);

        $this->setCommunicationModels();
        $this->setComUserModel();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            base_path('vendor/commtrv/communication/src/config/config.php'), 'communication'
        );
    }

    private function setCommunicationModels()
    {
        $config = $this->app->make('config');

        CommunicationModels::setMessageModel($config->get('communication.message_model', CommunicationMessage::class));
        CommunicationModels::setThreadModel($config->get('communication.thread_model', CommunicationThread::class));
        CommunicationModels::setParticipantModel($config->get('communication.participant_model', CommunicationParticipant::class));

        CommunicationModels::setTables([
            'messages' => $config->get('communication.communication_messages_table', CommunicationModels::message()->getTable()),
            'participants' => $config->get('communication.communication_participants_table', CommunicationModels::participant()->getTable()),
            'threads' => $config->get('communication.communication_threads_table', CommunicationModels::thread()->getTable()),
        ]);
    }

    private function setComUserModel()
    {
        $config = $this->app->make('config');

        $model = $config->get('auth.providers.users.model', function () use ($config) {
            return $config->get('auth.model', $config->get('messenger.user_model'));
        });

        CommunicationModels::setUserModel($model);

        CommunicationModels::setTables([
            'users' => (new $model)->getTable(),
        ]);
    }
}
