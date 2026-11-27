<footer class="site-footer">
    <div class="container">
        <div class="footer-top-row">
            <div class="footer-headline-col">
                <h3 class="footer-experience-title">Новое измерение полётов</h3>
                <p class="footer-experience-desc">
                    Aether Airways — флагманская авиакомпания с интеллектуальным бронированием, фиксацией тарифа на 24 часа без сборов и бережным отношением к каждому пассажиру.
                </p>
            </div>

            <div class="footer-links-grid">
                <div>
                    <div class="footer-col-title">Продукт</div>
                    <ul class="footer-links-list">
                        <li><a href="{{ route('flights.index') }}">Поиск рейсов</a></li>
                        <li><a href="{{ auth()->check() ? route('profile') : route('login') }}">Мои бронирования</a></li>
                        <li><a href="{{ route('home') }}#special-offers">Спецпредложения</a></li>
                        <li><a href="{{ route('animals') }}">Перевозка животных</a></li>
                    </ul>
                </div>

                <div>
                    <div class="footer-col-title">Пассажирам</div>
                    <ul class="footer-links-list">
                        <li><a href="{{ auth()->check() ? route('profile', ['tab' => 'tabPassport']) : route('login') }}">Паспортные данные</a></li>
                        <li><a href="{{ auth()->check() ? route('profile', ['tab' => 'tabMiles']) : route('login') }}">Бонусные мили</a></li>
                        @guest
                            <li><a href="{{ route('login') }}">Вход в систему</a></li>
                            <li><a href="{{ route('register') }}">Регистрация (+1 000 миль)</a></li>
                        @else
                            <li><a href="{{ route('profile') }}">Личный кабинет</a></li>
                            @if(auth()->user()->isAdmin())
                                <li><a href="{{ route('admin.index') }}" style="color: #6366f1;">Панель администратора</a></li>
                            @endif
                        @endguest
                    </ul>
                </div>

                <div>
                    <div class="footer-col-title">Контакты</div>
                    <ul class="footer-links-list">
                        <li><a href="tel:88005553535">8 (800) 555-35-35</a></li>
                        <li><a href="mailto:support@aether-airways.com">support@aether-airways.com</a></li>
                        <li><span>Круглосуточная поддержка</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom-row">
            <div class="footer-copy">
                &copy; {{ date('Y') }} Aether Airways. Все права защищены.
            </div>
        </div>
    </div>
</footer>
