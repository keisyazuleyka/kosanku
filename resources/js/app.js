import { createIcons, icons } from 'lucide';

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
    window.refreshIcons = () => createIcons({ icons });

    const navToggle = document.querySelector('[data-nav-toggle]');
    const navPanel = document.querySelector('[data-nav-panel]');
    navToggle?.addEventListener('click', () => navPanel?.classList.toggle('hidden'));

    const profileToggle = document.querySelector('[data-profile-toggle]');
    const profileMenu = document.querySelector('[data-profile-menu]');
    profileToggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu?.classList.toggle('hidden');
    });
    document.addEventListener('click', () => profileMenu?.classList.add('hidden'));

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm') || 'Lanjutkan tindakan ini?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    const preview = document.querySelector('[data-image-preview]');
    const input = document.querySelector('[data-image-input]');
    input?.addEventListener('change', () => {
        if (!preview) {
            return;
        }
        preview.innerHTML = '';
        [...input.files].forEach((file) => {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'h-24 w-32 rounded-xl border border-[#E4E4E7] object-cover';
            preview.appendChild(img);
        });
    });

    const start = document.querySelector('[name="start_date"]');
    const duration = document.querySelector('[name="duration_months"]');
    const rooms = document.querySelector('[name="rooms_requested"]');
    const estimate = document.querySelector('[data-estimate]');
    const monthly = Number(estimate?.dataset.monthly || 0);
    const deposit = Number(estimate?.dataset.deposit || 0);

    const renderEstimate = () => {
        if (!estimate) {
            return;
        }
        const months = Number(duration?.value || 1);
        const roomCount = Number(rooms?.value || 1);
        const total = monthly * months * roomCount + deposit;
        estimate.textContent = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(total);
    };

    [start, duration, rooms].forEach((el) => el?.addEventListener('change', renderEstimate));
    renderEstimate();
});

window.toggleCardFavorite = async function(event, propertyId, btn) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch(`/favorit/${propertyId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token || '',
                'Accept': 'application/json'
            }
        });
        if (res.status === 401) {
            if (typeof window.openAuthModal === 'function') {
                window.openAuthModal('login');
            } else {
                window.location.href = '/login';
            }
            return;
        }
        const data = await res.json();
        if (data.success) {
            const svg = btn.querySelector('svg');
            const icon = btn.querySelector('i');
            if (data.is_favorited) {
                btn.classList.add('text-rose-500');
                btn.classList.remove('text-[#667085]');
                if (svg) svg.classList.add('fill-rose-500', 'text-rose-500');
                if (icon) icon.classList.add('fill-rose-500', 'text-rose-500');
            } else {
                btn.classList.remove('text-rose-500');
                btn.classList.add('text-[#667085]');
                if (svg) svg.classList.remove('fill-rose-500', 'text-rose-500');
                if (icon) icon.classList.remove('fill-rose-500', 'text-rose-500');
            }
        }
    } catch (e) {
        console.error('Error toggling favorite:', e);
    }
};
