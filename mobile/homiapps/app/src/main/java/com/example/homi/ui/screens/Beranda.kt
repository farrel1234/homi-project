// File: beranda.kt
package com.example.homi.ui.screens

import androidx.annotation.DrawableRes
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextDecoration
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.Dp
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

/* ===== Tokens ===== */
private val BlueMain     = Color(0xFF2F7FA3)
private val BlueButton   = Color(0xFF4F8EA9)
private val AccentOrange = Color(0xFFE26A2C)

private val BlueBorder   = Color(0xFF2F7FA3)
private val TextPrimary  = Color(0xFF0E0E0E)
private val LineGray     = Color(0xFFE6E6E6)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))
private val SuezOne     = FontFamily(Font(R.font.suez_one_regular))

// ✅ jangan private (biar bisa dipakai dari NavHost)
enum class BottomTab { BERANDA, DIREKTORI, RIWAYAT, AKUN }

private data class BottomNavItem(
    val tab: BottomTab,
    val label: String,
    @DrawableRes val iconSelected: Int,
    @DrawableRes val iconUnselected: Int
)

@Composable
fun DashboardScreen(
    startTab: BottomTab = BottomTab.BERANDA,
    onPengajuan: (() -> Unit)? = null,
    onPengaduan: (() -> Unit)? = null,
    onPembayaran: (() -> Unit)? = null,
    onDetailPengumumanClicked: (() -> Unit)? = null,

    onRiwayatItemClick: (() -> Unit)? = null,
    onRiwayatPengajuanItemClick: ((StatusPengajuan) -> Unit)? = null,

    onUbahKataSandi: (() -> Unit)? = null,
    onLaporkanMasalah: (() -> Unit)? = null,
    onKeluarConfirmed: (() -> Unit)? = null,
    onProsesPengajuan: (() -> Unit)? = null,
) {
    var currentTab by rememberSaveable { mutableStateOf(startTab) }

    LaunchedEffect(startTab) { currentTab = startTab }

    Column(modifier = Modifier.fillMaxSize()) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .weight(1f)
        ) {
            when (currentTab) {
                BottomTab.BERANDA -> BerandaSection(
                    onPengajuan = onPengajuan,
                    onPengaduan = onPengaduan,
                    onPembayaran = onPembayaran,
                    onDetailPengumumanClicked = onDetailPengumumanClicked
                )
                BottomTab.DIREKTORI -> DirektoriSection()
                BottomTab.RIWAYAT -> Riwayat1Screen(
                    onItemClick = { onRiwayatItemClick?.invoke() },
                    onPengajuanItemClick = { status -> onRiwayatPengajuanItemClick?.invoke(status) }
                )
                BottomTab.AKUN -> AkunScreen(
                    onUbahKataSandi = onUbahKataSandi,
                    onProsesPengajuan = onProsesPengajuan,
                    onLaporkanMasalah = onLaporkanMasalah,
                    onKeluarConfirmed = { onKeluarConfirmed?.invoke() }
                )
            }
        }

        BottomNavBar(
            currentTab = currentTab,
            onTabSelected = { selected -> currentTab = selected }
        )
    }
}

/* ---------- BERANDA (SCROLLABLE) ---------- */

