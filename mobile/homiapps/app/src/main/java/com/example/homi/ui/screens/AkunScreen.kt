package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.zIndex
import com.example.homi.R
import kotlinx.coroutines.delay

/* ===== Tokens ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFFF9966)
private val DangerRed    = Color(0xFFF7A477)
private val DividerLine  = Color(0xFFE0E0E0)
private val TextPrimary  = Color(0xFF0E0E0E)
private val OutlineBlue  = Color(0xFF4D8FB0)
private val FieldBg      = Color(0xFFF1F2F4)
private val HintColor    = Color(0xFF9AA4AF)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

@Composable
fun AkunScreen(
    onUbahKataSandi: (() -> Unit)? = null,
    onProsesPengajuan: (() -> Unit)? = null,
    onLaporkanMasalah: (() -> Unit)? = null,
    // dipanggil saat user konfirmasi keluar pada popup
    onKeluarConfirmed: (() -> Unit)? = null,
    onNamaDisimpan: ((String) -> Unit)? = null, // callback opsional setelah Simpan
    initialShowRename: Boolean = false,         // untuk Preview Interactive
    initialShowLogout: Boolean = false,         // untuk Preview Interactive (popup Keluar)
    logoutIllustrationRes: Int = R.drawable.ic_keluar // ganti ke R.drawable.keluar kalau ada
) {
    var showRename by rememberSaveable { mutableStateOf(initialShowRename) }
    var showLogout by rememberSaveable { mutableStateOf(initialShowLogout) }
    var showRenameSuccess by rememberSaveable { mutableStateOf(false) }
    var namaBaru   by rememberSaveable { mutableStateOf("") }

    Box(Modifier.fillMaxSize()) {
        /* ===== HEADER & BODY ===== */
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(BlueMain)
                .statusBarsPadding()
        ) {
            Spacer(Modifier.height(30.dp))

            // Header profil
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(top = 12.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Image(
                    painter = painterResource(R.drawable.icon_profile),
                    contentDescription = "Foto Profil",
                    contentScale = ContentScale.Crop,
                    modifier = Modifier
                        .size(84.dp)
                        .clip(CircleShape)
                )
                Spacer(Modifier.height(8.dp))

                // Klik nama -> Popup Ubah Nama
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    modifier = Modifier.clickable {
                        showRename = true
                        namaBaru = ""
                    }
                ) {
                    Text(
                        text = "Lily",
                        fontFamily = PoppinsSemi,
                        fontSize = 18.sp,
                        color = Color.White
                    )
                    Spacer(Modifier.width(6.dp))
                    Image(
                        painter = painterResource(R.drawable.edit_pen),
                        contentDescription = "Ubah Nama",
                        modifier = Modifier.size(14.dp)
                    )
                }
            }

            Spacer(Modifier.height(20.dp))

            // Kartu menu
            Card(
                modifier = Modifier.fillMaxSize(),
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 20.dp, vertical = 20.dp)
                ) {
                    MenuRow("Ubah Nama") {
                        showRename = true
                        namaBaru = ""
                    }
                    Divider(color = DividerLine, thickness = 1.dp)

                    MenuRow("Ubah Kata Sandi", onClick = onUbahKataSandi)
                    Divider(color = DividerLine, thickness = 1.dp)

                    MenuRow("Proses Pengajuan Layanan", onClick = onProsesPengajuan)
                    Divider(color = DividerLine, thickness = 1.dp)

                    MenuRow("Laporkan Masalah", onClick = onLaporkanMasalah)
                    Divider(color = DividerLine, thickness = 1.dp)

                    // Klik Keluar -> Popup Keluar
                    MenuRow("Keluar") { showLogout = true }
                }
            }
        }

        /* ===== POPUP: Ubah Nama ===== */
        if (showRename) {
            DimOverlay {
                RenamePopupCard(
                    namaBaru = namaBaru,
                    onNamaChange = { namaBaru = it },
                    onBatal = { showRename = false },
                    onSimpan = {
                        val trimmed = namaBaru.trim()
                        if (trimmed.isNotEmpty()) {
                            onNamaDisimpan?.invoke(trimmed)
                            showRename = false
                            showRenameSuccess = true   // tampilkan popup sukses
                        }
                    }
                )
            }
        }

        /* ===== POPUP: Konfirmasi Keluar ===== */
        if (showLogout) {
            DimOverlay {
                LogoutPopupCard(
                    illustrationRes = logoutIllustrationRes,
                    onBatal = { showLogout = false },
                    onKeluar = {
                        showLogout = false
                        onKeluarConfirmed?.invoke()
                    }
                )
            }
        }

        /* ===== POPUP: Berhasil Ubah Nama (auto-close 2 detik) ===== */
        if (showRenameSuccess) {
            DimOverlay {
                RenameSuccessPopup(
                    onTimeout = { showRenameSuccess = false }
                )
            }
        }
    }
}

