<?php

return [

    // Навигација
    'nav' => [
        'home' => 'Дома',
        'auctions' => 'Аукции',
        'categories' => 'Категории',
        'dashboard' => 'Контролна табла',
        'watchlist' => 'Листа на следење',
        'messages' => 'Пораки',
        'wallet' => 'Партичник',
        'profile' => 'Профил',
        'settings' => 'Поставки',
        'logout' => 'Одјави се',
        'login' => 'Пријави се',
        'register' => 'Регистрирај се',
    ],

    // Аукции
    'auctions' => [
        'title' => 'Аукции',
        'active' => 'Активни аукции',
        'ending_soon' => 'Наскоро завршува',
        'new' => 'Нови аукции',
        'featured' => 'Издвоени',
        'create' => 'Креирај аукција',
        'edit' => 'Уреди аукција',
        'view' => 'Погледни аукција',
        'details' => 'Детали за аукцијата',
        'description' => 'Опис',
        'category' => 'Категорија',
        'condition' => 'Состојба',
        'start_price' => 'Почетна цена',
        'current_price' => 'Тековна цена',
        'buy_now' => 'Купи сега',
        'reserve_price' => 'Резервна цена',
        'duration' => 'Времетраење',
        'ends_at' => 'Завршува на',
        'time_remaining' => 'Преостанато време',
        'bids' => 'Понуди',
        'bid_count' => ':count понуди',
        'watchers' => 'Набљудувачи',
        'views' => 'Прегледи',
        'seller' => 'Продавач',
        'winner' => 'Победник',
        'status' => 'Статус',
        'no_auctions' => 'Нема аукции',
        'search_placeholder' => 'Пребарай аукции...',
    ],

    // Лицитирање
    'bidding' => [
        'place_bid' => 'Стави понуда',
        'your_bid' => 'Вашата понуда',
        'minimum_bid' => 'Минимална понуда',
        'bid_amount' => 'Износ на понуда',
        'bid_history' => 'Историја на понуди',
        'highest_bid' => 'Највисока понуда',
        'you_are_winning' => 'Вие водите!',
        'you_are_outbid' => 'Надјачани сте!',
        'bid_placed' => 'Понудата е успешно поставена',
        'bid_too_low' => 'Износот на понудата е премногу низок',
        'cannot_bid_own' => 'Не можете да лицитирате на вашата аукција',
        'auction_ended' => 'Оваа аукција заврши',
        'proxy_bid' => 'Прокси понуда',
        'max_bid' => 'Максимална понуда',
        'auto_bid' => 'Автоматска понуда до вашиот максимум',
    ],

    // Автентификација
    'auth' => [
        'login' => 'Пријави се',
        'register' => 'Регистрирај се',
        'logout' => 'Одјави се',
        'email' => 'Е-пошта',
        'password' => 'Лозинка',
        'password_confirm' => 'Потврди ја лозинката',
        'remember_me' => 'Запомни ме',
        'forgot_password' => 'Заборавена лозинка?',
        'reset_password' => 'Ресетирај лозинка',
        'send_reset_link' => 'Испрати линк за ресетирање',
        'name' => 'Име',
        'phone' => 'Телефон',
        'register_as' => 'Регистрирај се како',
        'buyer' => 'Купувач',
        'seller' => 'Продавач',
        'already_have_account' => 'Веќе имате сметка?',
        'dont_have_account' => 'Немате сметка?',
        'verify_email' => 'Верификувај е-пошта',
        'verification_sent' => 'Линкот за верификација е испратен',
        'login_success' => 'Успешна пријава',
        'logout_success' => 'Успешна одјава',
        'register_success' => 'Успешна регистрација',
    ],

    // Типови корисници
    'user_types' => [
        'buyer' => 'Купувач',
        'seller' => 'Продавач',
        'verified_seller' => 'Верификуван продавач',
        'admin' => 'Администратор',
        'moderator' => 'Модератор',
    ],

    // Контролна табла
    'dashboard' => [
        'title' => 'Контролна табла',
        'welcome' => 'Добредојдовте, :name!',
        'active_bids' => 'Активни понуди',
        'won_auctions' => 'Добиени аукции',
        'watchlist_count' => 'Ставки на листата за следење',
        'wallet_balance' => 'Состојба на партичник',
        'recent_activity' => 'Недавни активности',
        'quick_links' => 'Брзи линкови',
    ],

    // Партичник
    'wallet' => [
        'title' => 'Партичник',
        'balance' => 'Состојба',
        'available' => 'Достапно',
        'in_escrow' => 'Во ескроу',
        'total' => 'Вкупно',
        'deposit' => 'Депозит',
        'withdraw' => 'Исплата',
        'transactions' => 'Трансакции',
        'transaction_history' => 'Историја на трансакции',
        'amount' => 'Износ',
        'type' => 'Тип',
        'date' => 'Датум',
        'status' => 'Статус',
        'deposit_success' => 'Депозитот е успешен',
        'withdraw_success' => 'Исплатата е успешна',
        'insufficient_funds' => 'Недоволно средства',
    ],

    // Нарачки
    'orders' => [
        'title' => 'Нарачки',
        'order' => 'Нарачка',
        'my_orders' => 'Моите наранчки',
        'order_number' => 'Нарачка бр. :number',
        'total' => 'Вкупно',
        'status' => 'Статус',
        'pending_payment' => 'Чека плаќање',
        'paid' => 'Платено',
        'awaiting_shipment' => 'Чека испорака',
        'shipped' => 'Испратено',
        'delivered' => 'Доставено',
        'completed' => 'Завршено',
        'cancelled' => 'Откажано',
        'disputed' => 'Во спор',
        'pay_now' => 'Плати сега',
        'track_order' => 'Следи наранчка',
        'confirm_delivery' => 'Потврди достава',
    ],

    // Достава
    'shipping' => [
        'title' => 'Достава',
        'method' => 'Метод на достава',
        'address' => 'Адреса за достава',
        'city' => 'Град',
        'postal_code' => 'Поштенски број',
        'country' => 'Држава',
        'tracking' => 'Следење',
        'tracking_number' => 'Број за следење',
        'courier' => 'Курир',
        'estimated_delivery' => 'Проценета достава',
        'shipped_by' => 'Испратено од :seller',
    ],

    // Категории
    'categories' => [
        'all' => 'Сите категории',
        'electronics' => 'Електроника',
        'vehicles' => 'Возила',
        'fashion' => 'Мода',
        'home_garden' => 'Дом и градина',
        'sports' => 'Спорт и на отворено',
        'collectibles' => 'Колекционерство',
        'toys' => 'Играчки и хобија',
        'other' => 'Останато',
    ],

    // Состојби
    'conditions' => [
        'new' => 'Ново',
        'used' => 'Користено',
        'refurbished' => 'Обновено',
        'excellent' => 'Одлично',
        'good' => 'Добро',
        'fair' => 'Задоволително',
        'poor' => 'Лошо',
    ],

    // Пораки
    'messages' => [
        'title' => 'Пораки',
        'send' => 'Испрати',
        'reply' => 'Одговори',
        'message' => 'Порака',
        'from' => 'Од',
        'to' => 'За',
        'subject' => 'Наслов',
        'no_messages' => 'Нема пораки',
        'write_message' => 'Напиши порака',
    ],

    // Нотификации
    'notifications' => [
        'title' => 'Нотификации',
        'mark_read' => 'Означи како прочитано',
        'mark_unread' => 'Означи како непрочитано',
        'delete' => 'Избриши',
        'no_notifications' => 'Нема нотификации',
        'outbid' => 'Надјачани сте',
        'won' => 'Добивте аукција',
        'payment_received' => 'Плаќањето е применето',
        'item_shipped' => 'Артиклот е испратен',
    ],

    // Валидација
    'validation' => [
        'required' => 'Ова поле е задолжително',
        'email' => 'Внесете валидна е-пошта',
        'min' => 'Минимум :min карактери',
        'max' => 'Максимум :max карактери',
        'numeric' => 'Внесете број',
        'confirmed' => 'Потврдата не се совпаѓа',
        'unique' => 'Оваа вредност е веќе зафатена',
        'accepted' => 'Мора да биде прифатено',
    ],

    // Грешки
    'errors' => [
        'not_found' => 'Не е пронајдено',
        'unauthorized' => 'Неовластен пристап',
        'forbidden' => 'Забрането',
        'server_error' => 'Грешка на серверот',
        'page_not_found' => 'Страницата не е пронајдена',
        'go_home' => 'Оди дома',
    ],

    // Копчиња
    'buttons' => [
        'save' => 'Зачувај',
        'cancel' => 'Откажи',
        'delete' => 'Избриши',
        'edit' => 'Уреди',
        'view' => 'Погледни',
        'search' => 'Пребарај',
        'filter' => 'Филтер',
        'reset' => 'Ресетирај',
        'submit' => 'Испрати',
        'confirm' => 'Потврди',
        'back' => 'Назад',
        'next' => 'Натаму',
        'previous' => 'Назад',
        'close' => 'Затвори',
        'yes' => 'Да',
        'no' => 'Не',
    ],

    // Време
    'time' => [
        'days' => 'дена',
        'hours' => 'часа',
        'minutes' => 'минути',
        'seconds' => 'секунди',
        'day' => 'ден',
        'hour' => 'час',
        'minute' => 'минута',
        'second' => 'секунда',
        'ago' => 'пред',
        'just_now' => 'Токму сега',
    ],

    // Статуси
    'status' => [
        'active' => 'Активно',
        'inactive' => 'Неактивно',
        'pending' => 'На чекање',
        'approved' => 'Одобрено',
        'rejected' => 'Одбиено',
        'success' => 'Успех',
        'error' => 'Грешка',
        'warning' => 'Предупредување',
        'info' => 'Инфо',
    ],

    // Футер
    'footer' => [
        'about' => 'За нас',
        'contact' => 'Контакт',
        'terms' => 'Услови за користење',
        'privacy' => 'Политика за приватност',
        'help' => 'Помош',
        'faq' => 'Често поставувани прашања',
        'copyright' => '© :year Aukcije.ba. Сите права се задржани.',
    ],

    // Останато
    'misc' => [
        'loading' => 'Вчитување...',
        'no_results' => 'Нема резултати',
        'show_more' => 'Прикажи повеќе',
        'show_less' => 'Прикажи помалку',
        'read_more' => 'Прочитај повеќе',
        'share' => 'Сподели',
        'copy' => 'Копирај',
        'copied' => 'Копирано!',
        'success' => 'Успех',
        'error' => 'Грешка',
    ],

];
