package com.example.homi.navigation

object Routes {
    const val Splash = "splash"
    const val TampilanAwal = "tampilan_awal"
    const val TampilanAwal2 = "tampilan_awal_2"
    const val TampilanAwal3 = "tampilan_awal_3"

    const val Login = "login"
    const val Daftar = "daftar"
    const val Konfirmasi = "konfirmasi"

    const val Beranda = "beranda"
    const val DetailPengumuman = "detail_pengumuman/{id}"
    fun detailPengumuman(id: Long) = "detail_pengumuman/$id"

    const val Pembayaran = "pembayaran"

    const val FormAjuan1 = "form_ajuan_1"
    const val ProsesPengajuan = "proses_pengajuan"

    const val FormPengaduan = "form_pengaduan"
    const val DetailRiwayatPengaduan = "detail_riwayat_pengaduan"
    const val DetailRiwayatPengajuan = "detail_riwayat_pengajuan"

    const val UbahKataSandi = "ubah_kata_sandi"
    const val LupaKataSandi = "lupa_kata_sandi" // 1 aja, jangan dobel

    // ✅ NEW
    const val Akun = "akun"
    const val LaporkanMasalah = "laporkan_masalah"

    // ✅ TAMBAHAN (biar FormAjuan1 gak error)
    const val SuratDomisili = "surat_domisili"
    const val SuratPengantar = "surat_pengantar"
    const val SuratUsaha = "surat_usaha"
    const val SuratKematian = "surat_kematian"
    const val SuratBelumMenikah = "surat_belum_menikah"

    // ✅ kalau kamu pakai screen status surat
    const val PengajuanSuratStatus = "pengajuan_surat_status"
}
