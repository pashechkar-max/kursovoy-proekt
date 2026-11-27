@extends('layouts.app')

@section('title', 'Центр управления полётами — Aether Airways Admin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('style/pages/admin.css') }}?v=1">
@endsection

@section('content')
<main class="page-wrapper" style="padding: 32px 0;">
    <div class="container">

        <section class="admin-hero">
            <div>
                <div class="admin-badge-row">
                    <span style="font-size: 13px; color: var(--ag-text-secondary); font-weight: 600;">Уровень доступа: Администратор</span>
                </div>
                <h1 class="admin-hero-title">Управление рейсами и центр полётов</h1>
                <p class="admin-hero-desc">
                    Управление рейсами, контроль расписания, фиксация статусов и автоматическое начисление бонусных миль пассажирам (Функция 12).
                </p>
                <div style="margin-top: 18px; display: flex; gap: 10px;">
                    <a href="{{ route('profile') }}" class="btn btn-secondary btn-sm">Личный кабинет</a>
                    <a href="{{ route('flights.index') }}" class="btn btn-secondary btn-sm" target="_blank">Витрина рейсов ↗</a>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openThemeModal()">Палитра оформления</button>
                </div>
            </div>

            <div class="admin-hero-stats">
                <div class="admin-stat-card">
                    <span class="admin-stat-val highlight">{{ $stats['total_users'] }}</span>
                    <span class="admin-stat-label">Пользователей</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-val">{{ $stats['total_flights'] }}</span>
                    <span class="admin-stat-label">Всего рейсов</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-val" style="color: #10b981;">{{ $stats['active_bookings'] }}</span>
                    <span class="admin-stat-label">Активных броней</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-val" style="color: #6366f1;">{{ $stats['completed_bookings'] }}</span>
                    <span class="admin-stat-label">Завершено</span>
                </div>
            </div>
        </section>

        <section class="admin-card-section">
            <div class="admin-section-header">
                <div>
                    <h2 class="admin-section-title">Расписание и завершение рейсов</h2>
                    <p class="admin-section-sub">
                        При завершении рейса система автоматически переводит активные брони в статус «completed» и начисляет бонусные мили пассажирам.
                    </p>
                </div>

                <div class="admin-filter-tabs">
                    <button type="button" class="admin-tab-btn active" onclick="filterFlightRows('all', this)">Все рейсы ({{ $flights->count() }})</button>
                    <button type="button" class="admin-tab-btn" onclick="filterFlightRows('scheduled', this)">Запланированные ({{ $flights->where('status', 'scheduled')->count() }})</button>
                    <button type="button" class="admin-tab-btn" onclick="filterFlightRows('completed', this)">Завершённые ({{ $flights->where('status', 'completed')->count() }})</button>
                </div>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Рейс</th>
                            <th>Маршрут полёта</th>
                            <th style="white-space: nowrap;">Вылет</th>
                            <th style="white-space: nowrap;">Прибытие</th>
                            <th style="white-space: nowrap;">Тариф</th>
                            <th style="white-space: nowrap;">Кресла / Брони</th>
                            <th style="white-space: nowrap;">Статус рейса</th>
                            <th style="text-align: right; white-space: nowrap;">Действие (Функция 12)</th>
                        </tr>
                    </thead>
                    <tbody id="adminFlightsTableBody">
                        @foreach($flights as $flight)
                            <tr class="flight-data-row" data-status="{{ $flight->status }}">
                                <td>
                                    <span class="flight-pill">{{ $flight->flight_number }}</span>
                                </td>
                                <td>
                                    <strong style="color: var(--ag-text-primary);">{{ $flight->departure_city }}</strong>
                                    <span class="route-arrow">→</span>
                                    <strong style="color: var(--ag-text-primary);">{{ $flight->arrival_city }}</strong>
                                </td>
                                <td>
                                    <span style="font-weight: 600;">{{ $flight->departure_time->format('d.m.Y') }}</span>
                                    <span style="color: var(--ag-text-secondary); font-size: 13px;">{{ $flight->departure_time->format('H:i') }}</span>
                                </td>
                                <td>
                                    <span style="font-weight: 600;">{{ $flight->arrival_time->format('d.m.Y') }}</span>
                                    <span style="color: var(--ag-text-secondary); font-size: 13px;">{{ $flight->arrival_time->format('H:i') }}</span>
                                </td>
                                <td class="cell-price" style="white-space: nowrap;">
                                    <span style="font-weight: 700; white-space: nowrap;">{{ number_format($flight->price, 0, '.', '&nbsp;') }}&nbsp;₽</span>
                                </td>
                                <td>
                                    <span title="Всего мест / Активных броней" style="font-size: 13px; font-weight: 600; background: var(--ag-surface); padding: 4px 10px; border-radius: 8px; border: 1px solid var(--ag-border);">
                                        {{ $flight->bookings_count }} / {{ $flight->seats_count }}
                                    </span>
                                </td>
                                <td>
                                    @if($flight->status === 'scheduled')
                                        <span class="status-badge status-scheduled">
                                            запланирован
                                        </span>
                                    @elseif($flight->status === 'completed')
                                        <span class="status-badge status-completed">
                                            завершён
                                        </span>
                                    @else
                                        <span class="status-badge status-cancelled">
                                            отменён
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if($flight->status !== 'completed')
                                        <form action="{{ route('admin.flights.complete', $flight) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Завершить рейс {{ $flight->flight_number }} ({{ $flight->departure_city }} → {{ $flight->arrival_city }}) и начислить пассажирам мили за полёт?');">
                                            @csrf
                                            <button type="submit" class="btn-complete-flight">
                                                Завершить
                                            </button>
                                        </form>
                                    @else
                                        <span style="color: #10b981; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                            Мили начислены
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-card-section">
            <div class="admin-section-header">
                <div>
                    <h2 class="admin-section-title">Последние бронирования пользователей</h2>
                    <p class="admin-section-sub">
                        Мониторинг активных броней с 24-часовой фиксацией, выданных квитанций и статусов оплаты.
                    </p>
                </div>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Номер брони</th>
                            <th>Пассажир</th>
                            <th>Рейс</th>
                            <th>Место / Класс</th>
                            <th>Стоимость</th>
                            <th>Статус</th>
                            <th>Срок брони</th>
                            <th style="text-align: right;">Квитанция билета</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $b)
                            <tr>
                                <td>
                                    <span class="flight-pill">{{ $b->booking_number }}</span>
                                </td>
                                <td>
                                    <strong style="color: var(--ag-text-primary);">{{ $b->passenger_name }} {{ $b->passenger_surname }}</strong>
                                    <div style="font-size: 12px; color: var(--ag-text-secondary);">{{ $b->user ? $b->user->email : '—' }}</div>
                                </td>
                                <td>
                                    <span style="font-weight: 700;">{{ $b->flight ? $b->flight->flight_number : '—' }}</span>
                                    <div style="font-size: 12px; color: var(--ag-text-secondary);">
                                        {{ $b->flight ? $b->flight->departure_city . ' → ' . $b->flight->arrival_city : '—' }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700;">{{ $b->seat ? $b->seat->seat_number : '—' }}</span>
                                    <span style="font-size: 12px; color: var(--ag-text-secondary); text-transform: capitalize;">({{ $b->booking_class }})</span>
                                </td>
                                <td>
                                    <strong style="font-size: 15px; color: var(--ag-text-primary);">{{ number_format($b->price, 0, '.', ' ') }} ₽</strong>
                                </td>
                                <td>
                                    @if($b->status === 'active')
                                        <span class="status-badge status-scheduled"><span class="status-dot"></span> активна</span>
                                    @elseif($b->status === 'completed')
                                        <span class="status-badge status-completed"><span class="status-dot"></span> завершена</span>
                                    @elseif($b->status === 'cancelled')
                                        <span class="status-badge status-cancelled"><span class="status-dot"></span> отменена</span>
                                    @else
                                        <span class="status-badge" style="background: rgba(100, 116, 139, 0.1); color: #64748b;"><span class="status-dot"></span> {{ $b->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size: 13px; color: var(--ag-text-secondary);">
                                        до {{ $b->expires_at ? $b->expires_at->format('d.m.Y H:i') : '—' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="{{ route('bookings.receipt', $b) }}" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-size: 12px;" title="Скачать PDF">
                                            📄 PDF
                                        </a>
                                        <a href="{{ route('bookings.receipt.view', $b) }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-size: 12px;" title="Просмотр в браузере">
                                            Просмотр
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 30px; color: var(--ag-text-secondary);">
                                    Бронирования в системе пока отсутствуют.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</main>
@endsection

@section('scripts')
<script>
    function filterFlightRows(status, btnElem) {
        document.querySelectorAll('.admin-tab-btn').forEach(b => b.classList.remove('active'));
        btnElem.classList.add('active');

        const rows = document.querySelectorAll('.flight-data-row');
        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            if (status === 'all' || rowStatus === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
