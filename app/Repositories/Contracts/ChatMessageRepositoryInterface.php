<?php

namespace App\Repositories\Contracts;

interface ChatMessageRepositoryInterface
{
    public function getByConversationId(int $conversationId);

    public function create(array $data);
}
