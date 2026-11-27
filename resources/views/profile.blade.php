@extends('layouts.app')

@section('title', 'Личный кабинет пассажира — Aether Airways')

@section('styles')
    <link rel="stylesheet" href="{{ asset('style/pages/profile.css') }}?v=1">
@endsection

@section('content')
<main class="page-wrapper profile-page-wrapper">
    <div class="container">

        <section class="profile-hero">
            <div class="profile-hero-left">
                <div class="profile-large-coral-avatar">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>

                <div class="profile-hero-meta">
                    <h1 class="profile-display-name">{{ $user->name }} {{ $user->surname }}</h1>
                    <span class="profile-handle">{{ $user->email }} • {{ $user->role === 'admin' ? 'Администратор' : 'Пассажир' }}</span>
                    <p class="profile-bio">
                        Aether Airways Priority •
                        @if($user->hasCompletePassport())
                            <span class="passport-status-ok">✓ Паспортные данные заполнены</span>
                        @else
                            <span class="passport-status-warning">⚠ Паспорт не заполнен (требуется для бронирования)</span>
                        @endif
                    </p>
                    <div class="profile-actions-row">
                        <button type="button" class="btn btn-primary btn-sm" onclick="openThemeModal()">
                            Изменить палитру цветов
                        </button>
                        @if($user->isAdmin())
                            <a href="{{ route('admin.index') }}" class="btn btn-primary btn-sm btn-admin-shortcut">
                                Панель администратора
                            </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="profile-logout-form">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm btn-logout-danger">
                                Выйти из аккаунта
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="profile-hero-right">
                <div class="profile-stat-box">
                    <span class="profile-stat-val" id="userMilesBalance">{{ number_format($user->miles, 0, '.', ' ') }}</span>
                    <span class="profile-stat-label">Бонусных миль</span>
                </div>
                <div class="profile-stat-box">
                    <span class="profile-stat-val">{{ $allBookings->count() }}</span>
                    <span class="profile-stat-label">Всего бронирований</span>
                </div>
                <div class="profile-stat-box">
                    <span class="profile-stat-val stat-val-highlight">{{ $upcomingBookings->count() }}</span>
                    <span class="profile-stat-label">Активных</span>
                </div>
            </div>
        </section>

        <div class="profile-tabs-center-wrap">
            <div class="profile-pill-tabs">
                <button type="button" class="profile-nav-tab {{ ($activeTab ?? 'tabBookings') === 'tabBookings' ? 'active' : '' }}" onclick="switchProfileTab('tabBookings')" data-tab="tabBookings">
                    Бронирования <span class="tab-count-pill">{{ $allBookings->count() }}</span>
                </button>
                <button type="button" class="profile-nav-tab {{ ($activeTab ?? '') === 'tabPassport' ? 'active' : '' }}" onclick="switchProfileTab('tabPassport')" data-tab="tabPassport">
                    Паспортные данные
                </button>
                <button type="button" class="profile-nav-tab {{ ($activeTab ?? '') === 'tabMiles' ? 'active' : '' }}" onclick="switchProfileTab('tabMiles')" data-tab="tabMiles">
                    Бонусные мили <span class="tab-count-pill">{{ number_format($user->miles, 0, '.', ' ') }}</span>
                </button>
            </div>
        </div>

        <div class="profile-tab-pane {{ ($activeTab ?? 'tabBookings') === 'tabBookings' ? 'active' : '' }}" id="tabBookings">

            <h2 class="bookings-group-title">
                Предстоящие бронирования (Фиксация на 24 часа)
            </h2>

            @forelse($upcomingBookings as $booking)
                <div class="active-booking-card mb-24">
                    <div class="booking-header-row">
                        <div class="booking-ref-block">
                            <span class="booking-ref-number">{{ $booking->booking_number }}</span>
                            <span class="badge badge-active"><span class="badge-dot"></span>АКТИВНО</span>
                            <span class="badge badge-expired"><span class="badge-dot"></span> {{ ucfirst($booking->booking_class) }}</span>
                            @if($booking->extension_count > 0)
                                <span class="badge badge-miles">Продлено (1/1)</span>
                            @endif
                            @if($booking->hasPet())
                                <span class="badge badge-miles badge-pet-info">Питомец: {{ $booking->petBooking->pet_name }} ({{ $booking->petBooking->transportTypeName() }})</span>
                            @endif
                        </div>

                        <div class="booking-expiry-timer-box">
                            <span>Срок брони: <strong>до {{ $booking->expires_at->format('d.m.Y H:i') }}</strong></span>
                        </div>
                    </div>

                    <div class="booking-details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Маршрут и рейс</span>
                            <span class="detail-value">
                                {{ $booking->flight ? $booking->flight->departure_city : '—' }}
                                <svg class="ag-arrow-icon arrow-inline" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                {{ $booking->flight ? $booking->flight->arrival_city : '—' }}
                                • {{ $booking->flight ? $booking->flight->flight_number : '' }}
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Дата и время вылета</span>
                            <span class="detail-value">{{ $booking->flight ? $booking->flight->departure_time->format('d.m.Y, H:i') : '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Выбранное место</span>
                            <span class="detail-value">{{ $booking->seat ? $booking->seat->seat_number : '—' }} ({{ $booking->booking_class }})</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Зафиксированная стоимость</span>
                            <span class="detail-value detail-value-accent">{{ number_format($booking->price, 0, '.', ' ') }} ₽</span>
                        </div>
                        @if($booking->hasPet())
                            <div class="detail-item detail-pet-box">
                                <span class="detail-label detail-label-pet">Услуга перевозки животного ({{ $booking->petBooking->petTypeName() }})</span>
                                <span class="detail-value detail-value-pet">
                                    Кличка: <strong>{{ $booking->petBooking->pet_name }}</strong>
                                    @if($booking->petBooking->breed) • Порода: {{ $booking->petBooking->breed }} @endif
                                    • Вес: <strong>{{ $booking->petBooking->weight }} кг</strong>
                                    • Размещение: <strong>{{ $booking->petBooking->transportTypeName() }}</strong>
                                    • Ветпаспорт: {{ $booking->petBooking->vet_passport_number }}
                                    • Доплата: {{ number_format($booking->petBooking->service_fee, 0, '.', ' ') }} ₽
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="booking-actions-bar">
                        <div class="booking-actions-left actions-flex-wrap">

                            @if($booking->canUpgrade())
                                <button type="button" class="btn btn-secondary btn-sm" onclick="openUpgradeModal({{ $booking->id }}, '{{ $booking->booking_number }}')">
                                    Апгрейд до Комфорт (-2 000 миль)
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-sm btn-disabled-dim" disabled title="Апгрейд недоступен">
                                    Комфорт включен
                                </button>
                            @endif

                            @if($booking->canExtend())
                                <button type="button" class="btn btn-secondary btn-sm" onclick="openExtendModal({{ $booking->id }}, '{{ $booking->booking_number }}')">
                                    Продлить бронь (+24ч за 500 миль)
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-sm btn-disabled-dim" disabled title="Продление уже использовано или недоступно">
                                    Продление (1/1)
                                </button>
                            @endif

                            <a href="{{ route('bookings.receipt', $booking) }}" class="btn btn-secondary btn-sm" title="Скачать PDF-файл">
                                📄 Скачать PDF
                            </a>
                            <a href="{{ route('bookings.receipt.view', $booking) }}" target="_blank" class="btn btn-secondary btn-sm" title="Открыть в браузере">
                                Просмотр билета
                            </a>

                            <button type="button" class="btn btn-secondary btn-sm btn-cancel-booking" onclick="openCancelModal({{ $booking->id }}, '{{ $booking->booking_number }}')">
                                Отменить бронь
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state-card empty-state-card-active">
                    <p class="empty-state-card-desc">У вас пока нет активных предстоящих бронирований.</p>
                    <a href="{{ route('flights.index') }}" class="btn btn-primary btn-sm">Найти и забронировать рейс</a>
                </div>
            @endforelse

            <h2 class="bookings-group-title title-archived mt-36">
                История прошедших рейсов и завершённых бронирований
            </h2>

            @forelse($pastBookings as $booking)
                <div class="past-booking-card mb-12">
                    <div>
                        <span class="booking-ref-number ref-sm">{{ $booking->booking_number }}</span>
                        <div class="booking-archived-date">{{ $booking->created_at->format('d.m.Y') }}</div>
                    </div>
                    <div>
                        <strong class="text-primary">
                            {{ $booking->flight ? $booking->flight->departure_city : '—' }}
                            <svg class="ag-arrow-icon arrow-inline" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                            {{ $booking->flight ? $booking->flight->arrival_city : '—' }}
                        </strong>
                        <div class="booking-archived-detail">
                            Рейс {{ $booking->flight ? $booking->flight->flight_number : '' }}
                            • Место {{ $booking->seat ? $booking->seat->seat_number : '—' }} ({{ $booking->booking_class }})
                            @if($booking->hasPet())
                                • Питомец: {{ $booking->petBooking->pet_name }} ({{ $booking->petBooking->transportTypeName() }})
                            @endif
                            • {{ number_format($booking->price, 0, '.', ' ') }} ₽
                        </div>
                    </div>
                    <div>
                        @if($booking->status === 'completed')
                            <span class="badge badge-completed"><span class="badge-dot"></span>ЗАВЕРШЕНО</span>
                        @elseif($booking->status === 'expired')
                            <span class="badge badge-expired"><span class="badge-dot"></span>ИСТЕКЛА</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="badge badge-expired badge-cancelled"><span class="badge-dot"></span>ОТМЕНЕНА</span>
                        @else
                            <span class="badge badge-active">{{ $booking->status }}</span>
                        @endif
                    </div>
                    <div>
                        @if($booking->status === 'completed')
                            <span class="badge badge-miles">+{{ (int)($booking->price * 0.2) }} миль начислено</span>
                        @else
                            <span class="text-secondary text-sm">—</span>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('bookings.receipt', $booking) }}" class="btn btn-secondary btn-sm">
                            Квитанция PDF
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-state-card empty-state-card-past">
                    <p class="empty-state-card-muted">Архивных бронирований пока нет.</p>
                </div>
            @endforelse
        </div>

        <div class="profile-tab-pane {{ ($activeTab ?? '') === 'tabPassport' ? 'active' : '' }}" id="tabPassport">
            <div class="passport-card">
                <h2 class="profile-tab-title">
                    Личные и паспортные данные пассажира
                </h2>
                <p class="profile-tab-sub">
                    Данные автоматически подставляются при оформлении бронирования и фиксируются в маршрутной квитанции.
                </p>

                @if($errors->any())
                    <div class="auth-error-banner">
                        <ul class="auth-error-list auth-error-list-red">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" id="profilePassportForm" novalidate>
                    @csrf
                    <div class="passport-form-grid">
                        <div class="form-group">
                            <label class="form-label required" for="passName">Имя</label>
                            <input type="text" id="passName" name="name" class="form-control @error('name') input-error @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="field-validation-error">{{ $message }}</span>
                            @enderror
                            <span class="field-validation-error client-err" id="clientErrName"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="passSurname">Фамилия</label>
                            <input type="text" id="passSurname" name="surname" class="form-control @error('surname') input-error @enderror" value="{{ old('surname', $user->surname) }}" required>
                            @error('surname')
                                <span class="field-validation-error">{{ $message }}</span>
                            @enderror
                            <span class="field-validation-error client-err" id="clientErrSurname"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="passEmail">Электронная почта (логин)</label>
                            <input type="email" id="passEmail" class="form-control form-control-disabled" value="{{ $user->email }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="passPhone">Номер телефона</label>
                            <input type="tel" id="passPhone" name="phone" class="form-control @error('phone') input-error @enderror" value="{{ old('phone', $user->phone) }}" placeholder="+7 (999) 000-00-00">
                            @error('phone')
                                <span class="field-validation-error">{{ $message }}</span>
                            @enderror
                            <span class="field-validation-error client-err" id="clientErrPhone"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="passBirth">Дата рождения</label>
                            <input type="date" id="passBirth" name="birth_date" class="form-control @error('birth_date') input-error @enderror" value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                            @error('birth_date')
                                <span class="field-validation-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="passCitizenship">Гражданство</label>
                            <input type="text" id="passCitizenship" name="citizenship" class="form-control @error('citizenship') input-error @enderror" value="{{ old('citizenship', $user->citizenship ?? 'Российская Федерация') }}">
                            @error('citizenship')
                                <span class="field-validation-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="passSeries">Серия паспорта (4 цифры)</label>
                            <input type="text" id="passSeries" name="passport_series" maxlength="4" class="form-control @error('passport_series') input-error @enderror" value="{{ old('passport_series', $user->passport_series) }}" placeholder="6914" required>
                            @error('passport_series')
                                <span class="field-validation-error">{{ $message }}</span>
                            @enderror
                            <span class="field-validation-error client-err" id="clientErrSeries"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="passNum">Номер паспорта (6 цифр)</label>
                            <input type="text" id="passNum" name="passport_number" maxlength="6" class="form-control @error('passport_number') input-error @enderror" value="{{ old('passport_number', $user->passport_number) }}" placeholder="482010" required>
                            @error('passport_number')
                                <span class="field-validation-error">{{ $message }}</span>
                            @enderror
                            <span class="field-validation-error client-err" id="clientErrNum"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="passIssueDate">Дата выдачи паспорта</label>
                            <input type="date" id="passIssueDate" name="passport_issued_date" class="form-control @error('passport_issued_date') input-error @enderror" value="{{ old('passport_issued_date', $user->passport_issued_date ? $user->passport_issued_date->format('Y-m-d') : '') }}" required>
                            @error('passport_issued_date')
                                <span class="field-validation-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="passIssuer">Кем выдан паспорт</label>
                            <input type="text" id="passIssuer" name="passport_issued_by" class="form-control @error('passport_issued_by') input-error @enderror" value="{{ old('passport_issued_by', $user->passport_issued_by) }}" placeholder="ГУ МВД по Томской области" required>
                            @error('passport_issued_by')
                                <span class="field-validation-error">{{ $message }}</span>
                            @enderror
                            <span class="field-validation-error client-err" id="clientErrIssuer"></span>
                        </div>
                    </div>

                    <div class="flex-end mt-24">
                        <button type="submit" class="btn btn-primary btn-md">
                            Сохранить паспортные данные
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="profile-tab-pane {{ ($activeTab ?? '') === 'tabMiles' ? 'active' : '' }}" id="tabMiles">
            <div class="miles-promo-banner">
                <div class="miles-promo-content">
                    <span class="miles-promo-tag">Программа лояльности AetherMiles</span>
                    <h3 class="miles-promo-title">Ваш баланс: {{ number_format($user->miles, 0, '.', ' ') }} бонусных миль</h3>
                    <p class="miles-promo-desc">
                        1 000 Welcome Miles при регистрации • Апгрейд класса Эконом → Комфорт за 2 000 миль • Продление срока активной брони на 24 часа за 500 миль.
                    </p>
                </div>
            </div>

            <div class="miles-history-card">
                <div class="miles-history-header">
                    <div>
                        <h2 class="miles-history-title">История начислений и списаний миль</h2>
                        <p class="miles-history-sub">
                            Все операции с милями фиксируются в транзакционном журнале базы данных.
                        </p>
                    </div>
                </div>

                <table class="miles-table">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Тип операции</th>
                            <th>Описание транзакции</th>
                            <th>Связанная бронь</th>
                            <th>Сумма миль</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td>{{ $tx->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    @if($tx->type === 'welcome')
                                        <span class="badge badge-active">welcome (+1 000)</span>
                                    @elseif($tx->type === 'flight')
                                        <span class="badge badge-completed">полет</span>
                                    @elseif($tx->type === 'upgrade')
                                        <span class="badge badge-op-upgrade">апгрейд</span>
                                    @elseif($tx->type === 'extension')
                                        <span class="badge badge-op-extend">продление</span>
                                    @endif
                                </td>
                                <td>{{ $tx->description }}</td>
                                <td>
                                    @if($tx->booking)
                                        <span class="booking-ref-number ref-sm">{{ $tx->booking->booking_number }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="{{ $tx->amount > 0 ? 'text-emerald' : 'text-rose' }} font-bold">
                                    {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 0, '.', ' ') }} миль
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="table-empty-td">
                                    История операций с милями пуста.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>
@endsection

@section('modals')

<div class="modal-backdrop" id="upgradeModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">Апгрейд класса за бонусные мили</h3>
            <button type="button" class="modal-close-btn" onclick="closeUpgradeModal()">&times;</button>
        </div>
        <form id="upgradeForm" action="" method="POST">
            @csrf
            <div class="modal-body">
                <div class="upgrade-summary-card">
                    <div class="upgrade-card-title">
                        Эконом
                        <svg class="ag-arrow-icon arrow-inline" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                        Комфорт = 2 000 миль
                    </div>
                    <p class="upgrade-card-sub">Увеличенное пространство для ног, приоритетная посадка и улучшенное бортовое питание.</p>
                </div>
                <div class="modal-summary-list">
                    <div class="flex-between">
                        <span class="text-secondary">Номер бронирования:</span>
                        <strong id="upgradeBookingNumber">—</strong>
                    </div>
                    <div class="flex-between">
                        <span class="text-secondary">Списание миль:</span>
                        <strong class="text-rose">-2 000 миль</strong>
                    </div>
                    <div class="modal-summary-row row-total flex-between">
                        <span class="text-secondary">Текущий баланс:</span>
                        <strong class="text-emerald">{{ number_format($user->miles, 0, '.', ' ') }} миль</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeUpgradeModal()">Отмена</button>
                <button type="submit" class="btn btn-primary" {{ $user->miles < 2000 ? 'disabled' : '' }}>
                    {{ $user->miles < 2000 ? 'Недостаточно миль' : 'Подтвердить апгрейд (-2 000 миль)' }}
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal-backdrop" id="extendModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">Продление активного бронирования на 24 часа</h3>
            <button type="button" class="modal-close-btn" onclick="closeExtendModal()">&times;</button>
        </div>
        <form id="extendForm" action="" method="POST">
            @csrf
            <div class="modal-body">
                <div class="upgrade-summary-card">
                    <div class="upgrade-card-title">+24 часа действия тарифа за 500 миль</div>
                    <p class="upgrade-card-sub">Продление доступно не более 1 раза для одного бронирования (ограничение 1/1).</p>
                </div>
                <div class="modal-summary-list">
                    <div class="flex-between">
                        <span class="text-secondary">Номер бронирования:</span>
                        <strong id="extendBookingNumber">—</strong>
                    </div>
                    <div class="flex-between">
                        <span class="text-secondary">Стоимость продления:</span>
                        <strong class="text-rose">-500 миль</strong>
                    </div>
                    <div class="modal-summary-row row-total flex-between">
                        <span class="text-secondary">Ваш баланс:</span>
                        <strong class="text-emerald">{{ number_format($user->miles, 0, '.', ' ') }} миль</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeExtendModal()">Отмена</button>
                <button type="submit" class="btn btn-primary" {{ $user->miles < 500 ? 'disabled' : '' }}>
                    {{ $user->miles < 500 ? 'Недостаточно миль' : 'Подтвердить продление (-500 миль)' }}
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal-backdrop" id="cancelModal">
    <div class="modal-dialog">
        <form id="cancelForm" action="" method="POST">
            @csrf
            <div class="modal-header">
                <div>
                    <h3 class="modal-title modal-title-danger">Отмена бронирования</h3>
                    <span class="text-secondary modal-subtitle-sm">Освобождение места рейса</span>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeCancelModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p class="modal-desc-text">
                    Вы уверены, что хотите отменить бронирование <strong id="cancelBookingNumber">—</strong>?
                </p>
                <div class="modal-data-box modal-data-box-danger">
                    ⚠ Бронирование перейдёт в статус <strong>cancelled</strong>, а выбранное вами место сразу станет доступно для других пассажиров.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCancelModal()">Назад</button>
                <button type="submit" class="btn btn-primary btn-danger-submit">
                    Подтвердить отмену
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchProfileTab(tabId) {
        document.querySelectorAll('.profile-tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.profile-nav-tab').forEach(t => t.classList.remove('active'));

        const targetPane = document.getElementById(tabId);
        const targetBtn = document.querySelector(`.profile-nav-tab[data-tab="${tabId}"]`);

        if (targetPane) targetPane.classList.add('active');
        if (targetBtn) targetBtn.classList.add('active');

        const url = new URL(window.location);
        url.searchParams.set('tab', tabId);
        window.history.replaceState({}, '', url);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const passportForm = document.getElementById('profilePassportForm');
        if (passportForm) {
            passportForm.addEventListener('submit', function (e) {
                let hasError = false;
                passportForm.querySelectorAll('.client-err').forEach(el => el.style.display = 'none');
                passportForm.querySelectorAll('.form-control').forEach(el => el.classList.remove('input-error'));

                const nameInput = document.getElementById('passName');
                if (!nameInput.value.trim() || nameInput.value.trim().length < 2) {
                    document.getElementById('clientErrName').textContent = 'Имя должно содержать минимум 2 буквы.';
                    document.getElementById('clientErrName').style.display = 'block';
                    nameInput.classList.add('input-error');
                    hasError = true;
                }

                const surnameInput = document.getElementById('passSurname');
                if (!surnameInput.value.trim() || surnameInput.value.trim().length < 2) {
                    document.getElementById('clientErrSurname').textContent = 'Фамилия должна содержать минимум 2 буквы.';
                    document.getElementById('clientErrSurname').style.display = 'block';
                    surnameInput.classList.add('input-error');
                    hasError = true;
                }

                const seriesInput = document.getElementById('passSeries');
                if (seriesInput.value && !/^\d{4}$/.test(seriesInput.value.trim())) {
                    document.getElementById('clientErrSeries').textContent = 'Серия паспорта РФ должна содержать ровно 4 цифры.';
                    document.getElementById('clientErrSeries').style.display = 'block';
                    seriesInput.classList.add('input-error');
                    hasError = true;
                }

                const numInput = document.getElementById('passNum');
                if (numInput.value && !/^\d{6}$/.test(numInput.value.trim())) {
                    document.getElementById('clientErrNum').textContent = 'Номер паспорта РФ должен содержать ровно 6 цифр.';
                    document.getElementById('clientErrNum').style.display = 'block';
                    numInput.classList.add('input-error');
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                }
            });
        }
    });

    function openUpgradeModal(bookingId, bookingNumber) {
        document.getElementById('upgradeBookingNumber').textContent = bookingNumber;
        document.getElementById('upgradeForm').action = "{{ url('/bookings') }}/" + bookingId + "/upgrade";
        openModal('upgradeModal');
    }

    function closeUpgradeModal() {
        closeModal('upgradeModal');
    }

    function openExtendModal(bookingId, bookingNumber) {
        document.getElementById('extendBookingNumber').textContent = bookingNumber;
        document.getElementById('extendForm').action = "{{ url('/bookings') }}/" + bookingId + "/extend";
        openModal('extendModal');
    }

    function closeExtendModal() {
        closeModal('extendModal');
    }

    function openCancelModal(bookingId, bookingNumber) {
        document.getElementById('cancelBookingNumber').textContent = bookingNumber;
        document.getElementById('cancelForm').action = "{{ url('/bookings') }}/" + bookingId + "/cancel";
        openModal('cancelModal');
    }

    function closeCancelModal() {
        closeModal('cancelModal');
    }
</script>
@endsection
