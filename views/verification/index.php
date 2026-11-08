<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/navbar_verification.php'; ?>

<main id="dashboard" class="flex flex-col max-w-[1440px] items-start gap-8 p-8 relative w-full flex-[0_0_auto]">
    <section aria-labelledby="page-title"
        class="flex flex-col items-start gap-2 relative self-stretch w-full flex-[0_0_auto]">
        <h1 id="page-title"
            class="relative flex items-center self-stretch mt-[-1.00px] [font-family:'Plus_Jakarta_Sans-Bold',Helvetica] font-bold text-[#00021c] text-4xl tracking-[-0.90px] leading-[44px]">
            Verifikasi&nbsp;&nbsp;Tanda Tangan Digital Dokumen
        </h1>
        <p
            class="mt-[-1.00px] [font-family:'Inter-Regular',Helvetica] font-normal text-[#46464e] text-sm tracking-[0] leading-[22px] relative max-w-screen-md">
            Layanan validasi publik non-repudiasi Jurusan Teknik Informatika dan Komputer Politeknik Negeri
            Jakarta.<br>
            Pemeriksaan keaslian stempel elektronik institusi dan sertifikat digital X.509 secara kriptografis
            real-time.
        </p>
    </section>
    <section aria-labelledby="upload-title"
        class="flex flex-col items-start p-6 relative self-stretch w-full flex-[0_0_auto] bg-white rounded-lg border border-solid border-[#c7c5cf] shadow-[0px_1px_2px_#0000000d]">
        <header
            class="flex items-center justify-around gap-[118.83px] pt-0 pb-5 px-0 relative self-stretch w-full flex-[0_0_auto] border-b [border-bottom-style:solid] border-[#c7c5cf]">
            <div class="inline-flex flex-col items-start relative flex-[0_0_auto]">
                <h2 id="upload-title"
                    class="[font-family:'Plus_Jakarta_Sans-Bold',Helvetica] font-bold text-[#00021c] text-lg tracking-[-0.18px] leading-6 whitespace-nowrap">
                    Upload Dokumen</h2>
                <p
                    class="[font-family:'Inter-Regular',Helvetica] font-normal text-[#46464e] text-xs tracking-[0.06px] leading-[18px] whitespace-nowrap">
                    Pilih metode autentikasi dokumen: pencarian identifier terindeks atau perbandingan SHA-256
                    berkas biner.</p>
            </div>
        </header>
        <form
            class="flex flex-col items-start pt-6 pb-0 px-0 relative self-stretch w-full flex-[0_0_auto] border-t [border-top-style:dashed] border-[#c7c5cf]"
            enctype="multipart/form-data">
            <label for="document-upload"
                class="flex items-center justify-between p-5 relative self-stretch w-full flex-[0_0_auto] bg-[#f2f4f680] rounded border-2 border-dashed border-[#c7c5cf] cursor-pointer">
                <span class="inline-flex items-center gap-4 relative flex-[0_0_auto]">
                    <span
                        class="flex w-12 h-12 items-center justify-center relative bg-white rounded border border-solid border-[#c7c5cf]">
                        <img class="relative w-[23.33px] h-[23.33px]" src="img/image.svg" alt=""
                            aria-hidden="true">
                    </span>
                    <span class="inline-flex flex-col items-start relative flex-[0_0_auto]">
                        <strong
                            class="[font-family:'Inter-Bold',Helvetica] font-bold text-[#00021c] text-sm tracking-[0.14px] leading-5 whitespace-nowrap">Pilih
                            File PDF yang akan diverifikasi</strong>
                        <span
                            class="[font-family:'Inter-Regular',Helvetica] font-normal text-[#46464e] text-xs tracking-[0.06px] leading-[18px] whitespace-nowrap">Atau
                            Drag &amp; Drop file PDF dokumen disini</span>
                    </span>
                </span>
                <span
                    class="all-unset box-border inline-flex items-center gap-2 px-4 py-2 relative flex-[0_0_auto] bg-white rounded border border-solid border-[#c7c5cf]">
                    <img class="relative w-3 h-3" src="img/icon-6.svg" alt="" aria-hidden="true">
                    <span
                        class="[font-family:'Inter-SemiBold',Helvetica] font-semibold text-[#00021c] text-[13px] text-center leading-[22px] whitespace-nowrap">Pilih
                        File PDF</span>
                </span>
            </label>
            <input id="document-upload" name="document" type="file" accept="application/pdf,.pdf"
                class="sr-only" aria-describedby="upload-title">
        </form>
    </section>
    <section aria-label="Status verifikasi berhasil" role="status"
        class="flex flex-col items-start gap-6 p-6 relative self-stretch w-full flex-[0_0_auto] bg-green-100 rounded-lg overflow-hidden border-2 border-solid border-green-300">
        <div class="flex items-center justify-around gap-6 relative self-stretch w-full flex-[0_0_auto]">
            <div class="inline-flex items-center gap-4 relative flex-[0_0_auto]">
                <div
                    class="flex w-14 h-14 items-center justify-center relative bg-green-700 rounded-xl shadow-[0px_1px_2px_#0000000d]">
                    <img class="relative w-[22.67px] h-[28.33px]" src="img/icon-3.svg" alt=""
                        aria-hidden="true">
                </div>
                <div class="inline-flex flex-col items-start gap-1 relative flex-[0_0_auto]">
                    <h2
                        class="[font-family:'Plus_Jakarta_Sans-ExtraBold',Helvetica] font-extrabold text-green-900 text-[22px] tracking-[-0.55px] leading-7 whitespace-nowrap">
                        DOKUMEN ASLI TERVERIFIKASI &amp; SAH SECARA HUKUM</h2>
                    <p
                        class="[font-family:'Inter-Regular',Helvetica] font-normal text-green-800 text-sm leading-[22px] whitespace-nowrap">
                        Tanda tangan elektronik sah dan terdaftar dalam sistem verifikasi PNJ.</p>
                </div>
            </div>
        </div>
    </section>
    <section aria-label="Status verifikasi gagal" role="alert"
        class="flex flex-col items-start gap-6 p-6 relative self-stretch w-full flex-[0_0_auto] bg-[#fff0ef] rounded-lg overflow-hidden border-2 border-solid border-red-700">
        <div class="flex items-center justify-around gap-6 relative self-stretch w-full flex-[0_0_auto]">
            <div class="inline-flex items-center gap-4 relative flex-[0_0_auto]">
                <div
                    class="flex w-14 h-14 items-center justify-center relative bg-variable-collection-red rounded-xl shadow-[0px_1px_2px_#0000000d]">
                    <img class="relative w-[23px] h-7" src="img/vector.svg" alt="" aria-hidden="true">
                </div>
                <div class="inline-flex flex-col items-start gap-1 relative flex-[0_0_auto]">
                    <h2
                        class="[font-family:'Plus_Jakarta_Sans-ExtraBold',Helvetica] font-extrabold text-variable-collection-red text-[22px] tracking-[-0.55px] leading-7 whitespace-nowrap">
                        DOKUMEN TIDAK VALID</h2>
                    <p
                        class="[font-family:'Inter-Regular',Helvetica] font-normal text-variable-collection-red text-sm leading-[22px] whitespace-nowrap">
                        Tanda tangan elektronik tidak terdaftar dalam sistem verifikasi PNJ.</p>
                </div>
            </div>
        </div>
    </section>
    <div class="flex items-start relative self-stretch w-full flex-[0_0_auto]">
        <section aria-labelledby="signer-title"
            class="flex flex-col items-start gap-5 p-6 relative self-stretch w-full flex-[0_0_auto] bg-white rounded-lg border border-solid border-[#c7c5cf] shadow-[0px_1px_2px_#0000000d]">
            <header
                class="justify-around gap-[128.28px] pt-0 pb-4 px-0 flex items-center relative self-stretch w-full flex-[0_0_auto] border-b [border-bottom-style:solid] border-[#c7c5cf]">
                <div>
                    <h2 id="signer-title"
                        class="flex items-center gap-2 [font-family:'Plus_Jakarta_Sans-Bold',Helvetica] font-bold text-[#00021c] text-lg tracking-[-0.18px] leading-6 whitespace-nowrap">
                        <img class="relative w-[17.88px] h-[14.67px]" src="img/icon-8.svg" alt=""
                            aria-hidden="true">
                        Detail Penanda Tangan
                    </h2>
                    <p
                        class="[font-family:'Inter-Regular',Helvetica] font-normal text-[#46464e] text-xs tracking-[0.06px] leading-[18px] whitespace-nowrap">
                        Hierarki tanda tangan elektronik dan sertifikasi X.509 PNJ CA</p>
                </div>
            </header>
            <div class="flex flex-col items-start gap-4 relative self-stretch w-full flex-[0_0_auto]">
                <article
                    class="flex flex-col items-start gap-3.5 pl-5 pr-4 py-4 relative self-stretch w-full flex-[0_0_auto] bg-white rounded border border-solid border-[#c7c5cf]">
                    <div class="flex items-start justify-between relative self-stretch w-full flex-[0_0_auto]">
                        <div class="inline-flex flex-col items-start gap-1 relative flex-[0_0_auto]">
                            <h3
                                class="flex items-center gap-2 [font-family:'Plus_Jakarta_Sans-Bold',Helvetica] font-bold text-[#00021c] text-base leading-[22px]">
                                <span
                                    class="flex w-6 h-6 items-center justify-center bg-[#151b3d] rounded-xl text-white text-xs">1</span>
                                Dr. Bambang Warsito, S.T., M.T.
                            </h3>
                            <p
                                class="pl-8 [font-family:'Inter-Medium',Helvetica] font-medium text-[#46464e] text-[13px] leading-[22px]">
                                Dosen Kaprodi Teknik Informatika • NIP 198003152005011002</p>
                        </div>
                        <time datetime="2026-02-25T14:22:05+07:00"
                            class="[font-family:'Liberation_Mono-Regular',Helvetica] text-[#46464e] text-[11px] text-right leading-[22px]">25
                            Feb 2026, 14:22:05 WIB</time>
                    </div>
                    <div
                        class="flex items-start justify-center gap-2 pl-8 pt-3 relative self-stretch w-full flex-[0_0_auto] border-t [border-top-style:solid] border-[#eceef0]">
                        <div class="flex flex-col items-start p-2 relative flex-1 grow bg-[#f2f4f6] rounded-sm">
                            <strong
                                class="[font-family:'Inter-Bold',Helvetica] text-[#46464e] text-[10px] leading-[22px]">INTEGRITAS
                                KEY ID</strong>
                            <code
                                class="[font-family:'Liberation_Mono-Regular',Helvetica] text-[#00021c] text-xs leading-[22px]">KEY: 7b84-f102-cc91</code>
                        </div>
                    </div>
                </article>
                <article
                    class="flex flex-col items-start gap-3.5 pl-5 pr-4 py-4 relative self-stretch w-full flex-[0_0_auto] bg-white rounded border border-solid border-[#c7c5cf]">
                    <div class="flex items-start justify-between relative self-stretch w-full flex-[0_0_auto]">
                        <div class="inline-flex flex-col items-start gap-1 relative flex-[0_0_auto]">
                            <h3
                                class="flex items-center gap-2 [font-family:'Plus_Jakarta_Sans-Bold',Helvetica] font-bold text-[#00021c] text-base leading-[22px]">
                                <span
                                    class="flex w-6 h-6 items-center justify-center bg-[#151b3d] rounded-xl text-white text-xs">2</span>
                                Anita Hidayati, S.Kom., M.Kom.
                            </h3>
                            <p
                                class="pl-8 [font-family:'Inter-Medium',Helvetica] font-medium text-[#46464e] text-[13px] leading-[22px]">
                                Sekretaris Jurusan TIK • NIP 197908102008122002</p>
                        </div>
                        <time datetime="2026-02-25T15:05:12+07:00"
                            class="[font-family:'Liberation_Mono-Regular',Helvetica] text-[#46464e] text-[11px] text-right leading-[22px]">25
                            Feb 2026, 15:05:12 WIB</time>
                    </div>
                    <div
                        class="flex items-start justify-center gap-2 pl-8 pt-3 relative self-stretch w-full flex-[0_0_auto] border-t [border-top-style:solid] border-[#eceef0]">
                        <div class="flex flex-col items-start p-2 relative flex-1 grow bg-[#f2f4f6] rounded-sm">
                            <strong
                                class="[font-family:'Inter-Bold',Helvetica] text-[#46464e] text-[10px] leading-[22px]">INTEGRITAS
                                KEY ID</strong>
                            <code
                                class="[font-family:'Liberation_Mono-Regular',Helvetica] text-[#00021c] text-xs leading-[22px]">KEY: 4d11-e809-aa72</code>
                        </div>
                    </div>
                </article>
            </div>
            <p
                class="flex items-center gap-2 pt-2 [font-family:'Inter-Regular',Helvetica] text-[#46464e] text-xs leading-[22px]">
                <img class="relative w-[14.67px] h-3.5" src="img/icon-7.svg" alt="" aria-hidden="true">
                Semua penandatanganan mematuhi UU ITE No. 11/2008 &amp; PP No. 71/2019 tentang Penyelenggaraan
                Sistem dan Transaksi Elektronik.
            </p>
        </section>
        <section aria-labelledby="preview-title"
            class="flex flex-col items-start gap-4 p-5 relative flex-1 grow bg-white rounded-lg border border-solid border-[#c7c5cf] shadow-[0px_1px_2px_#0000000d]">
            <header
                class="justify-between pt-0 pb-3 px-0 flex items-center relative self-stretch w-full flex-[0_0_auto] border-b [border-bottom-style:solid] border-[#c7c5cf]">
                <h2 id="preview-title"
                    class="inline-flex items-center gap-2 [font-family:'Plus_Jakarta_Sans-Bold',Helvetica] font-bold text-[#00021c] text-lg leading-6">
                    <img class="relative w-[15px] h-[15px]" src="img/icon-4.svg" alt="" aria-hidden="true">
                    Pratinjau Dokumen
                </h2>
                <span class="inline-flex items-center gap-1.5 text-[#46464e] text-xs">
                    <img class="relative w-3 h-3" src="img/icon.svg" alt="" aria-hidden="true">
                    100%
                </span>
            </header>
            <figure
                class="flex items-start justify-center p-3 relative self-stretch w-full flex-[0_0_auto] bg-[#f2f4f6] rounded overflow-hidden border border-solid border-[#c7c5cf]">
                <div
                    class="flex-col max-w-[420px] items-start gap-[15px] p-6 flex-1 grow self-stretch bg-white rounded-sm border-slate-300 flex relative border border-solid">
                    <div
                        class="flex items-center gap-3 pt-0 pb-3 relative self-stretch w-full flex-[0_0_auto] border-b-2 border-[#00021c]">
                        <div
                            class="w-10 h-10 items-center justify-center bg-slate-50 rounded-sm border-slate-300 flex relative border border-solid">
                            <span
                                class="[font-family:'Inter-Bold',Helvetica] font-bold text-slate-800 text-[8px] text-center leading-[10px]">LOGO<br>PNJ</span>
                        </div>
                        <div class="flex flex-col items-center gap-px relative flex-1 grow text-center">
                            <strong
                                class="[font-family:'Inter-Bold',Helvetica] text-slate-900 text-[9px] leading-3">KEMENTERIAN
                                PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</strong>
                            <strong
                                class="[font-family:'Inter-Bold',Helvetica] text-[#151b3d] text-[11px] leading-[13.8px]">POLITEKNIK
                                NEGERI JAKARTA</strong>
                            <strong
                                class="[font-family:'Inter-Bold',Helvetica] text-slate-800 text-[9px] leading-[11.2px]">JURUSAN
                                TEKNIK INFORMATIKA DAN KOMPUTER</strong>
                            <span
                                class="[font-family:'Inter-Regular',Helvetica] text-slate-500 text-[7.5px] leading-[9.4px]">Jl.
                                Prof. Dr. G.A. Siwabessy, Kampus UI Depok 16425 | http://tik.pnj.ac.id</span>
                        </div>
                    </div>
                    <div
                        class="flex flex-col items-center gap-0.5 py-1 relative self-stretch w-full flex-[0_0_auto]">
                        <strong
                            class="[font-family:'Inter-Bold',Helvetica] text-slate-900 text-[11px] text-center leading-[13.8px] underline">SURAT
                            KETERANGAN MAHASISWA AKTIF</strong>
                        <span
                            class="[font-family:'Liberation_Mono-Regular',Helvetica] text-slate-600 text-[8.5px] text-center leading-[10.6px]">Nomor:
                            104/PL3/TIK/AK/2026</span>
                    </div>
                    <div
                        class="flex flex-col items-start gap-[7px] relative self-stretch w-full flex-[0_0_auto]">
                        <p
                            class="[font-family:'Inter-Regular',Helvetica] text-slate-700 text-[9px] leading-[14.6px]">
                            Ketua Jurusan Teknik Informatika dan Komputer Politeknik Negeri Jakarta dengan
                            ini<br>menerangkan dengan sebenarnya bahwa:</p>
                        <dl class="flex flex-col items-start gap-1 pl-3 relative self-stretch w-full">
                            <div class="grid grid-cols-3 grid-rows-[13.81px] h-fit w-full">
                                <dt class="text-slate-500 text-[8.5px]">Nama Lengkap</dt>
                                <dd
                                    class="col-span-2 [font-family:'Liberation_Mono-Bold',Helvetica] text-slate-900 text-[8.5px]">
                                    : Theo Town</dd>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-[13.81px] h-fit w-full">
                                <dt class="text-slate-500 text-[8.5px]">Nomor Induk (NIM)</dt>
                                <dd
                                    class="col-span-2 [font-family:'Liberation_Mono-Bold',Helvetica] text-slate-900 text-[8.5px]">
                                    : 2107411045</dd>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-[13.81px] h-fit w-full">
                                <dt class="text-slate-500 text-[8.5px]">Program Studi</dt>
                                <dd class="col-span-2 text-slate-900 text-[8.5px]">: D4 Teknik Informatika</dd>
                            </div>
                            <div class="grid grid-cols-3 grid-rows-[13.81px] h-fit w-full">
                                <dt class="text-slate-500 text-[8.5px]">Semester / TA</dt>
                                <dd class="col-span-2 text-slate-900 text-[8.5px]">: VIII (Delapan) / Genap
                                    2025/2026</dd>
                            </div>
                        </dl>
                        <p
                            class="[font-family:'Inter-Regular',Helvetica] text-slate-700 text-[9px] leading-[14.6px]">
                            Adalah benar mahasiswa aktif pada Jurusan Teknik Informatika dan Komputer
                            yang<br>terdaftar pada Pangkalan Data Pendidikan Tinggi (PDDikti).</p>
                    </div>
                    <div
                        class="flex items-end justify-center gap-2 pt-[17px] relative self-stretch w-full flex-[0_0_auto] border-t border-slate-200">
                        <div class="flex flex-col items-start gap-0.5 relative flex-1 grow">
                            <strong
                                class="[font-family:'Inter-Bold',Helvetica] text-slate-700 text-[7.5px] leading-[9.4px]">Keterangan
                                Dokumen:</strong>
                            <p
                                class="[font-family:'Inter-Regular',Helvetica] text-slate-500 text-[7.5px] leading-[9.4px]">
                                Dokumen ini ditandatangani secara digital<br>menggunakan sertifikat elektronik
                                resmi PNJ.</p>
                            <code
                                class="[font-family:'Liberation_Mono-Regular',Helvetica] text-slate-400 text-[7px] leading-[8.8px]">UUID: a8f19c4d2e77-2026</code>
                        </div>
                        <div
                            class="flex flex-col items-end p-2 relative flex-1 grow bg-[#f8fafcb2] rounded-sm border border-solid border-[#00021ccc]">
                            <span
                                class="w-8 h-8 flex items-center justify-center bg-white border border-[#00021c] [font-family:'Liberation_Mono-Regular',Helvetica] text-[#00021c] text-[7px] text-center">[QR-<br>TTD]</span>
                            <strong
                                class="[font-family:'Liberation_Mono-Bold',Helvetica] text-[#151b3d] text-[7.5px]">TERVERIFIKASI
                                TTE</strong>
                            <span
                                class="[font-family:'Liberation_Mono-Regular',Helvetica] text-slate-600 text-[7.5px]">Jurusan
                                TIK PNJ</span>
                            <strong
                                class="[font-family:'Liberation_Mono-Bold',Helvetica] text-emerald-700 text-[6.5px]">DIGITALLY
                                SIGNED</strong>
                        </div>
                    </div>
                </div>
                <figcaption class="sr-only">Pratinjau Surat Keterangan Mahasiswa Aktif yang telah ditandatangani
                    secara digital.</figcaption>
            </figure>
        </section>
    </div>
</main>

<?php require_once 'views/layouts/footer.php'; ?>