<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Facades\BotManager;
use App\Facades\BotMethods;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Telegram\Bot\FileUpload\InputFile;

class TelegramController extends Controller
{
    public function getSelf(Request $request)
    {
        if (env("APP_DEBUG")) {
            $user = User::query()->first();
            $user->role = RoleEnum::ADMIN->value;
            $user->base_role = RoleEnum::ADMIN->value;
        } else {
            $user = User::query()
                ->find($request->botUser->id);
            $user->base_role = $user->role;
            Log::info("ENV DEBUG FALSE" . print_r($user->toArray(), true));
        }


        return response()->json($user);
    }

    public function registerWebhooks(Request $request)
    {
        return response()->json(BotManager::bot()->setWebhook());
    }

    public function handler(Request $request)
    {
        BotManager::bot()->handler();

        return response()->json([
            "message" => "Ok"
        ]);
    }

    public function uploadAnyKindOfMedia(...$data)
    {
        $caption = $data[2] ?? null;
        $doc = $data[3] ?? null;
        $type = $data[4] ?? "document";

        $botUser = BotManager::bot()->currentBotUser();

        if (!$botUser->is_admin && !$botUser->is_manager) {
            BotManager::bot()
                ->sendMessage(
                    $botUser->telegram_chat_id,
                    "Данная опция доступна только персоналу бота!");
            return;
        }

        $docToSend = $doc->file_id ?? null;


        BotManager::bot()
            ->sendMessage(
                $botUser->telegram_chat_id,
                "Медиа файл загружен!");

    }

    public function getMyId(...$data)
    {
        $message = "Ваш чат id: <pre><code>" . ($data[0]->chat->id ?? 'не указан') . "</code></pre>\nИдентификатор топика: " . ($data[0]->message_thread_id ?? 'Не указан');

        BotManager::bot()
            ->reply($message);
    }

    public function aboutCommand(...$data)
    {
        BotManager::bot()
            ->replyPhoto("Хочешь такой же бот для своего бизнеса? ",
                InputFile::create(public_path() . "/images/cashman.jpg"),
                [
                    [
                        [
                            "text" => "🔥Перейти в нашего бота для заявок",
                            "url" => "https://t.me/cashman_dn_bot"
                        ]
                    ],
                    [
                        [
                            "text" => "\xF0\x9F\x8D\x80Написать в тех. поддержку",
                            "url" => "https://t.me/EgorShipilov"
                        ],
                    ],

                ]
            );
    }

    public function helpCommand(...$data)
    {
        BotManager::bot()->reply("Как пользоваться ботом");
    }


    public function homePage(Request $request)
    {

        /* if (env("APP_DEBUG")) {
             $user = User::query()->first();
             $user->role = RoleEnum::SUPERADMIN->value;
         } else
             $user = BotManager::bot()->currentBotUser();

         if (is_null($user))
             throw new HttpException(404, "Ошибочка");*/

        Inertia::setRootView("bot");
        return Inertia::render('Main');
    }

    public function startCommand()
    {

        $keyboard = [
            [
                ["text" => "Открыть приложение", "web_app" => [
                    "url" => env("APP_URL") . "/bot#/"]
                ],
            ],
        ];

        $slash = env("APP_DEBUG") ? "\\" : "/";

        $text = `<b>Поздравьте наших бойцов с Днём защитника Отечества — отправьте им видео с личными и тёплыми пожеланиями.</b>

На 23 февраля мы создадим уникальные поздравительные открытки. Внутри каждой — специальный QR-код, который хранит одно-единственное видео, адресованное только одному герою. Эти открытки мы передадим нашим защитникам.

🟥<b>Требования к видео:</b>
1. Ориентация любая (вертикальная/горизонтальная)
2. Хронометраж — 1-3 минуты
3. Чёткий звук и изображение
4. В кадре только один человек

<b>Поздравления принимаются только от жителей старше 18 лет.</b>

🟦<b>Рекомендации к содержанию видео:</b>
1. Начните с обращения в единственном числе, например: «Дорогой защитник!», «Здравствуй, солдат!» и т.д. Ваше видео будет адресовано только одному бойцу, а не нескольким.
2. Расскажите немного о себе: как вас зовут, из какого вы города. Так боец сможет виртуально познакомиться с вами.
3. Поблагодарите бойца за его нелёгкий труд. Нашим солдатам важно знать, что их службу ценят, а их самих поддерживают в каждом уголке страны.
4. Поздравьте с Днём защитника Отечества и произнесите самые искренние пожелания.

<b>Заполните небольшую анкету и пришлите своё видео. Для этого нажмите кнопку ниже.</b>
🍀🍀🍀`;

        \App\Facades\BotManager::bot()
            ->replyPhoto($text,
                InputFile::create(public_path() . $slash . "photo_2026-01-28_16-29-01.jpg",
                    "photo_2026-01-28_16-29-01.jpg"
                )
                , $keyboard);
    }
}
