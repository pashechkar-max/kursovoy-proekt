@extends('layouts.app')

@section('title', 'Поиск и бронирование рейсов — Aether Airways')

@section('styles')
    <link rel="stylesheet" href="{{ asset('style/pages/flights.css') }}?v=1">
@endsection

@section('content')
<main class="page-wrapper">
    <section class="flights-hero">
        <div class="container">
            <h1 class="flights-hero-title">Поиск и бронирование рейсов</h1>
            <p class="flights-hero-subtitle">
                Фиксация стоимости на 24 часа, выбор места в самолете и накопление бонусных миль.
            </p>

            <form class="flights-search-bar" action="{{ route('flights.index') }}" method="GET">
                <div class="search-input-col">
                    <label class="form-label" for="fromCity">Откуда</label>
                    <input type="text" id="fromCity" name="from" class="form-control" value="{{ $departureCity ?? '' }}" placeholder="Город вылета" list="depList">
                    <datalist id="depList">
                        @foreach($departureCities as $dCity)
                            <option value="{{ $dCity }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="search-input-col">
                    <label class="form-label" for="toCity">Куда</label>
                    <input type="text" id="toCity" name="to" class="form-control" value="{{ $arrivalCity ?? '' }}" placeholder="Город прибытия" list="arrList">
                    <datalist id="arrList">
                        @foreach($arrivalCities as $aCity)
                            <option value="{{ $aCity }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="search-input-col">
                    <label class="form-label" for="flightDate">Дата вылета</label>
                    <input type="date" id="flightDate" name="date" class="form-control" value="{{ $flightDate ?? '' }}">
                </div>

                <input type="hidden" name="price_sort" id="hiddenPriceSort" value="{{ $priceSort ?? 'asc' }}">
                <input type="hidden" name="time_filter" id="hiddenTimeFilter" value="{{ $timeFilter ?? '' }}">
                <input type="hidden" name="with_pets" id="hiddenWithPets" value="{{ !empty($withPets) ? '1' : '0' }}">

                <div class="search-btn-col">
                    <button type="submit" class="btn btn-primary btn-search-submit">
                        Найти рейсы
                    </button>
                </div>
            </form>

            <div class="flights-layout">

                <aside class="flights-sidebar">
                    <div class="filter-section">
                        <h3 class="filter-title">Сортировка по стоимости</h3>
                        <div class="form-group">
                            <select id="priceSortSelect" class="form-control select-clickable" onchange="applyFilters()">
                                <option value="asc" {{ ($priceSort ?? 'asc') === 'asc' ? 'selected' : '' }}>Сначала дешёвые</option>
                                <option value="desc" {{ ($priceSort ?? '') === 'desc' ? 'selected' : '' }}>Сначала дорогие</option>
                            </select>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h3 class="filter-title">Перевозка животных</h3>
                        <label class="filter-pill-label">
                            <input type="checkbox" id="withPetsCheckbox" value="1" {{ !empty($withPets) ? 'checked' : '' }} onchange="applyFilters()">
                            <span>Только с местами для питомцев</span>
                        </label>
                    </div>

                    <div class="filter-section">
                        <h3 class="filter-title">Время вылета</h3>
                        <div class="filter-radio-group">
                            <label class="filter-pill-label">
                                <input type="radio" name="timeFilterRadio" value="" {{ empty($timeFilter) ? 'checked' : '' }} onchange="applyFilters()">
                                Любое время
                            </label>
                            <label class="filter-pill-label">
                                <input type="radio" name="timeFilterRadio" value="morning" {{ ($timeFilter ?? '') === 'morning' ? 'checked' : '' }} onchange="applyFilters()">
                                Утренние рейсы (06:00 – 12:00)
                            </label>
                            <label class="filter-pill-label">
                                <input type="radio" name="timeFilterRadio" value="day" {{ ($timeFilter ?? '') === 'day' ? 'checked' : '' }} onchange="applyFilters()">
                                Дневные рейсы (12:00 – 18:00)
                            </label>
                            <label class="filter-pill-label">
                                <input type="radio" name="timeFilterRadio" value="evening" {{ ($timeFilter ?? '') === 'evening' ? 'checked' : '' }} onchange="applyFilters()">
                                Вечерние рейсы (18:00 – 24:00)
                            </label>
                        </div>
                    </div>

                    <div class="sidebar-mascot-tip">
                        <div>
                            <div class="sidebar-mascot-tip-title">Гарантия Aether Airways</div>
                            <div class="sidebar-mascot-tip-sub">24-часовая фиксация</div>
                        </div>
                        <p class="sidebar-mascot-tip-desc">
                            Любой выбранный рейс фиксируется на 24 часа со статусом «активно». Оплатить или продлить можно в личном кабинете.
                        </p>
                    </div>
                </aside>

                <section class="flights-results-content">
                    <div class="flights-results-header">
                        <span class="flights-count">
                            Найдено рейсов: <strong>{{ $flights->count() }}</strong>
                        </span>
                        <span class="badge badge-active"><span class="badge-dot"></span>Доступны для бронирования</span>
                    </div>

                    <div class="flights-list" id="flightsResultsList">
                        @forelse($flights as $flight)
                            <div class="ticket-card flight-card" data-price="{{ $flight->price }}">
                                <div class="ticket-top-row">
                                    <div class="ticket-airline-block">
                                        <span class="ticket-airline-tag">РЕЙС {{ $flight->flight_number }}</span>
                                        <span class="ticket-airline-name">Aether Airways</span>
                                    </div>
                                    <div class="ticket-top-badges">
                                        <span class="badge badge-active flights-planned-badge">Запланирован</span>
                                        <div class="ticket-price">{{ number_format($flight->price, 0, '.', ' ') }} ₽</div>
                                    </div>
                                </div>

                                <div class="ticket-timeline-row">
                                    <div class="station-box">
                                        <span class="station-code">{{ $flight->departure_time->format('H:i') }}</span>
                                        <span class="station-sub">{{ $flight->departure_city }}, {{ $flight->departure_time->format('d.m.Y') }}</span>
                                    </div>
                                    <div class="timeline-center">
                                        <span class="timeline-duration">
                                            {{ $flight->departure_time->diff($flight->arrival_time)->format('%h ч %i мин') }}
                                        </span>
                                        <div class="timeline-line"></div>
                                        <span class="timeline-flight-num">{{ $flight->flight_number }}</span>
                                    </div>
                                    <div class="station-box station-box-arrival">
                                        <span class="station-code">{{ $flight->arrival_time->format('H:i') }}</span>
                                        <span class="station-sub">{{ $flight->arrival_city }}, {{ $flight->arrival_time->format('d.m.Y') }}</span>
                                    </div>
                                </div>

                                <div class="ticket-bottom-row">
                                    <div class="ticket-pet-status">
                                        @if($flight->availablePetCabinQuota() > 0)
                                            <span class="pet-status-allow">
                                                <span>Места для питомцев:</span> <strong>{{ $flight->availablePetCabinQuota() }} в салоне</strong>
                                            </span>
                                        @else
                                            <span class="pet-status-disallow">
                                                <span>В салоне занято</span> ({{ $flight->availablePetHoldQuota() }} в багаже)
                                            </span>
                                        @endif
                                    </div>

                                    <button type="button" class="btn btn-choose-flight" onclick="openSeatModal({{ $flight->id }})">
                                        Выбрать место и забронировать
                                        <svg class="ag-arrow-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state-box">
                                <h3 class="empty-state-title">Рейсы не найдены</h3>
                                <p class="empty-state-desc">Попробуйте изменить параметры поиска или выбрать другое время вылета.</p>
                                <a href="{{ route('flights.index') }}" class="btn btn-secondary">Сбросить фильтры</a>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </section>
