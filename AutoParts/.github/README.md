# GitHub Actions Setup для AutoParts

## 📋 Що було налаштовано

Проект автоматично запускатиме 3 workflows при push на GitHub:

### 1. **Build & Test** (`.github/workflows/build.yml`)
- ✅ Перевіряє синтаксис PHP
- ✅ Встановлює залежності через Composer
- ✅ Запускає тести (якщо є)
- ✅ Перевіряє code standards
- ✅ Створює artifacts (artcifacts можна скачати)

**Тригери:** push на `master` і `develop`, pull request

### 2. **Deploy** (`.github/workflows/deploy.yml`)
- 🚀 Встановлює production залежності
- 📦 Створює release package (zip файл)
- 📝 Автоматично створює GitHub Release
- 📥 Завантажує release asset

**Тригери:** push на `master` або manual `workflow_dispatch`

### 3. **Code Quality** (`.github/workflows/quality.yml`)
- 🔍 Статичний аналіз з PHPStan
- 🔐 Security audit через Composer
- 📊 Звіт про якість коду

**Тригери:** push на `master` і `develop`, pull request

---

## 🚀 Як використовувати

### Спосіб 1: Автоматичний запуск
1. Зробіть commit і push на GitHub
   ```bash
   git add .
   git commit -m "Додав GitHub Actions workflows"
   git push origin master
   ```

2. Перейдіть на GitHub → ваш репозиторій → **Actions**
3. Бачитимете виконання workflows у реальному часі

### Спосіб 2: Ручний запуск Deploy
1. Перейдіть на GitHub → **Actions** → **Deploy**
2. Натисніть **Run workflow** → **Run workflow**
3. Workflow запуститься на `master` branch

### Спосіб 3: Локальна перевірка перед push
```bash
# Перевірити синтаксис PHP локально
for file in $(find ./App -name "*.php"); do php -l "$file"; done

# Встановити залежності
composer install

# Запустити тести (якщо налаштовані)
composer test
```

---

## 📊 Статус workflows

Щоб бачити статус:

1. **Значок на GitHub** - на головній сторінці репо (verde = success)
2. **Actions tab** - детальний лог виконання
3. **Pull Requests** - статус checks показується перед мерджем

---

## ⚙️ Конфігурація Secrets (для розгортання)

Якщо плануєте розгортувати на сервер, додайте в **GitHub Settings → Secrets**:

```
DEPLOY_HOST        = ваш.сервер.com
DEPLOY_USER        = ssh_user
DEPLOY_PASSWORD    = ssh_password
DEPLOY_PATH        = /var/www/autoparts
```

Тоді можна додати крок у `deploy.yml` для SSH розгортання.

---

## 🔧 Налаштування для вашого проекту

### Додати тести
```bash
composer require --dev phpunit/phpunit
```

### Додати code standards
```bash
composer require --dev squizlabs/php_codesniffer
```

Потім в `composer.json`:
```json
{
  "scripts": {
    "test": "phpunit",
    "lint": "phpcs --standard=PSR12 App/"
  }
}
```

### Додати .env в GitHub Actions
Якщо потрібно переддати `.env` в workflow:

1. Створіть `.env.example` у репо (без чутливих даних)
2. У workflow:
   ```yaml
   - name: Setup environment
     run: cp .env.example .env
   ```

---

## 📝 Мониторинг workflows

**GitHub повідомляє про:**
- ✅ Успішні білди
- ❌ Помилки у синтаксисі
- ⚠️ Попередження про залежності
- 🔐 Уразливості у пакетах

**Як отримувати сповіщення:**
- GitHub → Settings → Notifications
- Обереження автоматично йдуть на email

---

## 🎯 Че́ргові кроки

1. **Зробити перший commit** з workflows
2. **Перевірити Actions tab** - має з'явитися перший білд
3. **Налаштувати CI/CD для розгортання** (якщо потрібно на сервер)
4. **Додати branch protection** - вимагати успішного білду перед мерджем:
   - GitHub → Settings → Branches → main → Require status checks to pass

---

**Успіхів з білдами! 🚀**
