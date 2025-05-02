<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>منصة المصورين</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            /* Basic RTL support and custom styles */
            body {
                direction: rtl;
                font-family: 'Cairo', sans-serif; /* Example Arabic font */
            }

            /* Add custom styles based on the image design here */
            .btn-primary {
                background-color: #D97706; /* Orange color from image */
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: 0.375rem;
                font-weight: 600;
            }

            .btn-secondary {
                background-color: white;
                color: #D97706;
                border: 1px solid #D97706;
                padding: 0.75rem 1.5rem;
                border-radius: 0.375rem;
                font-weight: 600;
            }

            .search-box {
                background-color: white;
                padding: 2rem;
                border-radius: 0.5rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                display: flex;
                gap: 1rem;
                align-items: center;
            }

            .photographer-card {
                background-color: white;
                border-radius: 0.5rem;
                overflow: hidden;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }

            .photographer-card img {
                width: 100%;
                height: 200px; /* Adjust as needed */
                object-fit: cover;
            }

            .photographer-card .info {
                padding: 1rem;
            }

            /* Add more specific styles as needed */

            /* Tailwind overrides or additional base styles if needed */
            /* Note: The body font-family is already set above, keeping this comment for context */
        </style>

    </head>
    <body class="antialiased bg-gray-100">
        <div class="container mx-auto px-4">

            @if (Route::has('login'))
                <div class="sm:fixed sm:top-0 sm:left-0 p-6 text-left z-10">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">لوحة التحكم</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">تسجيل الدخول</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">تسجيل</a>
                        @endif
                    @endauth
                </div>
            @endif

            <!-- Header Section -->
            <header class="bg-[#FFF9F0] py-16 md:py-24 mt-16 rounded-lg">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div class="text-center md:text-right">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4">منصة لعرض أعمال المصورين وحجزهم</h1>
                        <p class="text-gray-600 mb-8">اكتشف أفضل المصورين المحترفين في مجالات متعددة واحجز خدماتهم بسهولة وأمان</p>
                        <div class="flex justify-center md:justify-start gap-4">
                            <a href="#" class="btn-primary hover:bg-orange-700 transition duration-300">استعرض المصورين</a>
                            <a href="#" class="btn-secondary hover:bg-gray-50 transition duration-300">ابدأ الآن</a>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <img src="https://via.placeholder.com/400x300.png?text=Photographer+Image" alt="Photographer taking a picture" class="rounded-lg shadow-lg max-w-full h-auto align-middle border-none">
                    </div>
                </div>
            </header>

            <!-- Search Section -->
            <section id="search-section" class="my-16">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">ابحث عن المصور المناسب</h2>
                <div class="max-w-3xl mx-auto search-box flex flex-col md:flex-row items-center justify-center gap-4 p-6 md:p-8">
                    <div class="flex-1 w-full md:w-auto">
                        <label for="specialty" class="block text-sm font-medium text-gray-700 mb-1">التخصص</label>
                        <select id="specialty" name="specialty" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                            <option>اختر التخصص</option>
                            <option>تصوير زفاف</option>
                            <option>تصوير بورتريه</option>
                            <option>تصوير منتجات</option>
                            <!-- Add more options here -->
                        </select>
                    </div>
                    <div class="flex-1 w-full md:w-auto">
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">المدينة</label>
                        <select id="city" name="city" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                            <option>اختر المدينة</option>
                            <option>الرياض</option>
                            <option>جدة</option>
                            <option>الدمام</option>
                            <!-- Add more options here -->
                        </select>
                    </div>
                    <button type="submit" class="btn-primary w-full md:w-auto hover:bg-orange-700 transition duration-300 mt-4 md:mt-0 md:self-end py-2">بحث <span class="ml-2">&#x1F50D;</span></button>
                </div>
            </section>

            <!-- Gallery Section -->
            <section id="gallery-section" class="my-16">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">أعمال مميزة من مصورينا</h2>
                <p class="text-center text-gray-600 mb-12">استعرض مجموعة من أفضل الأعمال المقدمة من مصورينا المحترفين في مختلف المجالات</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Photographer Card 1 -->
                    <div class="photographer-card">
                        <img src="https://via.placeholder.com/350x200.png?text=Wedding+Photo" alt="أعمال المصور أحمد محمد">
                        <div class="info">
                            <div class="flex items-center mb-2">
                                <img src="https://via.placeholder.com/40x40.png?text=A" alt="أحمد محمد" class="w-10 h-10 rounded-full ml-3 object-cover">
                                <div>
                                    <h3 class="font-semibold text-gray-800">أحمد محمد</h3>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span class="text-yellow-500">★★★★☆</span> <span class="mr-1">(4.5)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500 mb-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                الرياض
                            </div>
                            <a href="#" class="text-sm text-orange-600 hover:text-orange-800 font-semibold">عرض الملف</a>
                        </div>
                    </div>

                    <!-- Photographer Card 2 -->
                    <div class="photographer-card">
                        <img src="https://via.placeholder.com/350x200.png?text=Portrait+Photo" alt="أعمال المصورة سارة عبدالك">
                        <div class="info">
                            <div class="flex items-center mb-2">
                                <img src="https://via.placeholder.com/40x40.png?text=S" alt="سارة عبدالك" class="w-10 h-10 rounded-full ml-3 object-cover">
                                <div>
                                    <h3 class="font-semibold text-gray-800">سارة عبدالك</h3>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span class="text-yellow-500">★★★★★</span> <span class="mr-1">(5.0)</span>
                                    </div>
                                </div>
                            </div>
                             <div class="text-sm text-gray-500 mb-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                جدة
                            </div>
                            <a href="#" class="text-sm text-orange-600 hover:text-orange-800 font-semibold">عرض الملف</a>
                        </div>
                    </div>

                    <!-- Photographer Card 3 -->
                    <div class="photographer-card">
                        <img src="https://via.placeholder.com/350x200.png?text=Product+Photo" alt="أعمال المصور خالد المصري">
                        <div class="info">
                            <div class="flex items-center mb-2">
                                <img src="https://via.placeholder.com/40x40.png?text=K" alt="خالد المصري" class="w-10 h-10 rounded-full ml-3 object-cover">
                                <div>
                                    <h3 class="font-semibold text-gray-800">خالد المصري</h3>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span class="text-yellow-500">★★★★☆</span> <span class="mr-1">(4.0)</span>
                                    </div>
                                </div>
                            </div>
                             <div class="text-sm text-gray-500 mb-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                الدمام
                            </div>
                            <a href="#" class="text-sm text-orange-600 hover:text-orange-800 font-semibold">عرض الملف</a>
                        </div>
                    </div>
                    <!-- Add more cards as needed -->
                </div>

                <div class="text-center mt-12">
                    <a href="#" class="btn-secondary hover:bg-gray-50 transition duration-300">عرض المزيد من المصورين</a>
                </div>
            </section>

        </div>
        <!-- Include JS file if needed -->
        <!-- <script src="{{ asset('js/script.js') }}"></script> -->
    </body>
</html>

