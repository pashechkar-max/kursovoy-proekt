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
                    Управление рейсами, контроль расписания, фиксация статусов, автоматическое начисление бонусных миль пассажирам и полное администрирование пользователей.
                </p>
                <div style="margin-top: 18px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="#usersSection" class="btn btn-secondary btn-sm">Пользователи</a>
                    <a href="{{ route('flights.index') }}" class="btn btn-secondary btn-sm" target="_blank">Витрина рейсов</a>
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
                                    <span style="font-weight: 700; white-space: nowrap;">{{ number_format($flight->price, 0, '.', ' ') }} ₽</span>
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

        <section class="admin-card-section" id="usersSection">
            <div class="admin-section-header">
                <div>
                    <h2 class="admin-section-title">Управление пользователями и ролями</h2>
                    <p class="admin-section-sub">
                        Просмотр всех учётных записей, создание новых пользователей, редактирование профилей и баланса миль, назначение ролей и удаление аккаунтов.
                    </p>
                </div>

                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <div class="admin-filter-tabs">
                        <button type="button" class="admin-tab-btn active" onclick="filterUserRows('all', this)">Все ({{ $users->count() }})</button>
                        <button type="button" class="admin-tab-btn" onclick="filterUserRows('admin', this)">Администраторы ({{ $users->where('role', 'admin')->count() }})</button>
                        <button type="button" class="admin-tab-btn" onclick="filterUserRows('user', this)">Пассажиры ({{ $users->where('role', 'user')->count() }})</button>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openCreateUserModal()">
                        Создать пользователя
                    </button>
                </div>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Пользователь</th>
                            <th>Контакты</th>
                            <th style="white-space: nowrap;">Роль</th>
                            <th style="white-space: nowrap;">Баланс миль</th>
                            <th style="white-space: nowrap; text-align: center;">Брони</th>
                            <th style="text-align: right; white-space: nowrap;">Действия</th>
                        </tr>
                    </thead>
                    <tbody id="adminUsersTableBody">
                        @foreach($users as $u)
                            <tr class="user-data-row" data-role="{{ $u->role }}">
                                <td>
                                    <span class="flight-pill">#{{ $u->id }}</span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: {{ $u->role === 'admin' ? '#4f46e5' : '#10b981' }}; color: #fff; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                            {{ mb_substr($u->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <strong style="color: var(--ag-text-primary); font-size: 14.5px;">{{ $u->name }} {{ $u->surname }}</strong>
                                            @if($u->id === auth()->id())
                                                <span style="font-size: 11px; font-weight: 700; background: rgba(79, 70, 229, 0.15); color: #4f46e5; padding: 2px 6px; border-radius: 6px; margin-left: 4px;">Вы</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--ag-text-primary);">{{ $u->email }}</div>
                                    <div style="font-size: 12px; color: var(--ag-text-secondary);">{{ $u->phone ?: 'Телефон не указан' }}</div>
                                </td>
                                <td>
                                    @if($u->role === 'admin')
                                        <span class="status-badge" style="background: rgba(79, 70, 229, 0.12); color: #4f46e5; border: 1px solid rgba(79, 70, 229, 0.3);">
                                            Администратор
                                        </span>
                                    @else
                                        <span class="status-badge" style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            Пассажир
                                        </span>
                                    @endif
                                </td>
                                <td class="cell-price">
                                    <span style="font-weight: 700; color: #4f46e5;">{{ number_format($u->miles, 0, '.', ' ') }}</span>
                                    <span style="font-size: 12px; color: var(--ag-text-secondary); font-weight: 500;">миль</span>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-size: 13px; font-weight: 600; background: var(--ag-surface); padding: 3px 9px; border-radius: 8px; border: 1px solid var(--ag-border);">
                                        {{ $u->bookings_count }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px; align-items: center;">
                                        @if($u->id !== auth()->id())
                                            <form action="{{ route('admin.users.role', $u) }}" method="POST" style="margin: 0; display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-size: 12px;" title="{{ $u->role === 'admin' ? 'Понизить до обычного пассажира' : 'Назначить администратором' }}">
                                                    {{ $u->role === 'admin' ? 'Сделать юзером' : 'Сделать админом' }}
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-size: 12px;" data-user="{{ json_encode($u->only(['id', 'name', 'surname', 'email', 'role', 'phone', 'miles'])) }}" onclick="openEditUserModal(JSON.parse(this.getAttribute('data-user')))" title="Редактировать пользователя">
                                            Изменить
                                        </button>

                                        @if($u->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="margin: 0; display: inline-block;" onsubmit="return confirm('Вы уверены, что хотите удалить пользователя {{ $u->name }} {{ $u->surname }} ({{ $u->email }})? Все связанные бронирования и мили также будут удалены.');">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-size: 12px; color: #ef4444; border-color: rgba(239, 68, 68, 0.3);" title="Удалить пользователя">
                                                    Удалить
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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
                                        <span class="status-badge status-scheduled">активна</span>
                                    @elseif($b->status === 'completed')
                                        <span class="status-badge status-completed">завершена</span>
                                    @elseif($b->status === 'cancelled')
                                        <span class="status-badge status-cancelled">отменена</span>
                                    @else
                                        <span class="status-badge" style="background: rgba(100, 116, 139, 0.1); color: #64748b;">{{ $b->status }}</span>
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
                                            PDF
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

<div class="modal-backdrop" id="createUserModal">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Создание нового пользователя</h3>
                    <span class="text-secondary modal-header-sub">Заполните учётные данные и назначьте роль</span>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeCreateUserModal()">&times;</button>
            </div>
            <div class="modal-body" style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Имя *</label>
                        <input type="text" name="name" class="form-control" required placeholder="Иван">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Фамилия *</label>
                        <input type="text" name="surname" class="form-control" required placeholder="Иванов">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Электронная почта *</label>
                        <input type="email" name="email" class="form-control" required placeholder="user@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Пароль (мин. 8 символов) *</label>
                        <input type="password" name="password" class="form-control" required minlength="8" placeholder="••••••••">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Роль в системе *</label>
                        <select name="role" class="form-control" required>
                            <option value="user" selected>Обычный пассажир (user)</option>
                            <option value="admin">Администратор (admin)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Телефон</label>
                        <input type="text" name="phone" class="form-control" placeholder="+7 999 000-00-00">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Стартовые мили</label>
                        <input type="number" name="miles" class="form-control" value="1000" min="0" placeholder="1000">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCreateUserModal()">Отмена</button>
                <button type="submit" class="btn btn-primary">Создать пользователя</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-backdrop" id="editUserModal">
    <div class="modal-dialog">
        <form id="editUserForm" action="" method="POST">
            @csrf
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Редактирование пользователя</h3>
                    <span class="text-secondary modal-header-sub" id="editUserHeaderSub">ID: —</span>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeEditUserModal()">&times;</button>
            </div>
            <div class="modal-body" style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Имя *</label>
                        <input type="text" name="name" id="editUserName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Фамилия *</label>
                        <input type="text" name="surname" id="editUserSurname" class="form-control" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Электронная почта *</label>
                        <input type="email" name="email" id="editUserEmail" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Новый пароль</label>
                        <input type="password" name="password" class="form-control" minlength="8" placeholder="Оставьте пустым, если не меняется">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Роль в системе *</label>
                        <select name="role" id="editUserRole" class="form-control" required>
                            <option value="user">Обычный пассажир (user)</option>
                            <option value="admin">Администратор (admin)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Телефон</label>
                        <input type="text" name="phone" id="editUserPhone" class="form-control" placeholder="+7 999 000-00-00">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 6px;">Баланс миль</label>
                        <input type="number" name="miles" id="editUserMiles" class="form-control" min="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditUserModal()">Отмена</button>
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterFlightRows(status, btnElem) {
        document.querySelectorAll('.admin-section-header .admin-filter-tabs button').forEach(b => {
            if (b.closest('.admin-section-header') === btnElem.closest('.admin-section-header')) {
                b.classList.remove('active');
            }
        });
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

    function filterUserRows(role, btnElem) {
        const parentTabs = btnElem.parentElement;
        parentTabs.querySelectorAll('.admin-tab-btn').forEach(b => b.classList.remove('active'));
        btnElem.classList.add('active');

        const rows = document.querySelectorAll('.user-data-row');
        rows.forEach(row => {
            const userRole = row.getAttribute('data-role');
            if (role === 'all' || userRole === role) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function openCreateUserModal() {
        openModal('createUserModal');
    }

    function closeCreateUserModal() {
        closeModal('createUserModal');
    }

    function openEditUserModal(user) {
        document.getElementById('editUserForm').action = "{{ url('/admin/users') }}/" + user.id;
        document.getElementById('editUserHeaderSub').textContent = 'ID: #' + user.id + ' • ' + (user.email || '');
        document.getElementById('editUserName').value = user.name || '';
        document.getElementById('editUserSurname').value = user.surname || '';
        document.getElementById('editUserEmail').value = user.email || '';
        document.getElementById('editUserRole').value = user.role || 'user';
        document.getElementById('editUserPhone').value = user.phone || '';
        document.getElementById('editUserMiles').value = user.miles ?? 0;

        openModal('editUserModal');
    }

    function closeEditUserModal() {
        closeModal('editUserModal');
    }
</script>
@endsection
