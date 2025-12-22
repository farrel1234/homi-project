// File: AppNavHostAnimated.kt
package com.example.homi.navigation

import androidx.compose.animation.ExperimentalAnimationApi
import androidx.compose.animation.core.tween
import androidx.compose.animation.fadeIn
import androidx.compose.animation.fadeOut
import androidx.compose.animation.slideInVertically
import androidx.compose.animation.slideOutVertically
import androidx.compose.runtime.Composable
import com.example.homi.ui.screens.*
import com.google.accompanist.navigation.animation.AnimatedNavHost
import com.google.accompanist.navigation.animation.composable
import com.google.accompanist.navigation.animation.rememberAnimatedNavController

@OptIn(ExperimentalAnimationApi::class)
@Composable
fun AppNavHostAnimated() {
    val navController = rememberAnimatedNavController()

    AnimatedNavHost(
        navController = navController,
        startDestination = Routes.Splash
    ) {

        /* =================== SPLASH =================== */
        composable(
            route = Routes.Splash,
            exitTransition = { fadeOut(tween(250)) },
            popEnterTransition = { fadeIn(tween(250)) }
        ) {
            SplashScreen(
                onSplashFinished = {
                    navController.navigate(Routes.TampilanAwal) {
                        popUpTo(Routes.Splash) { inclusive = true }
                    }
                }
            )
        }

        /* =================== INTRO =================== */
        composable(
            route = Routes.TampilanAwal,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            TampilanAwalScreen(
                onNextClicked = { navController.navigate(Routes.TampilanAwal2) }
            )
        }

        composable(
            route = Routes.TampilanAwal2,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            TampilanAwalScreen2(
                onNextClicked = { navController.navigate(Routes.TampilanAwal3) }
            )
        }

        composable(
            route = Routes.TampilanAwal3,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            TampilanAwalScreen3(
                onNextClicked = { navController.navigate(Routes.Login) }
            )
        }

        /* =================== AUTH =================== */
        composable(
            route = Routes.Login,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            LoginScreen(
                onLoginSuccess = {
                    navController.navigate(Routes.Beranda) {
                        popUpTo(Routes.Login) { inclusive = true }
                        launchSingleTop = true
                    }
                },
                onRegisterClicked = { navController.navigate(Routes.Daftar) },
                onForgotPasswordClicked = { navController.navigate(Routes.LupaKataSandi) }
            )
        }

        composable(
            route = Routes.Daftar,
            enterTransition = { fadeIn(tween(250)) },
            exitTransition = { fadeOut(tween(200)) }
        ) {
            DaftarScreen(onGoLogin = { navController.popBackStack() })
        }

        composable(
            route = Routes.Konfirmasi,
            enterTransition = { fadeIn(tween(250)) },
            exitTransition = { fadeOut(tween(200)) }
        ) {
            KonfirmasiDaftarScreen(navController = navController)
        }

        composable(
            route = Routes.LupaKataSandi,
            enterTransition = { fadeIn(tween(250)) },
            exitTransition = { fadeOut(tween(200)) }
        ) {
            LupaKataSandiEmailScreen(
                onBack = { navController.popBackStack() },
                onOtpSent = { _ -> navController.popBackStack(Routes.Login, inclusive = false) }
            )
        }

        /* =================== BERANDA (Dashboard) =================== */
        composable(
            route = Routes.Beranda,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            DashboardScreen(
                onPengajuan = { navController.navigate(Routes.FormAjuan1) },
                onPengaduan = { navController.navigate(Routes.FormPengaduan) },
                onPembayaran = { navController.navigate(Routes.Pembayaran) },
                onDetailPengumumanClicked = { navController.navigate(Routes.DetailPengumuman) },

                // ✅ RIWAYAT: langsung ke screen tujuan (sementara tanpa id)
                onRiwayatItemClick = { navController.navigate(Routes.ProsesPengajuan) },
                onRiwayatPengajuanItemClick = { navController.navigate(Routes.PengajuanSuratStatus) },

                onUbahKataSandi = { navController.navigate(Routes.UbahKataSandi) },
                onLaporkanMasalah = { navController.navigate(Routes.LaporkanMasalah) },

                onProsesPengajuan = { navController.navigate(Routes.ListProsesPengaduan) },
                onKeluarConfirmed = {
                    navController.navigate(Routes.Login) {
                        popUpTo(Routes.Beranda) { inclusive = true }
                    }
                }
            )
        }

        /* =================== BERANDA AKUN (opsional) ===================
           Kalau kamu butuh navigate balik ke tab Akun secara paksa.
           Kalau DashboardScreen kamu punya parameter startTab, tinggal pakai di sini. */
        composable(route = Routes.BerandaAkun) {
            // Kalau DashboardScreen kamu punya startTab, pakai ini:
            // DashboardScreen(startTab = BottomTab.AKUN, ...callbacks sama...)
            DashboardScreen(
                // biar aman, callbacks tetap sama:
                onPengajuan = { navController.navigate(Routes.FormAjuan1) },
                onPengaduan = { navController.navigate(Routes.FormPengaduan) },
                onPembayaran = { navController.navigate(Routes.Pembayaran) },
                onDetailPengumumanClicked = { navController.navigate(Routes.DetailPengumuman) },
                onRiwayatItemClick = { navController.navigate(Routes.ProsesPengajuan) },
                onRiwayatPengajuanItemClick = { navController.navigate(Routes.PengajuanSuratStatus) },
                onUbahKataSandi = { navController.navigate(Routes.UbahKataSandi) },
                onLaporkanMasalah = { navController.navigate(Routes.LaporkanMasalah) },
                onProsesPengajuan = { navController.navigate(Routes.ListProsesPengaduan) },
                onKeluarConfirmed = {
                    navController.navigate(Routes.Login) {
                        popUpTo(Routes.Beranda) { inclusive = true }
                    }
                }
            )
        }

        /* =================== FORM PENGADUAN =================== */
        composable(
            route = Routes.FormPengaduan,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            FormPengaduanScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { _, _, _, _ ->
                    // ✅ submit pengaduan -> stepper
                    navController.navigate(Routes.ProsesPengajuan) { launchSingleTop = true }
                }
            )
        }

        /* =================== PROSES (STEPPER) =================== */
        composable(
            route = Routes.ProsesPengajuan,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            ProsesPengajuanScreen(
                jenisPengajuan = "Pengaduan Warga",
                onBack = { navController.popBackStack() },
                onWhatsappClick = { /* TODO */ }
            )
        }

        /* =================== DETAIL PENGUMUMAN =================== */
        composable(
            route = Routes.DetailPengumuman,
            enterTransition = {
                slideInVertically(animationSpec = tween(320), initialOffsetY = { it / 3 }) + fadeIn(tween(320))
            },
            exitTransition = { fadeOut(tween(200)) },
            popEnterTransition = { fadeIn(tween(220)) },
            popExitTransition = {
                slideOutVertically(animationSpec = tween(260), targetOffsetY = { it / 3 }) + fadeOut(tween(260))
            }
        ) { DetailPengumumanScreen() }

        /* =================== PEMBAYARAN =================== */
        composable(
            route = Routes.Pembayaran,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) { PembayaranIuranScreen(onBack = { navController.popBackStack() }) }

        /* =================== FORM AJUAN 1 =================== */
        composable(
            route = Routes.FormAjuan1,
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) {
            FormAjuan1(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { route ->
                    // route = Routes.SuratDomisili / Routes.SuratPengantar / dll
                    navController.navigate(route)
                }
            )
        }

        /* =================== FORM SURAT ===================
           Kalau nama screen kamu beda (misal tanpa "Form"), sesuaikan aja pemanggilannya. */

        composable(route = Routes.SuratDomisili) {
            FormSuratDomisiliScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { navController.navigate(Routes.PengajuanSuratStatus) }
            )
        }

        composable(route = Routes.SuratPengantar) {
            FormSuratPengantarScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { navController.navigate(Routes.PengajuanSuratStatus) }
            )
        }

        composable(route = Routes.SuratUsaha) {
            // kamu bilang namanya SuratKeteranganUsahaScreen
            FormSuratKeteranganUsahaScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { navController.navigate(Routes.PengajuanSuratStatus) }
            )
        }

        composable(route = Routes.SuratKematian) {
            // kamu bilang namanya SuratKeteranganKematianScreen
            FormSuratKeteranganKematianScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { navController.navigate(Routes.PengajuanSuratStatus) }
            )
        }

        composable(route = Routes.SuratBelumMenikah) {
            FormSuratBelumMenikahScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { navController.navigate(Routes.PengajuanSuratStatus) }
            )
        }

        /* =================== STATUS SURAT =================== */
        composable(route = Routes.PengajuanSuratStatus) {
            PengajuanSuratStatusScreen(
                onBack = { navController.popBackStack() },
                jenisSurat = "Surat Domisili",
                nomorPengajuan = "REQ-0001",
                tanggal = "19 Desember 2025",
                status = SuratStatus.PROCESSED,
                onDownloadPdf = { /* TODO */ }
            )
        }

        /* =================== LAINNYA =================== */
        composable(route = Routes.DetailRiwayatPengaduan) {
            DetailRiwayatPengaduan(onBack = { navController.popBackStack() })
        }

        composable(route = Routes.DetailRiwayatPengajuan) {
            RiwayatDiterimaScreen(onBack = { navController.popBackStack() })
        }

        composable(route = Routes.LaporkanMasalah) {
            LaporkanMasalahScreen(onBack = { navController.popBackStack() })
        }

        composable(route = Routes.UbahKataSandi) {
            UbahKataSandiScreen(
                onBack = { navController.popBackStack() },
                onSelesai = { navController.popBackStack() },
                onLupaKataSandi = { navController.navigate(Routes.LupaKataSandi) }
            )
        }

        composable(route = Routes.ListProsesPengaduan) {
            ListProsesPengaduanScreen(onBack = { navController.popBackStack() })
        }
    }
}