@Composable
private fun BerandaSection(
    onPengajuan: (() -> Unit)?,
    onPengaduan: (() -> Unit)?,
    onPembayaran: (() -> Unit)?,
    onDetailPengumumanClicked: (() -> Unit)?
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 20.dp, vertical = 14.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Image(
                painter = painterResource(R.drawable.icon_profile),
                contentDescription = "Profil",
                modifier = Modifier
                    .size(80.dp)
                    .clip(CircleShape)
            )
            Spacer(Modifier.width(12.dp))
            Column {
                Text("Hai, Lily", fontFamily = PoppinsSemi, fontSize = 20.sp, color = Color.White)
                Text("Selamat Datang di Homi", fontFamily = PoppinsSemi, fontSize = 20.sp, color = Color.White)
                Text(
                    "Menghubungkan Warga, Membangun Kebersamaan",
                    fontFamily = PoppinsReg,
                    fontSize = 12.sp,
                    color = Color.White
                )
            }
            Spacer(Modifier.weight(1f))
        }

        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            LazyColumn(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 16.dp),
                contentPadding = PaddingValues(top = 18.dp, bottom = 24.dp),
                verticalArrangement = Arrangement.spacedBy(14.dp)
            ) {
                item {
                    Text(
                        text = "Pengumuman",
                        fontFamily = PoppinsSemi,
                        fontSize = 20.sp,
                        color = AccentOrange,
                        modifier = Modifier.fillMaxWidth(),
                        textAlign = TextAlign.Center
                    )
                }

                item {
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(220.dp)
                            .clip(RoundedCornerShape(16.dp))
                    ) {
                        Image(
                            painter = painterResource(R.drawable.img_pengumuman),
                            contentDescription = "Kegiatan Gotong Royong",
                            contentScale = ContentScale.Crop,
                            modifier = Modifier.fillMaxSize()
                        )
                        Box(
                            modifier = Modifier
                                .matchParentSize()
                                .background(Color.Black.copy(alpha = 0.30f))
                        )
                        Text(
                            text = "Kegiatan Gotong Royong",
                            fontFamily = SuezOne,
                            color = Color.White,
                            fontSize = 18.sp,
                            textAlign = TextAlign.Center,
                            textDecoration = TextDecoration.Underline,
                            modifier = Modifier
                                .align(Alignment.TopCenter)
                                .padding(top = 10.dp)
                                .fillMaxWidth()
                                .clickable(enabled = onDetailPengumumanClicked != null) {
                                    onDetailPengumumanClicked?.invoke()
                                }
                        )
                        Text(
                            text = "Jumat/3 Sep 25\nArea Masjid,\nSemua Warga\nPerumahan Hawai Garden",
                            fontFamily = PoppinsReg,
                            fontSize = 12.sp,
                            color = Color.White,
                            modifier = Modifier
                                .align(Alignment.BottomStart)
                                .padding(12.dp)
                        )
                    }
                }

                item {
                    MenuButton(
                        icon = R.drawable.icon_pengajuan,
                        title = "Pengajuan Layanan",
                        onClick = onPengajuan
                    )
                }

                item {
                    MenuButton(
                        icon = R.drawable.icon_pengaduan,
                        title = "Pengaduan Warga",
                        onClick = onPengaduan
                    )
                }

                item {
                    MenuButton(
                        icon = R.drawable.icon_pembayaran,
                        title = "Pembayaran Iuran",
                        onClick = onPembayaran
                    )
                }
            }
        }
    }
}

/* ---------- DIREKTORI ---------- */

@Composable
private fun DirektoriSection() {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        Spacer(Modifier.height(8.dp))
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Spacer(Modifier.width(24.dp))
            Text(
                text = "Direktori",
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                color = Color.White,
                modifier = Modifier.weight(1f),
                textAlign = TextAlign.Center
            )
            Spacer(Modifier.width(24.dp))
        }

        Spacer(Modifier.height(6.dp))
        Text(
            text = "Berikut adalah nama dan alamat warga perumahan\nHawaii Garden",
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = Color.White,
            lineHeight = 18.sp,
            textAlign = TextAlign.Center,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 24.dp)
        )

        Spacer(Modifier.height(24.dp))

        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(16.dp)
            ) {
                Card(
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(44.dp),
                    shape = RoundedCornerShape(24.dp),
                    border = BorderStroke(2.dp, BlueBorder),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
                ) {
                    Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                        Text(
                            text = "🔍 Cari",
                            fontFamily = PoppinsSemi,
                            fontSize = 16.sp,
                            color = BlueBorder
                        )
                    }
                }

                Spacer(Modifier.height(16.dp))
                DirektoriTable(data = sampleDirektori)
            }
        }
    }
}

@Composable
private fun DirektoriTable(data: List<Pair<String, String>>) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(8.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, LineGray, RoundedCornerShape(8.dp))
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(AccentOrange)
                    .clip(RoundedCornerShape(topStart = 8.dp, topEnd = 8.dp))
                    .height(IntrinsicSize.Min)
                    .padding(vertical = 10.dp, horizontal = 12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "Nama",
                    fontFamily = PoppinsSemi,
                    color = Color.White,
                    fontSize = 14.sp,
                    modifier = Modifier.weight(1f)
                )
                Box(
                    modifier = Modifier
                        .fillMaxHeight()
                        .width(1.dp)
                        .background(Color.White.copy(alpha = 0.6f))
                )
                Text(
                    text = "Blok Alamat",
                    fontFamily = PoppinsSemi,
                    color = Color.White,
                    fontSize = 14.sp,
                    modifier = Modifier
                        .weight(1f)
                        .padding(start = 12.dp)
                )
            }

            LazyColumn {
                items(data) { (nama, alamat) ->
                    TableRow(nama, alamat)
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(1.dp)
                            .background(LineGray)
                    )
                }
            }
        }
    }
}

