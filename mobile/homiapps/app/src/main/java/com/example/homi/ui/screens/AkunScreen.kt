// File: app/src/main/java/com/example/homi/ui/screens/AkunScreen.kt
package com.example.homi.ui.screens

import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.Edit
import androidx.compose.material.icons.automirrored.outlined.ExitToApp
import androidx.compose.material.icons.automirrored.outlined.HelpOutline
import androidx.compose.material.icons.outlined.Lock
import androidx.compose.material.icons.outlined.Sync
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.ui.components.HomiDialog
import com.example.homi.data.local.TokenStore
import com.example.homi.data.remote.ApiClient
import com.example.homi.data.repository.AccountRepository
import kotlinx.coroutines.flow.flow
import kotlinx.coroutines.launch

/* ===== Tokens (ikut gaya kamu) ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val FieldBg      = Color(0xFFF1F2F4)
private val TextDark     = Color(0xFF0E0E0E)
private val HintGray     = Color(0xFF8A8A8A)
private val AccentOrange = Color(0xFFE26A2C)
private val BorderGray   = Color(0xFFE6E6E6)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AkunScreen(
    tokenStore: TokenStore,
    onUbahKataSandi: (() -> Unit)? = null,
    onEditProfil: (() -> Unit)? = null,
    onLaporkanMasalah: (() -> Unit)? = null,
    onKeluarConfirmed: (() -> Unit)? = null
) {
    val scope = rememberCoroutineScope()
    val snackbarHostState = remember { SnackbarHostState() }

    // Repo dibuat di sini biar NavHost gak perlu diubah
    val api = remember { ApiClient.getApi(tokenStore) }
    val accountRepo = remember { AccountRepository(api) }

    // Ambil nama dari TokenStore (biar konsisten ke Beranda)
    val nameFlow = runCatching { tokenStore.nameFlow }.getOrNull() ?: flow { emit("Warga") }
    val savedName by nameFlow.collectAsState(initial = "Warga")
    val displayName = savedName?.trim().takeIf { !it.isNullOrBlank() } ?: "Warga"
    
    val nikFlow = runCatching { tokenStore.nikFlow }.getOrNull() ?: flow { emit("") }
    val savedNik by nikFlow.collectAsState(initial = "")
    val displayNik = savedNik?.trim().takeIf { !it.isNullOrBlank() } ?: "NIK belum tersedia"

    var showEditName by remember { mutableStateOf(false) }
    var inputName by remember { mutableStateOf("") }
    var loadingUpdate by remember { mutableStateOf(false) }

    var showLogoutConfirm by remember { mutableStateOf(false) }

    val context = androidx.compose.ui.platform.LocalContext.current
    var uploadingPhoto by remember { mutableStateOf(false) }

    val photoPickerLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent()
    ) { uri: android.net.Uri? ->
        uri?.let {
            scope.launch {
                uploadingPhoto = true
                try {
                    val file = java.io.File(context.cacheDir, "temp_profile.jpg")
                    context.contentResolver.openInputStream(it)?.use { input ->
                        file.outputStream().use { output -> input.copyTo(output) }
                    }
                    accountRepo.updateProfilePhoto(file)
                    // Refresh profile to get new photo (if needed)
                    snackbarHostState.showSnackbar("Foto profil berhasil diperbarui.")
                } catch (e: Exception) {
                    snackbarHostState.showSnackbar("Gagal unggah foto: ${e.message}")
                } finally {
                    uploadingPhoto = false
                }
            }
        }
    }

    // ✅ Auto-sync nama & NIK dari server ketika halaman akun kebuka
    LaunchedEffect(Unit) {
        runCatching { accountRepo.fetchMyProfileData() }
            .onSuccess { (remoteName, remoteNik) ->
                if (remoteName.isNotBlank()) tokenStore.saveName(remoteName)
                if (remoteNik.isNotBlank()) tokenStore.saveNik(remoteNik)
            }
    }

    Scaffold(
        snackbarHost = { SnackbarHost(hostState = snackbarHostState) },
        containerColor = Color.White
    ) { padding ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(Color.White)
        ) {
            // Header Background Blue
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(220.dp)
                    .background(BlueMain)
            )

            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .statusBarsPadding()
                    .padding(top = padding.calculateTopPadding())
            ) {
                Spacer(Modifier.height(10.dp))

                // User Info Header
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 18.dp, vertical = 20.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Box(contentAlignment = Alignment.BottomEnd) {
                        Box(
                            modifier = Modifier
                                .size(80.dp)
                                .clip(CircleShape)
                                .background(Color.White.copy(alpha = 0.2f)),
                            contentAlignment = Alignment.Center
                        ) {
                            if (uploadingPhoto) {
                                CircularProgressIndicator(color = Color.White, modifier = Modifier.size(24.dp))
                            } else {
                                Image(
                                    painter = painterResource(R.drawable.icon_profile),
                                    contentDescription = "Profil",
                                    modifier = Modifier.fillMaxSize()
                                )
                            }
                        }
                        
                        // Edit Photo Button
                        Surface(
                            shape = CircleShape,
                            color = Color.White,
                            shadowElevation = 4.dp,
                            modifier = Modifier
                                .size(28.dp)
                                .clickable { photoPickerLauncher.launch("image/*") }
                        ) {
                            Box(contentAlignment = Alignment.Center) {
                                Icon(
                                    imageVector = Icons.Outlined.Edit,
                                    contentDescription = "Ganti Foto",
                                    tint = BlueMain,
                                    modifier = Modifier.size(14.dp)
                                )
                            }
                        }
                    }

                    Spacer(Modifier.width(16.dp))

                    Column(modifier = Modifier.weight(1f)) {
                        Text(
                            text = displayName,
                            fontFamily = PoppinsSemi,
                            fontSize = 20.sp,
                            color = Color.White,
                            maxLines = 1,
                            overflow = TextOverflow.Ellipsis
                        )
                        Text(
                            text = "Warga Homi Garden",
                            fontFamily = PoppinsReg,
                            fontSize = 13.sp,
                            color = Color.White.copy(alpha = 0.8f)
                        )
                    }

                    IconButton(
                        onClick = {
                            inputName = displayName
                            showEditName = true
                        }
                    ) {
                        Icon(
                            imageVector = Icons.Outlined.Edit,
                            contentDescription = "Ubah Nama",
                            tint = Color.White
                        )
                    }
                }

                // White Sheet Content
                Surface(
                    color = Color.White,
                    shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                    modifier = Modifier
                        .fillMaxWidth()
                        .weight(1f)
                ) {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState()) // Tambahkan scroll agar aman jika menu banyak
                            .padding(horizontal = 20.dp, vertical = 24.dp)
                    ) {
                        Text(
                            "Pengaturan Akun",
                            fontFamily = PoppinsSemi,
                            fontSize = 15.sp,
                            color = BlueMain,
                            modifier = Modifier.padding(bottom = 12.dp, start = 8.dp)
                        )

                        MenuItemRow(
                            title = "Informasi Diri",
                            subtitle = "Lengkapi NIK, Alamat, TTL, Tipe Rumah",
                            icon = Icons.Outlined.Edit,
                            enabled = onEditProfil != null,
                            onClick = { onEditProfil?.invoke() }
                        )

                        DividerThin()

                        MenuItemRow(
                            title = "Ubah Kata Sandi",
                            subtitle = "Perbarui keamanan akun",
                            icon = Icons.Outlined.Lock,
                            enabled = onUbahKataSandi != null,
                            onClick = { onUbahKataSandi?.invoke() }
                        )

                        DividerThin()

                        MenuItemRow(
                            title = "Sinkronkan Data",
                            subtitle = "Perbarui data dari server",
                            icon = Icons.Outlined.Sync,
                            onClick = {
                                scope.launch {
                                    val msg = runCatching { accountRepo.fetchMyProfileName() }
                                        .fold(
                                            onSuccess = { remote ->
                                                if (!remote.isNullOrBlank()) {
                                                    tokenStore.saveName(remote)
                                                    "Data berhasil disinkronkan."
                                                } else {
                                                    "Data di server kosong."
                                                }
                                            },
                                            onFailure = { e -> e.message ?: "Gagal sinkron data." }
                                        )
                                    snackbarHostState.showSnackbar(msg)
                                }
                            }
                        )
                        
                        DividerThin()

                        MenuItemRow(
                            title = "Laporkan Masalah",
                            subtitle = "Bantuan & Dukungan",
                            icon = Icons.AutoMirrored.Outlined.HelpOutline,
                            enabled = onLaporkanMasalah != null,
                            onClick = { onLaporkanMasalah?.invoke() }
                        )

                        Spacer(Modifier.weight(1f))

                        Button(
                            onClick = { showLogoutConfirm = true },
                            colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFFF1F0)),
                            shape = RoundedCornerShape(16.dp),
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(56.dp)
                        ) {
                            Icon(
                                imageVector = Icons.AutoMirrored.Outlined.ExitToApp,
                                contentDescription = "Keluar",
                                tint = Color(0xFFDC2626)
                            )
                            Spacer(Modifier.width(10.dp))
                            Text(
                                text = "Keluar dari Akun",
                                color = Color(0xFFDC2626),
                                fontFamily = PoppinsSemi,
                                fontSize = 16.sp
                            )
                        }
                        
                        Spacer(Modifier.height(16.dp))
                    }
                }
            }
        }
    }

    // ===== Dialog Edit Nama =====
    if (showEditName) {
        HomiDialog(
            onDismissRequest = { if (!loadingUpdate) showEditName = false },
            title = "Ubah Nama",
            description = "Nama ini akan ditampilkan di Beranda & Akun.",
            icon = Icons.Outlined.Edit,
            confirmButtonText = "Simpan",
            onConfirm = {
                val newNameTrimmed = inputName.trim()
                if (newNameTrimmed.isBlank()) {
                    scope.launch { snackbarHostState.showSnackbar("Nama tidak boleh kosong.") }
                    return@HomiDialog
                }

                loadingUpdate = true
                scope.launch {
                    val msg = runCatching {
                        // 1) update ke server
                        accountRepo.updateNameToServer(newNameTrimmed)
                        // 2) fetch lagi biar sesuai hasil server
                        val fresh = accountRepo.fetchMyProfileName()
                        tokenStore.saveName(fresh ?: newNameTrimmed)
                    }.fold(
                        onSuccess = {
                            showEditName = false
                            "Nama berhasil diubah."
                        },
                        onFailure = { e ->
                            e.message ?: "Gagal mengubah nama."
                        }
                    )

                    loadingUpdate = false
                    snackbarHostState.showSnackbar(msg)
                }
            },
            dismissButtonText = "Batal",
            onDismiss = { showEditName = false },
            content = {
                OutlinedTextField(
                    value = inputName,
                    onValueChange = { inputName = it },
                    singleLine = true,
                    shape = RoundedCornerShape(12.dp),
                    textStyle = LocalTextStyle.current.copy(
                        fontFamily = PoppinsReg,
                        fontSize = 14.sp,
                        color = TextDark
                    ),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = BlueMain,
                        unfocusedBorderColor = BorderGray,
                    ),
                    placeholder = { Text("Masukkan nama...", fontFamily = PoppinsReg, color = HintGray) },
                    modifier = Modifier.fillMaxWidth()
                )
            }
        )
    }

    // ===== Dialog Konfirmasi Logout =====
    if (showLogoutConfirm) {
        HomiDialog(
            onDismissRequest = { showLogoutConfirm = false },
            title = "Keluar?",
            description = "Kamu yakin ingin keluar dari akun ini?",
            icon = Icons.AutoMirrored.Outlined.ExitToApp,
            iconTint = Color(0xFFDC2626),
            confirmButtonText = "Keluar",
            confirmButtonColor = Color(0xFFDC2626),
            onConfirm = {
                showLogoutConfirm = false
                onKeluarConfirmed?.invoke()
            },
            dismissButtonText = "Batal",
            onDismiss = { showLogoutConfirm = false }
        )
    }
}

@Composable
private fun DividerThin() {
    HorizontalDivider(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 8.dp),
        thickness = 1.dp,
        color = BorderGray.copy(alpha = 0.5f)
    )
}

@Composable
private fun MenuItemRow(
    title: String,
    subtitle: String,
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    enabled: Boolean = true,
    onClick: () -> Unit
) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(16.dp))
            .clickable(enabled = enabled) { onClick() }
            .padding(horizontal = 8.dp, vertical = 14.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Surface(
            shape = CircleShape,
            color = if (enabled) BlueMain.copy(alpha = 0.1f) else Color(0xFFF5F5F5),
            modifier = Modifier.size(44.dp)
        ) {
            Box(contentAlignment = Alignment.Center) {
                Icon(
                    imageVector = icon,
                    contentDescription = title,
                    tint = if (enabled) BlueMain else HintGray,
                    modifier = Modifier.size(22.dp)
                )
            }
        }

        Spacer(Modifier.width(16.dp))

        Column(modifier = Modifier.weight(1f)) {
            Text(
                text = title,
                fontFamily = PoppinsSemi,
                fontSize = 15.sp,
                color = if (enabled) TextDark else HintGray
            )
            Text(
                text = subtitle,
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = HintGray
            )
        }
    }
}
