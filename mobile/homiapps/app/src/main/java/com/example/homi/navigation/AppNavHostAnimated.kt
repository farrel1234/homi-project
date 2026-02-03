package com.example.homi.navigation

import android.os.Build
import android.util.Log
import android.widget.Toast
import androidx.annotation.RequiresApi
import androidx.compose.animation.ExperimentalAnimationApi
import androidx.compose.animation.core.tween
import androidx.compose.animation.fadeIn
import androidx.compose.animation.fadeOut
import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavType
import androidx.navigation.navArgument
import com.example.homi.data.local.TokenStore
import com.example.homi.data.remote.ApiClient
import com.example.homi.data.repository.*
import com.example.homi.ui.screens.*
import com.example.homi.ui.viewmodel.*
import com.example.homi.util.DateUtils
import com.example.homi.util.FileUtils
import com.example.homi.utils.fixLocalhostUrl
import com.google.accompanist.navigation.animation.AnimatedNavHost
import com.google.accompanist.navigation.animation.composable
import com.google.accompanist.navigation.animation.rememberAnimatedNavController
import kotlinx.coroutines.launch

@RequiresApi(Build.VERSION_CODES.O)
@OptIn(ExperimentalAnimationApi::class)
@Composable
fun AppNavHostAnimated(tokenStore: TokenStore) {
    val navController = rememberAnimatedNavController()
    val ctx = LocalContext.current

    // ===== API & REPOS (1x saja) =====
    val api = remember { ApiClient.getApi(tokenStore) }
    val serviceRepo = remember { ServiceRequestRepository(api) }
    val feeRepo = remember { FeeRepository(api) }
    val complaintRepo = remember { ComplaintRepository(api) }
    val directoryRepo = remember { DirectoryRepository(api) }
    val notifRepo = remember { NotificationRepository(api) }
    val accountRepo = remember { AccountRepository(api) }

    val notifVm: NotificationViewModel =
        viewModel(factory = NotificationViewModelFactory(notifRepo))

    AnimatedNavHost(
        navController = navController,
        startDestination = Routes.Splash
    ) {

        // =================== SPLASH & INTRO ===================
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

        composable(
            route = Routes.TampilanAwal,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            TampilanAwalScreen(
                onNextClicked = {
                    navController.navigate(Routes.TampilanAwal2) {
                        launchSingleTop = true
                        popUpTo(Routes.TampilanAwal) { inclusive = true }
                    }
                }
            )
        }

        composable(
            route = Routes.TampilanAwal2,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            TampilanAwalScreen2(
                onNextClicked = {
                    navController.navigate(Routes.TampilanAwal3) {
                        launchSingleTop = true
                        popUpTo(Routes.TampilanAwal2) { inclusive = true }
                    }
                }
            )
        }

        composable(
            route = Routes.TampilanAwal3,
            enterTransition = { fadeIn(tween(300)) },
            exitTransition = { fadeOut(tween(220)) }
        ) {
            TampilanAwalScreen3(
                onNextClicked = {
                    navController.navigate(Routes.Login) {
                        launchSingleTop = true
                        popUpTo(Routes.TampilanAwal3) { inclusive = true }
                    }
                }
            )
        }

        // =================== AUTH ===================
        composable(route = Routes.Login) {
            val authVm: AuthViewModel = viewModel(factory = AuthViewModelFactory(tokenStore))
            val scope = rememberCoroutineScope()

            LoginScreen(
                vm = authVm,
                onLoginSuccess = {
                    scope.launch {
                        runCatching { accountRepo.fetchMyProfileName() }
                            .getOrNull()
                            ?.trim()
                            ?.takeIf { it.isNotBlank() }
                            ?.let { tokenStore.saveName(it) }
                    }

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
            DaftarScreen(
                tokenStore = tokenStore,
                onGoLogin = {
                    navController.navigate(Routes.Login) {
                        launchSingleTop = true
                        popUpTo(Routes.Daftar) { inclusive = true }
                    }
                },
                onGoOtp = { email, job, houseType, housing, block, houseNumber ->
                    navController.currentBackStackEntry?.savedStateHandle?.set("register_email", email)
                    navController.currentBackStackEntry?.savedStateHandle?.set("register_job", job)
                    navController.currentBackStackEntry?.savedStateHandle?.set("register_house_type", houseType)
                    navController.currentBackStackEntry?.savedStateHandle?.set("register_housing", housing)

                    navController.currentBackStackEntry?.savedStateHandle?.set("register_block", block)
                    navController.currentBackStackEntry?.savedStateHandle?.set("register_house_number", houseNumber)

                    navController.navigate(Routes.Konfirmasi)
                }

            )
        }

        composable(
            route = Routes.Konfirmasi,
            enterTransition = { fadeIn(tween(250)) },
            exitTransition = { fadeOut(tween(200)) }
        ) {
            KonfirmasiDaftarScreen(
                navController = navController,
                tokenStore = tokenStore
            )
        }

        composable(route = Routes.LupaKataSandi) {
            LupaKataSandiEmailScreen(
                onBack = { navController.popBackStack() },
                onOtpSent = { _ -> navController.popBackStack(Routes.Login, inclusive = false) }
            )
        }

        // =================== BERANDA ===================
        composable(route = Routes.Beranda) {
            val annVm: AnnouncementViewModel =
                viewModel(factory = AnnouncementViewModelFactory(tokenStore))

            val dirVm: DirectoryViewModel =
                viewModel(factory = DirectoryViewModelFactory(directoryRepo))

            val entry = navController.currentBackStackEntry!!
            val refreshRiwayat by entry.savedStateHandle
                .getStateFlow("refreshRiwayat", false)
                .collectAsState()

            LaunchedEffect(refreshRiwayat) {
                if (refreshRiwayat) entry.savedStateHandle["refreshRiwayat"] = false
            }

            DashboardScreen(
                annVm = annVm,
                tokenStore = tokenStore,
                dirVm = dirVm,
                notifVm = notifVm,
                onNotifications = { navController.navigate(Routes.Notifications) },
                serviceRepo = serviceRepo,
                complaintRepo = complaintRepo,
                refreshRiwayatKey = refreshRiwayat,

                onPengajuan = { navController.navigate(Routes.FormAjuan1) },
                onPengaduan = { navController.navigate(Routes.FormPengaduan) },
                onPembayaran = { navController.navigate(Routes.Pembayaran) },

                onDetailPengumumanClicked = { announcementId ->
                    navController.navigate(Routes.detailPengumuman(announcementId))
                },

                onOpenPengaduanStepper = { id ->
                    navController.navigate(Routes.prosesPengajuanLayanan(id))
                },

                onOpenSuratStatus = { id ->
                    navController.navigate(Routes.pengajuanSuratStatus(id))
                },

                onUbahKataSandi = { navController.navigate(Routes.UbahKataSandi) },
                onLaporkanMasalah = { navController.navigate(Routes.LaporkanMasalah) },

                onKeluarConfirmed = {
                    navController.navigate(Routes.Login) {
                        popUpTo(Routes.Beranda) { inclusive = true }
                        launchSingleTop = true
                    }
                }
            )
        } // ✅ INI penutup composable Beranda (yang kemarin kamu kelewat)

        // =================== NOTIFICATIONS ===================
        composable(route = Routes.Notifications) {
            val vm: NotificationViewModel =
                viewModel(factory = NotificationViewModelFactory(notifRepo))
            NotifikasiScreen(
                vm = vm,
                onBack = { navController.popBackStack() }
            )
        }

        // =================== DETAIL PENGUMUMAN ===================
        composable(
            route = Routes.DetailPengumuman,
            arguments = listOf(navArgument("id") { type = NavType.LongType })
        ) { backStackEntry ->
            val id = backStackEntry.arguments?.getLong("id") ?: return@composable

            // pakai VM yang sama (factory tokenStore)
            val annVm: AnnouncementViewModel = viewModel(
                factory = AnnouncementViewModelFactory(tokenStore)
            )

            // load detail pas masuk screen
            LaunchedEffect(id) { annVm.loadDetail(id) }

                val state by annVm.state.collectAsState()
                val data = state.detail

            if (data != null) {
                DetailPengumumanScreen(
                    announcement = data,
                    onBack = { navController.popBackStack() }
                )
            } else {
                // loading sederhana
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator()
                }
            )
        }

        // =================== PEMBAYARAN (DETAIL + UPLOAD) ===================
        composable(
            route = Routes.PembayaranIuran,
            arguments = listOf(navArgument("invoiceId") { type = NavType.LongType })
        ) { bse ->
            val invoiceId = bse.arguments?.getLong("invoiceId") ?: return@composable
            val ctxLocal = LocalContext.current

            val prev = navController.previousBackStackEntry?.savedStateHandle
            val amount = prev?.get<String>("pay_amount") ?: "-"
            val bulan = prev?.get<String>("pay_bulan") ?: "-"
            val trxId = prev?.get<String>("pay_trxId") ?: "-"

            val serverBase = remember { "http://192.168.1.18:8000" }
            var qrUrl by rememberSaveable { mutableStateOf<String?>(null) }

            fun normalizeQrUrl(raw: String?): String? {
                if (raw.isNullOrBlank()) return null
                var fixed = fixLocalhostUrl(raw) ?: raw

                fixed = fixed
                    .replace("http://localhost", serverBase)
                    .replace("https://localhost", serverBase)
                    .replace("http://127.0.0.1", serverBase)
                    .replace("https://127.0.0.1", serverBase)

                if (fixed.startsWith("/storage/")) {
                    fixed = serverBase.trimEnd('/') + fixed
                }
                return fixed
            }

            LaunchedEffect(invoiceId) {
                try {
                    val qr = feeRepo.getActiveQr()
                    Log.d("QR_DEBUG", "ACTIVE_QR id=${qr.id} raw=${qr.imageUrl}")

                    val fixed = normalizeQrUrl(qr.imageUrl)
                    qrUrl = if (fixed.isNullOrBlank()) null
                    else {
                        val sep = if (fixed.contains("?")) "&" else "?"
                        "$fixed${sep}v=${qr.id}"
                    }
                } catch (e: Exception) {
                    Log.e("QR_DEBUG", "getActiveQr failed: ${e.message}", e)
                    qrUrl = null
                }
            }

            PembayaranIuranScreen(
                amount = amount,
                bulan = bulan,
                transaksiId = trxId,
                qrUrl = qrUrl,
                onBack = { navController.popBackStack() },
                onUploadBukti = { uri ->
                    val part = FileUtils.uriToMultipart(ctxLocal, uri, "proof_image")
                    feeRepo.uploadProof(invoiceId, part)

                    navController.previousBackStackEntry
                        ?.savedStateHandle
                        ?.set("refreshTagihan", true)
                }
            )
        }

        // =================== SURAT ===================
        composable(route = Routes.FormAjuan1) {
            FormAjuan1(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { routeTujuan ->
                    navController.navigate(routeTujuan) { launchSingleTop = true }
                }
            )
        }

        composable(route = Routes.SuratPengantar) {
            val scope = rememberCoroutineScope()
            val ctxLocal = LocalContext.current

            FormSuratPengantarScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { payload ->
                    scope.launch {
                        try {
                            val id = serviceRepo.submitSurat(
                                requestTypeId = 1,
                                subject = "Surat Pengantar",
                                reporterName = payload["nama"] ?: payload["nama_pelapor"] ?: "",
                                requestDateIso = DateUtils.todayIso(),
                                place = "Hawai Garden",
                                dataInput = payload
                            )
                            navController.navigate(Routes.pengajuanSuratStatus(id)) {
                                popUpTo(Routes.FormAjuan1) { inclusive = false }
                                launchSingleTop = true
                            }
                        } catch (e: Exception) {
                            Toast.makeText(ctxLocal, e.message ?: "Gagal submit", Toast.LENGTH_SHORT).show()
                        }
                    }
                }
            )
        }

        composable(route = Routes.SuratDomisili) {
            val scope = rememberCoroutineScope()
            val ctxLocal = LocalContext.current

            FormSuratDomisiliScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { payload ->
                    scope.launch {
                        try {
                            val id = serviceRepo.submitSurat(
                                requestTypeId = 5,
                                subject = "Surat Keterangan Domisili",
                                reporterName = payload["nama"].orEmpty(),
                                requestDateIso = DateUtils.todayIso(),
                                place = "Hawai Garden",
                                dataInput = payload
                            )
                            navController.navigate(Routes.pengajuanSuratStatus(id)) {
                                popUpTo(Routes.FormAjuan1) { inclusive = false }
                                launchSingleTop = true
                            }
                        } catch (e: Exception) {
                            Toast.makeText(ctxLocal, e.message ?: "Gagal submit", Toast.LENGTH_SHORT).show()
                        }
                    }
                }
            )
        }

        composable(route = Routes.SuratKematian) {
            val scope = rememberCoroutineScope()
            val ctxLocal = LocalContext.current

            FormSuratKeteranganKematianScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { payload ->
                    scope.launch {
                        try {
                            val reporter = payload["nama_pelapor"].orEmpty()
                            val id = serviceRepo.submitSurat(
                                requestTypeId = 6,
                                subject = "Surat Keterangan Kematian",
                                reporterName = reporter,
                                requestDateIso = DateUtils.todayIso(),
                                place = "Hawai Garden",
                                dataInput = payload
                            )
                            navController.navigate(Routes.pengajuanSuratStatus(id)) {
                                popUpTo(Routes.FormAjuan1) { inclusive = false }
                                launchSingleTop = true
                            }
                        } catch (e: Exception) {
                            Toast.makeText(ctxLocal, e.message ?: "Gagal submit", Toast.LENGTH_SHORT).show()
                        }
                    }
                }
            )
        }

        composable(route = Routes.SuratUsaha) {
            val scope = rememberCoroutineScope()
            val ctxLocal = LocalContext.current

            FormSuratKeteranganUsahaScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { payload ->
                    scope.launch {
                        try {
                            val id = serviceRepo.submitSurat(
                                requestTypeId = 7,
                                subject = "Surat Keterangan Usaha",
                                reporterName = payload["nama"].orEmpty(),
                                requestDateIso = DateUtils.todayIso(),
                                place = "Hawai Garden",
                                dataInput = payload
                            )
                            navController.navigate(Routes.pengajuanSuratStatus(id)) {
                                popUpTo(Routes.FormAjuan1) { inclusive = false }
                                launchSingleTop = true
                            }
                        } catch (e: Exception) {
                            Toast.makeText(ctxLocal, e.message ?: "Gagal submit", Toast.LENGTH_SHORT).show()
                        }
                    }
                }
            )
        }

        composable(route = Routes.SuratBelumMenikah) {
            val scope = rememberCoroutineScope()
            val ctxLocal = LocalContext.current

            FormSuratBelumMenikahScreen(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { payload ->
                    scope.launch {
                        try {
                            val id = serviceRepo.submitSurat(
                                requestTypeId = 8,
                                subject = "Surat Keterangan Belum Menikah",
                                reporterName = payload["nama"].orEmpty(),
                                requestDateIso = DateUtils.todayIso(),
                                place = "Hawai Garden",
                                dataInput = payload
                            )
                            navController.navigate(Routes.pengajuanSuratStatus(id)) {
                                popUpTo(Routes.FormAjuan1) { inclusive = false }
                                launchSingleTop = true
                            }
                        } catch (e: Exception) {
                            Toast.makeText(ctxLocal, e.message ?: "Gagal submit", Toast.LENGTH_SHORT).show()
                        }
                    }
                }
            )
        }

        composable(
            route = Routes.PengajuanSuratStatus,
            arguments = listOf(navArgument("id") { type = NavType.LongType })
        ) { bse ->
            val id = bse.arguments?.getLong("id") ?: return@composable
            PengajuanSuratStatusScreen(
                id = id,
                repo = serviceRepo,
                onBack = { navController.popBackStack() }
            )
        }

        composable(
            route = Routes.ProsesPengajuanLayanan,
            arguments = listOf(navArgument("id") { type = NavType.LongType }),
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) { backStackEntry ->
            val layananId = backStackEntry.arguments?.getLong("id") ?: 0L
            ProsesPengajuanScreen(
                onBack = { navController.popBackStack() },
                onWhatsappClick = { /* TODO */ }
            )
        }

        // =================== FORM PENGADUAN ===================
        composable(route = Routes.FormPengaduan) {
            FormLaporanScreen(
                complaintRepo = complaintRepo,
                onBack = { navController.popBackStack() },
                onCreated = { createdId: Long ->
                    runCatching { navController.getBackStackEntry(Routes.Beranda) }
                        .getOrNull()
                        ?.savedStateHandle
                        ?.set("refreshRiwayat", true)

                    runCatching { navController.getBackStackEntry(Routes.Beranda) }
                        .getOrNull()
                        ?.savedStateHandle
                        ?.set("last_complaint_id", createdId)

                    navController.navigate(Routes.prosesPengajuanLayanan(createdId)) {
                        launchSingleTop = true
                    }
                }
            )
        }

        // =================== STEPPER (LEGACY tanpa id) ===================
        composable(route = Routes.ProsesPengajuanLayanan) {
            val lastId = runCatching { navController.getBackStackEntry(Routes.Beranda) }
                .getOrNull()
                ?.savedStateHandle
                ?.get<Long>("last_complaint_id")

            if (lastId == null) {
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator()
                }
            } else {
                ProsesPengajuanLayananScreen(
                    complaintId = lastId,
                    complaintRepo = complaintRepo,
                    onBack = { navController.popBackStack() },
                    onWhatsappClick = { /* no-op */ }
                )
            }
        }

        // =================== STEPPER (BY ID) ===================
        composable(
            route = Routes.ProsesPengajuanLayananDetail,
            arguments = listOf(navArgument("id") { type = NavType.LongType })
        ) { bse ->
            val id = bse.arguments?.getLong("id") ?: return@composable

            ProsesPengajuanLayananScreen(
                complaintId = id,
                complaintRepo = complaintRepo,
                onBack = { navController.popBackStack() },
                onWhatsappClick = { /* no-op */ }
            )
        }

        // =================== RIWAYAT (legacy) ===================
        composable(route = Routes.DetailRiwayatPengaduan) {
            DetailRiwayatPengaduan(onBack = { navController.popBackStack() })
        }

        composable(route = Routes.DetailRiwayatPengajuan) {
            RiwayatDiterimaScreen(onBack = { navController.popBackStack() })
        }

        // =================== LAPOR MASALAH ===================
        composable(route = Routes.LaporkanMasalah) {
            LaporkanMasalahScreen(
                onBack = { navController.popBackStack() },
                onGoAkun = { navController.popBackStack() }
            )
        }

        // =================== UBAH PASSWORD ===================
        composable(route = Routes.UbahKataSandi) {
            UbahKataSandiScreen(
                accountRepo = accountRepo,
                onBack = { navController.popBackStack() }
            )
        }
    }
}
