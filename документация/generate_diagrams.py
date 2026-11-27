import os
from PIL import Image, ImageDraw, ImageFont

os.makedirs('docs_assets', exist_ok=True)
font_path = 'C:/Windows/Fonts/arial.ttf'
font_bold_path = 'C:/Windows/Fonts/arialbd.ttf'

def get_font(size, bold=False):
    p = font_bold_path if bold and os.path.exists(font_bold_path) else font_path
    return ImageFont.truetype(p, size)

def draw_multiline(draw, xy, text, font, fill, line_spacing=4, align='center'):
    lines = text.split('\n')
    cx, start_y = xy
    # calculate line heights
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
def create_use_case_diagram():
    w, h = 1400, 1000
    img = Image.new('RGB', (w, h), color='#FFFFFF')
    draw = ImageDraw.Draw(img)

    # Title
    title_font = get_font(22, bold=True)
    draw.text((w // 2, 35), "Диаграмма вариантов использования (Use Case Diagram)", fill='#1E293B', font=title_font, anchor='mm')
    draw.text((w // 2, 65), "Информационная система авиакомпании «Aether Airways»", fill='#64748B', font=get_font(15), anchor='mm')

    # Border frame for system boundary
    sys_x1, sys_y1, sys_x2, sys_y2 = 320, 95, 1080, 960
    draw.rounded_rectangle([sys_x1, sys_y1, sys_x2, sys_y2], radius=16, fill='#F8FAFC', outline='#CBD5E1', width=2)
    draw.text((sys_x1 + 25, sys_y1 + 20), "Система «Aether Airways»", fill='#475569', font=get_font(16, bold=True))

    # Actors
    actors = [
        {"name": "Гость", "x": 160, "y": 260, "color": "#0284C7"},
        {"name": "Авторизованный\nпассажир", "x": 160, "y": 570, "color": "#4F46E5"},
        {"name": "Администратор\nсистемы", "x": 1240, "y": 520, "color": "#059669"},
    ]

    def draw_actor(ax, ay, name, col):
        # Head
        draw.ellipse([ax - 18, ay - 45, ax + 18, ay - 9], fill='#FFFFFF', outline=col, width=3)
        # Body
        draw.line([ax, ay - 9, ax, ay + 25], fill=col, width=3)
        # Arms
        draw.line([ax - 28, ay + 2, ax + 28, ay + 2], fill=col, width=3)
        # Legs
        draw.line([ax, ay + 25, ax - 20, ay + 65], fill=col, width=3)
        draw.line([ax, ay + 25, ax + 20, ay + 65], fill=col, width=3)
        # Text
        draw_multiline(draw, (ax, ay + 75), name, get_font(13, bold=True), '#0F172A', align='center')

    for a in actors:
        draw_actor(a["x"], a["y"], a["name"], a["color"])

    # Use Cases (Ellipses)
    # Left column inside system: Гость + Пассажир
    # Center/Right: Пассажир + Админ
    use_cases = [
        # id, text, cx, cy, actors_linked
        (1, "Поиск рейсов по городам и дате", 520, 160, [0, 1]),
        (2, "Просмотр ТОП-3 спецпредложений", 520, 230, [0, 1]),
        (3, "Регистрация (+1000 Welcome Miles)", 520, 300, [0]),
        (4, "Авторизация и выход из системы", 520, 370, [0, 1, 2]),
        (5, "Фильтрация рейсов (цена/время)", 520, 440, [0, 1]),
        (6, "Выбор кресла на интерактивной схеме", 520, 510, [1]),
        (7, "Создание бронирования на 24 часа", 520, 580, [1]),
        (8, "Ведение личных и паспортных данных", 520, 650, [1]),
        (9, "История бронирований и статусы", 520, 720, [1]),
        (10, "Апгрейд класса (Эконом → Комфорт)", 520, 790, [1]),
        (11, "Продление брони (+24ч за 500 миль)", 520, 860, [1]),
        (12, "Отмена активного бронирования", 880, 250, [1]),
        (13, "Формирование и скачивание PDF-квитанции", 880, 350, [1, 2]),
        (14, "Смена темы оформления (5 палитр)", 880, 450, [0, 1, 2]),
        (15, "Мониторинг расписания и броней", 880, 570, [2]),
        (16, "Завершение рейса и начисление миль", 880, 680, [2]),
        (17, "Управление статусами рейсов", 880, 790, [2]),
    ]

    uc_coords = {}
    for uc_id, text, cx, cy, linked in use_cases:
        rx, ry = 160, 26
        draw.ellipse([cx - rx, cy - ry, cx + rx, cy + ry], fill='#FFFFFF', outline='#3B82F6', width=2)
        draw.text((cx, cy), text, fill='#1E293B', font=get_font(12, bold=True), anchor='mm', align='center')
        uc_coords[uc_id] = (cx, cy, rx, ry, linked)

    # Draw connection lines
    for uc_id, (cx, cy, rx, ry, linked) in uc_coords.items():
        for act_idx in linked:
            ax, ay = actors[act_idx]["x"], actors[act_idx]["y"]
            col = actors[act_idx]["color"]
            if ax < cx:
                # Left actor
                draw.line([ax + 35, ay + 20, cx - rx, cy], fill=col, width=1)
            else:
                # Right actor
                draw.line([ax - 35, ay + 20, cx + rx, cy], fill=col, width=1)

    img.save('docs_assets/use_case_diagram.png', dpi=(300, 300))
    print("Use Case diagram generated.")

# 2. State Machine Diagram
def create_state_machine_diagram():
    w, h = 1300, 700
    img = Image.new('RGB', (w, h), color='#FFFFFF')
    draw = ImageDraw.Draw(img)

    # Title
    draw.text((w // 2, 35), "Диаграмма состояний бронирования (State Machine Diagram)", fill='#1E293B', font=get_font(22, bold=True), anchor='mm')
    draw.text((w // 2, 65), "Жизненный цикл заказа в таблице bookings авиакомпании «Aether Airways»", fill='#64748B', font=get_font(15), anchor='mm')

    # Initial state
    draw.ellipse([80, 320, 110, 350], fill='#0F172A', outline='#0F172A')
    draw.line([110, 335, 230, 335], fill='#0F172A', width=2)
    draw.polygon([(230, 335), (218, 330), (218, 340)], fill='#0F172A')
    draw.text((170, 315), "Создание брони", fill='#475569', font=get_font(12), anchor='mb')

    # States
    # 1. Active State
    draw.rounded_rectangle([230, 260, 490, 410], radius=14, fill='#ECFDF5', outline='#10B981', width=3)
    draw.text((360, 290), "СТАТУС: active", fill='#065F46', font=get_font(16, bold=True), anchor='mm')
    draw.line([240, 315, 480, 315], fill='#A7F3D0', width=1)
    draw_multiline(draw, (360, 325), "• Тариф зафиксирован на 24 часа\n• Место в салоне закреплено\n• Паспортные данные сохранены", get_font(12), '#047857', align='center')

    # Self loops on Active state:
    # Upgrade
    draw.arc([300, 160, 420, 260], start=180, end=360, fill='#6366F1', width=2)
    draw.polygon([(420, 260), (415, 248), (425, 248)], fill='#6366F1')
    draw_multiline(draw, (360, 160), "Апгрейд класса (-2000 миль)\nЭконом → Комфорт", get_font(11, bold=True), '#4338CA', align='center')

    # Extend
    draw.arc([300, 410, 420, 510], start=0, end=180, fill='#0284C7', width=2)
    draw.polygon([(300, 410), (295, 422), (305, 422)], fill='#0284C7')
    draw_multiline(draw, (360, 470), "Продление на 24ч (-500 миль)\n(не более 1 раза)", get_font(11, bold=True), '#0369A1', align='center')

    # 2. Expired State
    draw.line([490, 300, 750, 160], fill='#EF4444', width=2)
    draw.polygon([(750, 160), (738, 160), (745, 170)], fill='#EF4444')
    draw_multiline(draw, (620, 195), "Истечение 24 часов\nбез оплаты", get_font(12, bold=True), '#B91C1C', align='center')

    draw.rounded_rectangle([750, 90, 1030, 230], radius=14, fill='#FEF2F2', outline='#EF4444', width=2)
    draw.text((890, 125), "СТАТУС: expired", fill='#991B1B', font=get_font(16, bold=True), anchor='mm')
    draw.line([760, 150, 1020, 150], fill='#FECDD3', width=1)
    draw_multiline(draw, (890, 160), "• Срок фиксации истёк\n• Место снова доступно\n• Переходит в архив", get_font(12), '#7F1D1D', align='center')

    # 3. Cancelled State
    draw.line([490, 335, 750, 335], fill='#F59E0B', width=2)
    draw.polygon([(750, 335), (738, 330), (738, 340)], fill='#F59E0B')
    draw_multiline(draw, (620, 305), "Отмена пассажиром\nили администратором", get_font(12, bold=True), '#B45309', align='center')

    draw.rounded_rectangle([750, 270, 1030, 410], radius=14, fill='#FFFBEB', outline='#F59E0B', width=2)
    draw.text((890, 305), "СТАТУС: cancelled", fill='#92400E', font=get_font(16, bold=True), anchor='mm')
    draw.line([760, 330, 1020, 330], fill='#FDE68A', width=1)
    draw_multiline(draw, (890, 340), "• Бронирование отменено\n• Место освобождено\n• Переходит в архив", get_font(12), '#78350F', align='center')

    # 4. Completed State
    draw.line([490, 370, 750, 510], fill='#3B82F6', width=2)
    draw.polygon([(750, 510), (745, 500), (738, 510)], fill='#3B82F6')
    draw_multiline(draw, (620, 450), "Завершение рейса\n(начисление миль)", get_font(12, bold=True), '#1D4ED8', align='center')

    draw.rounded_rectangle([750, 450, 1030, 590], radius=14, fill='#EFF6FF', outline='#3B82F6', width=2)
    draw.text((890, 485), "СТАТУС: completed", fill='#1E40AF', font=get_font(16, bold=True), anchor='mm')
    draw.line([760, 510, 1020, 510], fill='#BFDBFE', width=1)
    draw_multiline(draw, (890, 520), "• Рейс успешно выполнен\n• Начислены мили пассажиру\n• Формируется квитанция", get_font(12), '#1E3A8A', align='center')

    # Final States (Bullseye)
    def draw_final_state(fx, fy):
        draw.ellipse([fx - 16, fy - 16, fx + 16, fy + 16], fill='#FFFFFF', outline='#0F172A', width=2)
        draw.ellipse([fx - 9, fy - 9, fx + 9, fy + 9], fill='#0F172A', outline='#0F172A')

    # lines to final state
    draw.line([1030, 160, 1140, 160], fill='#0F172A', width=2)
    draw_final_state(1160, 160)

    draw.line([1030, 340, 1140, 340], fill='#0F172A', width=2)
    draw_final_state(1160, 340)

    draw.line([1030, 520, 1140, 520], fill='#0F172A', width=2)
    draw_final_state(1160, 520)

    img.save('docs_assets/state_machine_diagram.png', dpi=(300, 300))
    print("State Machine diagram generated.")

# 3. Database Schema Diagram (ERD)
def create_database_schema_diagram():
    w, h = 1400, 950
    img = Image.new('RGB', (w, h), color='#FFFFFF')
    draw = ImageDraw.Draw(img)

    draw.text((w // 2, 35), "Схема данных реляционной СУБД (ERD / Таблицы aether_airways)", fill='#1E293B', font=get_font(22, bold=True), anchor='mm')
    draw.text((w // 2, 65), "Архитектура связей между сущностями базы данных системы бронирования авиабилетов", fill='#64748B', font=get_font(15), anchor='mm')

    # Tables info: (name, x, y, width, height, header_color, fields)
    tables = [
        {
            "name": "users (Пользователи)",
            "x": 60, "y": 120, "w": 350, "h": 340, "col": "#4F46E5",
            "fields": [
                ("id", "BIGINT UNSIGNED (PK)", True),
                ("name", "VARCHAR(255)", False),
                ("surname", "VARCHAR(255)", False),
                ("email", "VARCHAR(255) UNIQUE", False),
                ("phone", "VARCHAR(50)", False),
                ("birth_date", "DATE", False),
                ("passport_series", "VARCHAR(4)", False),
                ("passport_number", "VARCHAR(6)", False),
                ("citizenship", "VARCHAR(100)", False),
                ("miles", "INT (Баланс миль)", False),
                ("role", "ENUM('admin','user')", False),
                ("password", "VARCHAR(255)", False),
            ]
        },
        {
            "name": "flights (Авиарейсы)",
            "x": 520, "y": 120, "w": 360, "h": 270, "col": "#0284C7",
            "fields": [
                ("id", "BIGINT UNSIGNED (PK)", True),
                ("flight_number", "VARCHAR(50) UNIQUE", False),
                ("departure_city", "VARCHAR(255)", False),
                ("arrival_city", "VARCHAR(255)", False),
                ("departure_time", "DATETIME", False),
                ("arrival_time", "DATETIME", False),
                ("price", "DECIMAL(10,2)", False),
                ("status", "ENUM('scheduled','completed','cancelled')", False),
            ]
        },
        {
            "name": "seats (Кресла в салоне)",
            "x": 980, "y": 120, "w": 350, "h": 230, "col": "#059669",
            "fields": [
                ("id", "BIGINT UNSIGNED (PK)", True),
                ("flight_id", "BIGINT UNSIGNED (FK)", True),
                ("seat_number", "VARCHAR(10)", False),
                ("seat_class", "ENUM('economy','comfort','business')", False),
                ("is_available", "BOOLEAN", False),
            ]
        },
        {
            "name": "special_offers (Спецпредложения)",
            "x": 980, "y": 420, "w": 350, "h": 260, "col": "#D97706",
            "fields": [
                ("id", "BIGINT UNSIGNED (PK)", True),
                ("flight_id", "BIGINT UNSIGNED (FK)", True),
                ("title", "VARCHAR(255)", False),
                ("description", "TEXT", False),
                ("discount", "INT (Скидка %)", False),
                ("start_date", "DATE", False),
                ("end_date", "DATE", False),
                ("is_active", "BOOLEAN", False),
            ]
        },
        {
            "name": "bookings (Бронирования на 24 часа)",
            "x": 480, "y": 500, "w": 440, "h": 390, "col": "#DC2626",
            "fields": [
                ("id", "BIGINT UNSIGNED (PK)", True),
                ("user_id", "BIGINT UNSIGNED (FK)", True),
                ("flight_id", "BIGINT UNSIGNED (FK)", True),
                ("seat_id", "BIGINT UNSIGNED (FK)", True),
                ("booking_number", "VARCHAR(20) UNIQUE", False),
                ("booking_class", "ENUM('economy','comfort','business')", False),
                ("price", "DECIMAL(10,2)", False),
                ("passenger_name", "VARCHAR(255)", False),
                ("passenger_surname", "VARCHAR(255)", False),
                ("passport_series", "VARCHAR(4)", False),
                ("passport_number", "VARCHAR(6)", False),
                ("status", "ENUM('active','expired','completed','cancelled')", False),
                ("expires_at", "DATETIME", False),
                ("extension_count", "TINYINT (0 или 1)", False),
                ("receipt_path", "VARCHAR(255)", False),
            ]
        },
        {
            "name": "mile_transactions (История миль)",
            "x": 60, "y": 560, "w": 360, "h": 260, "col": "#7C3AED",
            "fields": [
                ("id", "BIGINT UNSIGNED (PK)", True),
                ("user_id", "BIGINT UNSIGNED (FK)", True),
                ("booking_id", "BIGINT UNSIGNED (FK, NULL)", True),
                ("type", "ENUM('welcome','flight','upgrade','extension')", False),
                ("amount", "INT (+/- мили)", False),
                ("description", "VARCHAR(255)", False),
                ("created_at", "TIMESTAMP", False),
            ]
        }
    ]

    for t in tables:
        x, y, tw, th = t["x"], t["y"], t["w"], t["h"]
        # Card outer border
        draw.rounded_rectangle([x, y, x + tw, y + th], radius=10, fill='#FFFFFF', outline='#CBD5E1', width=2)
        # Header box
        draw.rounded_rectangle([x, y, x + tw, y + 42], radius=10, fill=t["col"])
        draw.rectangle([x, y + 25, x + tw, y + 42], fill=t["col"])
        draw.text((x + 14, y + 12), t["name"], fill='#FFFFFF', font=get_font(14, bold=True))

        # Fields
        cur_y = y + 52
        for fname, ftype, is_key in t["fields"]:
            col_txt = '#0F172A' if not is_key else '#DC2626'
            bullet = "🔑 " if is_key else "• "
            draw.text((x + 14, cur_y), bullet + fname, fill=col_txt, font=get_font(12, bold=is_key))
            draw.text((x + tw - 14, cur_y), ftype, fill='#64748B', font=get_font(11), anchor='ra')
            cur_y += 24

    # Connectors between tables (1 to N lines)
    # users -> bookings
    draw.line([410, 240, 450, 240, 450, 560, 480, 560], fill='#4F46E5', width=2)
    draw.text((415, 222), "1", fill='#4F46E5', font=get_font(13, bold=True))
    draw.text((465, 542), "∞", fill='#4F46E5', font=get_font(15, bold=True))

    # users -> mile_transactions
    draw.line([220, 460, 220, 560], fill='#7C3AED', width=2)
    draw.text((228, 470), "1", fill='#7C3AED', font=get_font(13, bold=True))
    draw.text((228, 535), "∞", fill='#7C3AED', font=get_font(15, bold=True))

    # flights -> seats
    draw.line([880, 200, 980, 200], fill='#0284C7', width=2)
    draw.text((888, 182), "1", fill='#0284C7', font=get_font(13, bold=True))
    draw.text((965, 182), "∞", fill='#0284C7', font=get_font(15, bold=True))

    # flights -> bookings
    draw.line([700, 390, 700, 500], fill='#0284C7', width=2)
    draw.text((708, 400), "1", fill='#0284C7', font=get_font(13, bold=True))
    draw.text((708, 475), "∞", fill='#0284C7', font=get_font(15, bold=True))

    # flights -> special_offers
    draw.line([880, 320, 940, 320, 940, 480, 980, 480], fill='#D97706', width=2)
    draw.text((888, 302), "1", fill='#D97706', font=get_font(13, bold=True))
    draw.text((965, 462), "∞", fill='#D97706', font=get_font(15, bold=True))

    # bookings -> mile_transactions
    draw.line([480, 680, 420, 680], fill='#DC2626', width=2)
    draw.text((465, 662), "1", fill='#DC2626', font=get_font(13, bold=True))
    draw.text((430, 662), "∞", fill='#DC2626', font=get_font(15, bold=True))

    img.save('docs_assets/database_schema_diagram.png', dpi=(300, 300))
    print("Database Schema ERD diagram generated.")

if __name__ == '__main__':
    create_use_case_diagram()
    create_state_machine_diagram()
    create_database_schema_diagram()
    print("All diagrams generated successfully.")