@Composable
private fun TableRow(nama: String, alamat: String) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .heightIn(min = 52.dp)
            .padding(horizontal = 12.dp, vertical = 10.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = nama,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = TextPrimary,
            modifier = Modifier.weight(1f)
        )
        Box(
            modifier = Modifier
                .fillMaxHeight()
                .width(1.dp)
                .background(LineGray)
        )
        Text(
            text = alamat,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = TextPrimary,
            modifier = Modifier
                .weight(1f)
                .padding(start = 12.dp)
        )
    }
}

/* Dummy data Direktori */
private val sampleDirektori = listOf(
    "Awal Abyad" to "Blok I No.3",
    "Biswan" to "Blok AA1 No 10",
    "Sardinia" to "Blok I No.3",
    "Muhammad Iwan" to "Blok III0 No 1",
    "Irwansyah" to "Blok I No.3",
    "Wawan Gustiar" to "Blok AA1 No 10",
    "Irwan Baharuddin" to "Blok I No.3",
    "Muhammad Wawan" to "Blok AA1 No 10",
)

/* ===== Menu card biru (lebih simetris) ===== */
@Composable
private fun MenuButton(
    @DrawableRes icon: Int,
    title: String,
    onClick: (() -> Unit)? = null
) {
    val shape = RoundedCornerShape(16.dp)
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .height(76.dp)
            .clip(shape)
            .background(BlueButton)
            .clickable(enabled = onClick != null) { onClick?.invoke() }
            .padding(horizontal = 14.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        // slot icon fixed biar sejajar/simetris
        Box(
            modifier = Modifier.size(56.dp),
            contentAlignment = Alignment.Center
        ) {
            Image(
                painter = painterResource(icon),
                contentDescription = title,
                modifier = Modifier.size(48.dp),
                contentScale = ContentScale.Fit
            )
        }
        Spacer(Modifier.width(12.dp))
        Text(
            text = title,
            fontFamily = PoppinsSemi,
            color = Color.White,
            fontSize = 16.sp
        )
        Spacer(Modifier.weight(1f))
    }
}

/* ===== Bottom Navigation Bar ===== */
@Composable
private fun BottomNavBar(
    currentTab: BottomTab,
    onTabSelected: (BottomTab) -> Unit
) {
    val items = listOf(
        BottomNavItem(BottomTab.BERANDA, "Beranda", R.drawable.homeoren, R.drawable.icon_home),
        BottomNavItem(BottomTab.DIREKTORI, "Direktori", R.drawable.direktorioren, R.drawable.icon_direktori),
        BottomNavItem(BottomTab.RIWAYAT, "Riwayat", R.drawable.riwayatoren, R.drawable.icon_riwayat),
        BottomNavItem(BottomTab.AKUN, "Akun", R.drawable.akunoren, R.drawable.icon_akun)
    )

    Surface(color = Color(0xFFF6F6F6), tonalElevation = 8.dp, shadowElevation = 8.dp) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 10.dp, vertical = 6.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            items.forEach { item ->
                val selected = item.tab == currentTab
                val iconId = if (selected) item.iconSelected else item.iconUnselected
                val labelColor = if (selected) Color.White else Color(0xFF6C6C6C)

                Column(
                    modifier = Modifier
                        .weight(1f)
                        .clickable { onTabSelected(item.tab) },
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Box(
                        modifier = Modifier
                            .clip(RoundedCornerShape(50))
                            .background(if (selected) AccentOrange else Color.Transparent)
                            .padding(horizontal = 14.dp, vertical = 6.dp),
                        contentAlignment = Alignment.Center
                    ) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Image(
                                painter = painterResource(id = iconId),
                                contentDescription = item.label,
                                modifier = Modifier.size(20.dp),
                                contentScale = ContentScale.Fit
                            )
                            Spacer(Modifier.height(2.dp))
                            Text(
                                text = item.label,
                                fontFamily = PoppinsReg,
                                fontSize = 10.sp,
                                color = labelColor,
                                maxLines = 1
                            )
                        }
                    }
                }
            }
        }
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewDashboardBaru() {
    MaterialTheme { DashboardScreen() }
}
