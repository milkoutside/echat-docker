<?php

use App\Events\TestEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use src\Web\Controllers\Clients\ClientsController;
use src\Web\Controllers\Echat\EchatController;
use src\Web\Controllers\Files\FilesController;
use src\Web\Controllers\Inbox\InboxController;
use src\Web\Controllers\Messages\MessagesController;
use src\Web\Controllers\Messages\MessagesEchatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('echat')->group(function () {
    Route::post('/messages', [MessagesEchatController::class, 'sendMessage']);
    Route::post('/read-all-messages-by-client', [MessagesController::class, 'readAllMessagesByClient']);
    Route::post('/receive-status-telegram', [MessagesEchatController::class, 'receiveStatusTelegram']);
    Route::post('/receive-status-viber', [MessagesEchatController::class, 'receiveStatusViber']);
    Route::post('/receive-status-whatsapp', [MessagesEchatController::class, 'receiveStatusWhatsApp']);
    Route::post('/incoming-message-telegram', [MessagesEchatController::class, 'incomingMessageTelegram']);
    Route::post('/incoming-message-viber', [MessagesEchatController::class, 'incomingMessageViber']);
    Route::post('/incoming-message-whatsapp', [MessagesEchatController::class, 'incomingMessageWhatsapp']);
    Route::get('/get-senders', [EchatController::class, 'getSenders']);
    Route::post('/delete-sender', [EchatController::class, 'deleteSender']);
    Route::post('/create-sender', [EchatController::class, 'createSender']);

});
Route::prefix('clients')->group(function () {
    Route::get('/', [ClientsController::class, 'getClients']);

    Route::post('/by-phone-or-username', [ClientsController::class, 'getClientByPhoneOrUsername']);
    Route::post('/by-phones-or-username', [ClientsController::class, 'getClientByPhonesOrUsername']);
    Route::post('/merge-by-data', [ClientsController::class, 'mergeClientsByData']);

    Route::post('/by-id', [ClientsController::class, 'getClientById']);

    Route::post('/create-or-update', [ClientsController::class, 'createOrUpdateClient']);
    Route::post('/update-by-id', [ClientsController::class, 'updateClientById']);
});
Route::prefix('client-files')->group(function () {
    Route::post('/upload-file', [FilesController::class, 'createFileForClient']);
});
Route::prefix('messages')->group(function () {

    Route::post('/get-messages', [MessagesController::class, 'getMessages']);

});
// Тестовый маршрут лучше скрыть за env, или удалить на проде. Оставим, но ограничим префиксом только в локали.
if (config('app.env') !== 'production') {
    Route::get('/send-test-event', function () {
        broadcast(new TestEvent('Hello from route!'));
        return response()->json(['status' => 'Event broadcasted']);
    });
}
Route::prefix('inbox')->group(function () {
    Route::post('/get-last-message-with-clients', [InboxController::class, 'getLastMessageWithClients']);
    Route::post('/find-client-last-message-with-clients', [InboxController::class, 'findClient']);
});
