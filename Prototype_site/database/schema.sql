-- PostgreSQL schema for AutoParts shop

CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

INSERT INTO roles (name, description) VALUES
('Адміністратор', 'Повний доступ до панелі керування'),
('Менеджер', 'Керує замовленнями та каталогом'),
('Покупець', 'Стандартний клієнт')
ON CONFLICT DO NOTHING;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT REFERENCES roles(id) DEFAULT 3,
    phone VARCHAR(30),
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    address TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    parent_id INT REFERENCES categories(id) ON DELETE SET NULL,
    description TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE brands (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    slug VARCHAR(150) NOT NULL UNIQUE
);

CREATE TABLE car_models (
    id SERIAL PRIMARY KEY,
    brand VARCHAR(150) NOT NULL,
    model VARCHAR(150) NOT NULL,
    generation VARCHAR(100),
    year_from SMALLINT,
    year_to SMALLINT
);

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    category_id INT NOT NULL REFERENCES categories(id),
    brand_id INT REFERENCES brands(id),
    slug VARCHAR(150) NOT NULL UNIQUE,
    sku VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price NUMERIC(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    compatibility TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE product_images (
    id SERIAL PRIMARY KEY,
    product_id INT REFERENCES products(id) ON DELETE CASCADE,
    path VARCHAR(255) NOT NULL,
    alt VARCHAR(255),
    is_main BOOLEAN DEFAULT FALSE
);

CREATE TABLE product_car_model (
    product_id INT REFERENCES products(id) ON DELETE CASCADE,
    car_model_id INT REFERENCES car_models(id) ON DELETE CASCADE,
    PRIMARY KEY (product_id, car_model_id)
);

CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    guest_name VARCHAR(255),
    guest_email VARCHAR(255),
    guest_phone VARCHAR(30),
    status VARCHAR(50) NOT NULL DEFAULT 'new',
    total NUMERIC(12,2) NOT NULL,
    payment_method VARCHAR(50),
    delivery_method VARCHAR(50),
    delivery_address TEXT NOT NULL,
    notes TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(id) ON DELETE CASCADE,
    product_id INT REFERENCES products(id),
    price NUMERIC(12,2) NOT NULL,
    quantity INT NOT NULL,
    name_snapshot VARCHAR(255)
);

