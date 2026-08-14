<?php

namespace App\Repositories\Eloquent;

use App\Models\ChatMessage;
use App\Repositories\Contracts\ChatMessageRepositoryInterface;

class ChatMessageRepository implements ChatMessageRepositoryInterface
{
    public function __construct(
        private ChatMessage $message
    ) {}

    public function getByConversationId(int $conversationId)
    {
        return $this->message
            ->where('conversation_id', $conversationId)->oldest()->get();
    }

    public function create(array $data)
    {
        return $this->message->create($data);
    }
}
