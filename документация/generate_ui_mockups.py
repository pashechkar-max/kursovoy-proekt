import os
from PIL import Image, ImageDraw, ImageFont

os.makedirs('docs_assets', exist_ok=True)
font_path = 'C:/Windows/Fonts/arial.ttf'
font_bold_path = 'C:/Windows/Fonts/arialbd.ttf'

def get_font(size, bold=False):
    p = font_bold_path if bold and os.path.exists(font_bold_path) else font_path
    return ImageFont.truetype(p, size)

def draw_multiline(draw, xy, text, font, fill, line_spacing=4, align='left'):
    lines = text.split('\n')
    cx, start_y = xy
    cur_y = start_y
    for l in lines:
        bbox = font.getbbox(l)
        lw = bbox[2] - bbox[0]
        lh = bbox[3] - bbox[1]
        if align == 'center':
            lx = cx - lw // 2
        elif align == 'right':
            lx = cx - lw
        else:
            lx = cx
        draw.text((lx, cur_y), l, fill=fill, font=font)
        cur_y += lh + line_spacing

def draw_browser_chrome(draw, w, title="Aether Airways — localhost/my_site"):
    draw.rounded_rectangle([0, 0, w, 40], radius=8, fill='#E2E8F0')
    draw.ellipse([14, 14, 26, 26], fill='#EF4444')
    draw.ellipse([34, 14, 46, 26], fill='#F59E0B')
    draw.ellipse([54, 14, 66, 26], fill='#10B981')
    draw.rounded_rectangle([100, 8, w - 100, 32], radius=6, fill='#FFFFFF')
    draw.text((120, 11), f"🔒 http://{title}", fill='#64748B', font=get_font(12))

