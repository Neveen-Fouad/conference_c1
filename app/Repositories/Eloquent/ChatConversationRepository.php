<?php

namespace App\Repositories\Eloquent;

use App\Models\ChatConversation;
use App\Repositories\Contracts\ChatConversationRepositoryInterface;

class ChatConversationRepository implements ChatConversationRepositoryInterface
{
    public function __construct(
        private ChatConversation $conversation
    ) {
    }

    public function getByClient(int $clientId)
    {
        return $this->conversation
            ->where('client_id', $clientId)->latest()->get();
    }

    public function findForClient(
        int $conversationId,
        int $clientId
    ) {
        return $this->conversation
            ->with('messages')
            ->where('id', $conversationId)
            ->where('client_id', $clientId)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return $this->conversation->create($data);
    }
}