-- Returns/Exchanges table (14-day return policy as per Ukrainian Consumer Protection Law)
CREATE TABLE returns (
    id SERIAL PRIMARY KEY,
    order_id INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    reason VARCHAR(100) NOT NULL,
    description TEXT,
    items_json JSONB,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    return_method VARCHAR(50),
    tracking_number VARCHAR(100),
    notes TEXT,
    admin_comment TEXT,
    deadline_days INT DEFAULT 14,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Optional session-based carts can зберігатися в таблиці, якщо потрібно історію:
CREATE TABLE carts (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    session_id VARCHAR(255),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE cart_items (
    id SERIAL PRIMARY KEY,
    cart_id INT REFERENCES carts(id) ON DELETE CASCADE,
    product_id INT REFERENCES products(id),
    quantity INT NOT NULL CHECK (quantity > 0)
);

-- Seed data for root and leaf categories (двигун, гальмівна система, підвіска, електрика)
INSERT INTO categories (name, slug, parent_id, description)
VALUES
    ('Двигун', 'dvigun', NULL, 'Вузли та агрегати двигуна.'),
    ('Гальмівна система', 'brake-system', NULL, 'Усе для гальмівної системи.'),
    ('Підвіска', 'suspension', NULL, 'Стійки, важелі, шарніри та амортизатори.'),
    ('Електрика', 'electrics', NULL, 'Електричні та електронні компоненти.')
ON CONFLICT (slug) DO NOTHING;

-- Підкатегорії для root-категорії «Двигун»
WITH engine AS (SELECT id FROM categories WHERE slug = 'dvigun')
INSERT INTO categories (name, slug, parent_id, description)
SELECT name, slug, engine.id, description
FROM (
    VALUES
        ('Масла', 'engine-oil', 'Моторні мастила та рідини.'),
        ('Фільтри', 'engine-filters', 'Масляні та повітряні фільтри.'),
        ('ГРМ', 'engine-timing', 'Ремені, ланцюги та ролики ГРМ.'),
        ('Прокладки', 'engine-gaskets', 'Прокладки та сальники двигуна.')
) AS data(name, slug, description)
CROSS JOIN engine
ON CONFLICT (slug) DO NOTHING;

-- Підкатегорії для root-категорії «Гальмівна система»
WITH brakes AS (SELECT id FROM categories WHERE slug = 'brake-system')
INSERT INTO categories (name, slug, parent_id, description)
SELECT name, slug, brakes.id, description
FROM (
    VALUES
        ('Гальмівні колодки', 'brake-pads', 'Комплекти передніх та задніх колодок.'),
        ('Гальмівні диски', 'brake-discs', 'Вентильовані та суцільні диски різних діаметрів.'),
        ('Гальмівні шланги', 'brake-hoses', 'Армовані шланги та трубки для контуру.')
) AS data(name, slug, description)
CROSS JOIN brakes
ON CONFLICT (slug) DO NOTHING;

-- Підкатегорії для root-категорії «Підвіска»
WITH suspension AS (SELECT id FROM categories WHERE slug = 'suspension')
INSERT INTO categories (name, slug, parent_id, description)
SELECT name, slug, suspension.id, description
FROM (
    VALUES
        ('Амортизатори', 'suspension-shocks', 'Передні та задні газо-масляні стійки.'),
        ('Важелі', 'suspension-arms', 'Поперечні та поздовжні важелі з сайлентблоками.'),
        ('Опори та підшипники', 'suspension-mounts', 'Опори стійок та ступиць.')
) AS data(name, slug, description)
CROSS JOIN suspension
ON CONFLICT (slug) DO NOTHING;

-- Підкатегорії для root-категорії «Електрика»
WITH electrics AS (SELECT id FROM categories WHERE slug = 'electrics')
INSERT INTO categories (name, slug, parent_id, description)
SELECT name, slug, electrics.id, description
FROM (
    VALUES
        ('Акумулятори', 'electric-batteries', 'Стартерні акумулятори 12В.'),
        ('Датчики', 'electric-sensors', 'Кисневі, температурні та ABS датчики.'),
        ('Світло', 'electric-lighting', 'Фари, лампи та денні ходові вогні.')
) AS data(name, slug, description)
CROSS JOIN electrics
ON CONFLICT (slug) DO NOTHING;

-- Тестові товари, прив’язані до leaf-підкатегорій двигуна
INSERT INTO products (category_id, brand_id, slug, sku, name, description, price, stock, is_active)
VALUES
    ((SELECT id FROM categories WHERE slug = 'engine-oil'), NULL, 'motul-8100-5w30', 'OIL-0001',
     'Motul 8100 X-clean 5W30 5L', 'Синтетична моторна олива для сучасних дизельних та бензинових двигунів.', 1650.00, 24, TRUE),
    ((SELECT id FROM categories WHERE slug = 'engine-filters'), NULL, 'mann-filter-w712', 'FLT-0102',
     'MANN-FILTER W712/52', 'Оригінальний масляний фільтр для популярних моделей VAG.', 320.00, 40, TRUE),
    ((SELECT id FROM categories WHERE slug = 'engine-timing'), NULL, 'gates-timing-kit', 'TIM-2001',
     'Gates PowerGrip Kit', 'Комплект ГРМ з роликами та натягувачем.', 2890.00, 12, TRUE),
    ((SELECT id FROM categories WHERE slug = 'engine-gaskets'), NULL, 'victor-reinz-head-gasket', 'GST-3300',
     'Victor Reinz прокладка ГБЦ', 'Посилена прокладка головки блоку під дизель 2.0.', 1350.00, 8, TRUE)
ON CONFLICT (slug) DO NOTHING;

-- Тестові товари для гальмівної системи
INSERT INTO products (category_id, brand_id, slug, sku, name, description, price, stock, is_active)
VALUES
    ((SELECT id FROM categories WHERE slug = 'brake-pads'), NULL, 'ferodo-premier-pads', 'BRK-1001',
     'Ferodo Premier колодки передні', 'Комплект керамічних гальмівних колодок для VW Passat B8.', 1450.00, 18, TRUE),
    ((SELECT id FROM categories WHERE slug = 'brake-discs'), NULL, 'brembo-max-discs', 'BRK-1102',
     'Brembo MAX 288mm', 'Пара вентильованих дисків з насічками.', 2750.00, 10, TRUE),
    ((SELECT id FROM categories WHERE slug = 'brake-hoses'), NULL, 'ate-brake-hose', 'BRK-1203',
     'ATE шланг гальмівний', 'Армований шланг заднього контуру, довжина 320 мм.', 420.00, 30, TRUE)
ON CONFLICT (slug) DO NOTHING;

-- Тестові товари для підвіски
INSERT INTO products (category_id, brand_id, slug, sku, name, description, price, stock, is_active)
VALUES
    ((SELECT id FROM categories WHERE slug = 'suspension-shocks'), NULL, 'kyb-excel-g-shock', 'SUS-2001',
     'KYB Excel-G передній амортизатор', 'Газо-масляний амортизатор для Toyota Corolla E150.', 2150.00, 14, TRUE),
    ((SELECT id FROM categories WHERE slug = 'suspension-arms'), NULL, 'lemforder-front-arm', 'SUS-2102',
     'Lemforder важіль передній', 'Посилений нижній важіль з шаровою опорою.', 1890.00, 9, TRUE),
    ((SELECT id FROM categories WHERE slug = 'suspension-mounts'), NULL, 'sasic-top-mount', 'SUS-2203',
     'Sasic опора стійки', 'Гумово-металева опора амортизатора з підшипником.', 760.00, 22, TRUE)
ON CONFLICT (slug) DO NOTHING;

-- Тестові товари для електрики
INSERT INTO products (category_id, brand_id, slug, sku, name, description, price, stock, is_active)
VALUES
    ((SELECT id FROM categories WHERE slug = 'electric-batteries'), NULL, 'varta-blue-60ah', 'ELC-3001',
     'Varta Blue Dynamic 60Ah', 'Стартерний акумулятор з пусковим струмом 540 А.', 3150.00, 11, TRUE),
    ((SELECT id FROM categories WHERE slug = 'electric-sensors'), NULL, 'bosch-lambda-sensor', 'ELC-3102',
     'Bosch Lambda Sensor', 'Універсальний кисневий датчик з підігрівом.', 1850.00, 16, TRUE),
    ((SELECT id FROM categories WHERE slug = 'electric-lighting'), NULL, 'philips-led-daylight', 'ELC-3203',
     'Philips LED DayLight 9', 'Комплект денних ходових вогнів з контролером.', 2350.00, 7, TRUE)
ON CONFLICT (slug) DO NOTHING;
