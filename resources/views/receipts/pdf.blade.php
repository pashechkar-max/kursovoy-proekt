<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Маршрутная квитанция — {{ $booking->booking_number }}</title>
    <style>
        @page {
            margin: 24px 28px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #0f172a;
            margin-bottom: 16px;
            padding-bottom: 10px;
        }
        .header-table td {
            border: none;
            padding: 0 0 10px 0;
            vertical-align: top;
        }
        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 1px;
            line-height: 1.2;
        }
        .subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.3;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.2;
        }
        .booking-badge {
            display: inline-block;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            color: #0f172a;
            margin-top: 4px;
        }
        .section-title {
            font-size: 11.5px;
            font-weight: bold;
            color: #334155;
            background: #f8fafc;
            padding: 5px 8px;
            border-left: 3px solid #4f46e5;
            margin-top: 14px;
            margin-bottom: 8px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table th, table.data-table td {
            padding: 6px 8px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 10px;
            font-weight: bold;
        }
        .route-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 12px;
        }
        .city-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.2;
        }
        .city-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .status-pill {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        .status-active {
            background: #dcfce7;
            color: #15803d;
        }
        .status-completed {
            background: #e0e7ff;
            color: #4338ca;
        }
        .status-expired {
            background: #fee2e2;
            color: #b91c1c;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-size: 9.5px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.4;
        }
        .barcode {
            letter-spacing: 4px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-top: 14px;
            color: #0f172a;
        }
        .text-right {
            text-align: right;
        }
        .route-table {
            width: 100%;
            border-collapse: collapse;
        }
        .route-dep-col {
            width: 42%;
            border: none;
            padding: 0;
        }
        .route-arr-col {
            width: 42%;
            text-align: right;
            border: none;
            padding: 0;
        }
        .route-arrow-col {
            width: 16%;
            text-align: center;
            border: none;
            padding: 0;
        }
        .route-arrow-icon {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
        }
        .route-flight-num {
            font-size: 11px;
            font-weight: bold;
            color: #334155;
        }
        .route-time-val {
            font-size: 12px;
            color: #0f172a;
        }
        .th-passenger {
            width: 40%;
        }
        .th-passport,
        .th-email {
            width: 30%;
        }
        .pet-memo-box {
            font-size: 9.5px;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .barcode-caption {
            text-align: center;
            font-size: 9.5px;
            color: #64748b;
            margin-top: 3px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="logo">AETHER AIRWAYS</div>
                <div class="subtitle">Флагманская авиакомпания • Маршрутная квитанция пассажира</div>
            </td>
            <td class="text-right">
                <div class="doc-title">ЭЛЕКТРОННЫЙ БИЛЕТ / КВИТАНЦИЯ</div>
                <div class="booking-badge">БРОНЬ № {{ $booking->booking_number }}</div>
            </td>
        </tr>
    </table>

    <div class="route-box">
        <table class="route-table">
            <tr>
                <td class="route-dep-col">
                    <div class="city-title">{{ $booking->flight ? $booking->flight->departure_city : '—' }}</div>
                    <div class="city-sub">Дата и время вылета:</div>
                    <strong class="route-time-val">{{ $booking->flight ? $booking->flight->departure_time->format('d.m.Y H:i') : '—' }}</strong>
                </td>
                <td class="route-arrow-col">
                    <div class="route-arrow-icon">&rarr;</div>
                    <div class="route-flight-num">{{ $booking->flight ? $booking->flight->flight_number : '' }}</div>
                </td>
                <td class="route-arr-col">
                    <div class="city-title">{{ $booking->flight ? $booking->flight->arrival_city : '—' }}</div>
                    <div class="city-sub">Дата и время прилёта:</div>
                    <strong class="route-time-val">{{ $booking->flight ? $booking->flight->arrival_time->format('d.m.Y H:i') : '—' }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">ДАННЫЕ ПАССАЖИРА И ДОКУМЕНТА</div>
    <table class="data-table">
        <tr>
            <th class="th-passenger">ФИО Пассажира</th>
            <th class="th-passport">Серия и номер паспорта</th>
            <th class="th-email">Электронная почта</th>
        </tr>
        <tr>
            <td><strong>{{ $booking->passenger_name }} {{ $booking->passenger_surname }}</strong></td>
            <td>{{ $booking->passport_series }} {{ $booking->passport_number }}</td>
            <td>{{ $booking->user ? $booking->user->email : '—' }}</td>
        </tr>
    </table>

    <div class="section-title">ИНФОРМАЦИЯ О БРОНИРОВАНИИ И ТАРИФЕ</div>
    <table class="data-table">
        <tr>
            <th>Номер бронирования</th>
            <th>Выбранное место</th>
            <th>Класс обслуживания</th>
            <th>Зафиксированная стоимость</th>
            <th>Текущий статус</th>
            <th>Срок действия брони</th>
        </tr>
        <tr>
            <td><strong>{{ $booking->booking_number }}</strong></td>
            <td><strong>{{ $booking->seat ? $booking->seat->seat_number : '—' }}</strong></td>
            <td>{{ ucfirst($booking->booking_class) }}</td>
            <td><strong>{{ number_format($booking->price, 0, '.', ' ') }} ₽</strong></td>
            <td>
                @if($booking->status === 'active')
                    <span class="status-pill status-active">АКТИВНО</span>
                @elseif($booking->status === 'completed')
                    <span class="status-pill status-completed">ЗАВЕРШЕНО</span>
                @else
                    <span class="status-pill status-expired">{{ strtoupper($booking->status) }}</span>
                @endif
            </td>
            <td>до {{ $booking->expires_at->format('d.m.Y H:i') }}</td>
        </tr>
    </table>

    @if($booking->hasPet())
        <div class="section-title">СПЕЦИАЛЬНЫЕ УСЛУГИ: ПЕРЕВОЗКА ЖИВОТНОГО (SSR PETC / AVIH / EUPS)</div>
        <table class="data-table">
            <tr>
                <th>Кличка и вид</th>
                <th>Порода</th>
                <th>Вес брутто (с контейнером)</th>
                <th>Способ размещения</th>
                <th>Габариты контейнера</th>
                <th>Ветпаспорт / микрочип</th>
            </tr>
            <tr>
                <td><strong>{{ $booking->petBooking->pet_name }}</strong> ({{ $booking->petBooking->petTypeName() }})</td>
                <td>{{ $booking->petBooking->breed ?? 'Не указана' }}</td>
                <td><strong>{{ $booking->petBooking->weight }} кг</strong></td>
                <td>{{ $booking->petBooking->transportTypeName() }}</td>
                <td>{{ $booking->petBooking->carrier_dimensions ?? '44x30x26 см' }}</td>
                <td>{{ $booking->petBooking->vet_passport_number }}</td>
            </tr>
        </table>
        <div class="pet-memo-box">
            <strong>Памятка ветеринарного контроля:</strong> Для посадки на рейс пассажир обязан предъявить оригинал ветеринарного паспорта с действующей вакцинацией против бешенства и пройти ветеринарный контроль в аэропорту вылета перед регистрацией.
        </div>
    @endif

    <div class="barcode">
        ||||| | |||| ||| |||||| || ||||| |||| |||||
    </div>
    <div class="barcode-caption">
        {{ $booking->booking_number }}-{{ $booking->flight ? $booking->flight->flight_number : '' }}-{{ $booking->seat ? $booking->seat->seat_number : '' }}
    </div>

    <div class="footer">
        Маршрутная квитанция сгенерирована автоматически системой бронирования Aether Airways.<br>
        Тариф зафиксирован на 24 часа. Сохраняйте данный документ до окончания поездки.
    </div>

</body>
</html>
