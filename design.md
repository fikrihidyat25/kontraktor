# Aturan Inti Desain (Core Design Guidelines)

## Identitas Visual Utama
- **Warna Utama (Primary Blue/Navy)**: `#1E3A8A` (atau PUPR Navy `#233862`) WAJIB dipertahankan sebagai warna identitas utama dan tidak boleh diganti dengan warna pastel atau warna lain.
- **Warna Aksen**: Kuning PUPR (`#FACC15` / `#FFB800`) untuk aksen, focus ring, dan peringatan.

# Tambahan: Formal-Modern Layer (anti jadul, anti Gen-Z-norak)

> Tujuannya: tetap tegas/birokratis sesuai aturan inti, tapi terasa "dirancang tahun ini", bukan tahun 2012 dan bukan juga "soft SaaS pastel".

## Prinsip Dasar
"Modern" di sini BUKAN berarti: warna pastel, bentuk rounded-bulat, ilustrasi kartun, atau gradient. "Modern" berarti: presisi tipografi, micro-interaction halus, dan kerapatan visual yang konsisten. Acuan rasa: Linear, Stripe Dashboard, Vercel, gov.uk — bukan Duolingo atau aplikasi fintech retail.

## A. Tipografi — Kontras Berat, Bukan Bentuk Bulat
- Heading WAJIB bold dengan tracking rapat: `font-bold tracking-tight`
- Body tetap regular weight, ukuran kecil (sesuai skala yang sudah ada)
- Kontras berat antara H1 dan body harus terasa jelas — ini yang bikin "modern", bukan font bulat seperti Quicksand/Baloo
- Angka di tabel/dashboard: gunakan font dengan tabular figures kalau bisa (pertimbangkan ganti body font ke Inter/Public Sans/IBM Plex Sans — lihat catatan terpisah soal Poppins)

## B. Micro-interaction (motion tetap minim, tapi tidak nol)
- Hover state: transisi warna 150ms tetap berlaku (sesuai aturan inti)
- WAJIB ada focus-ring yang jelas dan tegas untuk keyboard navigation (bukan default browser outline biru buram — buat custom, warna aksen kuning PUPR, ketebalan 2px, tanpa blur)
- Button aktif (`:active`) boleh sedikit menurunkan brightness instan (tanpa transisi/delay) untuk kesan "responsif" — bukan scale-up/bounce
- Skeleton loading (bukan spinner generik) untuk tabel saat data dimuat — ini sinyal "modern" yang kuat dan tetap sesuai mood fungsional

## C. Whitespace Selektif (density tetap, tapi tidak di semua tempat)
- Dashboard/tabel kerja: TETAP padat sesuai aturan inti, TIDAK berubah
- Landing page publik & halaman login: boleh sedikit lebih lega (`py-16` hingga `py-24` antar section) — supaya kerasa "dirancang", bukan "ditumpuk". Ini SATU-SATUNYA tempat whitespace lebih lega diperbolehkan.

## D. Dark Mode (opsional, sinyal modern murah)
- Jika resource memungkinkan: sediakan dark mode untuk dashboard internal (bukan landing page publik)
- Dark mode TETAP harus tegas: background `#0F172A` atau sejenisnya, bukan abu-abu pekat tanpa kontras. Aksen kuning PUPR tetap dipakai, tapi cek kontras AA untuk teks di atasnya.

## E. Craft & Presisi (yang paling penting, paling sering diabaikan AI)
- Border 1px konsisten di SEMUA komponen sejenis — tidak boleh ada yang 1px dan yang lain 2px secara tidak sengaja
- Icon harus align secara vertikal dengan teks di sebelahnya (cek `align-items: center` di semua kombinasi icon+label)
- Angka di kolom tabel: rata kanan (right-aligned), bukan rata kiri seperti teks biasa — ini detail yang sering dilewatkan AI
- Currency/Rupiah: format konsisten (`Rp 1.250.000.000`, bukan `Rp1250000000` atau `Rp 1,250,000,000`)
- Spacing antar elemen harus konsisten ke satu sistem skala (4px/8px), jangan ada angka acak seperti `px-[13px]`

## F. DILARANG — Trap "Modern" yang Sebenarnya Sudah Basi
- DILARANG glassmorphism (background blur transparan) — basi sejak 2021
- DILARANG neumorphism (soft UI, shadow ganda terang-gelap)
- DILARANG ilustrasi kartun/flat-design orang-orangan untuk empty state atau halaman error — ganti dengan ikon outline kecil + teks abu-abu
- DILARANG warna pastel sebagai usaha "biar gak kaku" — ini yang justru bikin kesan murahan, bukan modern
- DILARANG font bulat/rounded (Quicksand, Baloo, Fredoka) untuk kesan "approachable" — bertentangan langsung dengan mood "berwibawa"
- DILARANG mengubah border-radius jadi besar (rounded-xl/rounded-2xl) di komponen data (tabel, badge, input) demi kesan "friendly"

## G. Empty State & Error State
- Tidak ada ilustrasi/karakter kartun
- Format: ikon outline kecil (Heroicons) + 1 baris teks abu-abu deskriptif + 1 CTA teks (bukan button besar mencolok) jika ada aksi lanjutan
- Contoh teks: "Belum ada data laporan untuk periode ini" — bukan "Oops! Sepertinya kosong nih 👀"
