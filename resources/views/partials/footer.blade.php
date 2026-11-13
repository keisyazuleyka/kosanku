<footer class="mt-20 border-t border-[#E4E8EF] bg-white text-[#667085]">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 md:grid-cols-4 sm:px-6">
        <div>
            <div class="flex items-center">
                <img src="{{ asset('images/kosanku-logo.png') }}" alt="Kosanku" class="h-9 w-auto object-contain">
            </div>
            <p class="mt-3 text-xs leading-relaxed text-[#667085]">
                Find your space. Feel at home.
            </p>
            <div class="mt-3 flex items-center gap-2 text-xs font-semibold text-[#405BE8]">
                <span>Instagram / TikTok:</span>
                <span class="rounded-lg bg-[#EEF2FF] px-2 py-0.5">@kosanku</span>
            </div>
            <p class="mt-4 text-[11px] text-[#98A2B3]">
                &copy; {{ date('Y') }} Kosanku Indonesia. All rights reserved.
            </p>
        </div>

        <div class="text-xs space-y-2.5">
            <p class="font-bold text-[#182230] text-sm">Jelajah</p>
            <a class="block hover:text-[#405BE8]" href="{{ route('kost.index') }}">Semua Kost</a>
            <a class="block hover:text-[#405BE8]" href="{{ route('pages.how') }}">Cara Kerja</a>
            <a class="block hover:text-[#405BE8]" href="{{ route('favorites.index') }}">Favorit Saya</a>
        </div>

        <div class="text-xs space-y-2.5">
            <p class="font-bold text-[#182230] text-sm">Legal & Privasi</p>
            <a class="block hover:text-[#405BE8]" href="{{ route('pages.terms') }}">Syarat dan Ketentuan</a>
            <a class="block hover:text-[#405BE8]" href="{{ route('pages.privacy') }}">Kebijakan Privasi</a>
        </div>

        <div class="text-xs space-y-2.5">
            <p class="font-bold text-[#182230] text-sm">Bantuan & Kontak</p>
            <p class="flex items-center gap-2">
                <i data-lucide="mail" class="h-3.5 w-3.5 text-[#405BE8]"></i>
                {{ setting('contact_email', 'halo@kosanku.test') }}
            </p>
            @if(setting('contact_phone'))
                <p class="flex items-center gap-2">
                    <i data-lucide="phone" class="h-3.5 w-3.5 text-[#405BE8]"></i>
                    {{ setting('contact_phone') }}
                </p>
            @endif
            <p class="flex items-center gap-2">
                <i data-lucide="at-sign" class="h-3.5 w-3.5 text-[#405BE8]"></i>
                <span>@kosanku</span>
            </p>
        </div>
    </div>
</footer>