</main>
@endsection

@section('modals')

<div class="modal-backdrop" id="seatModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" id="seatModalTitle">Выбор места в салоне самолёта</h3>
                <span class="text-secondary seat-modal-subtitle" id="seatModalSubtitle"></span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeSeatModal()">&times;</button>
        </div>

        <div class="modal-body seat-map-wrapper">
            <div class="seat-legend">
                <div class="legend-item"><div class="legend-sample legend-free"></div><span>Свободно</span></div>
                <div class="legend-item"><div class="legend-sample legend-comfort"></div><span>Комфорт (+2 000 ₽)</span></div>
                <div class="legend-item"><div class="legend-sample legend-business"></div><span>Бизнес (+6 000 ₽)</span></div>
                <div class="legend-item"><div class="legend-sample legend-selected"></div><span>Выбрано</span></div>
                <div class="legend-item"><div class="legend-sample legend-occupied"></div><span>Занято (активная бронь)</span></div>
            </div>

            <div class="fuselage-container" id="fuselageContainer">
                <div class="cockpit-shape">Носовая часть / Кабина экипажа</div>

                <div id="dynamicSeatMap">
                    <div class="seat-map-placeholder">
                        Загрузка схемы салона...
                    </div>
                </div>
            </div>

            <div class="seat-selected-summary">
                <div>
                    <span class="seat-summary-label">Выбранное место:</span>
                    <div class="seat-summary-val">
                        <span id="selectedSeatNumber">Не выбрано</span>
                        (<span id="selectedSeatClass">—</span>)
                    </div>
                </div>
                <div>
                    <span class="seat-summary-label">Итоговая стоимость:</span>
                    <div class="seat-summary-price" id="selectedSeatPrice">0 ₽</div>
                </div>
            </div>

            <div class="pet-booking-section">
                <div class="pet-header-row">
                    <label class="pet-toggle-label">
                        <input type="checkbox" name="has_pet" value="1" id="hasPetCheckbox" form="bookingForm" onchange="onPetToggle(this.checked)" class="pet-checkbox">
                        <span>Добавить перевозку питомца</span>
                    </label>
                    <div id="petQuotaDisplay" class="pet-quota-display">
                        <span>В салоне: <strong id="petCabinQuotaText" class="pet-quota-cabin">—</strong></span>
                        <span>В багаже: <strong id="petHoldQuotaText" class="pet-quota-hold">—</strong></span>
                    </div>
                </div>

                <div id="petOptionsContainer" class="pet-options-container">
                    <div class="pet-form-grid">
                        <div>
                            <label class="pet-field-label">Способ перевозки</label>
                            <select name="pet_transport_type" id="petTransportType" class="form-control pet-field-control" form="bookingForm" onchange="onPetTransportChange()">
                                <option value="cabin">В салоне под креслом (до 8 кг) — +2 500 ₽</option>
                                <option value="seat">На соседнем кресле (до 15 кг, у окна) — тариф</option>
                                <option value="hold">В багажном отсеке (до 50 кг) — +4 000 ₽</option>
                                <option value="guide">Собака-поводырь (бесплатно)</option>
                            </select>
                        </div>
                        <div>
                            <label class="pet-field-label">Вид животного</label>
                            <select name="pet_type" id="petType" class="form-control pet-field-control" form="bookingForm">
                                <option value="dog">Собака</option>
                                <option value="cat">Кошка</option>
                                <option value="bird">Птица</option>
                                <option value="other">Другое животное</option>
                            </select>
                        </div>
                        <div>
                            <label class="pet-field-label">Кличка питомца</label>
                            <input type="text" name="pet_name" id="petName" class="form-control pet-field-control" placeholder="Например: Арчи" form="bookingForm">
                        </div>
                        <div>
                            <label class="pet-field-label">Порода (необязательно)</label>
                            <input type="text" name="pet_breed" id="petBreed" class="form-control pet-field-control" placeholder="Например: Корги" form="bookingForm">
                        </div>
                        <div>
                            <label class="pet-field-label">Вес с переноской (кг)</label>
                            <input type="number" step="0.1" min="0.1" max="50" name="pet_weight" id="petWeight" value="5" class="form-control pet-field-control" form="bookingForm" oninput="onPetWeightChange()">
                        </div>
                        <div>
                            <label class="pet-field-label">№ ветпаспорта / чип</label>
                            <input type="text" name="pet_vet_passport_number" id="petVetPassport" class="form-control pet-field-control" placeholder="Серия, номер или чип" form="bookingForm">
                        </div>
                    </div>

                    <div id="petRuleAlert" class="pet-rule-alert">
                        <strong>Требования безопасности:</strong> Размещение с животными запрещено на аварийных рядах (ряды 7–8). При перевозке на отдельном кресле выбирайте место у окна (A, D или F).
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeSeatModal()">Отмена</button>

            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm" class="booking-form-inline">
                @csrf
                <input type="hidden" name="flight_id" id="bookingFlightId" value="">
                <input type="hidden" name="seat_id" id="bookingSeatId" value="">

                @auth
                    @if(auth()->user()->hasCompletePassport())
                        <button type="submit" class="btn btn-primary" id="confirmSeatBtn" disabled>
                            Забронировать на 24 часа
                        </button>
                    @else
                        <a href="{{ route('profile', ['tab' => 'tabPassport']) }}" class="btn btn-primary btn-warning-passport">
                            Сначала заполните паспортные данные
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        Войти для бронирования
                    </a>
                @endauth
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function applyFilters() {
        const priceSort = document.getElementById('priceSortSelect').value;
        const timeFilterRadio = document.querySelector('input[name="timeFilterRadio"]:checked');
        const timeFilter = timeFilterRadio ? timeFilterRadio.value : '';
        const withPets = document.getElementById('withPetsCheckbox')?.checked ? '1' : '0';

        document.getElementById('hiddenPriceSort').value = priceSort;
        document.getElementById('hiddenTimeFilter').value = timeFilter;
        const hiddenPets = document.getElementById('hiddenWithPets');
        if (hiddenPets) hiddenPets.value = withPets;

        document.querySelector('.flights-search-bar').submit();
    }

    let currentSelectedSeat = null;
    let currentFlightData = null;

    function openSeatModal(flightId) {
        const modal = document.getElementById('seatModal');
        const seatMap = document.getElementById('dynamicSeatMap');
        const title = document.getElementById('seatModalTitle');
        const subtitle = document.getElementById('seatModalSubtitle');
        const confirmBtn = document.getElementById('confirmSeatBtn');
        const bookingFlightId = document.getElementById('bookingFlightId');
        const bookingSeatId = document.getElementById('bookingSeatId');

        bookingFlightId.value = flightId;
        bookingSeatId.value = '';
        currentSelectedSeat = null;
        currentFlightData = null;
        if (confirmBtn) confirmBtn.disabled = true;

        const petCheck = document.getElementById('hasPetCheckbox');
        if (petCheck) {
            petCheck.checked = false;
            onPetToggle(false);
        }

        document.getElementById('selectedSeatNumber').textContent = 'Не выбрано';
        document.getElementById('selectedSeatClass').textContent = '—';
        document.getElementById('selectedSeatPrice').textContent = '0 ₽';

        openModal('seatModal');
        seatMap.innerHTML = '<div class="seat-map-loading">Загрузка кресел рейса...</div>';

        fetch("{{ url('/flights') }}/" + flightId + "/seats")
            .then(res => res.json())
            .then(data => {
                currentFlightData = data.flight;
                title.textContent = `Выбор места: Рейс ${data.flight.flight_number}`;
                subtitle.textContent = `${data.flight.departure_city} → ${data.flight.arrival_city} (${data.flight.departure_time})`;

                const cabinQuota = data.flight.available_pet_cabin ?? 4;
                const holdQuota = data.flight.available_pet_hold ?? 3;
                document.getElementById('petCabinQuotaText').textContent = cabinQuota > 0 ? `${cabinQuota} мест` : 'исчерпано';
                document.getElementById('petHoldQuotaText').textContent = holdQuota > 0 ? `${holdQuota} мест` : 'исчерпано';

                renderSeatMap(data.seats, data.flight.base_price);
            })
            .catch(err => {
                seatMap.innerHTML = '<div class="seat-map-error">Ошибка загрузки карты мест</div>';
            });
    }

    function closeSeatModal() {
        closeModal('seatModal');
    }

    function onPetToggle(checked) {
        const container = document.getElementById('petOptionsContainer');
        if (container) {
            container.style.display = checked ? 'block' : 'none';
        }

        const nameInput = document.getElementById('petName');
        const weightInput = document.getElementById('petWeight');
        const passportInput = document.getElementById('petVetPassport');

        if (nameInput) nameInput.required = checked;
        if (weightInput) weightInput.required = checked;
        if (passportInput) passportInput.required = checked;

        validateCurrentSeatForPet();
        updateTotalPrice();
    }

    function onPetTransportChange() {
        validatePetRules();
        validateCurrentSeatForPet();
        updateTotalPrice();
    }

    function onPetWeightChange() {
        validatePetRules();
    }

    function validatePetRules() {
        const transport = document.getElementById('petTransportType').value;
        const weight = parseFloat(document.getElementById('petWeight').value) || 0;
        const alertBox = document.getElementById('petRuleAlert');

        if (transport === 'cabin' && weight > 8) {
            alertBox.innerHTML = '<strong>Превышение веса:</strong> Для салона вес с переноской не более 8 кг. При весе до 15 кг выберите «На отдельном кресле», свыше 15 кг — «В багажном отсеке».';
            alertBox.style.background = 'rgba(239, 68, 68, 0.1)';
            alertBox.style.color = '#b91c1c';
        } else if (transport === 'seat' && weight > 15) {
            alertBox.innerHTML = '<strong>Превышение веса:</strong> Для отдельного кресла вес контейнера не более 15 кг. Животные свыше 15 кг перевозятся в багажном отсеке.';
            alertBox.style.background = 'rgba(239, 68, 68, 0.1)';
            alertBox.style.color = '#b91c1c';
        } else {
            alertBox.innerHTML = '<strong>Требования безопасности:</strong> Размещение с животными запрещено на аварийных рядах (ряды 7–8). При перевозке на отдельном кресле выбирайте место у окна (A, D или F).';
            alertBox.style.background = 'rgba(59, 130, 246, 0.1)';
            alertBox.style.color = '#1d4ed8';
        }
    }

    function validateCurrentSeatForPet() {
        const confirmBtn = document.getElementById('confirmSeatBtn');
        if (!currentSelectedSeat) {
            if (confirmBtn) confirmBtn.disabled = true;
            return;
        }

        const hasPet = document.getElementById('hasPetCheckbox')?.checked;
        if (!hasPet) {
            if (confirmBtn) confirmBtn.disabled = false;
            return;
        }

        const seatNum = currentSelectedSeat.seat_number;
        const row = parseInt(seatNum.replace(/\D/g, ''), 10);
        const letter = seatNum.replace(/[^a-zA-Z]/g, '').toUpperCase();
        const transport = document.getElementById('petTransportType').value;

        if (row === 7 || row === 8) {
            alert('По правилам авиационной безопасности пассажирам с животными запрещено бронировать места на аварийных рядах (7-8). Пожалуйста, выберите другое место.');
            if (confirmBtn) confirmBtn.disabled = true;
            return;
        }

        if (transport === 'seat' && !['A', 'D', 'F'].includes(letter)) {
            alert('При оформлении отдельного кресла для питомца разрешены только места у окна (A, D или F). Пожалуйста, выберите подходящее место.');
            if (confirmBtn) confirmBtn.disabled = true;
            return;
        }

        if (confirmBtn) confirmBtn.disabled = false;
    }

    function updateTotalPrice() {
        if (!currentSelectedSeat) {
            document.getElementById('selectedSeatPrice').textContent = '0 ₽';
            return;
        }

        let price = Number(currentSelectedSeat.price);
        const hasPet = document.getElementById('hasPetCheckbox')?.checked;

        if (hasPet) {
            const transport = document.getElementById('petTransportType').value;
            if (transport === 'cabin') {
                price += 2500;
            } else if (transport === 'hold') {
                price += 4000;
            } else if (transport === 'seat' && currentFlightData) {
                price += Number(currentFlightData.base_price);
            }
        }

        document.getElementById('selectedSeatPrice').textContent = price.toLocaleString('ru-RU') + ' ₽';
    }

    function renderSeatMap(seats, basePrice) {
        const container = document.getElementById('dynamicSeatMap');
        container.innerHTML = '';

        const business = seats.filter(s => s.seat_class === 'business');
        const comfort = seats.filter(s => s.seat_class === 'comfort');
        const economy = seats.filter(s => s.seat_class === 'economy');

        function buildSection(title, seatList, perRow) {
            if (seatList.length === 0) return;
            const secTitle = document.createElement('div');
            secTitle.className = 'cabin-section-title';
            secTitle.textContent = title;
            container.appendChild(secTitle);

            const rows = {};
            seatList.forEach(s => {
                const rowNum = s.seat_number.replace(/\D/g, '');
                if (!rows[rowNum]) rows[rowNum] = [];
                rows[rowNum].push(s);
            });

            Object.keys(rows).forEach(rNum => {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'seat-row';

                const rowLabel = document.createElement('span');
                rowLabel.className = 'row-num seat-row-num';
                rowLabel.textContent = rNum;
                rowDiv.appendChild(rowLabel);

                const half = Math.ceil(rows[rNum].length / 2);
                rows[rNum].forEach((s, idx) => {
                    if (idx === half) {
                        const aisle = document.createElement('div');
                        aisle.className = 'aisle-gap seat-aisle';
                        rowDiv.appendChild(aisle);
                    }

                    const seatDiv = document.createElement('button');
                    seatDiv.type = 'button';
                    seatDiv.className = `seat-item plane-seat seat-${s.seat_class}`;
                    seatDiv.setAttribute('data-seat-id', s.id);

                    if (!s.is_available) {
                        seatDiv.classList.add('occupied', 'seat-occupied');
                        seatDiv.disabled = true;
                        seatDiv.title = `Место ${s.seat_number} занято активной бронью`;
                    } else {
                        const classNameRu = s.seat_class === 'business' ? 'Бизнес' : (s.seat_class === 'comfort' ? 'Комфорт' : 'Эконом');
                        const isExitRow = (rNum === '7' || rNum === '8');
                        const exitNotice = isExitRow ? ' [Ряд у аварийного выхода: без животных]' : '';
                        seatDiv.title = `Место ${s.seat_number} (${classNameRu}, ${Number(s.price).toLocaleString('ru-RU')} ₽)${exitNotice}`;
                        seatDiv.onclick = () => selectSeat(s, seatDiv);
                    }
                    seatDiv.textContent = s.seat_number;
                    rowDiv.appendChild(seatDiv);
                });

                container.appendChild(rowDiv);
            });
        }

        buildSection('Бизнес-класс (2+2)', business, 4);
        buildSection('Комфорт-класс (2+2)', comfort, 4);
        buildSection('Эконом-класс (3+3)', economy, 6);
    }

    function selectSeat(seat, elem) {
        const hasPet = document.getElementById('hasPetCheckbox')?.checked;
        const seatRow = parseInt(seat.seat_number.replace(/\D/g, ''), 10);
        const seatLetter = seat.seat_number.replace(/[^a-zA-Z]/g, '').toUpperCase();
        const transport = document.getElementById('petTransportType').value;

        if (hasPet) {
            if (seatRow === 7 || seatRow === 8) {
                alert('По правилам авиационной безопасности пассажирам с животными запрещено бронировать места на аварийных рядах (7-8). Пожалуйста, выберите другое место.');
                return;
            }
            if (transport === 'seat' && !['A', 'D', 'F'].includes(seatLetter)) {
                alert('При оформлении отдельного кресла для питомца разрешены только места у окна (A, D или F). Пожалуйста, выберите подходящее место.');
                return;
            }
        }

        document.querySelectorAll('#dynamicSeatMap .seat-item, #dynamicSeatMap .plane-seat').forEach(el => {
            el.classList.remove('selected', 'seat-selected');
        });
        elem.classList.add('selected', 'seat-selected');

        currentSelectedSeat = seat;
        document.getElementById('bookingSeatId').value = seat.id;

        const classMap = {
            'business': 'Бизнес',
            'comfort': 'Комфорт',
            'economy': 'Эконом'
        };

        document.getElementById('selectedSeatNumber').textContent = seat.seat_number;
        document.getElementById('selectedSeatClass').textContent = classMap[seat.seat_class] || seat.seat_class;

        updateTotalPrice();

        const confirmBtn = document.getElementById('confirmSeatBtn');
        if (confirmBtn) confirmBtn.disabled = false;
    }
</script>
@endsection
