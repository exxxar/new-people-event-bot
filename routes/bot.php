<?php

use App\Facades\BotManager;
use App\Http\Controllers\Bots\InlineBotController;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Telegram\Bot\FileUpload\InputFile;

BotManager::bot()
    ->controller(\App\Http\Controllers\TelegramController::class)
    ->route("/.*Мой id|.*мой id", "getMyId")
    ->route("/start", "startCommand")
    ->route("/about", "aboutCommand")
    ->route("/help", "helpCommand")
    ->route("/start ([0-9a-zA-Z=]+)", "startWithParam")
    //->fallbackDocument("uploadAnyKindOfMedia")
    ->fallbackAudio("uploadAnyKindOfMedia")
    ->fallbackSticker("uploadAnyKindOfMedia")
    ->fallbackVideo("uploadAnyKindOfMedia");


BotManager::bot()
    ->fallbackDocument(function (...$data) {

    })
    ->fallbackPhoto(function (...$data) {

    });

BotManager::bot()
    ->location(function (...$data) {

    });
