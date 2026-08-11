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

    public function index(Request $request)
    {
        $conversations = $this->chatbotService
            ->getClientConversations($request->user()->id);

        return response()->json([

            'data' => $conversations,
        ]);
    }

    public function show(
        Request $request,
        int $conversationId
    ) {
        $conversation = $this->chatbotService->getConversation(
            $conversationId,
            $request->user()->id
        );

        return response()->json([

            'data' => $conversation,
        ]);
    }

    public function sendMessage(
        SendChatMessageRequest $request
    ) {
        $data = $request->validated();

        $result = $this->chatbotService->sendMessage(
            $data['message'],
            $request->user()->id,
            $data['conversation_id'] ?? null
        );

        return response()->json([

            'data' => $result,
        ], 201);
    }
}

