// File: AppNavHostAnimated.kt
package com.example.homi.navigation

import androidx.compose.animation.ExperimentalAnimationApi
import androidx.compose.animation.core.tween
import androidx.compose.animation.fadeIn
import androidx.compose.animation.fadeOut
import androidx.compose.animation.slideInVertically
import androidx.compose.animation.slideOutVertically
import androidx.compose.runtime.Composable
import com.google.accompanist.navigation.animation.AnimatedNavHost
import com.google.accompanist.navigation.animation.composable
import com.google.accompanist.navigation.animation.rememberAnimatedNavController

import com.example.homi.ui.screens.*

@OptIn(ExperimentalAnimationApi::class)
@Composable
fun AppNavHostAnimated() {
    val navController = rememberAnimatedNavController()

    AnimatedNavHost(
        navController = navController,
        startDestination = Routes.Splash
    ) {

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

        composable(route = Routes.TampilanAwal, enterTransition = { fadeIn(tween(300)) }, exitTransition = { fadeOut(tween(220)) }) {
            TampilanAwalScreen(
                onNextClicked = {
                    navController.navigate(Routes.TampilanAwal2) {
                        launchSingleTop = true
                        popUpTo(Routes.TampilanAwal) { inclusive = true }
                    }
                }
            )
        }

        composable(route = Routes.TampilanAwal2, enterTransition = { fadeIn(tween(300)) }, exitTransition = { fadeOut(tween(220)) }) {
            TampilanAwalScreen2(
                onNextClicked = {
                    navController.navigate(Routes.TampilanAwal3) {
                        launchSingleTop = true
                        popUpTo(Routes.TampilanAwal2) { inclusive = true }
                    }
                }
            )
        }

        composable(route = Routes.TampilanAwal3, enterTransition = { fadeIn(tween(300)) }, exitTransition = { fadeOut(tween(220)) }) {
            TampilanAwalScreen3(
                onNextClicked = {
                    navController.navigate(Routes.Login) {
                        launchSingleTop = true
                        popUpTo(Routes.TampilanAwal3) { inclusive = true }
                    }
                }
            )
        }

        composable(route = Routes.Login, enterTransition = { fadeIn(tween(300)) }, exitTransition = { fadeOut(tween(220)) }) {
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

        composable(route = Routes.Daftar, enterTransition = { fadeIn(tween(250)) }, exitTransition = { fadeOut(tween(200)) }) {
            DaftarScreen(
                onGoLogin = {
                    navController.navigate(Routes.Login) {
                        launchSingleTop = true
                        popUpTo(Routes.Daftar) { inclusive = true }
                    }
                }
            )
        }

        composable(route = Routes.Konfirmasi, enterTransition = { fadeIn(tween(250)) }, exitTransition = { fadeOut(tween(200)) }) {
            KonfirmasiDaftarScreen(navController = navController)
        }

        composable(route = Routes.LupaKataSandi, enterTransition = { fadeIn(tween(250)) }, exitTransition = { fadeOut(tween(200)) }) {
            LupaKataSandiEmailScreen(
                onBack = { navController.popBackStack() },
                onOtpSent = { _ ->
                    navController.popBackStack(Routes.Login, inclusive = false)
                }
            )
        }

        // =================== BERANDA ===================
        composable(route = Routes.Beranda, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            DashboardScreen(
                onPengajuan = { navController.navigate(Routes.FormAjuan1) },
                onPengaduan = { navController.navigate(Routes.FormPengaduan) }, // ✅ Beranda → Form Pengaduan
                onPembayaran = { navController.navigate(Routes.Pembayaran) },
                onDetailPengumumanClicked = { navController.navigate(Routes.DetailPengumuman) },
                onRiwayatItemClick = { navController.navigate(Routes.DetailRiwayatPengaduan) },
                onRiwayatPengajuanItemClick = { navController.navigate(Routes.DetailRiwayatPengajuan) },
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

        // =================== FORM PENGADUAN ===================
        composable(route = Routes.FormPengaduan, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            FormPengaduanScreen(
                onBack = { navController.popBackStack() },

                // ✅ INI KUNCINYA:
                // dipanggil setelah popup 2 detik (dari FormPengaduanScreen)
                onKonfirmasi = { _, _, _, _ ->
                    navController.navigate(Routes.PengaduanWarga) {
                        launchSingleTop = true
                    }
                }
            )
        }

        // =================== PENGADUAN WARGA (STEPPER) ===================
        composable(route = Routes.PengaduanWarga, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            PengaduanWargaScreen(
                onBack = { navController.popBackStack() },
                onWhatsappClick = { /* TODO */ }
            )
        }

        // (route lain-lain kamu biarkan seperti sebelumnya)
        composable(route = Routes.DetailPengumuman, enterTransition = {
            slideInVertically(animationSpec = tween(320), initialOffsetY = { it / 3 }) + fadeIn(tween(320))
        }, exitTransition = { fadeOut(tween(200)) }, popEnterTransition = { fadeIn(tween(220)) }, popExitTransition = {
            slideOutVertically(animationSpec = tween(260), targetOffsetY = { it / 3 }) + fadeOut(tween(260))
        }) { DetailPengumumanScreen() }

        composable(route = Routes.Pembayaran, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            PembayaranIuranScreen(onBack = { navController.popBackStack() })
        }

        composable(route = Routes.FormAjuan1, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            FormAjuan1(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { navController.navigate(Routes.ProsesPengajuan) { launchSingleTop = true } }
            )
        }

        composable(route = Routes.ProsesPengajuan, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            ProsesPengajuanScreen(onBack = { navController.popBackStack() }, onWhatsappClick = { })
        }

        composable(route = Routes.DetailRiwayatPengaduan, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            DetailRiwayatPengaduan(onBack = { navController.popBackStack() })
        }

        composable(route = Routes.DetailRiwayatPengajuan, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            RiwayatDiterimaScreen(onBack = { navController.popBackStack() })
        }

        composable(route = Routes.LaporkanMasalah, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            LaporkanMasalahScreen(onBack = { navController.popBackStack() }, onGoAkun = { navController.popBackStack() })
        }

        composable(route = Routes.UbahKataSandi, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            UbahKataSandiScreen(
                onBack = { navController.popBackStack() },
                onSelesai = { navController.popBackStack() },
                onLupaKataSandi = { navController.navigate(Routes.LupaKataSandi) }
            )
        }

        composable(route = Routes.ListProsesPengaduan, enterTransition = { fadeIn(tween(220)) }, exitTransition = { fadeOut(tween(180)) }) {
            ListProsesPengaduanScreen(onBack = { navController.popBackStack() })
        }
    }
}
