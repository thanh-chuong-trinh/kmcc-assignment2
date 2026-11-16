<?php $current_page = 'index'; ?>
<!DOCTYPE html>
<!--
  index.html - Home page for Second Volt
  Author: Trinh Thanh Chuong (106392431), Group 6 (KMCC), COS10026
  Acknowledgements:
  - Shared header, footer and styles.css conventions by the team (see README.md).
  - Product and category photos are from Wikimedia Commons under Creative Commons licences. Photographers and licences are listed in the Photo credits section of this page.
  - Acknowledgement of Country wording follows the guidance linked in the assignment brief.
  - Generative AI (Claude) is being used to review and fix minor problems of the webpage 
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Second Volt sells tested, used phones and laptops with honest condition grades and a 12 month warranty.">
  <meta name="keywords" content="second hand electronics, used phones, used laptops, refurbished, Second Volt">
  <meta name="author" content="Group 6, COS10026">
  <meta name="theme-color" content="#0a6fa8">
  <link rel="icon" href="images/logo.svg" type="image/svg+xml">
  <title>Second Volt - Used Electronics</title>
  <link rel="stylesheet" href="styles/style.css">
  <!-- Embedded CSS (assignment requirement): styles for the grade table on this page only -->
  <style>
    .grade-table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
    .grade-table caption { padding-bottom: 0.5rem; font-weight: 600; text-align: left; }
    .grade-table th, .grade-table td { padding: 0.6rem 0.75rem; text-align: left; border: 1px solid var(--line); vertical-align: top; }
    .grade-table thead th { background: var(--accent-soft); }
    .table-scroll { overflow-x: auto; }
  </style>
