package com.example.homi.navigation

object Routes {
    // ===== SPLASH & INTRO =====
    const val Splash = "splash"
    const val TampilanAwal = "tampilan_awal"
    const val TampilanAwal2 = "tampilan_awal_2"
    const val TampilanAwal3 = "tampilan_awal_3"
    const val TenantSelection = "tenant_selection"

    // ===== AUTH =====
    const val Login = "login"
    const val Daftar = "daftar"
    const val DaftarGoogle = "daftar_google?email={email}&name={name}&googleId={googleId}"
    fun daftarGoogle(email: String, name: String, googleId: String) =
        "daftar_google?email=$email&name=$name&googleId=$googleId"
    const val Konfirmasi = "konfirmasi"
    const val LupaKataSandi = "lupa_kata_sandi"
    const val LupaKataSandiOtp = "lupa_kata_sandi_otp"
    const val LupaKataSandiBaru = "lupa_kata_sandi_baru"

    // ===== MAIN =====
    const val Beranda = "beranda"
    const val Akun = "akun"

    // ✅ NOTIFICATIONS
    const val Notifications = "notifications"

    // ===== PENGUMUMAN =====
    const val DetailPengumuman = "detail_pengumuman/{id}"
    fun detailPengumuman(id: Long) = "detail_pengumuman/$id"

    // ===== PEMBAYARAN (TAGIHAN) =====
    const val Pembayaran = "pembayaran"
    const val PembayaranIuran = "pembayaran_iuran/{invoiceId}"
    fun pembayaranIuran(invoiceId: Long) = "pembayaran_iuran/$invoiceId"

    // ===== SURAT =====
    const val FormAjuan1 = "form_ajuan_1"
    const val FormPengajuanLayanan = "form_pengajuan_layanan"
    const val ProsesPengajuan = "proses_pengajuan"

    const val FormPengaduan = "form_pengaduan"
    const val DetailRiwayatPengaduan = "detail_riwayat_pengaduan/{id}"
    fun detailRiwayatPengaduan(id: Long) = "detail_riwayat_pengaduan/$id"

    const val DetailRiwayatPengajuan = "detail_riwayat_pengajuan/{id}"
    fun detailRiwayatPengajuan(id: Long) = "detail_riwayat_pengajuan/$id"

    const val UbahKataSandi = "ubah_kata_sandi"
    const val LaporkanMasalah = "laporkan_masalah"

    // ✅ TAMBAHAN
    const val SuratDomisili = "surat_domisili"
    const val SuratPengantar = "surat_pengantar"
    const val SuratUsaha = "surat_usaha"
    const val SuratKematian = "surat_kematian"
    const val SuratBelumMenikah = "surat_belum_menikah"

    const val PengajuanSuratStatus = "pengajuan_surat_status/{id}"
    fun pengajuanSuratStatus(id: Long) = "pengajuan_surat_status/$id"

    const val ProsesPengajuanLayanan = "proses_pengajuan_layanan"
    const val ProsesPengajuanLayananDetail = "proses_pengajuan_layanan/{id}"
    fun prosesPengajuanLayanan(id: Long) = "proses_pengajuan_layanan/$id"

    const val CompleteProfile = "complete_profile"
}
