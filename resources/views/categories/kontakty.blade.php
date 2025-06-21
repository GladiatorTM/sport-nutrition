@extends('layouts.app')
@section('content')
<div class="min-h-screen flex flex-col items-center bg-gradient-to-br from-yellow-100 via-blue-100 to-white mt-5">
    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl border-4 border-yellow-400 p-8 flex flex-col items-center">
        <div class="flex items-center gap-4 mb-4">
            <svg class="h-10 w-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            <h1 class="text-3xl font-extrabold text-yellow-500">КОНТАКТИ</h1>
        </div>
        <p class="text-lg text-gray-700 mb-6">Зв'яжіться з нами для отримання додаткової інформації!</p>
        
        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Контактна інформація -->
            <div class="flex flex-col gap-4">
                <div class="bg-yellow-100 border-l-4 border-yellow-400 p-4 rounded-lg text-yellow-800 font-bold">Наші контакти</div>
                <ul class="list-disc pl-6 text-gray-700 space-y-2">
                    <li><b>Телефон:</b> <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-gray-600">+380 44 123 4567</span>
                    </div></li>
                    <li><b>Email:</b> info@sport-nutrition.com</li>
                    <li><b>Адреса:</b> м. Київ, вул. Спортивна, 1</li>
                    <li><b>Графік роботи:</b> Пн-Пт: 9:00 - 18:00</li>
                </ul>
                
                <div class="bg-yellow-100 border-l-4 border-yellow-400 p-4 rounded-lg text-yellow-800 font-bold mt-4">Відгуки клієнтів</div>
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="flex text-yellow-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <span class="font-bold">Олександр К.</span>
                        </div>
                        <p class="text-gray-600">Чудовий магазин з великим вибором спортивного харчування. Консультанти дуже допомогли з вибором.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="flex text-yellow-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <span class="font-bold">Марія П.</span>
                        </div>
                        <p class="text-gray-600">Швидка доставка, якісні товари. Рекомендую!</p>
                    </div>
                </div>
            </div>

            <!-- Google Maps та фото -->
            <div class="flex flex-col gap-4">
                <div class="bg-yellow-100 border-l-4 border-yellow-400 p-4 rounded-lg text-yellow-800 font-bold">Наше місцезнаходження</div>
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2540.8278533985427!2d30.52091077677734!3d50.41513267159326!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40d4cf3c68398dc9%3A0x3f3eea47c9f98da2!2sBelok.ua%20-%20%D0%BC%D0%B0%D0%B3%D0%B0%D0%B7%D0%B8%D0%BD%20%D1%81%D0%BF%D0%BE%D1%80%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D0%B3%D0%BE%20%D1%85%D0%B0%D1%80%D1%87%D1%83%D0%B2%D0%B0%D0%BD%D0%BD%D1%8F%20%D1%82%D0%B0%20%D0%91%D0%90%D0%94%D1%96%D0%B2.!5e0!3m2!1suk!2sua!4v1710861234567!5m2!1suk!2sua" 
                    width="100%" 
                    height="300" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    class="rounded-lg shadow-lg">
                </iframe>
                
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <a href="https://www.google.com/maps/place/Belok.ua+-+%D0%BC%D0%B0%D0%B3%D0%B0%D0%B7%D0%B8%D0%BD+%D1%81%D0%BF%D0%BE%D1%80%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D0%B3%D0%BE+%D1%85%D0%B0%D1%80%D1%87%D1%83%D0%B2%D0%B0%D0%BD%D0%BD%D1%8F+%D1%82%D0%B0+%D0%91%D0%90%D0%94%D1%96%D0%B2./@50.4151327,30.5230995,3a,75y,90t/data=!3m8!1e2!3m6!1sCIHM0ogKEICAgID8yLjJaQ!2e10!3e12!6shttps:%2F%2Flh3.googleusercontent.com%2Fgps-cs-s%2FAC9h4nqvBvbg4yDdtd8IDVhK64ZERzeUY3omcOEEYA82h0O36WAkWkdynxIciDFAS5ry5NpbOSTQnJCihHWQbjQMPxh7xDxhlgJ1p7ZSVi5nr43xREH7a47rseD1BdxRN9rrYs2IWude%3Dw210-h100-k-no!7i5236!8i2484!4m18!1m8!3m7!1s0x40d4cf3c68398dc9:0x3f3eea47c9f98da2!2zQmVsb2sudWEgLSDQvNCw0LPQsNC30LjQvSDRgdC_0L7RgNGC0LjQstC90L7Qs9C-INGF0LDRgNGH0YPQstCw0L3QvdGPINGC0LAg0JHQkNCU0ZbQsi4!8m2!3d50.4151327!4d30.5230995!10e5!16s%2Fg%2F11cltww2lx!3m8!1s0x40d4cf3c68398dc9:0x3f3eea47c9f98da2!8m2!3d50.4151327!4d30.5230995!10e5!14m1!1BCgIYEg!16s%2Fg%2F11cltww2lx?entry=ttu&g_ep=EgoyMDI1MDYxMS4wIKXMDSoASAFQAw%3D%3D#" target="_blank">
                        <img src="https://lh3.googleusercontent.com/p/AF1QipO9u0AAnVMasAXzMezNhJiXnxJuawSXtvZInLk2=w427-h240-k-no" alt="Магазин Belok.ua на Google Maps" class="rounded-lg shadow-lg w-full h-48 object-cover">
                    </a>
                    <a href="https://www.google.com/maps/place/Belok.ua+-+%D0%BC%D0%B0%D0%B3%D0%B0%D0%B7%D0%B8%D0%BD+%D1%81%D0%BF%D0%BE%D1%80%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D0%B3%D0%BE+%D1%85%D0%B0%D1%80%D1%87%D1%83%D0%B2%D0%B0%D0%BD%D0%BD%D1%8F+%D1%82%D0%B0+%D0%91%D0%90%D0%94%D1%96%D0%B2./@50.4151327,30.5230995,3a,75y,90t/data=!3m8!1e2!3m6!1sCIHM0ogKEICAgID8yLjJaQ!2e10!3e12!6shttps:%2F%2Flh3.googleusercontent.com%2Fgps-cs-s%2FAC9h4nqvBvbg4yDdtd8IDVhK64ZERzeUY3omcOEEYA82h0O36WAkWkdynxIciDFAS5ry5NpbOSTQnJCihHWQbjQMPxh7xDxhlgJ1p7ZSVi5nr43xREH7a47rseD1BdxRN9rrYs2IWude%3Dw210-h100-k-no!7i5236!8i2484!4m18!1m8!3m7!1s0x40d4cf3c68398dc9:0x3f3eea47c9f98da2!2zQmVsb2sudWEgLSDQvNCw0LPQsNC30LjQvSDRgdC_0L7RgNGC0LjQstC90L7Qs9C-INGF0LDRgNGH0YPQstCw0L3QvdGPINGC0LAg0JHQkNCU0ZbQsi4!8m2!3d50.4151327!4d30.5230995!10e5!16s%2Fg%2F11cltww2lx!3m8!1s0x40d4cf3c68398dc9:0x3f3eea47c9f98da2!8m2!3d50.4151327!4d30.5230995!10e5!14m1!1BCgIYEg!16s%2Fg%2F11cltww2lx?entry=ttu&g_ep=EgoyMDI1MDYxMS4wIKXMDSoASAFQAw%3D%3D#" target="_blank">
                        <img src="https://lh3.googleusercontent.com/p/AF1QipOgVIxGTFmJXcznj6RfNsPzYib58Rs4Iu1Lnp4=w408-h612-k-no" alt="Інтер'єр Belok.ua на Google Maps" class="rounded-lg shadow-lg w-full h-48 object-cover">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 