/* ====== Shared overlay (dim hitam) ====== */
@Composable
private fun DimOverlay(content: @Composable BoxScope.() -> Unit) {
    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0x88000000))
            .zIndex(1f),
        contentAlignment = Alignment.Center,
        content = content
    )
}

/* ====== Popup Card: Ubah Nama ====== */
@Composable
private fun RenamePopupCard(
    namaBaru: String,
    onNamaChange: (String) -> Unit,
    onBatal: () -> Unit,
    onSimpan: () -> Unit
) {
    Card(
        shape = RoundedCornerShape(22.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        border = CardDefaults.outlinedCardBorder().copy(
            width = 2.dp,
            brush = androidx.compose.ui.graphics.SolidColor(OutlineBlue)
        ),
        modifier = Modifier
            .fillMaxWidth(0.86f)
            .wrapContentHeight()
            .shadow(10.dp, RoundedCornerShape(22.dp), clip = false)
            .zIndex(2f)
    ) {
        Column(
            modifier = Modifier.padding(horizontal = 20.dp, vertical = 18.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Image(
                painter = painterResource(R.drawable.ubah_nama),
                contentDescription = "Ubah Nama",
                modifier = Modifier
                    .size(160.dp)
                    .padding(top = 4.dp)
            )

            Spacer(Modifier.height(8.dp))

            Text(
                text = "Ubah Nama Pengguna Baru",
                fontFamily = PoppinsSemi,
                fontSize = 16.sp,
                color = TextPrimary,
                textAlign = TextAlign.Center,
                modifier = Modifier.fillMaxWidth()
            )

            Spacer(Modifier.height(12.dp))

            OutlinedTextField(
                value = namaBaru,
                onValueChange = onNamaChange,
                singleLine = true,
                placeholder = {
                    Text(
                        "Masukkan nama baru…",
                        fontFamily = PoppinsReg,
                        fontSize = 12.sp,
                        color = HintColor
                    )
                },
                shape = RoundedCornerShape(10.dp),
                modifier = Modifier.fillMaxWidth(),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = OutlineBlue,
                    unfocusedBorderColor = OutlineBlue,
                    focusedContainerColor = FieldBg,
                    unfocusedContainerColor = FieldBg,
                    cursorColor = OutlineBlue
                ),
                textStyle = LocalTextStyle.current.copy(
                    fontFamily = PoppinsReg,
                    fontSize = 14.sp,
                    color = TextPrimary
                )
            )

            Spacer(Modifier.height(14.dp))

            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.End,
                verticalAlignment = Alignment.CenterVertically
            ) {
                TextButton(onClick = onBatal) {
                    Text("Batal", fontFamily = PoppinsReg, fontSize = 13.sp, color = Color(0xFF6B7280))
                }
                Spacer(Modifier.width(8.dp))
                val canSave = namaBaru.isNotBlank()
                Button(
                    onClick = onSimpan,
                    enabled = canSave,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = AccentOrange,
                        disabledContainerColor = AccentOrange.copy(alpha = 0.5f)
                    ),
                    shape = RoundedCornerShape(10.dp),
                    contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp)
                ) {
                    Text("Simpan", fontFamily = PoppinsSemi, fontSize = 12.sp, color = Color.White)
                }
            }
        }
    }
}

