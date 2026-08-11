<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendChatMessageRequest;
use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        private ChatbotService $chatbotService
    ) {
    }

    private function getClientId(Request $request): int
    {
        $clientId = $request->user()?->client?->id;

        abort_unless(
            $clientId,
            404,
            'Client profile not found.'
        );

        return $clientId;
    }

    public function index(Request $request)
    {
        $conversations = $this->chatbotService
            ->getClientConversations(
                $this->getClientId($request)
            );

        return response()->json([
            'data' => $conversations,
        ]);
    }

    public function show(Request $request, int $conversationId)
    {
        $conversation = $this->chatbotService
            ->getConversation(
                $conversationId,
                $this->getClientId($request)
            );

        return response()->json([
            'data' => $conversation,
        ]);
    }

    public function sendMessage(SendChatMessageRequest $request)
    {
        $data = $request->validated();

        $result = $this->chatbotService->sendMessage(
            $data['message'],
            $this->getClientId($request),
            $data['conversation_id'] ?? null
        );

        return response()->json([
            'data' => $result,
        ], 201);
    }
}

