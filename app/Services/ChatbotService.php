<?php

namespace App\Services;

use App\Repositories\Contracts\ChatConversationRepositoryInterface;
use App\Repositories\Contracts\ChatMessageRepositoryInterface;
use Illuminate\Support\Str;

class ChatbotService
{
    public function __construct(
        private ChatConversationRepositoryInterface $conversationRepository,
        private ChatMessageRepositoryInterface $messageRepository,
        private GeminiService $geminiService
    ) {
    }

    public function getClientConversations(int $clientId)
    {
        return $this->conversationRepository
            ->getByClient($clientId);
    }

    public function getConversation(
        int $conversationId,
        int $clientId
    ) {
        return $this->conversationRepository
            ->findForClient($conversationId, $clientId);
    }

    public function sendMessage(
        string $message,
        int $clientId,
        ?int $conversationId = null
    ): array {
        if ($conversationId) {
            $conversation = $this->conversationRepository
                ->findForClient($conversationId, $clientId);
        } else {
            $conversation = $this->conversationRepository->create([
                'client_id' => $clientId,
                'title' => Str::limit($message, 50),
            ]);
        }

        $previousMessages = $this->messageRepository
            ->getByConversationId($conversation->id);

        $history = $previousMessages
            ->map(function ($item) {
                return [
                    'role' => $item->role,
                    'content' => $item->content,
                ];
            })
            ->toArray();

        $userMessage = $this->messageRepository->create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $message,
        ]);

        $history[] = [
            'role' => 'user',
            'content' => $message,
        ];
        $reply = $this->geminiService
            ->generateReply($history);
        $assistantMessage = $this->messageRepository->create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $reply,
        ]);
        return [
            'conversation_id' => $conversation->id,
            'user_message' => $userMessage,
            'assistant_message' => $assistantMessage,
        ];
    }
}
