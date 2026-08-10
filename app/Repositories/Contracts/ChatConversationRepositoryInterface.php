<?php

namespace App\Repositories\Contracts;

interface ChatConversationRepositoryInterface
{
    public function getByClient(int $clientId);

    public function findForClient(
        int $conversationId,
        int $clientId
    );

    public function create(array $data);
}
