<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<section class="hero position-relative text-white text-center">
  <div class="overlay position-absolute top-0 start-0 w-100 h-100"
    style="background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.7)); z-index:1;"></div>

  <div class="container position-relative py-5" style="z-index:1;">
    <h1 class="fw-bold display-5 mb-3">AutoParts — Все для вашого авто в одному місці</h1>
    <p class="lead mb-4 mx-auto" style="max-width: 800px;">
      Широкий вибір запчастин: оригінальні та якісні аналоги, швидка доставка по всій Україні та зручний пошук за маркою, моделлю або VIN.
    </p>

    <div class="d-flex justify-content-center gap-4 flex-wrap mb-4">
      <div class="feature-item text-center">
        <i class="bi bi-truck fs-1 mb-2 text-warning"></i>
        <p class="mb-0">Доставка 24/7</p>
      </div>
      <div class="feature-item text-center">
        <i class="bi bi-award fs-1 mb-2 text-warning"></i>
        <p class="mb-0">Гарантія якості</p>
      </div>
      <div class="feature-item text-center">
        <i class="bi bi-headset fs-1 mb-2 text-warning"></i>
        <p class="mb-0">Підтримка онлайн</p>
      </div>
      <div class="feature-item text-center">
        <i class="bi bi-search fs-1 mb-2 text-warning"></i>
        <p class="mb-0">Швидкий пошук</p>
      </div>
    </div>

    <a href="#" class="btn btn-outline-light btn-lg fw-semibold px-4">Дізнатись більше</a>
  </div>

  <img src="https://t4.ftcdn.net/jpg/03/80/98/93/240_F_380989347_IKOXAkY4e3pYmCyIrKSngo48EZhLFYDO.jpg" alt="Hero background"
    class="position-absolute top-0 start-0 w-100 h-100"
    style="object-fit: cover; z-index:0;">
</section>

<div class="search-module container position-relative mt-n4 z-2">
  <form class="search-form d-flex align-items-center justify-content-between gap-2 p-3 rounded-4 shadow-sm bg-white bg-opacity-90">

    <select class="form-select flex-fill" style="min-width: 140px;">
      <option selected disabled>Марка авто</option>
      <option>BMW</option>
      <option>Audi</option>
      <option>Mercedes</option>
    </select>

    <select class="form-select flex-fill" style="min-width: 140px;">
      <option selected disabled>Модель авто</option>
      <option>3 Series</option>
      <option>5 Series</option>
      <option>A4</option>
      <option>C-Class</option>
    </select>

    <select class="form-select flex-fill" style="min-width: 130px;">
      <option selected disabled>Рік</option>
      <option>2025</option>
      <option>2024</option>
      <option>2023</option>
    </select>

    <input type="text" class="form-control flex-grow-1" placeholder="VIN або артикул">
    <button type="submit" class="btn btn-warning fw-semibold px-4">Пошук</button>
  </form>
</div>

