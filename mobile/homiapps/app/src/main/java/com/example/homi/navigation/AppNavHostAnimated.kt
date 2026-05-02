package com.example.homi.navigation

import android.os.Build
import android.widget.Toast
import androidx.annotation.RequiresApi
import androidx.compose.animation.core.tween
import androidx.compose.animation.fadeIn
import androidx.compose.animation.fadeOut
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.ReportProblem
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavType
import androidx.navigation.navArgument
import com.example.homi.ui.components.HomiDialog
import com.example.homi.data.local.TokenStore
import com.example.homi.data.model.RequestTypeIds
import com.example.homi.data.remote.ApiClient
import com.example.homi.data.repository.*
import com.example.homi.ui.screens.*
import com.example.homi.ui.viewmodel.*
import com.example.homi.util.FileUtils
import com.example.homi.util.fixLocalhostUrl
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.example.homi.data.remote.ApiConfig
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.launch

@RequiresApi(Build.VERSION_CODES.O)
@Composable
fun AppNavHostAnimated(tokenStore: TokenStore) {
    val navController = rememberNavController()
    val ctxLocal = LocalContext.current
    val scope = rememberCoroutineScope()

    // ===== API & REPOS (1x saja) =====
    val api = remember { ApiClient.getApi(tokenStore) }
    val serviceRepo = remember { ServiceRequestRepository(api) }
    val feeRepo = remember { FeeRepository(api) }
    val complaintRepo = remember { ComplaintRepository(api) }
    val directoryRepo = remember { DirectoryRepository(api) }
    val notifRepo = remember { NotificationRepository(api) }
    val accountRepo = remember { AccountRepository(api) }
    val authRepo = remember { AuthRepository(api) }

    val tenantCode by tokenStore.tenantCodeFlow.collectAsState(initial = "")
    val token by tokenStore.tokenFlow.collectAsState(initial = null)

    val notifVm: NotificationViewModel =
        viewModel(factory = NotificationViewModelFactory(notifRepo))

    NavHost(
        navController = navController,
        startDestination = Routes.Splash
    ) {

        // =================== SPLASH & INTRO ===================
        composable(
            route = Routes.Splash,
            exitTransition = { fadeOut(tween(250)) },
            popEnterTransition = { fadeIn(tween(250)) }
        ) {
            val hasSeenOnboarding by tokenStore.hasSeenOnboardingFlow.collectAsState(initial = null)

            SplashScreen(
                onSplashFinished = {
                    if (hasSeenOnboarding == false) {
                        navController.navigate(Routes.TampilanAwal) {
                            popUpTo(Routes.Splash) { inclusive = true }
                        }
                    } else {
                        // Cek apakah sudah punya kode perumahan
                        val currentTenantCode = kotlinx.coroutines.runBlocking { tokenStore.tenantCodeFlow.first() }
                        val isTenantSet = currentTenantCode != ApiConfig.DEFAULT_TENANT_CODE && currentTenantCode.isNotBlank()

                        if (!isTenantSet) {
                            navController.navigate(Routes.TenantSelection) {
                                popUpTo(Routes.Splash) { inclusive = true }
                            }
                        } else if (!token.isNullOrBlank()) {
                            navController.navigate(Routes.Beranda) {
                                popUpTo(Routes.Splash) { inclusive = true }
                            }
                        } else {
                            navController.navigate(Routes.Login) {
                                popUpTo(Routes.Splash) { inclusive = true }
                            }
                        }
                    }
                }
            )
        }

        composable(
            route = Routes.TampilanAwal,
            enterTransition = { fadeIn(tween(400)) },
            exitTransition = { fadeOut(tween(300)) }
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
            enterTransition = { fadeIn(tween(400)) },
            exitTransition = { fadeOut(tween(300)) }
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
            enterTransition = { fadeIn(tween(400)) },
            exitTransition = { fadeOut(tween(300)) }
        ) {
            val scope = androidx.compose.runtime.rememberCoroutineScope()
            TampilanAwalScreen3(
                onNextClicked = {
                    scope.launch {
                        tokenStore.saveHasSeenOnboarding(true)
                        navController.navigate(Routes.TenantSelection) {
                            launchSingleTop = true
                            popUpTo(Routes.TampilanAwal3) { inclusive = true }
                        }
                    }
                }
            )
        }

        // =================== TENANT SELECTION ===================
        composable(route = Routes.TenantSelection) {
            TenantSelectionScreen(
                onCodeConfirmed = { code ->
                    scope.launch {
                        tokenStore.saveTenantCode(code)
                        navController.navigate(Routes.Login) {
                            popUpTo(Routes.TenantSelection) { inclusive = true }
                            launchSingleTop = true
                        }
                    }
                }
            )
        }

        // =================== AUTH ===================
        composable(route = Routes.Login) {
            val authVm: AuthViewModel = viewModel(factory = AuthViewModelFactory(tokenStore))
            val scope = androidx.compose.runtime.rememberCoroutineScope()

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
                onForgotPasswordClicked = { navController.navigate(Routes.LupaKataSandi) },
                onGoGoogleRegister = { email, name, gid ->
                    navController.navigate(Routes.daftarGoogle(email, name, gid))
                }
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
                onGoOtp = { email, tenant, _, _, _, _ ->
                    navController.currentBackStackEntry?.savedStateHandle?.set("register_email", email)
                    navController.currentBackStackEntry?.savedStateHandle?.set("register_tenant_code", tenant)
                    navController.navigate(Routes.Konfirmasi)
                }

            )
        }

        composable(
            route = Routes.DaftarGoogle,
            arguments = listOf(
                navArgument("email") { defaultValue = "" },
                navArgument("name") { defaultValue = "" },
                navArgument("googleId") { defaultValue = "" }
            )
        ) { bse ->
            val email = bse.arguments?.getString("email").orEmpty()
            val name = bse.arguments?.getString("name").orEmpty()
            val gid = bse.arguments?.getString("googleId").orEmpty()

            DaftarScreen(
                tokenStore = tokenStore,
                preName = name,
                preEmail = email,
                googleId = gid,
                onGoLogin = {
                    navController.navigate(Routes.Login) {
                        launchSingleTop = true
                        popUpTo(Routes.DaftarGoogle) { inclusive = true }
                    }
                },
                onGoOtp = { mail, tenant, _, _, _, _ ->
                    navController.currentBackStackEntry?.savedStateHandle?.set("register_email", mail)
                    navController.currentBackStackEntry?.savedStateHandle?.set("register_tenant_code", tenant)
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
                onRequestOtp = { email -> scope.launch { authRepo.forgotPassword(email, tenantCode) } },
                onOtpSent = { email ->
                    navController.currentBackStackEntry?.savedStateHandle?.set("forgot_email", email)
                    navController.navigate(Routes.LupaKataSandiOtp)
                }
            )
        }

        composable(route = Routes.LupaKataSandiOtp) {
            val email = navController.previousBackStackEntry
                ?.savedStateHandle
                ?.get<String>("forgot_email")
                ?.trim()
                .orEmpty()

            if (email.isBlank()) {
                LaunchedEffect(Unit) { navController.popBackStack() }
            } else {
                LupaKataSandiOtpScreen(
                    email = email,
                    onBack = { navController.popBackStack() },
                    onResendOtp = { target -> scope.launch { authRepo.forgotPassword(target, tenantCode) } },
                    onVerifyOtp = { target, otp ->
                        authRepo.verifyResetOtp(target, otp, tenantCode).resetToken
                    },
                    onVerified = { resetToken ->
                        navController.currentBackStackEntry?.savedStateHandle?.set("reset_token", resetToken)
                        navController.currentBackStackEntry?.savedStateHandle?.set("forgot_email", email)
                        navController.navigate(Routes.LupaKataSandiBaru)
                    }
                )
            }
        }

        composable(route = Routes.LupaKataSandiBaru) {
            val prev = navController.previousBackStackEntry?.savedStateHandle
            val resetToken = prev?.get<String>("reset_token").orEmpty()

            if (resetToken.isBlank()) {
                LaunchedEffect(Unit) { navController.popBackStack(Routes.Login, inclusive = false) }
            } else {
                ResetKataSandiBaruScreen(
                    onBack = { navController.popBackStack() },
                    onSubmitReset = { pass, confirm ->
                        authRepo.resetPassword(
                            resetToken = resetToken,
                            password = pass,
                            passwordConfirmation = confirm
                        )
                    },
                    onSuccessGoLogin = {
                        navController.navigate(Routes.Login) {
                            popUpTo(Routes.Login) { inclusive = true }
                            launchSingleTop = true
                        }
                    }
                )
            }
        }

        // =================== BERANDA ===================
        composable(route = Routes.Beranda) {
            val annVm: AnnouncementViewModel =
                viewModel(factory = AnnouncementViewModelFactory(tokenStore))

            val dirVm: DirectoryViewModel =
                viewModel(factory = DirectoryViewModelFactory(directoryRepo))

            val entry = navController.currentBackStackEntry ?: return@composable
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
                onPengajuanSurat = {
                    scope.launch {
                        if (accountRepo.isProfileComplete()) {
                            navController.navigate(Routes.FormAjuan1)
                        } else {
                            navController.currentBackStackEntry?.savedStateHandle?.set("after_complete_route", Routes.FormAjuan1)
                            navController.navigate(Routes.CompleteProfile)
                        }
                    }
                },
                onPengajuanLayanan = {
                    scope.launch {
                        if (accountRepo.isProfileComplete()) {
                            navController.navigate(Routes.FormPengajuanLayanan)
                        } else {
                            navController.currentBackStackEntry?.savedStateHandle?.set("after_complete_route", Routes.FormPengajuanLayanan)
                            navController.navigate(Routes.CompleteProfile)
                        }
                    }
                },
                onPengaduan = {
                    scope.launch {
                        if (accountRepo.isProfileComplete()) {
                            navController.navigate(Routes.FormPengaduan)
                        } else {
                            navController.currentBackStackEntry?.savedStateHandle?.set("after_complete_route", Routes.FormPengaduan)
                            navController.navigate(Routes.CompleteProfile)
                        }
                    }
                },
                onPembayaran = { navController.navigate(Routes.Pembayaran) },

                onDetailPengumumanClicked = { announcementId ->
                    navController.navigate(Routes.detailPengumuman(announcementId))
                },

                onProsesPengajuan = { id ->
                    navController.navigate(Routes.prosesPengajuanLayanan(id))
                },

                onOpenSuratStatus = { id ->
                    navController.navigate(Routes.pengajuanSuratStatus(id))
                },

                onUbahKataSandi = { navController.navigate(Routes.UbahKataSandi) },
                onEditProfil = { 
                    navController.currentBackStackEntry?.savedStateHandle?.remove<String>("after_complete_route")
                    navController.navigate(Routes.CompleteProfile) 
                },
                onLaporkanMasalah = { navController.navigate(Routes.LaporkanMasalah) },

                onKeluarConfirmed = {
                    navController.navigate(Routes.Login) {
                        popUpTo(Routes.Beranda) { inclusive = true }
                        launchSingleTop = true
                    }
                },
                onDetailRiwayatPengaduan = { id ->
                    navController.navigate(Routes.detailRiwayatPengaduan(id))
                },
                onDetailRiwayatPengajuan = { id ->
                    navController.navigate(Routes.pengajuanSuratStatus(id))
                }
            )
        }

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
            }
        }

        // =================== PEMBAYARAN (DETAIL + UPLOAD) ===================
        composable(
            route = Routes.PembayaranIuran,
            arguments = listOf(navArgument("invoiceId") { type = NavType.LongType })
        ) { bse ->
            val invoiceId = bse.arguments?.getLong("invoiceId") ?: return@composable

            val prev = navController.previousBackStackEntry?.savedStateHandle
            val amount = prev?.get<String>("pay_amount") ?: "-"
            val bulan = prev?.get<String>("pay_bulan") ?: "-"
            val trxId = prev?.get<String>("pay_trx_id") ?: "-"
            var qrUrl by remember(invoiceId) { mutableStateOf<String?>(null) }

            LaunchedEffect(invoiceId) {
                qrUrl = runCatching { feeRepo.getActiveQr().imageUrl }
                    .getOrNull()
                    ?.trim()
                    ?.takeIf { it.isNotBlank() }
                    ?.let { raw ->
                        var fixed = fixLocalhostUrl(raw) ?: raw
                        fixed = fixed
                            .replace("http://localhost", "http://10.0.2.2")
                            .replace("https://localhost", "http://10.0.2.2")
                            .replace("http://127.0.0.1", "http://10.0.2.2")
                            .replace("https://127.0.0.1", "http://10.0.2.2")
                        fixed
                    }
            }

            PembayaranIuranScreen(
                amount = amount,
                bulan = bulan,
                transaksiId = trxId,
                qrUrl = qrUrl,
                onBack = { navController.popBackStack() },
                onUploadBukti = { uri ->
                    val part = FileUtils.uriToMultipart(ctxLocal, uri)
                    feeRepo.uploadProof(invoiceId, part)
                }
            )
        }

        composable(route = Routes.Pembayaran) {
            TagihanIuranScreen(
                feeRepo = feeRepo,
                onBack = { navController.popBackStack() },
                onBayarClick = { _, item ->
                    navController.currentBackStackEntry?.savedStateHandle?.set("pay_amount", item.nominal)
                    navController.currentBackStackEntry?.savedStateHandle?.set("pay_bulan", item.bulan)
                    navController.currentBackStackEntry?.savedStateHandle?.set("pay_trx_id", item.trxId)
                    navController.navigate(Routes.pembayaranIuran(item.invoiceId))
                }
            )
        }

        composable(route = Routes.FormPengajuanLayanan) {
            val vm: SuratSubmitViewModel = viewModel(factory = SuratSubmitViewModelFactory(serviceRepo))
            val state by vm.state.collectAsState()

            LaunchedEffect(state.createdId) {
                val createdId = state.createdId ?: return@LaunchedEffect

                runCatching { navController.getBackStackEntry(Routes.Beranda) }
                    .getOrNull()
                    ?.savedStateHandle
                    ?.set("refreshRiwayat", true)

                navController.navigate(Routes.pengajuanSuratStatus(createdId)) {
                    popUpTo(Routes.Beranda) { inclusive = false }
                    launchSingleTop = true
                }
                vm.reset()
            }

            if (state.isBlockedByArrears) {
                HomiDialog(
                    onDismissRequest = { vm.reset() },
                    title = "Layanan Ditangguhkan",
                    description = state.error,
                    icon = Icons.Outlined.ReportProblem,
                    iconTint = Color(0xFFE26A2C),
                    confirmButtonText = "Lihat Tagihan",
                    onConfirm = {
                        vm.reset()
                        navController.navigate(Routes.Pembayaran)
                    },
                    dismissButtonText = "Tutup",
                    confirmButtonColor = Color(0xFFE26A2C)
                )
            }

            LaunchedEffect(state.error) {
                val msg = state.error ?: return@LaunchedEffect
                if (!state.isBlockedByArrears) {
                    Toast.makeText(ctxLocal, msg, Toast.LENGTH_LONG).show()
                    vm.reset()
                }
            }

            FormPengajuanLayananScreen(
                repo = serviceRepo,
                accountRepo = accountRepo,
                submitting = state.loading,
                onBack = { navController.popBackStack() },
                onSubmit = { requestTypeId, subject, payload ->
                    vm.submit(
                        requestTypeId = requestTypeId,
                        subject = subject,
                        payload = payload
                    )
                }
            )
        }

        // =================== FORM AJUAN SURAT ===================
        composable(route = Routes.FormAjuan1) {
            FormAjuan1(
                onBack = { navController.popBackStack() },
                onKonfirmasi = { route ->
                    navController.navigate(route)
                }
            )
        }

        composable(route = Routes.SuratDomisili) {
            val vm: SuratSubmitViewModel = viewModel(factory = SuratSubmitViewModelFactory(serviceRepo))
            val state by vm.state.collectAsState()

            LaunchedEffect(state.createdId) {
                val createdId = state.createdId ?: return@LaunchedEffect

                runCatching { navController.getBackStackEntry(Routes.Beranda) }
                    .getOrNull()
                    ?.savedStateHandle
                    ?.set("refreshRiwayat", true)

                navController.navigate(Routes.pengajuanSuratStatus(createdId)) {
                    popUpTo(Routes.Beranda) { inclusive = false }
                    launchSingleTop = true
                }
                vm.reset()
            }

            if (state.isBlockedByArrears) {
                HomiDialog(
                    onDismissRequest = { vm.reset() },
                    title = "Layanan Ditangguhkan",
                    description = state.error,
                    icon = Icons.Outlined.ReportProblem,
                    iconTint = Color(0xFFE26A2C),
                    confirmButtonText = "Lihat Tagihan",
                    onConfirm = {
                        vm.reset()
                        navController.navigate(Routes.Pembayaran)
                    },
                    dismissButtonText = "Tutup",
                    confirmButtonColor = Color(0xFFE26A2C)
                )
            }

            LaunchedEffect(state.error) {
                val msg = state.error ?: return@LaunchedEffect
                if (!state.isBlockedByArrears) {
                    Toast.makeText(ctxLocal, msg, Toast.LENGTH_LONG).show()
                    vm.reset()
                }
            }

            FormSuratDomisiliScreen(
                accountRepo = accountRepo,
                onBack = { navController.popBackStack() },
                onKonfirmasi = { payload ->
                    vm.submitByTypeKeywords(
                        fallbackRequestTypeId = RequestTypeIds.SURAT_DOMISILI,
                        typeKeywords = listOf("domisili"),
                        subject = "Pengajuan Surat Domisili",
                        payload = payload
                    )
                }
            )
        }

        composable(route = Routes.SuratPengantar) {
            val vm: SuratSubmitViewModel = viewModel(factory = SuratSubmitViewModelFactory(serviceRepo))
            val state by vm.state.collectAsState()

            LaunchedEffect(state.createdId) {
                val createdId = state.createdId ?: return@LaunchedEffect

                runCatching { navController.getBackStackEntry(Routes.Beranda) }
                    .getOrNull()
                    ?.savedStateHandle
                    ?.set("refreshRiwayat", true)

                navController.navigate(Routes.pengajuanSuratStatus(createdId)) {
                    popUpTo(Routes.Beranda) { inclusive = false }
                    launchSingleTop = true
                }
                vm.reset()
            }

            if (state.isBlockedByArrears) {
                HomiDialog(
                    onDismissRequest = { vm.reset() },
                    title = "Layanan Ditangguhkan",
                    description = state.error,
                    icon = Icons.Outlined.ReportProblem,
                    iconTint = Color(0xFFE26A2C),
                    confirmButtonText = "Lihat Tagihan",
                    onConfirm = {
                        vm.reset()
                        navController.navigate(Routes.Pembayaran)
                    },
                    dismissButtonText = "Tutup",
                    confirmButtonColor = Color(0xFFE26A2C)
                )
            }

            LaunchedEffect(state.error) {
                val msg = state.error ?: return@LaunchedEffect
                if (!state.isBlockedByArrears) {
                    Toast.makeText(ctxLocal, msg, Toast.LENGTH_LONG).show()
                    vm.reset()
                }
            }

            FormSuratPengantarScreen(
                accountRepo = accountRepo,
                onBack = { navController.popBackStack() },
                onKonfirmasi = { payload ->
                    vm.submitByTypeKeywords(
                        fallbackRequestTypeId = RequestTypeIds.SURAT_PENGANTAR,
                        typeKeywords = listOf("pengantar"),
                        subject = "Pengajuan Surat Pengantar",
                        payload = payload
                    )
                }
            )
        }

        composable(route = Routes.SuratUsaha) {
            val vm: SuratSubmitViewModel = viewModel(factory = SuratSubmitViewModelFactory(serviceRepo))
            val state by vm.state.collectAsState()

            LaunchedEffect(state.createdId) {
                val createdId = state.createdId ?: return@LaunchedEffect

                runCatching { navController.getBackStackEntry(Routes.Beranda) }
                    .getOrNull()
                    ?.savedStateHandle
                    ?.set("refreshRiwayat", true)

                navController.navigate(Routes.pengajuanSuratStatus(createdId)) {
                    popUpTo(Routes.Beranda) { inclusive = false }
                    launchSingleTop = true
                }
                vm.reset()
            }

            if (state.isBlockedByArrears) {
                HomiDialog(
                    onDismissRequest = { vm.reset() },
                    title = "Layanan Ditangguhkan",
                    description = state.error,
                    icon = Icons.Outlined.ReportProblem,
                    iconTint = Color(0xFFE26A2C),
                    confirmButtonText = "Lihat Tagihan",
                    onConfirm = {
                        vm.reset()
                        navController.navigate(Routes.Pembayaran)
                    },
                    dismissButtonText = "Tutup",
                    confirmButtonColor = Color(0xFFE26A2C)
                )
            }

            LaunchedEffect(state.error) {
                val msg = state.error ?: return@LaunchedEffect
                if (!state.isBlockedByArrears) {
                    Toast.makeText(ctxLocal, msg, Toast.LENGTH_LONG).show()
                    vm.reset()
                }
            }

            FormSuratKeteranganUsahaScreen(
                accountRepo = accountRepo,
                onBack = { navController.popBackStack() },
                onKonfirmasi = { payload ->
                    vm.submitByTypeKeywords(
                        fallbackRequestTypeId = RequestTypeIds.SURAT_USAHA,
                        typeKeywords = listOf("usaha"),
                        subject = "Pengajuan Surat Keterangan Usaha",
                        payload = payload
                    )
                }
            )
        }

        composable(route = Routes.SuratKematian) {
            val vm: SuratSubmitViewModel = viewModel(factory = SuratSubmitViewModelFactory(serviceRepo))
            val state by vm.state.collectAsState()

            LaunchedEffect(state.createdId) {
                val createdId = state.createdId ?: return@LaunchedEffect

                runCatching { navController.getBackStackEntry(Routes.Beranda) }
                    .getOrNull()
                    ?.savedStateHandle
                    ?.set("refreshRiwayat", true)

                navController.navigate(Routes.pengajuanSuratStatus(createdId)) {
                    popUpTo(Routes.Beranda) { inclusive = false }
                    launchSingleTop = true
                }
                vm.reset()
            }

            if (state.isBlockedByArrears) {
                HomiDialog(
                    onDismissRequest = { vm.reset() },
                    title = "Layanan Ditangguhkan",
                    description = state.error,
                    icon = Icons.Outlined.ReportProblem,
                    iconTint = Color(0xFFE26A2C),
                    confirmButtonText = "Lihat Tagihan",
                    onConfirm = {
                        vm.reset()
                        navController.navigate(Routes.Pembayaran)
                    },
                    dismissButtonText = "Tutup",
                    confirmButtonColor = Color(0xFFE26A2C)
                )
            }

            LaunchedEffect(state.error) {
                val msg = state.error ?: return@LaunchedEffect
                if (!state.isBlockedByArrears) {
                    Toast.makeText(ctxLocal, msg, Toast.LENGTH_LONG).show()
                    vm.reset()
                }
            }

            FormSuratKeteranganKematianScreen(
                accountRepo = accountRepo,
                onBack = { navController.popBackStack() },
                onKonfirmasi = { payload ->
                    vm.submitByTypeKeywords(
                        fallbackRequestTypeId = RequestTypeIds.SURAT_KEMATIAN,
                        typeKeywords = listOf("kematian"),
                        subject = "Pengajuan Surat Keterangan Kematian",
                        payload = payload
                    )
                }
            )
        }

        composable(route = Routes.SuratBelumMenikah) {
            val vm: SuratSubmitViewModel = viewModel(factory = SuratSubmitViewModelFactory(serviceRepo))
            val state by vm.state.collectAsState()

            LaunchedEffect(state.createdId) {
                val createdId = state.createdId ?: return@LaunchedEffect

                runCatching { navController.getBackStackEntry(Routes.Beranda) }
                    .getOrNull()
                    ?.savedStateHandle
                    ?.set("refreshRiwayat", true)

                navController.navigate(Routes.pengajuanSuratStatus(createdId)) {
                    popUpTo(Routes.Beranda) { inclusive = false }
                    launchSingleTop = true
                }
                vm.reset()
            }

            if (state.isBlockedByArrears) {
                HomiDialog(
                    onDismissRequest = { vm.reset() },
                    title = "Layanan Ditangguhkan",
                    description = state.error,
                    icon = Icons.Outlined.ReportProblem,
                    iconTint = Color(0xFFE26A2C),
                    confirmButtonText = "Lihat Tagihan",
                    onConfirm = {
                        vm.reset()
                        navController.navigate(Routes.Pembayaran)
                    },
                    dismissButtonText = "Tutup",
                    confirmButtonColor = Color(0xFFE26A2C)
                )
            }

            LaunchedEffect(state.error) {
                val msg = state.error ?: return@LaunchedEffect
                if (!state.isBlockedByArrears) {
                    Toast.makeText(ctxLocal, msg, Toast.LENGTH_LONG).show()
                    vm.reset()
                }
            }

            FormSuratBelumMenikahScreen(
                accountRepo = accountRepo,
                onBack = { navController.popBackStack() },
                onKonfirmasi = { payload ->
                    vm.submitByTypeKeywords(
                        fallbackRequestTypeId = RequestTypeIds.SURAT_BELUM_MENIKAH,
                        typeKeywords = listOf("belum menikah", "belum_nikah", "single"),
                        subject = "Pengajuan Surat Keterangan Belum Menikah",
                        payload = payload
                    )
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

        // =================== PROSES PENGADUAN (BY ID) ===================
        composable(
            route = Routes.ProsesPengajuanLayananDetail,
            arguments = listOf(navArgument("id") { type = NavType.LongType }),
            enterTransition = { fadeIn(tween(220)) },
            exitTransition = { fadeOut(tween(180)) }
        ) { backStackEntry ->
            val id = backStackEntry.arguments?.getLong("id") ?: 0L
            DetailRiwayatPengaduan(
                complaintId = id,
                complaintRepo = complaintRepo,
                onBack = { navController.popBackStack() }
            )
        }

        // =================== FORM PENGADUAN ===================
        composable(route = Routes.FormPengaduan) {
            FormLaporanScreen(
                complaintRepo = complaintRepo,
                accountRepo = accountRepo,
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
                        popUpTo(Routes.Beranda) { inclusive = false }
                        launchSingleTop = true
                    }
                }
            )
        }

        // =================== PROSES PENGADUAN (LEGACY / TANPA ID) ===================
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
                DetailRiwayatPengaduan(
                    complaintId = lastId,
                    complaintRepo = complaintRepo,
                    onBack = { navController.popBackStack() }
                )
            }
        }

        // =================== RIWAYAT (Modernized) ===================
        composable(
            route = Routes.DetailRiwayatPengaduan,
            arguments = listOf(navArgument("id") { type = NavType.LongType })
        ) { backStackEntry ->
            val id = backStackEntry.arguments?.getLong("id") ?: 0L
            DetailRiwayatPengaduan(
                complaintId = id,
                complaintRepo = complaintRepo,
                onBack = { navController.popBackStack() }
            )
        }

        composable(
            route = Routes.DetailRiwayatPengajuan,
            arguments = listOf(navArgument("id") { type = NavType.LongType })
        ) { backStackEntry ->
            val id = backStackEntry.arguments?.getLong("id") ?: 0L
            DetailRiwayatPengajuan(
                serviceRequestId = id,
                serviceRepo = serviceRepo,
                onBack = { navController.popBackStack() }
            )
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

        // =================== COMPLETE PROFILE ===================
        composable(route = Routes.CompleteProfile) {
            val vm: ProfileViewModel = viewModel(factory = ProfileViewModelFactory(accountRepo))
            CompleteProfileScreen(
                vm = vm,
                onBack = { navController.popBackStack() },
                onSuccess = {
                    // Navigate to stored route OR pop back
                    val afterRoute = navController.previousBackStackEntry?.savedStateHandle?.remove<String>("after_complete_route")
                    if (afterRoute != null) {
                        navController.navigate(afterRoute) {
                            popUpTo(Routes.CompleteProfile) {
                                inclusive = true
                            }
                        }
                    } else {
                        navController.popBackStack()
                    }
                }
            )
        }
    }
}
