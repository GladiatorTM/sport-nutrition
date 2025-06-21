<!-- Компонент чат-асистента -->
<!-- Надає інтерактивну допомогу користувачам через чат-інтерфейс -->

<!-- Кнопка відкриття чату (фіксована в правому нижньому куті) -->
<button id="chat-button" class="fixed bottom-4 right-4 bg-yellow-500 hover:bg-yellow-600 text-white rounded-full p-4 shadow-lg transition-all duration-300 z-50">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
    </svg>
</button>

<!-- Вікно чату (приховане за замовчуванням) -->
<div id="chat-window" class="fixed bottom-20 right-4 w-96 bg-white rounded-lg shadow-xl hidden z-50">
    <!-- Заголовок чату з кнопкою закриття -->
    <div class="bg-yellow-500 text-white p-4 rounded-t-lg flex justify-between items-center">
        <h3 class="font-semibold">Помічник</h3>
        <button id="close-chat" class="text-white hover:text-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    
    <!-- Область повідомлень чату -->
    <div id="chat-messages" class="h-96 overflow-y-auto p-4">
        <!-- Привітальне повідомлення від асистента -->
        <div class="mb-4">
            <div class="bg-gray-100 rounded-lg p-3 max-w-[80%]">
                <p class="text-sm">Вітаю! Я ваш помічник. Чим можу допомогти?</p>
            </div>
        </div>
    </div>
    
    <!-- Форма введення повідомлення -->
    <div class="border-t p-4">
        <form id="chat-form" class="flex gap-2">
            <!-- Поле введення тексту -->
            <textarea id="message-input" rows="2" class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:border-yellow-500 resize-none" placeholder="Введіть повідомлення. Shift+Enter — новий рядок, Enter — відправити"></textarea>
            <!-- Кнопка відправки повідомлення -->
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </form>
        <!-- Підказка про гарячі клавіші -->
        <div class="text-xs text-gray-400 mt-1">Shift+Enter — новий рядок, Enter — відправити</div>
    </div>
</div>

<script>
/**
 * JavaScript для функціональності чат-асистента
 */