<section class="py-5 bg-light">
  <div class="container">
    <h2 class="fw-bold mb-4 text-center">Обери марку свого авто</h2>

    <div class="row g-4 justify-content-center">
      <?php
      $brands = [
        'BMW' => 'https://upload.wikimedia.org/wikipedia/commons/f/f4/BMW_logo_%28gray%29.svg',
        'Audi' => 'https://upload.wikimedia.org/wikipedia/commons/9/92/Audi-Logo_2016.svg',
        'Mercedes-Benz' => 'https://upload.wikimedia.org/wikipedia/commons/9/90/Mercedes-Logo.svg',
        'Toyota' => 'https://upload.wikimedia.org/wikipedia/commons/9/9d/Toyota_carlogo.svg',
        'Volkswagen' => 'https://upload.wikimedia.org/wikipedia/commons/6/6d/Volkswagen_logo_2019.svg',
        'Ford' => 'https://upload.wikimedia.org/wikipedia/commons/3/3e/Ford_logo_flat.svg',
        'Honda' => 'https://upload.wikimedia.org/wikipedia/commons/1/12/Honda_Canada.webp',
        'Nissan' => 'https://upload.wikimedia.org/wikipedia/commons/1/15/Nissan_Logo_%281998-2001%29.png',
        'Mazda' => 'https://upload.wikimedia.org/wikipedia/commons/b/b6/Mazda_logo_with_emblem%2C_new.svg',
        'Hyundai' => 'https://upload.wikimedia.org/wikipedia/commons/4/44/Hyundai_Motor_Company_logo.svg',
        'Kia' => 'https://upload.wikimedia.org/wikipedia/commons/c/c4/KIA-Logo-1994-2012.png',
        'Renault' => 'https://upload.wikimedia.org/wikipedia/commons/4/44/Renault_1990.svg',
        'Peugeot' => 'https://logos-world.net/wp-content/uploads/2021/10/Peugeot-Logo.png',
        'Citroen' => 'https://upload.wikimedia.org/wikipedia/commons/0/0b/Citroen-logo-2009.png',
        'Opel' => 'https://upload.wikimedia.org/wikipedia/commons/7/70/Opel_Logo_2021.svg',
        'Skoda' => 'https://upload.wikimedia.org/wikipedia/commons/0/09/%C5%A0koda_nieuw.png',
        'Volvo' => 'https://upload.wikimedia.org/wikipedia/commons/b/b9/Volvo_Trucks_%26_Bus_logo.jpg',
        'Chevrolet' => 'https://upload.wikimedia.org/wikipedia/ru/thumb/7/7f/Chevrolet_new_logo.png/250px-Chevrolet_new_logo.png',
        'Mitsubishi' => 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Mitsubishi_motors_new_logo.svg',
        'Subaru' => 'https://upload.wikimedia.org/wikipedia/commons/c/ca/Subaru_logo_%28transparent%29.svg',
        'Lexus' => 'https://upload.wikimedia.org/wikipedia/commons/7/75/Lexus.svg',
        'Fiat' => 'https://upload.wikimedia.org/wikipedia/uk/thumb/7/7b/Fiat_Логотип.svg/320px-Fiat_Логотип.svg.png',
        'Jeep' => 'https://upload.wikimedia.org/wikipedia/commons/0/0d/Jeep_logo.svg',
        'Suzuki' => 'https://upload.wikimedia.org/wikipedia/commons/b/b9/Suzuki_logo_2025.svg',
        'Porsche' => 'https://porsche.ua/manifest/android-icon-192x192.png'
      ];

      $fallback = 'https://gavalimotors.com/adminpanel/assets/images/carnotfound.jpg';

      foreach ($brands as $name => $logoUrl):
        $imgSrc = !empty($logoUrl) ? $logoUrl : $fallback;
      ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <div class="card border-0 shadow-sm text-center brand-card h-100 p-3 hover-shadow d-flex flex-column align-items-center justify-content-center">
            <img src="<?= $imgSrc ?>" alt="<?= $name ?>"
              class="img-fluid mb-2"
              style="max-height: 60px; object-fit: contain;">
            <h6 class="fw-semibold mb-0"><?= $name ?></h6>
          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <h2 class="fw-bold mb-4 text-center">Категорії запчастин</h2>

    <div class="row g-4 d-flex justify-content-center align-items-center">
      <?php
      $categories = [
        ['name' => 'Двигун', 'img' => 'assets/img/engine.jpg'],
        ['name' => 'Гальмівна система', 'img' => 'https://5.imimg.com/data5/SELLER/Default/2023/9/345366707/DE/WZ/LB/1820704/brake-pads-for-caliper-disc-brakes-1000x1000.jpg'],
        ['name' => 'Підвіска', 'img' => 'https://t2.gstatic.com/licensed-image?q=tbn:ANd9GcQX2TH4BKXbF5uhHFCrM55bwqP6cIp6oEDcad6mPOSB_wQxVZ9ds8UcTQI4RJqr-ifBqVWqw5K1YwxF7iVxBZg'],
        ['name' => 'Електроніка', 'img' => 'https://assets.denso-am.eu/eyJidWNrZXQiOiJhc3NldHMuZGVuc28tYW0uY29tIiwia2V5IjoicHJvZHVjdGlvbi9wcm9kdWN0cy9BbGwtUk9UQVRJTkcvQWx0ZXJuYXRvci5qcGciLCJlZGl0cyI6eyJqcGVnIjp7InF1YWxpdHkiOjEwMCwicHJvZ3Jlc3NpdmUiOmZhbHNlLCJ0cmVsbGlzUXVhbnRpc2F0aW9uIjp0cnVlLCJvdmVyc2hvb3REZXJpbmdpbmciOnRydWUsIm9wdGltaXplU2NhbnMiOnRydWV9LCJyZXNpemUiOnsid2lkdGgiOjc0NCwiZml0IjoiY292ZXIifSwic2hhcnBlbiI6dHJ1ZX19'],
        ['name' => 'Трансмісія', 'img' => 'https://img.freepik.com/premium-photo/vehicle-gearbox-white-background_431161-30274.jpg'],
        ['name' => 'Кузовні деталі', 'img' => 'https://avtokolya.com.ua/images/shop/category/resized/kuzov_380x380.jpg'],
        ['name' => 'Інструменти та аксесуари', 'img' => 'https://i.pinimg.com/736x/e8/db/7b/e8db7bb6340c77440ee9f74b1c08b475.jpg'],
      ];
      foreach ($categories as $cat):
      ?>
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card border-0 shadow-sm category-card overflow-hidden h-100">
            <div class="ratio ratio-1x1">
              <img src="<?= $cat['img'] ?>" alt="<?= $cat['name'] ?>" class="w-100 h-100 object-fit-contain">
            </div>
            <div class="card-body text-center bg-white">
              <h6 class="fw-semibold mb-0"><?= $cat['name'] ?></h6>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>