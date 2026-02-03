// File: app/src/main/java/com/example/homi/ui/screens/AkunScreen.kt
package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.Edit
import androidx.compose.material.icons.outlined.ExitToApp
import androidx.compose.material.icons.outlined.HelpOutline
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
private val AccentOrange = Color(0xFFFFA06B)
private val BorderGray   = Color(0xFFE6E6E6)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AkunScreen(
    tokenStore: TokenStore,
    onUbahKataSandi: (() -> Unit)? = null,
    onProsesPengajuan: (() -> Unit)? = null, // kalau kamu punya halaman ini
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

    var showEditName by remember { mutableStateOf(false) }
    var inputName by remember { mutableStateOf("") }
    var loadingUpdate by remember { mutableStateOf(false) }

    var showLogoutConfirm by remember { mutableStateOf(false) }

    // ✅ Auto-sync nama dari server ketika halaman akun kebuka (biar setelah relogin ikut bener)
    LaunchedEffect(Unit) {
        runCatching { accountRepo.fetchMyProfileName() }
            .onSuccess { remoteName ->
                if (!remoteName.isNullOrBlank()) {
                    tokenStore.saveName(remoteName)
                }
            }
    }

    Scaffold(
        snackbarHost = { SnackbarHost(hostState = snackbarHostState) }
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(BlueMain)
                .statusBarsPadding()
                .padding(padding)
        ) {
            Spacer(Modifier.height(10.dp))

            // Header atas
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 18.dp, vertical = 10.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Image(
                    painter = painterResource(R.drawable.icon_profile),
                    contentDescription = "Profil",
                    modifier = Modifier
                        .size(74.dp)
                        .clip(CircleShape)
                )

                Spacer(Modifier.width(12.dp))

                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        text = displayName,
                        fontFamily = PoppinsSemi,
                        fontSize = 18.sp,
                        color = Color.White,
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis
                    )
                    Text(
                        text = "Akun Warga",
                        fontFamily = PoppinsReg,
                        fontSize = 12.sp,
                        color = Color.White.copy(alpha = 0.9f)
                    )
                }

                // tombol edit nama
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

            Spacer(Modifier.height(10.dp))

            // Sheet putih
            Surface(
                color = Color.White,
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                modifier = Modifier.fillMaxSize()
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .navigationBarsPadding()
                        .padding(horizontal = 16.dp, vertical = 18.dp)
                ) {
                    // ===== Menu =====
                    MenuItemRow(
                        title = "Sinkronkan Nama",
                        subtitle = "Ambil nama terbaru dari server",
                        icon = Icons.Outlined.Sync,
                        onClick = {
                            scope.launch {
                                val msg = runCatching { accountRepo.fetchMyProfileName() }
                                    .fold(
                                        onSuccess = { remote ->
                                            if (!remote.isNullOrBlank()) {
                                                tokenStore.saveName(remote)
                                                "Nama berhasil disinkronkan."
                                            } else {
                                                "Nama di server kosong / tidak ditemukan."
                                            }
                                        },
                                        onFailure = { e -> e.message ?: "Gagal sinkron nama." }
                                    )
                                snackbarHostState.showSnackbar(msg)
                            }
                        }
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

                    // optional: proses pengajuan
                    MenuItemRow(
                        title = "Proses Pengajuan",
                        subtitle = "Lihat status proses pengajuan",
                        icon = Icons.Outlined.HelpOutline,
                        enabled = onProsesPengajuan != null,
                        onClick = { onProsesPengajuan?.invoke() }
                    )

                    DividerThin()

                    MenuItemRow(
                        title = "Laporkan Masalah",
                        subtitle = "Hubungi admin/pengurus",
                        icon = Icons.Outlined.HelpOutline,
                        enabled = onLaporkanMasalah != null,
                        onClick = { onLaporkanMasalah?.invoke() }
                    )

                    Spacer(Modifier.height(18.dp))

                    Button(
                        onClick = { showLogoutConfirm = true },
                        colors = ButtonDefaults.buttonColors(containerColor = AccentOrange),
                        shape = RoundedCornerShape(12.dp),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(48.dp)
                    ) {
                        Icon(
                            imageVector = Icons.Outlined.ExitToApp,
                            contentDescription = "Keluar",
                            tint = Color.White
                        )
                        Spacer(Modifier.width(10.dp))
                        Text(
                            text = "Keluar",
                            color = Color.White,
                            fontFamily = PoppinsSemi,
                            fontSize = 15.sp
                        )
                    }
                }
            }
        }
    }

    // ===== Dialog Edit Nama =====
    if (showEditName) {
        AlertDialog(
            onDismissRequest = { if (!loadingUpdate) showEditName = false },
            title = {
                Text("Ubah Nama", fontFamily = PoppinsSemi)
            },
            text = {
                Column {
                    Text(
                        "Nama ini akan ditampilkan di Beranda & Akun.",
                        fontFamily = PoppinsReg,
                        fontSize = 12.sp,
                        color = HintGray
                    )
                    Spacer(Modifier.height(10.dp))
                    TextField(
                        value = inputName,
                        onValueChange = { inputName = it },
                        singleLine = true,
                        shape = RoundedCornerShape(10.dp),
                        textStyle = LocalTextStyle.current.copy(
                            fontFamily = PoppinsReg,
                            fontSize = 14.sp,
                            color = TextDark
                        ),
                        colors = TextFieldDefaults.colors(
                            focusedContainerColor = FieldBg,
                            unfocusedContainerColor = FieldBg,
                            focusedIndicatorColor = BlueMain,
                            unfocusedIndicatorColor = BlueMain,
                            cursorColor = BlueMain
                        ),
                        placeholder = { Text("Masukkan nama...", fontFamily = PoppinsReg, color = HintGray) },
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            },
            confirmButton = {
                TextButton(
                    enabled = !loadingUpdate,
                    onClick = {
                        val newName = inputName.trim()
                        if (newName.isBlank()) {
                            scope.launch { snackbarHostState.showSnackbar("Nama tidak boleh kosong.") }
                            return@TextButton
                        }

                        loadingUpdate = true
                        scope.launch {
                            val msg = runCatching {
                                // 1) update ke server
                                accountRepo.updateNameToServer(newName)
                                // 2) fetch lagi biar sesuai hasil server
                                val fresh = accountRepo.fetchMyProfileName()
                                tokenStore.saveName(fresh?.takeIf { it.isNotBlank() } ?: newName)
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
                    }
                ) {
                    Text("Simpan", fontFamily = PoppinsSemi, color = BlueMain)
                }
            },
            dismissButton = {
                TextButton(
                    enabled = !loadingUpdate,
                    onClick = { showEditName = false }
                ) {
                    Text("Batal", fontFamily = PoppinsReg, color = HintGray)
                }
            }
        )
    }

    // ===== Dialog Konfirmasi Logout =====
    if (showLogoutConfirm) {
        AlertDialog(
            onDismissRequest = { showLogoutConfirm = false },
            title = { Text("Keluar?", fontFamily = PoppinsSemi) },
            text = { Text("Kamu yakin ingin keluar dari akun ini?", fontFamily = PoppinsReg) },
            confirmButton = {
                TextButton(onClick = {
                    showLogoutConfirm = false
                    onKeluarConfirmed?.invoke()
                }) {
                    Text("Ya", fontFamily = PoppinsSemi, color = BlueMain)
                }
            },
            dismissButton = {
                TextButton(onClick = { showLogoutConfirm = false }) {
                    Text("Batal", fontFamily = PoppinsReg, color = HintGray)
                }
            }
        )
    }
}

@Composable
private fun DividerThin() {
    Divider(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 10.dp),
        thickness = 1.dp,
        color = BorderGray
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
            .clip(RoundedCornerShape(14.dp))
            .clickable(enabled = enabled) { onClick() }
            .padding(horizontal = 12.dp, vertical = 12.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Surface(
            shape = CircleShape,
            color = BlueMain.copy(alpha = if (enabled) 0.12f else 0.06f),
            modifier = Modifier.size(40.dp)
        ) {
            Box(contentAlignment = Alignment.Center) {
                Icon(
                    imageVector = icon,
                    contentDescription = title,
                    tint = if (enabled) BlueMain else HintGray
                )
            }
        }

        Spacer(Modifier.width(12.dp))

        Column(modifier = Modifier.weight(1f)) {
            Text(
                text = title,
                fontFamily = PoppinsSemi,
                fontSize = 14.sp,
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
