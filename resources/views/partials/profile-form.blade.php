<div class="max-w-2xl">
    <div class="rounded-2xl border border-[#E4E8EF] bg-white p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Avatar Header & Upload -->
            <div class="mb-6 pb-5 border-b border-[#E4E8EF] flex flex-col sm:flex-row items-center gap-5">
                <div class="relative group shrink-0">
                    <img src="{{ $user->avatarUrl() }}"
                         alt="{{ $user->name }}"
                         id="avatar-preview-img"
                         class="h-20 w-20 rounded-2xl object-cover border-2 border-[#405BE8]/30 shadow-xs">
                    <label for="avatar-file-input" class="absolute inset-0 bg-black/50 rounded-2xl flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition cursor-pointer">
                        <i data-lucide="camera" class="h-5 w-5"></i>
                    </label>
                </div>
                <div class="space-y-1.5 text-center sm:text-left flex-1 min-w-0">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h3 class="text-base font-bold text-[#182230] truncate">{{ $user->name }}</h3>
                        <span class="badge bg-[#EEF2FF] text-[#405BE8] border border-[#405BE8]/20 font-semibold">{{ $user->role->label() }}</span>
                    </div>
                    <p class="text-xs text-[#667085] truncate">{{ $user->email }}</p>
                    <div class="pt-1">
                        <label for="avatar-file-input" class="btn-ghost border border-[#E4E8EF] py-1 px-3 text-xs font-semibold cursor-pointer inline-flex items-center gap-1.5 hover:bg-[#EEF2FF] hover:text-[#405BE8]">
                            <i data-lucide="upload" class="h-3.5 w-3.5 text-[#405BE8]"></i>
                            <span>Ganti Foto Profil</span>
                        </label>
                        <input type="file" id="avatar-file-input" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="previewAvatar(this)">
                        <p class="text-[10px] text-[#98A2B3] mt-1">Format: JPG, PNG, WEBP (Maksimal 2 MB)</p>
                    </div>
                </div>
            </div>

            <!-- Full Name -->
            <div>
                <label class="block text-xs font-semibold text-[#182230] mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input class="input text-xs" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-semibold text-[#182230] mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                <input class="input text-xs" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone & City -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1.5">Nomor Telepon / WhatsApp</label>
                    <input class="input text-xs" type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#182230] mb-1.5">Kota Domisili</label>
                    <input class="input text-xs" type="text" name="city" value="{{ old('city', $user->city) }}" placeholder="Contoh: Depok, Jakarta Selatan">
                    @error('city')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label class="block text-xs font-semibold text-[#182230] mb-1.5">Alamat Lengkap</label>
                <textarea class="input text-xs" name="address" rows="3" placeholder="Alamat tinggal atau domisili saat ini">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bio -->
            <div>
                <label class="block text-xs font-semibold text-[#182230] mb-1.5">Bio Singkat</label>
                <textarea class="input text-xs" name="bio" rows="2" placeholder="Informasi singkat tentang diri Anda...">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-[#E4E8EF] flex items-center justify-between">
                <span class="text-[11px] text-[#667085]">Perubahan akan langsung disimpan ke database.</span>
                <button type="submit" class="btn-primary py-2.5 px-6 text-xs font-semibold cursor-pointer shadow-md shadow-blue-600/20">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview-img').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
