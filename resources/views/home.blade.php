@extends('layouts.app')

@section('title', 'Aether Airways — Будущее авиаперелётов в ваших руках')

@section('styles')
    <link rel="stylesheet" href="{{ asset('style/pages/home.css') }}?v=1">
@endsection

@section('content')
<main class="page-wrapper">

    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <a href="{{ route('flights.index') }}" class="hero-pill-badge">
                    <span>Зимние направления {{ date('Y') + 1 }} уже доступны для бронирования</span>
                    <svg class="ag-arrow-icon arrow-sm" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>

                <h1 class="hero-title">
                    Будущее авиаперелётов в ваших руках
                </h1>

                <p class="hero-subtitle">
                    Интеллектуальная система нового поколения: мгновенный подбор рейсов, фиксация тарифа на 24 часа без сборов, прямой выбор кресел в салоне и программа премиальных миль.
                </p>

                <div class="hero-actions">
                    <a href="{{ route('flights.index') }}" class="btn btn-primary btn-lg">Найти рейс</a>
                    <a href="#special-offers" class="btn btn-secondary btn-lg">Спецпредложения</a>
                </div>

                <div class="hero-search-console">
                    <div class="search-console-header">
                        <span class="search-console-caption">
                            Фиксация тарифа на 24 часа включена
                        </span>
                    </div>

                    <form class="search-console-grid" action="{{ route('flights.index') }}" method="GET">
                        <div class="search-field-box">
                            <label for="fromCity">Откуда</label>
                            <input type="text" id="fromCity" name="from" placeholder="Город вылета" value="Новосибирск" list="departureList" required>
                            <datalist id="departureList">
                                @foreach($departureCities as $dCity)
                                    <option value="{{ $dCity }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        <div class="search-field-box">
                            <label for="toCity">Куда</label>
                            <input type="text" id="toCity" name="to" placeholder="Город прилета" value="Сочи" list="arrivalList" required>
                            <datalist id="arrivalList">
                                @foreach($arrivalCities as $aCity)
                                    <option value="{{ $aCity }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        <div class="search-field-box">
                            <label for="flightDate">Дата вылета</label>
                            <input type="date" id="flightDate" name="date" value="{{ date('Y-m-d', strtotime('+5 days')) }}" required>
                        </div>

                        <div class="search-field-box">
                            <label for="passengerCount">Класс салона</label>
                            <select id="passengerCount" name="class">
                                <option value="economy">Эконом-класс</option>
                                <option value="comfort">Комфорт-класс</option>
                                <option value="business">Бизнес-класс</option>
                            </select>
                        </div>

                        <div class="search-btn-box">
                            <button type="submit" class="btn btn-primary search-console-submit">
                                Найти билеты
                                <svg class="ag-arrow-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="core-offerings-section">
        <div class="container">
            <div class="section-header">
                <div class="section-pretitle">Архитектура платформы</div>
                <h2 class="section-title">Создано для абсолютного комфорта полёта</h2>
                <p class="section-desc">Четыре ключевые подсистемы обеспечивают пассажирам предсказуемость, гибкость и премиальный уровень сервиса.</p>
            </div>

            <div class="core-grid">

                <div class="core-card">
                    <div class="core-card-top">
                        <span class="ag-badge ag-badge-active core-card-badge">БРОНИРОВАНИЕ НА 24 Ч</span>
                        <h3 class="core-card-title">Фиксация стоимости на 24 часа</h3>
                        <p class="core-card-desc">
                            Выбирайте рейс и фиксируйте цену без мгновенной оплаты. Система резервирует место и гарантирует сохранность тарифа на 24 часа со статусом «активно». При необходимости продлевайте бронь на +24 часа за 500 миль.
                        </p>
                    </div>
                    <div class="timer-interactive-box">
                        <div>
                            <div class="core-card-caption">Срок действия фиксации:</div>
                            <div class="timer-countdown-val">24 часа со старта</div>
                        </div>
                        <span class="ag-badge ag-badge-active">АКТИВНЫЙ СТАТУС</span>
                    </div>
                </div>

                <div class="core-card core-card-dark">
                    <div class="core-card-top">
                        <span class="ag-badge ag-badge-discount core-card-badge badge-translucent">СХЕМА САЛОНА ОНЛАЙН</span>
                        <h3 class="core-card-title">Выбор кресла в салоне лайнера</h3>
                        <p class="core-card-desc">
                            Интерактивная карта салона с проверкой занятости в реальном времени. Классы эконом, комфорт и бизнес. Мгновенный апгрейд класса обслуживания за 2 000 бонусных миль.
                        </p>
                    </div>
                    <div class="core-card-preview">
                        <img src="{{ asset('image/air.jpg') }}" alt="Aether Air" onerror="this.src='{{ asset('image/air-animal.jpg') }}'">
                    </div>
                </div>

                <div class="core-card">
                    <div class="core-card-top">
                        <span class="ag-badge ag-badge-completed core-card-badge">+1 000 WELCOME MILES</span>
                        <h3 class="core-card-title">Программа бонусных миль</h3>
                        <p class="core-card-desc">
                            Получайте 1 000 миль сразу при регистрации нового аккаунта. За каждый совершенный рейс начисляются дополнительные мили, которые можно потратить на апгрейд места или продление брони.
                        </p>
                    </div>
                    <div class="core-balance-box">
                        <div>
                            <div class="core-balance-label">Бонусы за регистрацию:</div>
                            <div class="core-balance-val">+1 000 миль</div>
                        </div>
                        <a href="{{ auth()->check() ? route('profile', ['tab' => 'tabMiles']) : route('register') }}" class="btn btn-secondary btn-sm">
                            {{ auth()->check() ? 'Баланс миль' : 'Регистрация' }}
                        </a>
                    </div>
                </div>

                <div class="core-card">
                    <div class="core-card-top">
                        <span class="ag-badge ag-badge-active core-card-badge">ПЕРЕВОЗКА ПИТОМЦЕВ</span>
                        <h3 class="core-card-title">Путешествия с питомцами</h3>
                        <p class="core-card-desc">
                            Первые авиалинии с климатическим регламентом перевозки домашних животных в салоне и багажном отсеке. Умные переноски, сопровождение ветеринаров и прозрачные правила.
                        </p>
                    </div>
                    <div class="core-card-preview">
                        <img src="{{ asset('image/air-animal.jpg') }}" alt="Aether Airways Pet Travel">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="special-offers-section" id="special-offers">
        <div class="container">
            <div class="section-header">
                <div class="section-pretitle">Специальные предложения</div>
                <h2 class="section-title">ТОП-3 направления с максимальной скидкой</h2>
                <p class="section-desc">Автоматическая выборка из базы актуальных спецпредложений с наибольшим размером скидки и прямой фиксацией тарифа.</p>
            </div>

            <div class="offers-grid">
                @forelse($topOffers as $offer)
                    <div class="offer-card">
                        <div>
                            <div class="offer-badge-row">
                                <span class="offer-discount-pill">−{{ (int)$offer->discount }}% СКИДКА</span>
                                @if($offer->flight)
                                    <span class="offer-flight-code">РЕЙС {{ $offer->flight->flight_number }}</span>
                                @endif
                            </div>
                            <h3 class="offer-route">
                                @if($offer->flight)
                                    {{ $offer->flight->departure_city }}
                                    <svg class="ag-arrow-icon arrow-inline" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                    {{ $offer->flight->arrival_city }}
                                @else
                                    {{ $offer->title }}
                                @endif
                            </h3>
                            <p class="offer-desc">{{ $offer->description ?? 'Выгодное предложение с ограниченным сроком действия.' }}</p>
                        </div>
                        <div>
                            @if($offer->flight)
                                @php
                                    $oldPrice = $offer->flight->price;
                                    $discountedPrice = $oldPrice * (1 - ($offer->discount / 100));
                                @endphp
                                <div class="offer-pricing-row">
                                    <span class="offer-price-current">{{ number_format($discountedPrice, 0, '.', ' ') }} ₽</span>
                                    <span class="offer-price-old">{{ number_format($oldPrice, 0, '.', ' ') }} ₽</span>
                                </div>
                                <a href="{{ route('flights.index', ['from' => $offer->flight->departure_city, 'to' => $offer->flight->arrival_city]) }}" class="btn btn-primary btn-md btn-block">
                                    Забронировать
                                </a>
                            @else
                                <a href="{{ route('flights.index') }}" class="btn btn-primary btn-md btn-block">Подробнее</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="offers-empty-text">Актуальные специальные предложения обновляются.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-card">
                <h2 class="newsletter-title">Будьте в курсе закрытых тарифов</h2>
                <p class="newsletter-desc">Подпишитесь на уведомления о спецпредложениях, начислении бонусных миль и открытии новых маршрутов.</p>

                <div class="newsletter-filter-chips">
                    <button type="button" class="newsletter-chip active">✓ Все обновления</button>
                    <button type="button" class="newsletter-chip">Спецпредложения</button>
                    <button type="button" class="newsletter-chip">Бонусные мили</button>
                    <button type="button" class="newsletter-chip">Перевозка питомцев</button>
                </div>

                <form class="newsletter-form-row" onsubmit="event.preventDefault(); alert('Вы успешно подписались на закрытые тарифы Aether Airways!');">
                    <input type="email" placeholder="Введите ваш email" required>
                    <button type="submit" class="btn btn-primary btn-md">Подписаться</button>
                </form>
            </div>
        </div>
    </section>

    <section class="prefooter-section">
        <div class="container">
            <div class="prefooter-card">
                <h2 class="prefooter-title">
                    Готовы к полёту с Aether Airways?
                </h2>
                <p class="prefooter-desc">
                    Откройте для себя новое измерение авиапутешествий. Зафиксируйте стоимость любого направления прямо сейчас на 24 часа.
                </p>
                <div class="prefooter-actions">
                    <a href="{{ route('flights.index') }}" class="btn btn-pill-white btn-lg">
                        Найти билеты
                        <svg class="ag-arrow-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                    <a href="{{ auth()->check() ? route('profile') : route('login') }}" class="btn btn-pill-glass btn-lg">
                        {{ auth()->check() ? 'Мой кабинет' : 'Войти в аккаунт' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