document.addEventListener('DOMContentLoaded', function() {
    // Отримуємо посилання на елементи DOM
    const chatButton = document.getElementById('chat-button');
    const chatWindow = document.getElementById('chat-window');
    const closeChat = document.getElementById('close-chat');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatMessages = document.getElementById('chat-messages');

    // Обробник кліку на кнопку чату (відкриття/закриття)
    chatButton.addEventListener('click', () => {
        chatWindow.classList.toggle('hidden');
    });

    // Обробник кліку на кнопку закриття чату
    closeChat.addEventListener('click', () => {
        chatWindow.classList.add('hidden');
    });

    /**
     * Обробник натискання клавіш в полі введення
     * Enter - відправка повідомлення
     * Shift+Enter - новий рядок
     */
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit', { cancelable: true }));
        }
        // Shift+Enter — стандартна поведінка (новий рядок)
    });

    /**
     * Обробник відправки форми чату
     */
    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const message = messageInput.value.trim();
        
        if (message) {
            // Додаємо повідомлення користувача в чат
            addMessage(message, 'user');
            messageInput.value = '';

            // Імітація затримки відповіді асистента (1 секунда)
            setTimeout(() => {
                const response = getAssistantResponse(message);
                addMessage(response, 'assistant');
            }, 1000);
        }
    });

    /**
     * Додає повідомлення в чат
     * @param {string} text - Текст повідомлення
     * @param {string} sender - Відправник ('user' або 'assistant')
     */
    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'mb-4 ' + (sender === 'user' ? 'text-right' : '');
        
        const messageBubble = document.createElement('div');
        // Стилізація бульбашки повідомлення залежно від відправника
        messageBubble.className = sender === 'user' 
            ? 'bg-yellow-500 text-white rounded-lg p-3 inline-block max-w-[80%]'
            : 'bg-gray-100 rounded-lg p-3 max-w-[80%]';
        
        messageBubble.textContent = text;
        messageDiv.appendChild(messageBubble);
        chatMessages.appendChild(messageDiv);
        
        // Автоматичне прокручування до останнього повідомлення
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    /**
     * Генерує відповідь асистента на основі повідомлення користувача
     * @param {string} message - Повідомлення користувача
     * @returns {string} Відповідь асистента
     */
    function getAssistantResponse(message) {
        // Базові відповіді на прості питання
        const responses = {
            'привіт': 'Вітаю! Як я можу допомогти?',
            'допомога': 'Що ви хочете зробити? (Додати товар, змінити категорію, подивитися статистику?)',
            'доставка': 'Доставка по Україні 1-3 дні. Безкоштовно від 2000 грн.',
            'ціни': 'У нас вигідні ціни на спортивне харчування.',
            'контакти': 'Телефон: +380 44 123 4567. Email: info@sportnutrition.ua',
            'протеїн': 'Який протеїн вас цікавить? (Смак, ціна, виробник?)',
            'амінокислоти': 'Які амінокислоти вас цікавлять? (BCAA, EAA, глютамін?)',
            'жиросжигатели': 'Потрібен жироспалювач? Запитайте — підкажу!',
            'предтрен': 'Потрібен предтренувальний комплекс? Запитайте!',
            'шутка': 'Чому протеїн завжди у гарному настрої? Бо його всі люблять! 😄',
        };

        // Детальні рекомендації для конкретних цілей
        const keywords = {
            'набір маси': 'Для набору маси рекомендую:\n1. Протеїн (2-3 порції на день)\n2. Гейнер (1-2 порції)\n3. Креатин (5г на день)\n4. BCAA (під час тренування)\n\nТак багато їжі, що навіть ваш холодильник буде в шоці! 😱',
            'схуднення': 'Для схуднення рекомендую:\n1. Протеїн (2 порції на день)\n2. L-карнітин\n3. Жиросжигатель (якщо немає протипоказань)\n4. BCAA (для збереження м\'язів)\n\nНе хвилюйтесь, я не буду вас запитувати про ваші улюблені десерти! 🍰',
            'витривалість': 'Для підвищення витривалості рекомендую:\n1. BCAA\n2. Бета-аланін\n3. Предтрен\n4. Електроліти\n\nТак багато енергії, що навіть ваші м\'язи будуть в шоці! ⚡',
            'відновлення': 'Для кращого відновлення рекомендую:\n1. BCAA\n2. Глютамін\n3. Омега-3\n4. Вітамінно-мінеральний комплекс\n\nВаші м\'язи будуть вдячні! 🙏'
        };

        // Відповіді на загальні теми (не пов'язані зі спортом)
        const generalTopics = {
            'кохання': 'Кохання - це чудово! Але знаєте, що ще чудово? Правильне спортивне харчування! Давайте поговоримо про ваші фітнес-цілі! ❤️',
            'робота': 'Робота важлива, але не забувайте про своє здоров\'я! Які у вас цілі в спорті? 💼',
            'навчання': 'Навчання - це добре, але не забувайте про фізичну активність! Який у вас рівень тренувань? 📚',
            'відпочинок': 'Відпочинок важливий, але знаєте, що ще важливіше? Правильне відновлення після тренувань! Розкажіть про ваші тренування? 🏖️'
        };

        // Приводимо повідомлення до нижнього регістру для порівняння
        message = message.toLowerCase();
        
        // Перевірка на загальні теми (пріоритет вище)
        for (let key in generalTopics) {
            if (message.includes(key)) {
                return generalTopics[key];
            }
        }

        // Перевірка на ключові слова для рекомендацій
        for (let key in keywords) {
            if (message.includes(key)) {
                return keywords[key];
            }
        }

        // Перевірка на базові відповіді
        for (let key in responses) {
            if (message.includes(key)) {
                return responses[key];
            }
        }

        // Стандартна відповідь якщо не знайдено відповідності
        return 'Що вас цікавить? (Товари, категорії, користувачі, допомога)';
    }
});
</script> 