/* ====== Popup Card: Konfirmasi Keluar ====== */
@Composable
private fun LogoutPopupCard(
    illustrationRes: Int,
    onBatal: () -> Unit,
    onKeluar: () -> Unit
) {
    Card(
        shape = RoundedCornerShape(22.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        border = CardDefaults.outlinedCardBorder().copy(
            width = 2.dp,
            brush = androidx.compose.ui.graphics.SolidColor(OutlineBlue)
        ),
        modifier = Modifier
            .fillMaxWidth(0.86f)
            .wrapContentHeight()
            .shadow(10.dp, RoundedCornerShape(22.dp), clip = false)
            .zIndex(2f)
    ) {
        Column(
            modifier = Modifier.padding(horizontal = 20.dp, vertical = 18.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            // ilustrasi (ganti ke R.drawable.keluar kalau tersedia)
            Image(
                painter = painterResource(illustrationRes),
                contentDescription = "Keluar",
                modifier = Modifier
                    .size(160.dp)
                    .padding(top = 4.dp)
            )

            Spacer(Modifier.height(8.dp))

            Text(
                text = "Keluar Akun?",
                fontFamily = PoppinsSemi,
                fontSize = 16.sp,
                color = TextPrimary,
                textAlign = TextAlign.Center,
                modifier = Modifier.fillMaxWidth()
            )

            Spacer(Modifier.height(6.dp))

            Text(
                text = "Kamu akan keluar dari akun ini. Yakin mau lanjut?",
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                color = Color(0xFF4B5563),
                textAlign = TextAlign.Center,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 8.dp)
            )

            Spacer(Modifier.height(14.dp))

            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.End,
                verticalAlignment = Alignment.CenterVertically
            ) {
                TextButton(onClick = onBatal) {
                    Text("Batal", fontFamily = PoppinsReg, fontSize = 13.sp, color = Color(0xFF6B7280))
                }
                Spacer(Modifier.width(8.dp))
                Button(
                    onClick = onKeluar,
                    colors = ButtonDefaults.buttonColors(containerColor = DangerRed),
                    shape = RoundedCornerShape(10.dp),
                    contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp)
                ) {
                    Text("Keluar", fontFamily = PoppinsSemi, fontSize = 12.sp, color = Color.White)
                }
            }
        }
    }
}

/* ===== Popup: Berhasil Ubah Nama (pakai image) ===== */
@Composable
private fun RenameSuccessPopup(
    onTimeout: () -> Unit
) {
    // Auto close setelah 2 detik
    LaunchedEffect(Unit) {
        delay(2000)
        onTimeout()
    }

    Box(
        modifier = Modifier
            .fillMaxWidth(0.82f)
            .wrapContentHeight()
            .zIndex(2f)
    ) {

        /* ================= LINGKARAN + ICON LONCENG ================= */
        Box(
            modifier = Modifier
                .size(78.dp)
                .align(Alignment.TopCenter)
                .offset(y = 20.dp)
                .clip(CircleShape)
                .background(BlueMain),
            contentAlignment = Alignment.Center
        ) {
            Image(
                painter = painterResource(R.drawable.notif),
                contentDescription = "Berhasil",
                modifier = Modifier.size(40.dp)
            )
        }

        /* ====================== CARD UTAMA ======================= */
        Card(
            shape = RoundedCornerShape(22.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            border = CardDefaults.outlinedCardBorder().copy(
                width = 2.dp,
                brush = androidx.compose.ui.graphics.SolidColor(OutlineBlue)
            ),
            modifier = Modifier
                .align(Alignment.Center)
                .padding(top = 60.dp)
        ) {

            Column(
                modifier = Modifier.padding(horizontal = 20.dp, vertical = 24.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {

                // Ilustrasi sukses
                Image(
                    painter = painterResource(R.drawable.bahagia),
                    contentDescription = "Nama berhasil diganti",
                    modifier = Modifier
                        .size(150.dp)
                        .padding(bottom = 12.dp)
                )

                Text(
                    text = "Nama Pengguna Berhasil\nDi Ganti !",
                    fontFamily = PoppinsSemi,
                    fontSize = 16.sp,
                    color = TextPrimary,
                    textAlign = TextAlign.Center
                )
            }
        }
    }
}

/* ===== Components ===== */
@Composable
private fun MenuRow(title: String, onClick: (() -> Unit)? = null) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clickable(enabled = onClick != null) { onClick?.invoke() }
            .padding(vertical = 16.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = title,
            fontFamily = PoppinsReg,
            fontSize = 14.sp,
            color = TextPrimary
        )
        Image(
            painter = painterResource(R.drawable.panahkembali),
            contentDescription = null,
            modifier = Modifier.size(16.dp)
        )
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewAkunWithPopups() {
    MaterialTheme {
        // previewkan popup ubah nama
        AkunScreen(initialShowRename = true, initialShowLogout = false)
    }
}
