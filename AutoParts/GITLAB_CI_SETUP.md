# GitLab CI/CD Setup для AutoParts

## 📋 Що було налаштовано

**`.gitlab-ci.yml`** містить повний pipeline з 3 стадіями:

### 🔨 **Build Stage**
- Встановлення Composer залежностей
- Кешування для швидших білдів
- Артифакти для наступних стадій

### ✅ **Test Stage**
- **PHP Syntax Check** — перевірка синтаксису всіх файлів
- **PHPStan** — статичний аналіз коду (level 5)
- **Security Audit** — перевірка уразливостей у залежностях

### 🚀 **Deploy Stage**
- **Staging Deploy** — розгортання на тестовий сервер (manual)
- **Production Deploy** — розгортання на production (manual)
- **Release Package** — створення zip для release

---

## 🎯 Переваги GitLab CI/CD порівняно з GitHub Actions

| Параметр | GitHub Actions | GitLab CI/CD |
|----------|---|---|
| **Вільні хвилини** | 2,000/місяць (публіч) | **50,000/місяць** ✅ |
| **Приватні репо** | Потребує платежу | Безплатно (до лімітів) ✅ |
| **Простота** | Середня | Простіша ✅ |
| **Docker Support** | Так | Так + краще ✅ |
| **SSH Deploy** | Складно | Просто ✅ |

---

## 🚀 Як запустити на GitLab

### Крок 1: Перенести репозиторій на GitLab
```bash
# Якщо ще не на GitLab, створіть новий проект:
# 1. gitlab.com → Create new project → Import project
# 2. GitHub → вставити URL вашого GitHub репо

# Або додайте GitLab як додатковий remote:
git remote add gitlab https://gitlab.com/ВАШ_USERNAME/autoparts.git
git push gitlab master
```

### Крок 2: Додайте SSH ключ для деплою (опціонально)
Якщо розгортаєте на власний сервер:

1. **GitLab** → ваш проект → **Settings** → **CI/CD** → **Variables**
2. Додайте:
   ```
   SSH_PRIVATE_KEY     = (скопіюйте вміст ~/.ssh/id_rsa)
   DEPLOY_HOST         = ваш.сервер.com
   DEPLOY_USER         = deploy_user
   DEPLOY_PATH         = /var/www/autoparts
   ```

### Крок 3: Запустіть pipeline
- Закомітьте `.gitlab-ci.yml`
- **GitLab** → **CI/CD** → **Pipelines** — бачитимете автоматичний запуск

---

## 📊 Моніторинг Pipeline

**GitLab UI:**
1. **CI/CD** → **Pipelines** — загальний статус
2. **CI/CD** → **Jobs** — деталі кожного job'у
3. **CI/CD** → **Schedules** — запуск за розписанням

**Статуси:**
- 🔵 Pending — очікує
- 🟡 Running — виконується
- 🟢 Passed — успіх
- 🔴 Failed — помилка

---

## 🔧 Налаштування для вашого проекту

### Додати PHPUnit тести
```bash
composer require --dev phpunit/phpunit
```

Потім в `.gitlab-ci.yml` додайте:
```yaml
test:phpunit:
  stage: test
  script:
    - composer test
```

### Автоматичний деплой на Vercel/Heroku
Додайте у **CI/CD Variables**:
```
VERCEL_TOKEN    = your_token
HEROKU_API_KEY  = your_key
```

---

## 📝 Приклади для деплою

### На VPS через SSH
```yaml
deploy:production:
  script:
    - ssh -i ~/.ssh/deploy_key user@server.com "cd /app && git pull && composer install && php artisan migrate"
```

### На Docker Registry
```yaml
deploy:docker:
  script:
    - docker build -t autoparts:latest .
    - docker push registry.gitlab.com/username/autoparts:latest
```

### На AWS S3
```yaml
deploy:s3:
  script:
    - aws s3 sync ./ s3://my-bucket/autoparts/ --delete
```

---

## ✨ Бонус: Auto DevOps (GitLab Premium)

Якщо у вас план Premium, можна включити **Auto DevOps**:
- Автоматичний Docker build
- Automatic deployment to Kubernetes
- Monitoring & error tracking

---

## 🎯 Наступні кроки

1. **Закомітьте `.gitlab-ci.yml`** на ваш репо
2. **Перейдіть на GitLab** (якщо там ще проекту немає)
3. **Переконайтесь, що pipeline запустився** → CI/CD → Pipelines
4. **Додайте SSH ключі** (якщо потрібен деплой)
5. **Натисніть "Run pipeline"** вручну для деплою

---

## 📚 Корисні посилання

- [GitLab CI/CD Docs](https://docs.gitlab.com/ee/ci/)
- [.gitlab-ci.yml Reference](https://docs.gitlab.com/ee/ci/yaml/)
- [GitLab Docker Images](https://hub.docker.com/r/gitlab/gitlab-runner/)

---

**Успіхів із GitLab CI/CD! 🚀**
