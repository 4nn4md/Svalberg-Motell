<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Svalberg Motel</title>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/header1.php"); ?>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Custom Styles -->
    <link href="http://localhost/Svalberg-Motell/www/assets/css/styles1.css" rel="stylesheet">

    <!-- Google Fonts for Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">

     <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }

        header {
            background: url('https://source.unsplash.com/1600x900/?nature,sea,coast') center center no-repeat;
            background-size: cover;
            padding: 100px 20px;
            color: white;
            text-align: center;
        }

        header h1 {
            font-size: 3rem;
            margin: 0;
        }

        section {
            padding: 60px 20px;
        }

        h2, h3 {
            font-family: 'Open Sans', sans-serif;
            color: #007BFF;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .content-section p {
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .services-list {
            list-style-type: none;
            padding-left: 0;
        }

        .services-list li {
            background: #e9ecef;
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            font-size: 1.1rem;
        }

        .contact-info {
            font-size: 1.1rem;
            line-height: 1.6;
        }

    </style>
</head>

<body>

    <header>
        <h1 style="color: #5E5E5E;">Welcome to Svalberg Motel</h1>
        <p style="color: #5E5E5E;">Your peaceful retreat by the sea and forest</p>
    </header>

    <main>
        <!-- About Us Section -->
        <section id="about">
            <div class="section-title">
                <h2>About Us</h2>
                <p>Discover the serenity of Svalberg Motel, your perfect escape in nature.</p>
            </div>
            <div class="content-section">
                <p>Svalberg is more than just a place to stay – it's a haven surrounded by natural beauty. Located between the gentle waves of the beach and lush green hiking trails, Svalberg offers the perfect combination of relaxation and adventure. Here, the tranquility of the sea meets the freshness of the forest air, creating a perfect balance for your getaway.</p>

                <h3>A Peaceful Oasis by the Coast</h3>
                <p>Imagine waking up to the sound of the waves crashing on the shore and stepping out to explore long coastal trails. Whether you choose to spend your day with your feet in the sand or hike through the beautiful forest trails, Svalberg is the perfect base for your coastal adventures.</p>

                <h3>Modern Comfort in Natural Surroundings</h3>
                <p>Enjoy modern comforts in rooms inspired by the surrounding nature. Each room is designed with serenity in mind, featuring natural tones, large windows, and an inviting atmosphere. After a day of exploring, relax in our outdoor spaces with a stunning view of the coast, or unwind in our cozy communal areas.</p>

                <h3>Experiences for Body and Soul</h3>
                <p>Svalberg is the ideal place to find balance between activity and relaxation. Start your day with a refreshing walk along the beach or in the forest, and end it by enjoying a sunset over the ocean. For those seeking more adventure, we offer local activities such as cycling, kayaking, and guided tours to help you explore even more of the area.</p>

                <p>Welcome to Svalberg – your home by the sea and mountains. Here, peace, nature, and genuine hospitality are at the heart of everything we do. Whether you're here to relax, explore, or simply be close to nature, you will find a place where time slows down, and the soul finds rest. We look forward to welcoming you to Svalberg!</p>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>We offer a variety of services to make your stay even more comfortable.</p>
            </div>
            <ul class="services-list">
                <li><strong>Free Wi-Fi:</strong> Stay connected during your visit.</li>
                <li><strong>Free Parking:</strong> Convenient and secure parking spaces for our guests.</li>
                <li><strong>Outdoor Area with Sea View:</strong> Relax and unwind while enjoying the stunning views of the coast.</li>
            </ul>
        </section>

        <!-- Contact Section -->
        <section id="contact">
            <div class="section-title">
                <h2>Contact Us</h2>
                <p>Have questions or need more information? Get in touch with us today!</p>
            </div>
            <div class="contact-info">
                <p>Email: <a href="mailto:contact@svalberg.no">contact@svalberg.no</a></p>
                <p>Phone: +47 123 45 678</p>
            </div>
        </section>
    </main>

     <!-- Footer -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Svalberg-Motell/www/assets/inc/footer1.php"); ?>
</body>

</html>
