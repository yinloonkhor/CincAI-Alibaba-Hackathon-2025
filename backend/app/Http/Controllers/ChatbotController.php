<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ChatbotLog;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    /**
     * The base URL for the AI API.
     *
     * @var string
     */
    protected $apiBaseUrl = 'http://47.250.52.250:8000';

    /**
     * Send a message to the AI chatbot and get a response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chat(Request $request)
    {
        Log::info('Chat request received', ['request_data' => $request->all()]);
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            Log::warning('Chat validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $message = $request->input('message');

        try {
            // Call the AI API
            $response = Http::timeout(30)->post($this->apiBaseUrl . '/chat', [
                'user_id' => $user->id,
                'message' => $message
            ]);

            // Check if the API call was successful
            if ($response->successful()) {
                $aiResponse = $response->json();
                Log::info('AI API response received', ['response' => $aiResponse]);

                // Store the chat log using the existing ChatbotLog model
                $chatLog = ChatbotLog::create([
                    'user_id' => $user->id,
                    'user_message' => $message,
                    'bot_response' => $aiResponse['answer'] ?? 'No response from AI'
                ]);

                Log::info('Chat log stored', ['chat_log_id' => $chatLog->id]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'answer' => $aiResponse['answer'] ?? 'No response from AI',
                        'chat_log_id' => $chatLog->id
                    ]
                ]);
            } else {
                // Handle API error
                Log::error('AI API error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                // Store the failed chat attempt
                ChatbotLog::create([
                    'user_id' => $user->id,
                    'user_message' => $message,
                    'bot_response' => 'API Error: ' . $response->status()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get response from AI service',
                    'error' => 'API Error: ' . $response->status()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception during chat request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Store the failed chat attempt
            ChatbotLog::create([
                'user_id' => $user->id,
                'user_message' => $message,
                'bot_response' => 'Error: ' . $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get chat history for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = $request->input('limit', 50); // Default to 50 messages
            
            $chatLogs = ChatbotLog::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $chatLogs
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve chat history', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve chat history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear chat history for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearHistory()
    {
        try {
            $user = Auth::user();
            $deleted = ChatbotLog::where('user_id', $user->id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Chat history cleared successfully',
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to clear chat history', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear chat history',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
