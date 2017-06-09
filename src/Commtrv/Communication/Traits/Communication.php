<?php

namespace Commtrv\Communication\Traits;

use Commtrv\Communication\Models\CommunicationMessage;
use Commtrv\Communication\Models\CommunicationModels;
use Commtrv\Communication\Models\CommunicationParticipant;
use Commtrv\Communication\Models\CommunicationThread;

trait Communication
{
    /**
     * Message relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comMessages()
    {
        return $this->hasMany(CommunicationModels::classname(CommunicationMessage::class));
    }

    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comParticipants()
    {
        return $this->hasMany(CommunicationModels::classname(CommunicationParticipant::class));
    }

    /**
     * Thread relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function comThreads()
    {
        return $this->belongsToMany(
            CommunicationModels::classname(CommunicationThread::class),
            CommunicationModels::table('participants'),
            'user_id',
            'thread_id'
        );
    }

    /**
     * Returns the new messages count for user.
     *
     * @return int
     */
    public function comNewThreadsCount()
    {
        return count($this->threadsWithNewMessages());
    }

    /**
     * Returns all threads with new messages.
     *
     * @return array
     */
    public function comThreadsWithNewMessages()
    {
        $threadsWithNewMessages = [];

        $participants = CommunicationModels::participant()->where('user_id', $this->id)->pluck('last_read', 'thread_id');

        /**
         * @todo: see if we can fix this more in the future.
         * Illuminate\Foundation is not available through composer, only in laravel/framework which
         * I don't want to include as a dependency for this package...it's overkill. So let's
         * exclude this check in the testing environment.
         */
        if (getenv('APP_ENV') == 'testing' || !str_contains(\Illuminate\Foundation\Application::VERSION, '5.0')) {
            $participants = $participants->all();
        }

        if ($participants) {
            $threads = CommunicationsModels::thread()->whereIn('id', array_keys($participants))->get();

            foreach ($threads as $thread) {
                if ($thread->updated_at > $participants[$thread->id]) {
                    $threadsWithNewMessages[] = $thread->id;
                }
            }
        }

        return $threadsWithNewMessages;
    }
}