</head>
<body>
<?php include 'header.inc'; ?>

  <main id="main">
    <!-- Hero: slogan, main heading and call to action. The background image is set in styles.css -->
    <section class="container hero" aria-labelledby="hero-title">
      <p class="slogan">Good electronics deserve a second owner.</p>
      <h1 id="hero-title">Used electronics, tested.</h1>
      <p>Phones, laptops and more at fair prices.</p>
      <a class="button" href="#phones">Shop phones</a>
    </section>

    <!-- Smartphone listings with grade and written condition notes -->
    <section class="container section" id="phones" aria-labelledby="phones-title">
      <h2 id="phones-title">Smartphones</h2>
      <p class="grade-key">Condition grades: A is like new, B shows light wear, C shows clear wear but works fully. Every phone is tested. Photos are from Wikimedia Commons (credits at the bottom of the page) and show the same model, not the exact unit.</p>
      <ul class="product-row">
        <li class="product">
          <img src="images/photos/phone-s22-ultra.jpg" width="900" height="900" alt="Black Samsung Galaxy S22 Ultra phone, back view, on a light surface">
          <h3>Samsung Galaxy S22 Ultra</h3>
          <p class="price">$433 <s>$593</s></p>
          <p class="grade"><span>Grade B</span> Good</p>
          <p class="condition">Fine scratches on the back and a small scuff on the frame. Screen is clear. Battery health 90%.</p>
        </li>
        <li class="product">
          <img src="images/photos/phone-a53.jpg" width="900" height="900" alt="Black Samsung Galaxy A53 5G phone lying on a white surface">
          <h3>Samsung Galaxy A53 5G</h3>
          <p class="price">$259 <s>$405</s></p>
          <p class="grade"><span>Grade A</span> Like new</p>
          <p class="condition">Screen and body have no marks. Battery health 94%. Original charger not included.</p>
        </li>
        <li class="product">
          <img src="images/photos/phone-a34.jpg" width="900" height="900" alt="Silver Samsung Galaxy A34 5G phone seen from the back">
          <h3>Samsung Galaxy A34 5G</h3>
          <p class="price">$189 <s>$296</s></p>
          <p class="grade"><span>Grade A</span> Like new</p>
          <p class="condition">No scratches or dents. Battery health 96%. Comes in a plain box with a new cable.</p>
        </li>
        <li class="product">
          <img src="images/photos/phone-a52.jpg" width="900" height="900" alt="White Samsung Galaxy A52 phone, front view">
          <h3>Samsung Galaxy A52</h3>
          <p class="price">$164 <s>$210</s></p>
          <p class="grade"><span>Grade B</span> Good</p>
          <p class="condition">Light scratches on the screen, visible only when it is off. Small scuff on the top corner. Battery health 88%.</p>
        </li>
      </ul>
    </section>

    <!-- Laptop listings with grade and written condition notes -->
    <section class="container section" id="laptops" aria-labelledby="laptops-title">
      <h2 id="laptops-title">Laptops</h2>
      <ul class="product-row">
        <li class="product">
          <img src="images/photos/laptop-zenbook3.jpg" width="900" height="900" alt="ASUS ZenBook 3 laptop open on a white background">
          <h3>ASUS ZenBook 3</h3>
          <p class="price">$498 <s>$799</s></p>
          <p class="grade"><span>Grade A</span> Like new</p>
          <p class="condition">12 inch ultraportable. Lid and keyboard are clean with no marks. Battery health 93%. Charger included.</p>
        </li>
        <li class="product">
          <img src="images/photos/laptop-elitebook.jpg" width="900" height="900" alt="HP EliteBook 850 G5 laptop open on a dark background">
          <h3>HP EliteBook 850 G5</h3>
          <p class="price">$412 <s>$689</s></p>
          <p class="grade"><span>Grade A</span> Like new</p>
          <p class="condition">15 inch business laptop. No scratches on the screen or body. Battery health 91%. Original box not included.</p>
        </li>
        <li class="product">
          <img src="images/photos/laptop-ideapad.jpg" width="900" height="900" alt="Silver Lenovo IdeaPad Flex 14 laptop open on a concrete floor">
          <h3>Lenovo IdeaPad Flex 14</h3>
          <p class="price">$356 <s>$620</s></p>
          <p class="grade"><span>Grade B</span> Good</p>
          <p class="condition">Light wear on the palm rest and a small dent on one corner of the lid. Screen has no dead pixels. Battery health 84%.</p>
        </li>
        <li class="product">
          <img src="images/photos/laptop-thinkpad.jpg" width="900" height="900" alt="IBM ThinkPad R51 laptop open, showing a web page on the screen">
          <h3>IBM ThinkPad R51</h3>
          <p class="price">$98 <s>$170</s></p>
          <p class="grade"><span>Grade C</span> Fair</p>
          <p class="condition">Older model with worn keys and scratches on the lid. Screen and ports work fully. Battery health 76%, so expect about 3 hours.</p>
        </li>
      </ul>
    </section>

    <!-- Category tiles that link to the product sections -->
    <section class="container section" id="categories" aria-labelledby="cat-title">
      <h2 id="cat-title">Categories</h2>
      <ul class="category-row">
        <li><a href="#phones"><img src="images/photos/phone-a53.jpg" width="900" height="600" alt="">Mobile</a></li>
        <li><a href="#laptops"><img src="images/photos/laptop-zenbook3.jpg" width="900" height="600" alt="">Laptops</a></li>
        <li><span><img src="images/photos/cat-camera.jpg" width="900" height="600" alt="">Cameras</span></li>
        <li><span><img src="images/photos/cat-audio.jpg" width="900" height="600" alt="">Audio</span></li>
      </ul>
    </section>

    <!-- About the company: purpose and identity -->
    <section class="container section about" id="about-us" aria-labelledby="about-title">
      <div>
        <h2 id="about-title">About Second Volt</h2>
        <p>Second Volt is a student-run store that tests, grades and resells used phones and laptops. Every device is checked by hand, wiped, and described honestly, so you know the condition before you buy.</p>
        <p>We keep good electronics out of landfill and offer them at fair prices, with a 12 month warranty on every order.</p>
      </div>
      <img src="images/photos/about-xps.jpg" width="900" height="600" alt="Two Dell XPS laptops open side by side on a white shelf">
    </section>

    <!-- Merged-cell table (rowspan and colspan) explaining condition grades and warranty -->
    <section class="container section" id="grades" aria-labelledby="grades-title">
      <h2 id="grades-title">Condition grades and warranty</h2>
      <div class="table-scroll">
        <table class="grade-table">
          <caption>How each grade is priced and covered</caption>
          <thead>
            <tr>
              <th rowspan="2" scope="col">Grade</th>
              <th rowspan="2" scope="col">What it means</th>
              <th colspan="2" scope="colgroup">Typical saving against new</th>
              <th rowspan="2" scope="col">Warranty</th>
            </tr>
            <tr>
              <th scope="col">Phones</th>
              <th scope="col">Laptops</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">A</th>
              <td>Like new, no marks you can see</td>
              <td>36%</td>
              <td>38% to 40%</td>
              <td rowspan="3">12 months. Covers faults and battery failure, not accidental damage.</td>
            </tr>
            <tr>
              <th scope="row">B</th>
              <td>Light scratches or scuffs, fully working</td>
              <td>22% to 27%</td>
              <td>43%</td>
            </tr>
            <tr>
              <th scope="row">C</th>
              <td>Clear wear, fully working</td>
              <td>Not stocked</td>
              <td>42%</td>
            </tr>
            <tr>
              <!-- Inline CSS (assignment requirement): small note text -->
              <td colspan="5" style="font-size: 0.85rem; color: #57534e;">Savings are based on the current listings above and change as stock changes.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Careers call to action that links to the Apply page -->
    <section class="container section" id="careers" aria-labelledby="careers-title">
      <div class="careers">
        <div>
          <h2 id="careers-title">Work with us</h2>
          <p>Second Volt is hiring a Front-end Developer and a UX Designer</p>
        </div>
        <a class="button" href="apply.php">Apply now</a>
      </div>
    </section>
    <!-- Photo credits required by the Creative Commons licences -->
    <section class="container section credits" id="credits" aria-labelledby="credits-title">
      <h2 id="credits-title">Photo credits</h2>
      <ul>
        <li>Samsung Galaxy S22 Ultra: <a href="https://commons.wikimedia.org/wiki/File:SAMSUNG_Galaxy_S22_Ultra_BLACK_(9).jpg">photo</a> by Dinkun Chen, <a href="https://creativecommons.org/licenses/by-sa/4.0">CC BY-SA 4.0</a>, via Wikimedia Commons. Resized and cropped.</li>
        <li>Samsung Galaxy A53 5G: <a href="https://commons.wikimedia.org/wiki/File:Galaxy-A53-5G_02.jpg">photo</a> by RuinDig/Yuki Uchida, <a href="https://creativecommons.org/licenses/by/4.0">CC BY 4.0</a>, via Wikimedia Commons. Resized and cropped.</li>
        <li>Samsung Galaxy A34 5G: <a href="https://commons.wikimedia.org/wiki/File:Samsung_Galaxy_A34_5G_20231122_HOF01948-HDR_RAW-Export_cens.png">photo</a> by PantheraLeo1359531, <a href="https://creativecommons.org/licenses/by/4.0">CC BY 4.0</a>, via Wikimedia Commons. Resized and cropped.</li>
        <li>Samsung Galaxy A52: <a href="https://commons.wikimedia.org/wiki/File:Samsung-Galaxy-A52-White.jpg">photo</a> by OnoMaker 12345, <a href="https://creativecommons.org/licenses/by-sa/4.0">CC BY-SA 4.0</a>, via Wikimedia Commons. Resized and cropped.</li>
        <li>ASUS ZenBook 3: <a href="https://commons.wikimedia.org/wiki/File:ASUS_ZenBook_3_UX390.jpg">photo</a> by Sinchen.Lin, <a href="https://creativecommons.org/licenses/by/2.0">CC BY 2.0</a>, via Wikimedia Commons. Resized and cropped.</li>
        <li>HP EliteBook 850 G5: <a href="https://commons.wikimedia.org/wiki/File:HP_EliteBook_850_G5.png">photo</a> by Infinatious, <a href="https://creativecommons.org/licenses/by-sa/4.0">CC BY-SA 4.0</a>, via Wikimedia Commons. Resized and cropped.</li>
        <li>Lenovo IdeaPad Flex 14: <a href="https://commons.wikimedia.org/wiki/File:Lenovo_IdeaPad_Flex14_laptop.jpg">photo</a> by Info-farmer, <a href="http://creativecommons.org/publicdomain/zero/1.0/deed.en">CC0</a>, via Wikimedia Commons. Resized and cropped.</li>
        <li>IBM ThinkPad R51: <a href="https://commons.wikimedia.org/wiki/File:IBM_Thinkpad_R51.jpg">photo</a> by André Karwath aka Aka, <a href="https://creativecommons.org/licenses/by-sa/4.0">CC BY-SA 4.0</a>, via Wikimedia Commons. Resized and cropped.</li>
        <li>Dell XPS laptops: <a href="https://commons.wikimedia.org/wiki/File:DELL_XPS_13_and_15_(37080596413).jpg">photo</a> by Tinh tế Photo, <a href="http://creativecommons.org/publicdomain/zero/1.0/deed.en">CC0</a>, via Wikimedia Commons. Resized and cropped.</li>
        <li>Sony Alpha 7R IV camera: <a href="https://commons.wikimedia.org/wiki/File:Sony_Alpha_7R_IV_elevated_front_view_of_camera_body_with_exposed_sensor.jpg">photo</a> by Franz van Duns, <a href="https://creativecommons.org/licenses/by-sa/4.0">CC BY-SA 4.0</a>, via Wikimedia Commons. Resized and cropped.</li>
        <li>Bose QuietComfort 25 headphones: <a href="https://commons.wikimedia.org/wiki/File:Bose_QuietComfort_25_Acoustic_Noise_Cancelling_Headphones_with_Carry_Case.jpg">photo</a> by Florian Fuchs, <a href="https://creativecommons.org/licenses/by-sa/3.0">CC BY-SA 3.0</a>, via Wikimedia Commons. Resized and cropped.</li>
      </ul>
    </section>
  </main>

<?php include 'footer.inc'; ?>
</body>
</html>
