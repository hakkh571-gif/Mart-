<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root {
            --primary-color: #DE1E4F;
            --text-dark: #1C1C1C;
            --text-muted: #8B96A5;
            --bg-light: #F7F7F7;
            --border-color: #E3E8EE;
            --font-sans: 'Inter', sans-serif;
            --font-serif: 'Playfair Display', serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {

            color: var(--text-dark);
            background-color: #FFFFFF;
            line-height: 1.5;
            overflow-x: hidden;
        }

        /* Top Utility Bar */
        .top-bar {
            background-color: #FFFFFF;
            border-bottom: 1px solid var(--border-color);
            padding: 10px 5%;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #505050;
        }

        .top-bar-left,
        .top-bar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .top-bar i {
            color: var(--text-muted);
            margin-right: 5px;
        }

        /* Main Header */
        header {
            padding: 20px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            gap: 15px;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .logo i {
            background-color: var(--primary-color);
            color: #fff;
            padding: 8px;
            border-radius: 8px;
            font-size: 18px;
        }

        /* Category Button */
        /* ប៊ូតុង ការបញ្ចុះតម្លៃ ជាមួយនឹងចលនាវិលឆ្វេងស្ដាំ */
        .btn-categories {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-family: var(--font-sans);
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            white-space: nowrap;

            /* កំណត់ឱ្យរត់ចលនាវិលផ្អៀងឆ្វេងស្ដាំ (tiltEffect) រៀងរាល់ 1.5 វិនាទីម្ដង */
            animation: tiltEffect 1.5s ease-in-out infinite;
            transform-origin: center;
            /* កំណត់ឱ្យវិលចេញពីចំណុចកណ្ដាលនៃប៊ូតុង */
        }

        /* បង្កើតចលនាវិលផ្អៀងទៅឆ្វេង និងទៅស្ដាំ (Tilt / Swing Animation) */
        @keyframes tiltEffect {

            0%,
            100% {
                transform: rotate(0deg);
                /* ត្រង់ធម្មតា */
            }

            15% {
                transform: rotate(-15deg);
                /* វិលផ្អៀងទៅឆ្វេង */
            }

            30% {
                transform: rotate(12deg);
                /* វិលផ្អៀងទៅស្ដាំ */
            }

            45% {
                transform: rotate(-10deg);
                /* វិលផ្អៀងទៅឆ្វេងបន្តិច */
            }

            60% {
                transform: rotate(8deg);
                /* វិលផ្អៀងទៅស្ដាំបន្តិច */
            }

            75% {
                transform: rotate(-4deg);
                /* វិលផ្អៀងទៅឆ្វេងតិចៗ */
            }

            90% {
                transform: rotate(3deg);
                /* វិលផ្អៀងទៅស្ដាំតិចៗ */
            }
        }

        /* ពេលយក Mouse ទៅដាក់ពីលើ វានឹងឈប់វិល ដើម្បីងាយស្រួលចុច */
        .btn-categories:hover {
            animation-play-state: paused;
            background-color: #be133e;
            transform: rotate(0deg);
            /* ឱ្យវាត្រង់មកវិញពេលរៀបចុច */
        }

        /* Search Bar */
        .search-container {
            flex-grow: 1;
            max-width: 600px;
            min-width: 250px;
            display: flex;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            overflow: hidden;
        }

        .search-container input {
            width: 100%;
            border: none;
            padding: 10px 15px;
            font-size: 14px;
            outline: none;
        }

        .search-container button {
            background: none;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            color: #505050;
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            background: var(--bg-light);
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }


        /* Main Container - កែប្រែត្រង់នេះឱ្យរីកធំស្មើ Header */
        .container {
            width: 100%;
            max-width: 100%;
            margin: 30px auto;
            padding: 0 5%;
            /* រក្សាគែម 5% ស្មើគ្នាគ្រប់ទំហំអេក្រង់ */
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, #FFB5C5 0%, #FFA4B4 100%);
            border-radius: 12px;
            padding: 50px 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            min-height: 380px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .hero-content {
            max-width: 50%;
            z-index: 2;
        }

        .hero-title {

            font-size: clamp(28px, 4vw, 46px);
            color: var(--text-dark);
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .hero-subtitle {
            font-size: 16px;
            color: #3F3F3F;
            margin-bottom: 25px;
        }

        .hero-btn {
            background: #fff;
            color: var(--text-dark);
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* Shoes Image & Discount Badge */
        .hero-image-wrapper {
            position: relative;
            z-index: 2;
        }

        .hero-img {
            max-height: 300px;
            object-fit: contain;
        }

        .discount-badge {
            background-color: #E25193;
            color: white;
            font-weight: bold;
            font-size: 24px;
            padding: 8px 18px;
            border-radius: 8px;
            position: absolute;
            bottom: 30px;
            left: -50px;
            transform: rotate(-15deg);
        }

        /* Slider Controls */
        .slider-btn {
            background: rgba(255, 255, 255, 0.8);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 3;
        }

        .slider-btn.prev {
            left: 20px;
        }

        .slider-btn.next {
            right: 20px;
        }

        /* Hot Categories Section */
        .categories-container {
            position: relative;
            margin-bottom: 40px;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            /* ឱ្យរត់ពេញ 7 ជួរស្មើគ្នាលើអេក្រង់ធំ */
            gap: 15px;
        }

        .category-card {
            background-color: var(--bg-light);
            border-radius: 10px;
            padding: 15px;
            height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            border: 1px solid transparent;
            transition: border-color 0.2s;
        }

        .category-card:hover {
            border-color: #ccc;
        }

        .category-name {
            font-size: 13px;
            font-weight: 500;
            color: #1C1C1C;
        }


        /* Section Heading */
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            /* 5 ជួរស្មើពេញអេក្រង់ធំ */
            gap: 20px;
        }

        .product-card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            position: relative;
            background: #fff;
            transition: box-shadow 0.2s;
        }

        .product-card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .btn-wishlist {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #fff;
            border: 1px solid var(--border-color);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            z-index: 2;
        }

        .product-img-wrapper {
            background-color: var(--bg-light);
            border-radius: 8px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .product-img {
            max-height: 85%;
            max-width: 85%;
            object-fit: contain;
        }

        .product-title {
            font-size: 14px;
            font-weight: 500;
            color: #505050;
            margin-bottom: 8px;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .rating-reviews {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .rating-reviews i {
            color: #FF9017;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .product-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .btn-cart {
            background: none;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #0D6EFD;
        }

        .category-next-btn {
            position: absolute;
            right: -15px;
            top: 50%;
            transform: translateY(-50%);
            background: #fff;
            border: 1px solid var(--border-color);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 5;
        }

        /* ==========================================
           RESPONSIVE MEDIA QUERIES
           ========================================== */

        @media (max-width: 1200px) {
            .categories-grid {
                grid-template-columns: repeat(5, 1fr);
            }

            .products-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 992px) {
            .nav-menu {
                justify-content: flex-start;
            }

            .categories-grid {
                display: flex;
                overflow-x: auto;
            }

            .category-card {
                min-width: 140px;
                flex: 1 0 auto;
            }

            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .category-next-btn {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .top-bar {
                display: none;
            }

            header {
                padding: 15px 5%;
            }

            .search-container {
                order: 3;
                width: 100%;
                max-width: 100%;
            }

            .logo {
                order: 1;
            }

            .btn-categories {
                order: 2;
            }

            .header-actions {
                order: 4;
                width: 100%;
                justify-content: space-between;
            }

            .action-btn {
                flex: 1;
                justify-content: center;
            }

            .hero-banner {
                flex-direction: column-reverse;
                text-align: center;
                padding: 30px 20px;
                min-height: auto;
                gap: 20px;
            }

            .hero-content {
                max-width: 100%;
            }

            .hero-img {
                max-height: 200px;
            }

            .discount-badge {
                left: auto;
                right: -10px;
                bottom: 10px;
            }

            .slider-btn {
                display: none;
            }

            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
        }

        .hero-img {
            transition: opacity 0.3s ease-in-out;
        }

        /* --- FOOTER STYLES --- */
        .site-footer {
            background-color: #f8f9fa;
            color: #333;
            font-family: inherit;
            margin-top: 50px;
            border-top: 1px solid #e5e5e5;
        }

        /* 1. Benefits Bar */
        .footer-features {
            background-color: #ffffff;
            padding: 25px 0;
            border-bottom: 1px solid #eee;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .feature-item i {
            font-size: 28px;
            color: #ff4757;
            /* ពណ៌ Highlight អាចដូរតាម Brand បាន */
        }

        .feature-item h4 {
            margin: 0 0 3px 0;
            font-size: 15px;
        }

        .feature-item p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        /* 2. Main Footer Links */
        .footer-main {
            padding: 50px 0;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .footer-logo {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 15px;
        }

        .footer-desc {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .social-links {
            display: flex;
            gap: 10px;
        }

        .social-links a {
            width: 35px;
            height: 35px;
            background-color: #e9ecef;
            color: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background-color: #ff4757;
            color: #fff;
        }

        .footer-col h3 {
            font-size: 16px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 8px;
        }

        .footer-col h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30px;
            height: 2px;
            background-color: #ff4757;
        }

        .footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-col ul li {
            margin-bottom: 10px;
        }

        .footer-col ul li a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .footer-col ul li a:hover {
            color: #ff4757;
        }

        .contact-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
            font-size: 13px;
        }

        .contact-list i {
            color: #ff4757;
        }

        /* 3. Bottom Bar */
        .footer-bottom {
            background-color: #222;
            color: #aaa;
            padding: 15px 0;
            font-size: 13px;
        }

        .bottom-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .payment-methods {
            display: flex;
            gap: 15px;
            font-size: 24px;
            color: #ccc;
        }

        /* Font សម្រាប់ចំណងជើងធំ (Title) ឱ្យមើលទៅលេចធ្លោបែបបុរាណ/ប្រណីត */
        .slide-title {
            font-family: Bayon, "Fallback Outline";
            font-size: 2.5rem;
            line-height: 1.4;
            color: #2c1810;
            /* ពណ៌ត្នោតកាហ្វេ */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Font សម្រាប់ចំណងជើងរង (Subtitle) */
        .slide-subtitle {
            font-family: 'Kh Muol', sans-serif;
            font-weight: 400;
            font-size: 1.1rem;
            color: #6f4e37;
        }

        /* 🎯 កំណត់ទំហំរូបភាពឱ្យស្មើគ្នានៅត្រង់នេះ */
        .category-img {
            width: 100%;
            /* កំណត់ទទឹងថេរ */
            height: 100%;
            /* កំណត់កម្ពស់ថេរ */
            object-fit: cover;
            /* ធ្វើឱ្យរូបពេញប្រអប់ដោយមិនខូចរាង (ឬប្រើ contain បើចង់ឃើញរូបទាំងមូល) */
            border-radius: 8px;
            /* ជ្រុងកោងបន្តិច (Optional) */
        }
    </style>
</head>
<body>
     <div class="container">
         <div class="products-grid">
    @foreach ($foods as $food)
            <div class="product">
                <div class="product-title">{{ $food->name }}</div>
            
               
                <span class="product-price">{{ $food->price }} USD</span>
                    
            </div>
    @endforeach
    </div>
    </div>
</body>
</html>