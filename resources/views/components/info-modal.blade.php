@props(['title', 'content'])

<div id="info-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="min-h-screen px-4 text-center">
        <div class="fixed inset-0" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="inline-block h-screen align-middle" aria-hidden="true">&#8203;</span>

        <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $title }}</h3>
                <button onclick="closeInfoModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="mt-4">
                {{ $content }}
            </div>
        </div>
    </div>
</div>

<script>
function openInfoModal() {
    document.getElementById('info-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeInfoModal() {
    document.getElementById('info-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Закриття модального вікна при кліку поза ним
document.addEventListener('click', function(event) {
    const modal = document.getElementById('info-modal');
    if (event.target === modal) {
        closeInfoModal();
    }
});
</script> 