# 1. Home Page UI Mockup
def create_ui_home():
    w, h = 1200, 750
    img = Image.new('RGB', (w, h), color='#FFFFFF')
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, w, "localhost/my_site/public/")

    # Header
    draw.rectangle([0, 40, w, 100], fill='#FFFFFF', outline='#E2E8F0')
    draw.text((40, 58), "AETHER AIRWAYS", fill='#0F172A', font=get_font(18, bold=True))
    draw.text((260, 62), "Рейсы     Мои бронирования     Спецпредложения     Питомцы", fill='#475569', font=get_font(13, bold=True))
    draw.rounded_rectangle([w - 300, 52, w - 180, 88], radius=8, fill='#F1F5F9')
    draw.text((w - 240, 62), "Войти", fill='#0F172A', font=get_font(13, bold=True), anchor='mm')
    draw.rounded_rectangle([w - 160, 52, w - 40, 88], radius=8, fill='#0F172A')
    draw.text((w - 100, 62), "Регистрация", fill='#FFFFFF', font=get_font(13, bold=True), anchor='mm')

    # Hero
    draw.rectangle([0, 100, w, 450], fill='#F8FAFC')
    draw.rounded_rectangle([w // 2 - 180, 130, w // 2 + 180, 160], radius=15, fill='#EFF6FF', outline='#BFDBFE')
    draw.text((w // 2, 145), "✨ Фиксация стоимости на 24 часа + 1000 Welcome Miles", fill='#1D4ED8', font=get_font(12, bold=True), anchor='mm')

    draw.text((w // 2, 200), "Будущее авиаперелётов в ваших руках", fill='#0F172A', font=get_font(30, bold=True), anchor='mm')
    draw.text((w // 2, 235), "Интеллектуальный поиск, 24-часовой холд тарифа без сборов и программа премиальных миль", fill='#64748B', font=get_font(14), anchor='mm')

    # Search Bar
    sb_y = 280
    draw.rounded_rectangle([100, sb_y, w - 100, sb_y + 110], radius=16, fill='#FFFFFF', outline='#CBD5E1', width=2)
    # Inputs
    cols = [
        ("ОТКУДА", "Новосибирск (OVB)", 130),
        ("КУДА", "Сочи (AER)", 340),
        ("ДАТА ВЫЛЕТА", "30.11.2026", 550),
        ("КЛАСС САЛОНА", "Эконом-класс", 730),
    ]
    for lbl, val, ix in cols:
        draw.text((ix, sb_y + 25), lbl, fill='#94A3B8', font=get_font(10, bold=True))
        draw.text((ix, sb_y + 48), val, fill='#0F172A', font=get_font(15, bold=True))
        draw.line([ix + 170, sb_y + 20, ix + 170, sb_y + 90], fill='#E2E8F0', width=1)

    draw.rounded_rectangle([w - 270, sb_y + 25, w - 120, sb_y + 85], radius=10, fill='#0F172A')
    draw.text((w - 195, sb_y + 55), "Найти рейсы", fill='#FFFFFF', font=get_font(14, bold=True), anchor='mm')

    # TOP-3 Special Offers Section
    draw.text((100, 480), "ТОП-3 направления с максимальной скидкой", fill='#0F172A', font=get_font(20, bold=True))
    draw.text((100, 510), "Автоматическая выборка предложений с наибольшей выгодой", fill='#64748B', font=get_font(13))

    offers = [
        ("Новосибирск → Сочи", "-25% СКИДКА", "9 375 ₽", "12 500 ₽", 100),
        ("Москва → Санкт-Петербург", "-20% СКИДКА", "4 160 ₽", "5 200 ₽", 450),
        ("Казань → Екатеринбург", "-15% СКИДКА", "5 865 ₽", "6 900 ₽", 800),
    ]
    for route, disc, p_new, p_old, ox in offers:
        draw.rounded_rectangle([ox, 545, ox + 300, 715], radius=14, fill='#FFFFFF', outline='#E2E8F0')
        draw.rounded_rectangle([ox + 16, 560, ox + 140, 584], radius=6, fill='#FEF2F2')
        draw.text((ox + 24, 566), disc, fill='#DC2626', font=get_font(11, bold=True))
        draw.text((ox + 16, 605), route, fill='#0F172A', font=get_font(14, bold=True))
        draw.text((ox + 16, 645), p_new, fill='#0F172A', font=get_font(18, bold=True))
        draw.text((ox + 130, 648), p_old, fill='#94A3B8', font=get_font(13))
        draw.line([ox + 130, 655, ox + 200, 655], fill='#94A3B8', width=1)
        draw.rounded_rectangle([ox + 16, 675, ox + 284, 705], radius=6, fill='#0F172A')
        draw.text((ox + 150, 690), "Забронировать", fill='#FFFFFF', font=get_font(11, bold=True), anchor='mm')

    img.save('docs_assets/ui_home.png', dpi=(300, 300))
    print("UI Home page generated.")

# 2. Seat Selection Modal UI Mockup
def create_ui_seats():
    w, h = 1200, 800
    img = Image.new('RGB', (w, h), color='#FFFFFF')
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, w, "localhost/my_site/public/flights")

    # Darkened Backdrop
    draw.rectangle([0, 40, w, h], fill='#0F172A')

    # Modal Dialog
    mx1, my1, mx2, my2 = 180, 70, 1020, 760
    draw.rounded_rectangle([mx1, my1, mx2, my2], radius=18, fill='#FFFFFF')

    # Modal Header
    draw.text((mx1 + 30, my1 + 25), "Выбор места: Рейс KS-501", fill='#0F172A', font=get_font(20, bold=True))
    draw.text((mx1 + 30, my1 + 52), "Новосибирск (OVB) → Сочи (AER) • Лайнер Airbus A320 • 82 кресла", fill='#64748B', font=get_font(13))
    draw.text((mx2 - 40, my1 + 25), "✕", fill='#94A3B8', font=get_font(20, bold=True))
    draw.line([mx1, my1 + 80, mx2, my1 + 80], fill='#E2E8F0', width=1)

    # Legend
    leg_y = my1 + 95
    legends = [
        ("Свободно", "#FFFFFF", "#CBD5E1"),
        ("Комфорт (+2 000 ₽)", "#EFF6FF", "#3B82F6"),
        ("Бизнес (+6 000 ₽)", "#FEF3C7", "#D97706"),
        ("Выбрано вами", "#0F172A", "#0F172A"),
        ("Занято", "#E2E8F0", "#94A3B8"),
    ]
    lx = mx1 + 30
    for l_txt, l_fill, l_border in legends:
        draw.rounded_rectangle([lx, leg_y, lx + 20, leg_y + 20], radius=4, fill=l_fill, outline=l_border)
        draw.text((lx + 28, leg_y + 3), l_txt, fill='#475569', font=get_font(11, bold=True))
        lx += 150

    # Fuselage Frame
    fx1, fy1, fx2, fy2 = mx1 + 100, my1 + 135, mx2 - 100, my2 - 95
    draw.rounded_rectangle([fx1, fy1, fx2, fy2], radius=24, fill='#F8FAFC', outline='#CBD5E1', width=2)
    draw.text(((fx1 + fx2) // 2, fy1 + 18), "Кабина пилотов / Носовая часть", fill='#94A3B8', font=get_font(12, bold=True), anchor='mm')

    # Draw seat rows
    # Business rows 1-2 (2+2)
    draw.text((fx1 + 20, fy1 + 45), "Бизнес-класс (2+2)", fill='#D97706', font=get_font(12, bold=True))
    sy = fy1 + 70
    for r in ["1", "2"]:
        draw.text((fx1 + 35, sy + 8), r, fill='#64748B', font=get_font(12, bold=True))
        # seats A B
        draw.rounded_rectangle([fx1 + 80, sy, fx1 + 125, sy + 32], radius=6, fill='#FEF3C7', outline='#D97706')
        draw.text((fx1 + 102, sy + 16), f"{r}A", fill='#92400E', font=get_font(11, bold=True), anchor='mm')
        draw.rounded_rectangle([fx1 + 140, sy, fx1 + 185, sy + 32], radius=6, fill='#E2E8F0', outline='#CBD5E1')
        draw.text((fx1 + 162, sy + 16), f"{r}B", fill='#94A3B8', font=get_font(11, bold=True), anchor='mm')
        # Aisle
        draw.line([fx1 + 270, sy, fx1 + 270, sy + 32], fill='#E2E8F0', width=1)
        # seats C D
        draw.rounded_rectangle([fx1 + 355, sy, fx1 + 400, sy + 32], radius=6, fill='#FEF3C7', outline='#D97706')
        draw.text((fx1 + 377, sy + 16), f"{r}C", fill='#92400E', font=get_font(11, bold=True), anchor='mm')
        draw.rounded_rectangle([fx1 + 415, sy, fx1 + 460, sy + 32], radius=6, fill='#FEF3C7', outline='#D97706')
        draw.text((fx1 + 437, sy + 16), f"{r}D", fill='#92400E', font=get_font(11, bold=True), anchor='mm')
        sy += 42

    # Comfort rows 3-4 (2+2)
    draw.text((fx1 + 20, sy + 10), "Комфорт-класс (2+2)", fill='#2563EB', font=get_font(12, bold=True))
    sy += 35
    for r in ["3", "4"]:
        draw.text((fx1 + 35, sy + 8), r, fill='#64748B', font=get_font(12, bold=True))
        draw.rounded_rectangle([fx1 + 80, sy, fx1 + 125, sy + 32], radius=6, fill='#EFF6FF', outline='#3B82F6')
        draw.text((fx1 + 102, sy + 16), f"{r}A", fill='#1D4ED8', font=get_font(11, bold=True), anchor='mm')
        draw.rounded_rectangle([fx1 + 140, sy, fx1 + 185, sy + 32], radius=6, fill='#EFF6FF', outline='#3B82F6')
        draw.text((fx1 + 162, sy + 16), f"{r}B", fill='#1D4ED8', font=get_font(11, bold=True), anchor='mm')
        # seats C D
        draw.rounded_rectangle([fx1 + 355, sy, fx1 + 400, sy + 32], radius=6, fill='#EFF6FF', outline='#3B82F6')
        draw.text((fx1 + 377, sy + 16), f"{r}C", fill='#1D4ED8', font=get_font(11, bold=True), anchor='mm')
        draw.rounded_rectangle([fx1 + 415, sy, fx1 + 460, sy + 32], radius=6, fill='#EFF6FF', outline='#3B82F6')
        draw.text((fx1 + 437, sy + 16), f"{r}D", fill='#1D4ED8', font=get_font(11, bold=True), anchor='mm')
        sy += 42

    # Economy rows 5-7 (3+3)
    draw.text((fx1 + 20, sy + 10), "Эконом-класс (3+3)", fill='#475569', font=get_font(12, bold=True))
    sy += 35
    for r in ["5", "6", "7"]:
        draw.text((fx1 + 35, sy + 8), r, fill='#64748B', font=get_font(12, bold=True))
        # selected 5B
        fill_b = '#0F172A' if r == "5" else '#FFFFFF'
        col_b = '#FFFFFF' if r == "5" else '#0F172A'
        draw.rounded_rectangle([fx1 + 80, sy, fx1 + 120, sy + 32], radius=6, fill='#FFFFFF', outline='#CBD5E1')
        draw.text((fx1 + 100, sy + 16), f"{r}A", fill='#0F172A', font=get_font(11, bold=True), anchor='mm')
        draw.rounded_rectangle([fx1 + 130, sy, fx1 + 170, sy + 32], radius=6, fill=fill_b, outline='#0F172A')
        draw.text((fx1 + 150, sy + 16), f"{r}B", fill=col_b, font=get_font(11, bold=True), anchor='mm')
        draw.rounded_rectangle([fx1 + 180, sy, fx1 + 220, sy + 32], radius=6, fill='#FFFFFF', outline='#CBD5E1')
        draw.text((fx1 + 200, sy + 16), f"{r}C", fill='#0F172A', font=get_font(11, bold=True), anchor='mm')

        draw.rounded_rectangle([fx1 + 320, sy, fx1 + 360, sy + 32], radius=6, fill='#FFFFFF', outline='#CBD5E1')
        draw.text((fx1 + 340, sy + 16), f"{r}D", fill='#0F172A', font=get_font(11, bold=True), anchor='mm')
        draw.rounded_rectangle([fx1 + 370, sy, fx1 + 410, sy + 32], radius=6, fill='#E2E8F0', outline='#CBD5E1')
        draw.text((fx1 + 390, sy + 16), f"{r}E", fill='#94A3B8', font=get_font(11, bold=True), anchor='mm')
        draw.rounded_rectangle([fx1 + 420, sy, fx1 + 460, sy + 32], radius=6, fill='#FFFFFF', outline='#CBD5E1')
        draw.text((fx1 + 440, sy + 16), f"{r}F", fill='#0F172A', font=get_font(11, bold=True), anchor='mm')
        sy += 40

    # Summary bar at bottom
    draw.line([mx1, my2 - 80, mx2, my2 - 80], fill='#E2E8F0', width=1)
    draw.text((mx1 + 30, my2 - 62), "Выбранное место: 5B (Эконом-класс)", fill='#0F172A', font=get_font(14, bold=True))
    draw.text((mx1 + 30, my2 - 38), "Итоговая стоимость: 12 500 ₽ (с фиксацией на 24 часа)", fill='#059669', font=get_font(13, bold=True))

    draw.rounded_rectangle([mx2 - 270, my2 - 65, mx2 - 30, my2 - 15], radius=10, fill='#0F172A')
    draw.text((mx2 - 150, my2 - 40), "Забронировать на 24ч", fill='#FFFFFF', font=get_font(13, bold=True), anchor='mm')

    img.save('docs_assets/ui_seats.png', dpi=(300, 300))
    print("UI Seats modal generated.")

# 3. Profile Page UI Mockup
def create_ui_profile():
    w, h = 1200, 750
    img = Image.new('RGB', (w, h), color='#FFFFFF')
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, w, "localhost/my_site/public/profile")

    # Header
    draw.rectangle([0, 40, w, 95], fill='#FFFFFF', outline='#E2E8F0')
    draw.text((40, 56), "AETHER AIRWAYS", fill='#0F172A', font=get_font(18, bold=True))
    draw.text((260, 60), "Рейсы     Мои бронирования     Спецпредложения     Питомцы", fill='#475569', font=get_font(13, bold=True))
    draw.ellipse([w - 65, 50, w - 25, 90], fill='#4F46E5')
    draw.text((w - 45, 70), "А", fill='#FFFFFF', font=get_font(16, bold=True), anchor='mm')

    # Profile Hero Banner
    draw.rounded_rectangle([40, 115, w - 40, 245], radius=20, fill='#F8FAFC', outline='#CBD5E1')
    draw.ellipse([70, 135, 150, 215], fill='#F43F5E')
    draw.text((110, 175), "А", fill='#FFFFFF', font=get_font(32, bold=True), anchor='mm')

    draw.text((180, 140), "Александра Смирнова", fill='#0F172A', font=get_font(22, bold=True))
    draw.text((180, 170), "hello.alex@gmail.com • Статус: Пассажир Priority", fill='#64748B', font=get_font(13))
    draw.text((180, 195), "✓ Паспортные данные заполнены и подтверждены", fill='#10B981', font=get_font(13, bold=True))

    # Stat blocks
    stats = [
        ("3 500", "БОНУСНЫХ МИЛЬ", "#4F46E5", w - 400),
        ("3", "ВСЕГО БРОНЕЙ", "#0F172A", w - 260),
        ("1", "АКТИВНЫХ", "#10B981", w - 120),
    ]
    for val, lbl, col, sx in stats:
        draw.rounded_rectangle([sx - 50, 135, sx + 50, 225], radius=12, fill='#FFFFFF', outline='#E2E8F0')
        draw.text((sx, 165), val, fill=col, font=get_font(26, bold=True), anchor='mm')
        draw.text((sx, 200), lbl, fill='#64748B', font=get_font(9, bold=True), anchor='mm')

    # Tabs
    draw.text((60, 275), "Вкладка: Предстоящие бронирования (1)    |    Паспортные данные    |    Бонусные мили (3 500)", fill='#4F46E5', font=get_font(14, bold=True))
    draw.line([40, 305, w - 40, 305], fill='#E2E8F0', width=1)

    # Active Booking Card
    draw.rounded_rectangle([40, 330, w - 40, 520], radius=16, fill='#FFFFFF', outline='#CBD5E1', width=2)
    # Card Header
    draw.rounded_rectangle([60, 350, 170, 380], radius=8, fill='#EFF6FF')
    draw.text((115, 365), "BK-89421", fill='#1D4ED8', font=get_font(13, bold=True), anchor='mm')

    draw.rounded_rectangle([190, 350, 320, 380], radius=8, fill='#ECFDF5')
    draw.text((255, 365), "● СТАТУС: active", fill='#059669', font=get_font(11, bold=True), anchor='mm')

    draw.text((w - 280, 365), "Срок брони: 23ч 54м", fill='#DC2626', font=get_font(14, bold=True))

    # Route info
    draw.text((60, 410), "Новосибирск (OVB)  →  Сочи (AER)", fill='#0F172A', font=get_font(18, bold=True))
    draw.text((60, 440), "Рейс KS-501 • Место: 5B (Эконом-класс) • Вылет: 30.11.2026 07:30", fill='#64748B', font=get_font(13))
    draw.text((w - 240, 420), "12 500 ₽", fill='#0F172A', font=get_font(24, bold=True))

    # Buttons
    draw.rounded_rectangle([60, 470, 250, 505], radius=8, fill='#EFF6FF', outline='#BFDBFE')
    draw.text((155, 487), "Повысить класс (-2000 миль)", fill='#1D4ED8', font=get_font(11, bold=True), anchor='mm')

    draw.rounded_rectangle([270, 470, 460, 505], radius=8, fill='#F0FDF4', outline='#BBF7D0')
    draw.text((365, 487), "Продлить на 24ч (-500 миль)", fill='#15803D', font=get_font(11, bold=True), anchor='mm')

    draw.rounded_rectangle([480, 470, 620, 505], radius=8, fill='#F1F5F9')
    draw.text((550, 487), "📄 Скачать PDF", fill='#0F172A', font=get_font(11, bold=True), anchor='mm')

    draw.rounded_rectangle([640, 470, 780, 505], radius=8, fill='#FEF2F2', outline='#FECDD3')
    draw.text((710, 487), "Отменить бронь", fill='#B91C1C', font=get_font(11, bold=True), anchor='mm')

    # Archived Bookings section header
    draw.text((40, 560), "История прошедших и завершённых бронирований", fill='#0F172A', font=get_font(18, bold=True))
    draw.rounded_rectangle([40, 595, w - 40, 675], radius=12, fill='#F8FAFC', outline='#E2E8F0')
    draw.text((60, 625), "BK-71203 • Москва (SVO) → Санкт-Петербург (LED) • Место 2A • 18 500 ₽", fill='#475569', font=get_font(13))
    draw.rounded_rectangle([w - 180, 615, w - 60, 645], radius=6, fill='#EFF6FF')
    draw.text((w - 120, 630), "completed", fill='#1D4ED8', font=get_font(11, bold=True), anchor='mm')

    img.save('docs_assets/ui_profile.png', dpi=(300, 300))
    print("UI Profile page generated.")

# 4. Admin Panel UI Mockup
def create_ui_admin():
    w, h = 1200, 750
    img = Image.new('RGB', (w, h), color='#FFFFFF')
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, w, "localhost/my_site/public/admin")

    # Command Center Hero
    draw.rounded_rectangle([40, 60, w - 40, 200], radius=18, fill='#0F172A')
    draw.rounded_rectangle([65, 80, 300, 106], radius=6, fill='#312E81')
    draw.text((75, 87), "⚡ AETHER COMMAND CENTER • ADMIN", fill='#A5B4FC', font=get_font(11, bold=True))

    draw.text((65, 125), "Центр управления полётами и бонусами", fill='#FFFFFF', font=get_font(24, bold=True))
    draw.text((65, 160), "Контроль расписания, фиксация статусов рейсов и начисление миль пассажирам (Функция 12)", fill='#94A3B8', font=get_font(13))

    # KPI stats
    kpis = [
        ("Пользователей", "5", "#38BDF8", w - 380),
        ("Всего рейсов", "8", "#818CF8", w - 260),
        ("Активных броней", "2", "#34D399", w - 140),
    ]
    for lbl, val, col, kx in kpis:
        draw.rounded_rectangle([kx - 45, 80, kx + 45, 175], radius=10, fill='#1E293B', outline='#334155')
        draw.text((kx, 115), val, fill=col, font=get_font(26, bold=True), anchor='mm')
        draw.text((kx, 150), lbl, fill='#94A3B8', font=get_font(9, bold=True), anchor='mm')

    # Flights table section
    draw.text((40, 225), "Расписание и завершение рейсов", fill='#0F172A', font=get_font(18, bold=True))

    # Table
    ty = 265
    draw.rounded_rectangle([40, ty, w - 40, 710], radius=14, fill='#FFFFFF', outline='#CBD5E1')
    # Table Header
    draw.rectangle([40, ty, w - 40, ty + 40], fill='#F8FAFC')
    draw.text((60, ty + 12), "РЕЙС", fill='#64748B', font=get_font(11, bold=True))
    draw.text((160, ty + 12), "МАРШРУТ", fill='#64748B', font=get_font(11, bold=True))
    draw.text((430, ty + 12), "ВЫЛЕТ", fill='#64748B', font=get_font(11, bold=True))
    draw.text((580, ty + 12), "БАЗОВАЯ ЦЕНА", fill='#64748B', font=get_font(11, bold=True))
    draw.text((730, ty + 12), "СТАТУС", fill='#64748B', font=get_font(11, bold=True))
    draw.text((880, ty + 12), "ДЕЙСТВИЕ (ФУНКЦИЯ 12)", fill='#64748B', font=get_font(11, bold=True))
    draw.line([40, ty + 40, w - 40, ty + 40], fill='#E2E8F0', width=1)

    f_rows = [
        ("KS-501", "Новосибирск → Сочи", "30.11 07:30", "12 500 ₽", "scheduled", True, ty + 50),
        ("SU-102", "Москва → Санкт-Петербург", "01.12 09:15", "5 200 ₽", "scheduled", True, ty + 120),
        ("S7-304", "Новосибирск → Владивосток", "02.12 11:00", "16 800 ₽", "scheduled", True, ty + 190),
        ("A3-705", "Казань → Екатеринбург", "28.11 14:20", "6 900 ₽", "completed", False, ty + 260),
        ("UT-408", "Сочи → Москва", "27.11 18:45", "11 400 ₽", "completed", False, ty + 330),
    ]

    for fnum, route, dep, pr, st, can_comp, ry in f_rows:
        draw.rounded_rectangle([55, ry + 8, 125, ry + 36], radius=6, fill='#F1F5F9')
        draw.text((90, ry + 22), fnum, fill='#0F172A', font=get_font(12, bold=True), anchor='mm')
        draw.text((160, ry + 15), route, fill='#0F172A', font=get_font(13, bold=True))
        draw.text((430, ry + 15), dep, fill='#475569', font=get_font(13))
        draw.text((580, ry + 15), pr, fill='#0F172A', font=get_font(13, bold=True))

        if st == 'scheduled':
            draw.rounded_rectangle([720, ry + 10, 830, ry + 34], radius=12, fill='#ECFDF5')
            draw.text((775, ry + 22), "● scheduled", fill='#059669', font=get_font(11, bold=True), anchor='mm')
        else:
            draw.rounded_rectangle([720, ry + 10, 830, ry + 34], radius=12, fill='#EFF6FF')
            draw.text((775, ry + 22), "● completed", fill='#2563EB', font=get_font(11, bold=True), anchor='mm')

        if can_comp:
            draw.rounded_rectangle([870, ry + 6, 1140, ry + 38], radius=8, fill='#10B981')
            draw.text((1005, ry + 22), "✓ Завершить и начислить мили", fill='#FFFFFF', font=get_font(11, bold=True), anchor='mm')
        else:
            draw.text((880, ry + 15), "✓ Мили начислены пассажирам", fill='#10B981', font=get_font(12, bold=True))

        draw.line([40, ry + 55, w - 40, ry + 55], fill='#F1F5F9', width=1)

    img.save('docs_assets/ui_admin.png', dpi=(300, 300))
    print("UI Admin page generated.")

# 5. PDF Receipt Mockup
def create_ui_pdf():
    w, h = 900, 1100
    img = Image.new('RGB', (w, h), color='#FFFFFF')
    draw = ImageDraw.Draw(img)

    # Outer Sheet
    draw.rectangle([20, 20, w - 20, h - 20], fill='#FFFFFF', outline='#CBD5E1', width=2)

    # Header Banner
    draw.rectangle([22, 22, w - 22, 140], fill='#0F172A')
    draw.text((60, 50), "AETHER AIRWAYS", fill='#FFFFFF', font=get_font(26, bold=True))
    draw.text((60, 85), "ЭЛЕКТРОННАЯ МАРШРУТНАЯ КВИТАНЦИЯ БРОНИРОВАНИЯ", fill='#94A3B8', font=get_font(12, bold=True))
    draw.text((60, 105), "Подтверждение 24-часовой фиксации тарифа и бронирования места", fill='#38BDF8', font=get_font(11))

    # Booking Number Box
    draw.rounded_rectangle([w - 280, 45, w - 50, 115], radius=10, fill='#1E293B')
    draw.text((w - 165, 65), "НОМЕР БРОНИРОВАНИЯ", fill='#94A3B8', font=get_font(9, bold=True), anchor='mm')
    draw.text((w - 165, 92), "BK-89421", fill='#38BDF8', font=get_font(18, bold=True), anchor='mm')

    # Flight Details Card
    draw.rounded_rectangle([50, 170, w - 50, 370], radius=14, fill='#F8FAFC', outline='#E2E8F0')
    draw.text((80, 195), "РЕЙС: KS-501", fill='#0F172A', font=get_font(18, bold=True))
    draw.text((w - 260, 195), "КЛАСС: ЭКОНОМ-КЛАСС", fill='#2563EB', font=get_font(14, bold=True))
    draw.line([80, 230, w - 80, 230], fill='#E2E8F0', width=1)

    # Stations
    draw.text((80, 255), "НОВОСИБИРСК (OVB)", fill='#0F172A', font=get_font(16, bold=True))
    draw.text((80, 285), "Толмачёво, Терминал B", fill='#64748B', font=get_font(12))
    draw.text((80, 310), "Вылет: 30.11.2026, 07:30 (Местное время)", fill='#0F172A', font=get_font(13, bold=True))

    draw.text((w // 2 - 20, 280), "4ч 30мин", fill='#94A3B8', font=get_font(13, bold=True))

    draw.text((w - 320, 255), "СОЧИ (AER)", fill='#0F172A', font=get_font(16, bold=True))
    draw.text((w - 320, 285), "Адлер, Терминал 1", fill='#64748B', font=get_font(12))
    draw.text((w - 320, 310), "Прибытие: 30.11.2026, 09:00 (Местное время)", fill='#0F172A', font=get_font(13, bold=True))

    # Passenger & Seat Info Card
    draw.rounded_rectangle([50, 400, w - 50, 560], radius=14, fill='#F8FAFC', outline='#E2E8F0')
    draw.text((80, 420), "СВЕДЕНИЯ О ПАССАЖИРЕ И МЕСТЕ", fill='#64748B', font=get_font(12, bold=True))
    draw.line([80, 445, w - 80, 445], fill='#E2E8F0', width=1)

    draw.text((80, 465), "Пассажир: Александра Смирнова", fill='#0F172A', font=get_font(14, bold=True))
    draw.text((80, 495), "Паспортные данные: Серия 4010, № 111222", fill='#475569', font=get_font(13))
    draw.text((80, 525), "Гражданство: Российская Федерация", fill='#475569', font=get_font(13))

    draw.text((w - 320, 465), "Назначенное место: 5B", fill='#059669', font=get_font(16, bold=True))
    draw.text((w - 320, 495), "Базовый тариф: 12 500.00 ₽", fill='#475569', font=get_font(13))
    draw.text((w - 320, 525), "Начисление за полёт: +2 500 миль", fill='#4F46E5', font=get_font(13, bold=True))

    # Hold conditions
    draw.rounded_rectangle([50, 590, w - 50, 780], radius=14, fill='#FFFBEB', outline='#FDE68A')
    draw.text((80, 615), "УСЛОВИЯ И РЕГЛАМЕНТ 24-ЧАСОВОЙ ФИКСАЦИИ ТАРИФА", fill='#B45309', font=get_font(13, bold=True))
    draw_multiline(draw, (80, 645),
        "1. Тариф зафиксирован на 24 часа с момента создания бронирования.\n"
        "2. Бронирование активно до: 01.12.2026, 07:30.\n"
        "3. Пассажир может продлить бронь ещё на +24 часа за 500 бонусных миль в личном кабинете.\n"
        "4. Доступен апгрейд класса обслуживания до «Комфорт» за 2 000 бонусных миль.\n"
        "5. По истечении срока действия не выкупленное бронирование аннулируется, а место возвращается в открытую продажу.",
        get_font(11), '#78350F', line_spacing=6)

    # Footer note & QR
    draw.line([50, 820, w - 50, 820], fill='#E2E8F0', width=1)
    # Fake QR Code
    draw.rectangle([80, 850, 200, 970], fill='#FFFFFF', outline='#0F172A', width=2)
    for qx in range(85, 195, 12):
        for qy in range(855, 965, 12):
            if (qx + qy) % 5 == 0 or (qx < 120 and qy < 890) or (qx > 160 and qy < 890):
                draw.rectangle([qx, qy, qx + 8, qy + 8], fill='#0F172A')

    draw.text((230, 860), "Aether Airways Priority Loyalty System", fill='#0F172A', font=get_font(15, bold=True))
    draw.text((230, 890), "Электронный документ сформирован автоматически библиотекой barryvdh/laravel-dompdf.\nИдентификатор документа: SHA256-BK-89421-20261130\nСлужба заботы о пассажирах: support@aether.local | 8 (800) 555-35-35", fill='#64748B', font=get_font(11))

    img.save('docs_assets/ui_pdf_receipt.png', dpi=(300, 300))
    print("UI PDF receipt generated.")

if __name__ == '__main__':
    create_ui_home()
    create_ui_seats()
    create_ui_profile()
    create_ui_admin()
    create_ui_pdf()
    print("All UI mockups generated successfully.")
