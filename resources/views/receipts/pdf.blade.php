<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Маршрутная квитанция — {{ $booking->booking_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 1px;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
        }
        .title-block {
            text-align: right;
            float: right;
        }
        .doc-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .booking-badge {
            display: inline-block;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 13px;
            color: #0f172a;
            margin-top: 5px;
        }
        .clear {
            clear: both;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #334155;
            background: #f8fafc;
            padding: 6px 10px;
            border-left: 3px solid #6366f1;
            margin-top: 20px;
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: bold;
        }
        .route-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .city-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .city-sub {
            font-size: 11px;
            color: #64748b;
        }
        .flight-info {
            text-align: center;
        }
        .status-pill {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
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
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            font-size: 10px;
            color: #94a3b8;
            text-align: center;
        }
        .barcode {
            letter-spacing: 5px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            color: #0f172a;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="title-block">
            <div class="doc-title">ЭЛЕКТРОННЫЙ БИЛЕТ / КВИТАНЦИЯ</div>
            <div class="booking-badge">БРОНЬ № {{ $booking->booking_number }}</div>
        </div>
        <div class="logo">AETHER AIRWAYS</div>
        <div class="subtitle">Флагманская авиакомпания • Маршрутная квитанция пассажира</div>
        <div class="clear"></div>
    </div>

    <!-- Маршрут и рейс -->
    <div class="route-box">
        <table>
            <tr>
                <td style="width: 40%; border: none;">
                    <div class="city-title">{{ $booking->flight ? $booking->flight->departure_city : '—' }}</div>
                    <div class="city-sub">Дата и время вылета:</div>
                    <strong>{{ $booking->flight ? $booking->flight->departure_time->format('d.m.Y H:i') : '—' }}</strong>
                </td>
                <td style="width: 20%; text-align: center; border: none;">
                    <div style="font-size: 18px; font-weight: bold; color: #4f46e5;">&rarr;</div>
                    <div style="font-size: 11px; font-weight: bold;">{{ $booking->flight ? $booking->flight->flight_number : '' }}</div>
                </td>
                <td style="width: 40%; text-align: right; border: none;">
                    <div class="city-title">{{ $booking->flight ? $booking->flight->arrival_city : '—' }}</div>
                    <div class="city-sub">Дата и время прилета:</div>
                    <strong>{{ $booking->flight ? $booking->flight->arrival_time->format('d.m.Y H:i') : '—' }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Данные пассажира -->
    <div class="section-title">ДАННЫЕ ПАССАЖИРА И ДОКУМЕНТА</div>
    <table>
        <tr>
            <th>ФИО Пассажира</th>
            <th>Серия и номер паспорта</th>
            <th>Электронная почта</th>
        </tr>
        <tr>
            <td><strong>{{ $booking->passenger_name }} {{ $booking->passenger_surname }}</strong></td>
            <td>{{ $booking->passport_series }} {{ $booking->passport_number }}</td>
            <td>{{ $booking->user ? $booking->user->email : '—' }}</td>
        </tr>
    </table>

    <!-- Детали билета и бронирования -->
    <div class="section-title">ИНФОРМАЦИЯ О БРОНИРОВАНИИ И ТАРИФЕ</div>
    <table>
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
            <td><strong>{{ number_format($booking->price, 2, '.', ' ') }} ₽</strong></td>
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
        <!-- Специальные услуги: перевозка животного -->
        <div class="section-title">СПЕЦИАЛЬНЫЕ УСЛУГИ: ПЕРЕВОЗКА ЖИВОТНОГО (SSR PETC / AVIH / EUPS)</div>
        <table>
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
        <div style="font-size: 10px; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; padding: 8px 10px; border-radius: 4px; margin-bottom: 15px;">
            <strong>Памятка ветеринарного контроля:</strong> Для посадки на рейс пассажир обязан предъявить оригинал ветеринарного паспорта с действующей вакцинацией против бешенства и пройти ветеринарный контроль в аэропорту вылета перед регистрацией.
        </div>
    @endif

    <div class="barcode">
        ||||| | |||| ||| |||||| || ||||| |||| |||||
    </div>
    <div style="text-align: center; font-size: 10px; color: #64748b; margin-top: 5px;">
        {{ $booking->booking_number }}-{{ $booking->flight ? $booking->flight->flight_number : '' }}-{{ $booking->seat ? $booking->seat->seat_number : '' }}
    </div>

    <div class="footer">
        Маршрутная квитанция сгенерирована автоматически системой бронирования Aether Airways.<br>
        Тариф зафиксирован на 24 часа. Сохраняйте данный документ до окончания поездки.
    </div>

</body>
</html>
