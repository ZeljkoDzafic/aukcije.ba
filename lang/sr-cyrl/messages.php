<?php

return [

    // Навигација
    'nav' => [
        'home' => 'Почетна',
        'auctions' => 'Аукције',
        'categories' => 'Категорије',
        'dashboard' => 'Контролна табла',
        'watchlist' => 'Листа праћења',
        'messages' => 'Поруке',
        'wallet' => 'Новчаник',
        'profile' => 'Профил',
        'settings' => 'Подешавања',
        'logout' => 'Одјави се',
        'login' => 'Пријави се',
        'register' => 'Региструј се',
    ],

    // Аукције
    'auctions' => [
        'title' => 'Аукције',
        'active' => 'Активне аукције',
        'ending_soon' => 'Убрзо завршава',
        'new' => 'Нове аукције',
        'featured' => 'Издвојене',
        'create' => 'Креирај аукцију',
        'edit' => 'Уреди аукцију',
        'view' => 'Погледај аукцију',
        'details' => 'Детаљи аукције',
        'description' => 'Опис',
        'category' => 'Категорија',
        'condition' => 'Стање',
        'start_price' => 'Почетна цена',
        'current_price' => 'Тренутна цена',
        'buy_now' => 'Купи одмах',
        'reserve_price' => 'Резервна цена',
        'duration' => 'Трајање',
        'ends_at' => 'Завршава',
        'time_remaining' => 'Преостало време',
        'bids' => 'Понуде',
        'bid_count' => ':count понуда',
        'watchers' => 'Пратилаца',
        'views' => 'Прегледа',
        'seller' => 'Продавац',
        'winner' => 'Победник',
        'status' => 'Статус',
        'no_auctions' => 'Нема аукција',
        'search_placeholder' => 'Претражи аукције...',
    ],

    // Лицитирање
    'bidding' => [
        'place_bid' => 'Лицитирај',
        'your_bid' => 'Ваша понуда',
        'minimum_bid' => 'Минимална понуда',
        'bid_amount' => 'Износ понуде',
        'bid_history' => 'Историја понуда',
        'highest_bid' => 'Највиша понуда',
        'you_are_winning' => 'Ви водите!',
        'you_are_outbid' => 'Надјачани сте!',
        'bid_placed' => 'Понуда успешно постављена',
        'bid_too_low' => 'Износ понуде је пренизак',
        'cannot_bid_own' => 'Не можете лицитирати на својој аукцији',
        'auction_ended' => 'Ова аукција је завршена',
        'proxy_bid' => 'Прокси понуда',
        'max_bid' => 'Максимална понуда',
        'auto_bid' => 'Аутоматско лицитирање до вашег максимума',
    ],

    // Аутентификација
    'auth' => [
        'login' => 'Пријави се',
        'register' => 'Региструј се',
        'logout' => 'Одјави се',
        'email' => 'Е-пошта',
        'password' => 'Лозинка',
        'password_confirm' => 'Потврди лозинку',
        'remember_me' => 'Запамти ме',
        'forgot_password' => 'Заборавили сте лозинку?',
        'reset_password' => 'Ресетуј лозинку',
        'send_reset_link' => 'Пошаљи линк за ресет',
        'name' => 'Име',
        'phone' => 'Телефон',
        'register_as' => 'Региструј се као',
        'buyer' => 'Купац',
        'seller' => 'Продавац',
        'already_have_account' => 'Већ имате налог?',
        'dont_have_account' => 'Немате налог?',
        'verify_email' => 'Верификуј е-пошту',
        'verification_sent' => 'Линк за верификацију послат',
        'login_success' => 'Успешна пријава',
        'logout_success' => 'Успешна одјава',
        'register_success' => 'Успешна регистрација',
    ],

    // Типови корисника
    'user_types' => [
        'buyer' => 'Купац',
        'seller' => 'Продавац',
        'verified_seller' => 'Верификовани продавац',
        'admin' => 'Администратор',
        'moderator' => 'Модератор',
    ],

    // Контролна табла
    'dashboard' => [
        'title' => 'Контролна табла',
        'welcome' => 'Добродошли, :name!',
        'active_bids' => 'Активне понуде',
        'won_auctions' => 'Добијене аукције',
        'watchlist_count' => 'Ставки на листи праћења',
        'wallet_balance' => 'Стање новчаника',
        'recent_activity' => 'Недавне активности',
        'quick_links' => 'Брзи линкови',
    ],

    // Новчаник
    'wallet' => [
        'title' => 'Новчаник',
        'balance' => 'Стање',
        'available' => 'Доступно',
        'in_escrow' => 'У ескроу',
        'total' => 'Укупно',
        'deposit' => 'Уплата',
        'withdraw' => 'Исплата',
        'transactions' => 'Трансакције',
        'transaction_history' => 'Историја трансакција',
        'amount' => 'Износ',
        'type' => 'Тип',
        'date' => 'Датум',
        'status' => 'Статус',
        'deposit_success' => 'Уплата успешна',
        'withdraw_success' => 'Исплата успешна',
        'insufficient_funds' => 'Недовољно средстава',
    ],

    // Нарџбе
    'orders' => [
        'title' => 'Нарџбе',
        'order' => 'Нарџба',
        'my_orders' => 'Моје нарџбе',
        'order_number' => 'Нарџба бр. :number',
        'total' => 'Укупно',
        'status' => 'Статус',
        'pending_payment' => 'Чека плаћање',
        'paid' => 'Плаћено',
        'awaiting_shipment' => 'Чека слање',
        'shipped' => 'Послато',
        'delivered' => 'Достављено',
        'completed' => 'Завршено',
        'cancelled' => 'Отказано',
        'disputed' => 'У спору',
        'pay_now' => 'Плати одмах',
        'track_order' => 'Прати нарџбу',
        'confirm_delivery' => 'Потврди доставу',
    ],

    // Достава
    'shipping' => [
        'title' => 'Достава',
        'method' => 'Метод доставе',
        'address' => 'Адреса доставе',
        'city' => 'Град',
        'postal_code' => 'Поштански број',
        'country' => 'Држава',
        'tracking' => 'Праћење',
        'tracking_number' => 'Број за праћење',
        'courier' => 'Курир',
        'estimated_delivery' => 'Процењена достава',
        'shipped_by' => 'Послао: :seller',
    ],

    // Категорије
    'categories' => [
        'all' => 'Све категорије',
        'electronics' => 'Електроника',
        'vehicles' => 'Возила',
        'fashion' => 'Мода',
        'home_garden' => 'Дом и башта',
        'sports' => 'Спорт и на отвореном',
        'collectibles' => 'Колекционарство',
        'toys' => 'Играчке и хобији',
        'other' => 'Остало',
    ],

    // Стања
    'conditions' => [
        'new' => 'Ново',
        'used' => 'Коришћено',
        'refurbished' => 'Обновљено',
        'excellent' => 'Одлично',
        'good' => 'Добро',
        'fair' => 'Задовољавајуће',
        'poor' => 'Лоше',
    ],

    // Поруке
    'messages' => [
        'title' => 'Поруке',
        'send' => 'Пошаљи',
        'reply' => 'Одговори',
        'message' => 'Порука',
        'from' => 'Од',
        'to' => 'За',
        'subject' => 'Наслов',
        'no_messages' => 'Нема порука',
        'write_message' => 'Напиши поруку',
    ],

    // Нотификације
    'notifications' => [
        'title' => 'Нотификације',
        'mark_read' => 'Означи као прочитано',
        'mark_unread' => 'Означи као непрочитано',
        'delete' => 'Обриши',
        'no_notifications' => 'Нема нотификација',
        'outbid' => 'Надјачани сте',
        'won' => 'Добили сте аукцију',
        'payment_received' => 'Плаћање примљено',
        'item_shipped' => 'Артикл послат',
    ],

    // Валидација
    'validation' => [
        'required' => 'Ово поље је обавезно',
        'email' => 'Унесите исправну е-пошту',
        'min' => 'Минимум :min карактера',
        'max' => 'Максимум :max карактера',
        'numeric' => 'Унесите број',
        'confirmed' => 'Потврда се не поклапа',
        'unique' => 'Ова вредност је већ заузета',
        'accepted' => 'Мора бити прихваћено',
    ],

    // Грешке
    'errors' => [
        'not_found' => 'Није пронађено',
        'unauthorized' => 'Неовлашћен приступ',
        'forbidden' => 'Забрањено',
        'server_error' => 'Грешка на серверу',
        'page_not_found' => 'Страница није пронађена',
        'go_home' => 'На почетну',
    ],

    // Дугмад
    'buttons' => [
        'save' => 'Сачувај',
        'cancel' => 'Откажи',
        'delete' => 'Обриши',
        'edit' => 'Уреди',
        'view' => 'Погледај',
        'search' => 'Претражи',
        'filter' => 'Филтрирај',
        'reset' => 'Ресетуј',
        'submit' => 'Пошаљи',
        'confirm' => 'Потврди',
        'back' => 'Назад',
        'next' => 'Даље',
        'previous' => 'Назад',
        'close' => 'Затвори',
        'yes' => 'Да',
        'no' => 'Не',
    ],

    // Време
    'time' => [
        'days' => 'дана',
        'hours' => 'сати',
        'minutes' => 'минута',
        'seconds' => 'секунди',
        'day' => 'дан',
        'hour' => 'сат',
        'minute' => 'минут',
        'second' => 'секунд',
        'ago' => 'пре',
        'just_now' => 'Управо сада',
    ],

    // Статуси
    'status' => [
        'active' => 'Активно',
        'inactive' => 'Неактивно',
        'pending' => 'На чекању',
        'approved' => 'Одобрено',
        'rejected' => 'Одбијено',
        'success' => 'Успех',
        'error' => 'Грешка',
        'warning' => 'Упозорење',
        'info' => 'Инфо',
    ],

    // Футер
    'footer' => [
        'about' => 'О нама',
        'contact' => 'Контакт',
        'terms' => 'Услови коришћења',
        'privacy' => 'Политика приватности',
        'help' => 'Помоћ',
        'faq' => 'Честа питања',
        'copyright' => '© :year Aukcije.ba. Сва права задржана.',
    ],

    // Остало
    'misc' => [
        'loading' => 'Учитавање...',
        'no_results' => 'Нема резултата',
        'show_more' => 'Прикажи више',
        'show_less' => 'Прикажи мање',
        'read_more' => 'Прочитај више',
        'share' => 'Подели',
        'copy' => 'Копирај',
        'copied' => 'Копирано!',
        'success' => 'Успех',
        'error' => 'Грешка',
    ],

];
