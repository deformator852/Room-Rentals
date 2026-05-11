import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.Pusher = Pusher;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const toastContainer = document.getElementById('app-notifications');

function showToast(message, type = 'info') {
    if (!toastContainer || !message) {
        return;
    }

    const colorClasses = {
        success: 'border-green-200 bg-green-50 text-green-800',
        error: 'border-red-200 bg-red-50 text-red-800',
        info: 'border-blue-200 bg-blue-50 text-blue-800',
    };

    const item = document.createElement('div');
    item.className = `pointer-events-auto rounded-xl border px-4 py-3 text-sm shadow ${colorClasses[type] ?? colorClasses.info}`;
    item.textContent = message;

    toastContainer.appendChild(item);
    setTimeout(() => item.remove(), 5500);
}

const appUser = window.AppUser;
const dropdown = document.getElementById('notifications-dropdown');
const toggleButton = document.getElementById('notifications-toggle');
const listNode = document.getElementById('notifications-list');
const badge = document.getElementById('notifications-badge');
const clearButton = document.getElementById('notifications-clear');

function updateBadge(unreadCount) {
    if (!badge) {
        return;
    }

    if (unreadCount > 0) {
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

function renderNotifications(notifications) {
    if (!listNode) {
        return;
    }

    if (!notifications.length) {
        listNode.innerHTML = '<div class="px-4 py-6 text-center text-sm text-zinc-500 dark:text-zinc-400">Немає сповіщень</div>';
        return;
    }

    listNode.innerHTML = notifications.map((notification) => {
        const unreadClass = notification.is_read ? '' : 'bg-blue-50/70 dark:bg-blue-950/20';
        const messageHtml = notification.action_url
            ? `<a href="${notification.action_url}" class="text-sm text-zinc-800 hover:text-blue-600 dark:text-zinc-100 dark:hover:text-blue-400">${notification.message}</a>`
            : `<p class="text-sm text-zinc-800 dark:text-zinc-100">${notification.message}</p>`;

        return `
            <div class="flex items-start justify-between gap-2 border-b border-zinc-100 px-4 py-3 dark:border-zinc-800 ${unreadClass}">
                <div>
                    ${messageHtml}
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">${notification.created_at ?? ''}</p>
                </div>
                <button data-delete-notification="${notification.id}" class="mt-1 rounded p-1 text-zinc-400 hover:bg-zinc-100 hover:text-red-600 dark:hover:bg-zinc-800" title="Видалити">
                    ✕
                </button>
            </div>
        `;
    }).join('');
}

async function loadNotifications() {
    if (!appUser?.notificationsUrl) {
        return;
    }

    const response = await window.axios.get(appUser.notificationsUrl);
    renderNotifications(response.data.notifications ?? []);
    updateBadge(response.data.unread_count ?? 0);
}

async function markAllAsRead() {
    if (!appUser?.readAllUrl) {
        return;
    }

    await window.axios.post(appUser.readAllUrl);
    updateBadge(0);
}

async function deleteNotification(notificationId) {
    await window.axios.delete(`/notifications/${notificationId}`);
    await loadNotifications();
}

async function clearNotifications() {
    if (!appUser?.clearNotificationsUrl) {
        return;
    }

    await window.axios.delete(appUser.clearNotificationsUrl);
    await loadNotifications();
}

if (toggleButton && dropdown) {
    toggleButton.addEventListener('click', async () => {
        const isHidden = dropdown.classList.contains('hidden');

        if (isHidden) {
            dropdown.classList.remove('hidden');
            await loadNotifications();
            await markAllAsRead();
            await loadNotifications();
        } else {
            dropdown.classList.add('hidden');
        }
    });

    document.addEventListener('click', (event) => {
        if (!dropdown.contains(event.target) && !toggleButton.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

if (clearButton) {
    clearButton.addEventListener('click', async () => {
        await clearNotifications();
    });
}

if (listNode) {
    listNode.addEventListener('click', async (event) => {
        const target = event.target.closest('[data-delete-notification]');
        if (!target) {
            return;
        }

        const notificationId = target.getAttribute('data-delete-notification');
        if (notificationId) {
            await deleteNotification(notificationId);
        }
    });
}

if (appUser?.id && import.meta.env.VITE_REVERB_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
        wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 80),
        wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 443),
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });

    window.Echo.private(`App.Models.User.${appUser.id}`)
        .listen('.booking.requested', async (event) => {
            showToast(event.message, 'info');
            await loadNotifications();
        })
        .listen('.booking.status-updated', async (event) => {
            const type = event.status === 'confirmed' ? 'success' : 'error';
            showToast(event.message, type);
            await loadNotifications();
        });
}

const bookingForm = document.getElementById('booking-form');
const checkInInput = document.getElementById('check_in');
const checkOutInput = document.getElementById('check_out');
const bookingConflictError = document.getElementById('booking-conflict-error');

function hasDateConflict(checkIn, checkOut, unavailableRanges) {
    return unavailableRanges.some((range) => checkIn < range.check_out && checkOut > range.check_in);
}

function validateBookingDates() {
    if (!bookingForm || !checkInInput || !checkOutInput || !bookingConflictError) {
        return true;
    }

    bookingConflictError.classList.add('hidden');
    checkInInput.setCustomValidity('');
    checkOutInput.setCustomValidity('');

    const checkIn = checkInInput.value;
    const checkOut = checkOutInput.value;
    const maxBookingDate = bookingForm.dataset.maxBookingDate;

    if (maxBookingDate && ((checkIn && checkIn > maxBookingDate) || (checkOut && checkOut > maxBookingDate))) {
        const message = 'Можна обрати дати не пізніше ніж через 1 місяць від сьогодні.';
        bookingConflictError.textContent = message;
        bookingConflictError.classList.remove('hidden');
        checkInInput.setCustomValidity(message);
        checkOutInput.setCustomValidity(message);
        return false;
    }

    if (!checkIn || !checkOut || checkOut <= checkIn) {
        return true;
    }

    const unavailableRanges = JSON.parse(bookingForm.dataset.unavailableRanges ?? '[]');
    const conflict = hasDateConflict(checkIn, checkOut, unavailableRanges);

    if (conflict) {
        const message = 'Обрані дати вже зайняті. Виберіть інший період.';
        bookingConflictError.textContent = message;
        bookingConflictError.classList.remove('hidden');
        checkInInput.setCustomValidity(message);
        checkOutInput.setCustomValidity(message);
        return false;
    }

    return true;
}

if (bookingForm && checkInInput && checkOutInput) {
    checkInInput.addEventListener('change', validateBookingDates);
    checkOutInput.addEventListener('change', validateBookingDates);

    bookingForm.addEventListener('submit', (event) => {
        if (!validateBookingDates()) {
            event.preventDefault();
            checkInInput.reportValidity();
        }
    });
}

function renderOccupiedCalendar() {
    const container = document.getElementById('occupied-calendar');
    if (!container || !bookingForm) {
        return;
    }

    const unavailableRanges = JSON.parse(bookingForm.dataset.unavailableRanges ?? '[]');
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const monthNames = ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'];
    const dayNames = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Нд'];

    const months = [];
    for (let offset = 0; offset < 42; offset += 1) {
        const date = new Date(today);
        date.setDate(today.getDate() + offset);

        const monthKey = `${date.getFullYear()}-${date.getMonth()}`;
        let month = months.find((item) => item.key === monthKey);
        if (!month) {
            month = {
                key: monthKey,
                year: date.getFullYear(),
                monthIndex: date.getMonth(),
                days: [],
            };
            months.push(month);
        }
        month.days.push(new Date(date));
    }

    container.innerHTML = months.map((month) => {
        const dayHeaders = dayNames.map((day) => `<div class="text-center font-semibold text-zinc-500 dark:text-zinc-400">${day}</div>`).join('');

        const dayCells = month.days.map((date) => {
            const iso = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
            const occupied = unavailableRanges.some((range) => iso >= range.check_in && iso < range.check_out);
            const dayClass = occupied
                ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300';

            return `<div class="rounded px-1 py-1 text-center ${dayClass}" title="${iso}">${date.getDate()}</div>`;
        }).join('');

        return `
            <div class="rounded-md border border-zinc-200 p-2 dark:border-zinc-700">
                <p class="mb-2 text-xs font-semibold text-zinc-700 dark:text-zinc-200">${monthNames[month.monthIndex]} ${month.year}</p>
                <div class="grid grid-cols-7 gap-1">${dayHeaders}${dayCells}</div>
            </div>
        `;
    }).join('');
}

renderOccupiedCalendar();
