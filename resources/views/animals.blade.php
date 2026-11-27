@extends('layouts.app')

@section('title', 'Перевозка животных — Aether Airways')

@section('styles')
    <link rel="stylesheet" href="{{ asset('style/pages/animals.css') }}?v=1">
@endsection

@section('content')
<main class="page-wrapper animals-page-wrapper">
    <div class="container">

        <section class="animals-hero-card">
            <div class="animals-hero-left">
                <span class="animals-hero-tag">Бережная забота в небе</span>
                <h1 class="animals-hero-title">Путешествия с домашними любимцами</h1>
                <p class="animals-hero-desc">
                    Возьмите любимого питомца с собой на борт Aether Airways. Размещение под креслом в салоне, на отдельном пассажирском месте или в отапливаемом багажном отсеке с постоянным климатическим контролем.
                </p>
                <a href="{{ route('flights.index') }}" class="btn btn-primary btn-md">
                    Забронировать рейс с питомцем
                    <svg class="ag-arrow-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>

            <div class="animals-hero-media">
                <img src="{{ asset('image/air-dog.jpg') }}" alt="Aether Airways Smart Pet Travel Carrier" onerror="this.src='{{ asset('image/air-animal.jpg') }}'">
            </div>
        </section>

        <section class="animals-grid">

            <div class="animal-option-card">
                <img src="{{ asset('image/air-birds.jpg') }}" alt="Маленькие собаки, кошки и птицы в салоне" class="animal-card-img" onerror="this.src='{{ asset('image/air-animal.jpg') }}'">
                <div class="animal-content">
                    <span class="animal-badge">В салоне под креслом</span>
                    <h2 class="animal-title">Маленькие собаки, кошки и птицы</h2>
                    <ul class="animal-specs-list">
                        <li>Вес животного вместе с переноской: до 8 кг</li>
                        <li>Габариты мягкой сумки-переноски: не более 44 × 30 × 26 см</li>
                        <li>Питомцы размещаются под впереди стоящим креслом на протяжении всего полёта</li>
                    </ul>
                    <div class="animal-footer-row">
                        <span class="animal-price-tag">от 2 500 ₽</span>
                        <a href="{{ route('flights.index') }}" class="btn btn-primary btn-sm">Выбрать рейс</a>
                    </div>
                </div>
            </div>

            <div class="animal-option-card">
                <img src="{{ asset('image/air-corgi.jpg') }}" alt="Питомец на отдельном пассажирском месте" class="animal-card-img animal-card-corgi" onerror="this.src='{{ asset('image/air-animal.jpg') }}'">
                <div class="animal-content">
                    <span class="animal-badge">На соседнем кресле</span>
                    <h2 class="animal-title">Питомец на отдельном пассажирском месте</h2>
                    <ul class="animal-specs-list">
                        <li>Вес животного вместе с жёстким контейнером: до 15 кг</li>
                        <li>Контейнер надёжно фиксируется специальными ремнями безопасности на кресле у иллюминатора</li>
                        <li>Питомец находится в поле зрения хозяина в комфорте всю дорогу</li>
                    </ul>
                    <div class="animal-footer-row">
                        <span class="animal-price-tag">по тарифу билета</span>
                        <a href="{{ route('flights.index') }}" class="btn btn-primary btn-sm">Выбрать рейс</a>
                    </div>
                </div>
            </div>

            <div class="animal-option-card">
                <img src="{{ asset('image/air-labrador.jpg') }}" alt="В отапливаемом багажном отсеке" class="animal-card-img animal-card-labrador" onerror="this.src='{{ asset('image/air-animal.jpg') }}'">
                <div class="animal-content">
                    <span class="animal-badge">Багажный отсек</span>
                    <h2 class="animal-title">В отапливаемом багажном отсеке</h2>
                    <ul class="animal-specs-list">
                        <li>Вес животного с клеткой: до 50 кг</li>
                        <li>Климат-контроль +18...+22 °C поддерживается автоматикой лайнера</li>
                        <li>Приоритетная бережная погрузка и выдача в аэропорту прибытия</li>
                    </ul>
                    <div class="animal-footer-row">
                        <span class="animal-price-tag">от 4 000 ₽</span>
                        <a href="{{ route('flights.index') }}" class="btn btn-primary btn-sm">Выбрать рейс</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection
