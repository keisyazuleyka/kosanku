<!-- Auth Modal Component (Login & Register Popup) -->
<div id="auth-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop overlay -->
    <div class="fixed inset-0 bg-zinc-900/50 backdrop-blur-sm transition-opacity" onclick="window.closeAuthModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-md border border-[#E4E8EF] p-6 sm:p-8">
            <!-- Close Button -->
            <button type="button" onclick="window.closeAuthModal()" class="absolute right-5 top-5 rounded-full p-1.5 text-[#667085] hover:bg-[#EEF2FF] hover:text-[#405BE8] transition cursor-pointer" aria-label="Tutup">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>

            <!-- Brand Header -->
            <div class="text-center mb-6">
                <img src="{{ asset('images/kosanku-logo.png') }}" alt="Kosanku" class="h-10 w-auto mx-auto object-contain">
                <p class="mt-2 text-xs text-[#667085]">Find your space. Feel at home.</p>
            </div>

            <!-- Tab Switcher -->
            <div class="flex rounded-2xl bg-[#EEF2FF] p-1 mb-6 text-xs font-bold">
                <button type="button" id="tab-login" onclick="window.switchAuthTab('login')" class="flex-1 rounded-xl py-2 transition text-[#405BE8] bg-white shadow-sm cursor-pointer">
                    Masuk
                </button>
                <button type="button" id="tab-register" onclick="window.switchAuthTab('register')" class="flex-1 rounded-xl py-2 transition text-[#667085] hover:text-[#182230] cursor-pointer">
                    Daftar Akun
                </button>
            </div>

            <!-- Error Notification Alert -->
            <div id="auth-modal-error" class="hidden mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700"></div>

            <!-- Form: Login -->
            <form id="form-login" method="POST" action="{{ route('login') }}" onsubmit="window.handleAuthSubmit(event, 'login')" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#98A2B3]"></i>
                        <input class="input pl-10 text-xs" type="email" name="email" required placeholder="nama@email.com">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-[#182230]">Kata Sandi</label>
                        <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-[#405BE8] hover:underline">Lupa sandi?</a>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#98A2B3]"></i>
                        <input class="input pl-10 text-xs" type="password" name="password" required placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-xs text-[#667085] cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-[#E4E8EF] text-[#405BE8] focus:ring-[#405BE8]/20">
                        <span>Ingat sesi masuk saya</span>
                    </label>
                </div>

                <button type="submit" id="btn-login-submit" class="btn-primary w-full py-2.5 text-xs font-semibold">
                    Masuk Sekarang
                </button>

                <p class="text-center text-xs text-[#667085] pt-2">
                    Belum punya akun?
                    <button type="button" onclick="window.switchAuthTab('register')" class="font-bold text-[#405BE8] hover:underline cursor-pointer">Daftar di sini</button>
                </p>
            </form>

            <!-- Form: Register -->
            <form id="form-register" method="POST" action="{{ route('register') }}" onsubmit="window.handleAuthSubmit(event, 'register')" class="hidden space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <i data-lucide="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#98A2B3]"></i>
                        <input class="input pl-10 text-xs" type="text" name="name" required placeholder="Contoh: Andi Pratama">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1">Alamat Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#98A2B3]"></i>
                        <input class="input pl-10 text-xs" type="email" name="email" required placeholder="andi@example.com">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1">Nomor WhatsApp / HP</label>
                    <div class="relative">
                        <i data-lucide="phone" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-[#98A2B3]"></i>
                        <input class="input pl-10 text-xs" type="tel" name="phone" placeholder="08123456789">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1">Daftar Sebagai</label>
                    <select class="input text-xs" name="role" required>
                        <option value="tenant">Penyewa Kost (Mahasiswa / Pekerja)</option>
                        <option value="owner">Pemilik Properti Kost</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-[#182230] mb-1">Kata Sandi</label>
                        <input class="input text-xs" type="password" name="password" required placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#182230] mb-1">Konfirmasi</label>
                        <input class="input text-xs" type="password" name="password_confirmation" required placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" id="btn-register-submit" class="btn-primary w-full py-2.5 text-xs font-semibold">
                    Daftar Akun Baru
                </button>

                <p class="text-center text-xs text-[#667085] pt-1">
                    Sudah punya akun?
                    <button type="button" onclick="window.switchAuthTab('login')" class="font-bold text-[#405BE8] hover:underline cursor-pointer">Masuk di sini</button>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
    window.openAuthModal = function(tab = 'login') {
        const modal = document.getElementById('auth-modal');
        if (modal) {
            modal.classList.remove('hidden');
            window.switchAuthTab(tab);
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }
    };

    window.closeAuthModal = function() {
        const modal = document.getElementById('auth-modal');
        if (modal) {
            modal.classList.add('hidden');
            const errBox = document.getElementById('auth-modal-error');
            if (errBox) errBox.classList.add('hidden');
        }
    };

    window.switchAuthTab = function(tab) {
        const formLogin = document.getElementById('form-login');
        const formRegister = document.getElementById('form-register');
        const tabLogin = document.getElementById('tab-login');
        const tabRegister = document.getElementById('tab-register');
        const errBox = document.getElementById('auth-modal-error');

        if (errBox) errBox.classList.add('hidden');

        if (tab === 'login') {
            formLogin?.classList.remove('hidden');
            formRegister?.classList.add('hidden');
            tabLogin?.classList.add('bg-white', 'text-[#405BE8]', 'shadow-sm');
            tabLogin?.classList.remove('text-[#667085]');
            tabRegister?.classList.remove('bg-white', 'text-[#405BE8]', 'shadow-sm');
            tabRegister?.classList.add('text-[#667085]');
        } else {
            formLogin?.classList.add('hidden');
            formRegister?.classList.remove('hidden');
            tabRegister?.classList.add('bg-white', 'text-[#405BE8]', 'shadow-sm');
            tabRegister?.classList.remove('text-[#667085]');
            tabLogin?.classList.remove('bg-white', 'text-[#405BE8]', 'shadow-sm');
            tabLogin?.classList.add('text-[#667085]');
        }
    };

    window.handleAuthSubmit = async function(event, type) {
        event.preventDefault();
        const form = event.target;
        const submitBtn = type === 'login' ? document.getElementById('btn-login-submit') : document.getElementById('btn-register-submit');
        const errBox = document.getElementById('auth-modal-error');
        const originalBtnText = submitBtn.innerHTML;

        if (errBox) errBox.classList.add('hidden');
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Memproses...';

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Success: redirect to dashboard or reload current page
                window.location.reload();
            } else {
                // Show errors
                let message = data.message || 'Terjadi kesalahan saat otentikasi.';
                if (data.errors) {
                    const firstErr = Object.values(data.errors)[0];
                    if (Array.isArray(firstErr)) {
                        message = firstErr[0];
                    }
                }
                if (errBox) {
                    errBox.textContent = message;
                    errBox.classList.remove('hidden');
                }
            }
        } catch (error) {
            console.error('Auth error:', error);
            // Fallback: normal submit if fetch fails
            form.submit();
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    };
</script>

