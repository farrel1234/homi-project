package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextDecoration
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

/* ====== Tokens ====== */
private val BlueMain   = Color(0xFF2F7FA3)
private val BlueText   = Color(0xFF2F7FA3)
private val CardLine   = Color(0xFFE0E0E0)
private val TextDark   = Color(0xFF0E0E0E)
private val LineGray   = Color(0xFFDDDDDD)
private val Success    = Color(0xFF22C55E)
private val Danger     = Color(0xFFEF4444)
private val AccentOrange = Color(0xFFFF9966)
private val BorderBlue = Color(0xFF4D8FB0)
private val LabelGray  = Color(0xFF444444)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

/* ====== Tab enum ====== */
private enum class RiwayatTab { PENGAJUAN, PENGADUAN }

/* ====== Model Pengaduan ====== */
data class RiwayatPengaduanItem(
    val nama: String,
    val tanggal: String,
    val tempat: String,
    val perihal: String,
    val status: StatusPengajuan          // 🔹 status diterima / ditolak
)

/* ====== Model Pengajuan ====== */
enum class StatusPengajuan { DITERIMA, DITOLAK }

data class RiwayatPengajuanItem(
    val nama: String,
    val jenisAjuan: String,
    val tanggal: String,
    val status: StatusPengajuan
)

/* =======================================================
 *  SCREEN GABUNGAN: RIWAYAT PENGAJUAN + PENGADUAN
 * ======================================================= */
@Composable
fun Riwayat1Screen(
    // klik salah satu card RIWAYAT PENGADUAN
    onItemClick: (() -> Unit)? = null,
    // klik salah satu card RIWAYAT PENGAJUAN -> kirim STATUS
    onPengajuanItemClick: ((StatusPengajuan) -> Unit)? = null
) {
    // supaya setelah balik dari detail, tetap di tab terakhir
    var selectedTab by rememberSaveable { mutableStateOf(RiwayatTab.PENGADUAN) }

    val pengaduanItems = remember { dummyPengaduanItems() }
    val pengajuanItems = remember { sampleRiwayatPengajuan() }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        /* Header */
        Spacer(Modifier.height(12.dp))
        Text(
            text = "Riwayat Layanan",
            fontFamily = PoppinsSemi,
            fontSize = 22.sp,
            color = Color.White,
            modifier = Modifier.fillMaxWidth(),
            textAlign = TextAlign.Center
        )
        Spacer(Modifier.height(8.dp))
        Text(
            text = "Anda dapat melihat riwayat pengajuan dan pengaduan\n" +
                    "yang telah diajukan oleh Anda",
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = Color.White.copy(alpha = 0.95f),
            textAlign = TextAlign.Center,
            lineHeight = 20.sp,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 8.dp)
        )

        /* White container rounded */
        Spacer(Modifier.height(16.dp))
        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Spacer(Modifier.height(16.dp))
            Column(Modifier.fillMaxSize()) {

                /* Tabs */
                Spacer(Modifier.height(14.dp))
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp),
                    horizontalArrangement = Arrangement.spacedBy(12.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Tombol Pengajuan
                    if (selectedTab == RiwayatTab.PENGAJUAN) {
                        Button(
                            onClick = { selectedTab = RiwayatTab.PENGAJUAN },
                            shape = RoundedCornerShape(16.dp),
                            colors = ButtonDefaults.buttonColors(containerColor = BlueMain),
                            modifier = Modifier
                                .weight(1f)
                                .height(48.dp)
                        ) {
                            Text(
                                "Pengajuan",
                                fontFamily = PoppinsSemi,
                                fontSize = 14.sp,
                                color = Color.White
                            )
                        }
                    } else {
                        OutlinedButton(
                            onClick = { selectedTab = RiwayatTab.PENGAJUAN },
                            border = BorderStroke(2.dp, BlueText),
                            colors = ButtonDefaults.outlinedButtonColors(contentColor = BlueText),
                            shape = RoundedCornerShape(16.dp),
                            modifier = Modifier
                                .weight(1f)
                                .height(48.dp)
                        ) {
                            Text("Pengajuan", fontFamily = PoppinsSemi, fontSize = 14.sp)
                        }
                    }

                    // Tombol Pengaduan
                    if (selectedTab == RiwayatTab.PENGADUAN) {
                        Button(
                            onClick = { selectedTab = RiwayatTab.PENGADUAN },
                            shape = RoundedCornerShape(16.dp),
                            colors = ButtonDefaults.buttonColors(containerColor = BlueMain),
                            modifier = Modifier
                                .weight(1f)
                                .height(48.dp)
                        ) {
                            Text(
                                "Pengaduan",
                                fontFamily = PoppinsSemi,
                                fontSize = 14.sp,
                                color = Color.White
                            )
                        }
                    } else {
                        OutlinedButton(
                            onClick = { selectedTab = RiwayatTab.PENGADUAN },
                            border = BorderStroke(2.dp, BlueText),
                            colors = ButtonDefaults.outlinedButtonColors(contentColor = BlueText),
                            shape = RoundedCornerShape(16.dp),
                            modifier = Modifier
                                .weight(1f)
                                .height(48.dp)
                        ) {
                            Text("Pengaduan", fontFamily = PoppinsSemi, fontSize = 14.sp)
                        }
                    }
                }

                /* List berdasarkan tab */
                Spacer(Modifier.height(16.dp))
                when (selectedTab) {
                    RiwayatTab.PENGADUAN -> PengaduanList(
                        items = pengaduanItems,
                        onItemClick = onItemClick
                    )
                    RiwayatTab.PENGAJUAN -> PengajuanList(
                        items = pengajuanItems,
                        onPengajuanItemClick = onPengajuanItemClick
                    )
                }
            }
        }
    }
}

/* =======================================================
 *  LIST & CARD PENGADUAN
 * ======================================================= */

@Composable
private fun PengaduanList(
    items: List<RiwayatPengaduanItem>,
    onItemClick: (() -> Unit)?
) {
    LazyColumn(
        contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp),
        modifier = Modifier.fillMaxSize()
    ) {
        items(items) { item ->
            RiwayatPengaduanCard(
                item = item,
                onClick = { onItemClick?.invoke() }
            )
        }
    }
}

@Composable
private fun RiwayatPengaduanCard(
    item: RiwayatPengaduanItem,
    onClick: () -> Unit
) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() },
        shape = RoundedCornerShape(1.dp),
        border = BorderStroke(1.dp, LineGray),
        colors = CardDefaults.cardColors(
            containerColor = Color(0xFFF9F9F9)
        ),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Column(Modifier.padding(horizontal = 16.dp, vertical = 14.dp)) {
            RowItem(label = "Nama Pelapor :", value = item.nama)
            Spacer(Modifier.height(8.dp))
            RowItem(label = "Tanggal :", value = item.tanggal)
            Spacer(Modifier.height(8.dp))
            RowItem(label = "Tempat :", value = item.tempat)
            Spacer(Modifier.height(8.dp))
            RowItem(
                label = "Perihal :",
                value = item.perihal,
                bold = true,
                color = BlueText
            )
            Spacer(Modifier.height(8.dp))
            // 🔹 Status diterima / ditolak
            RowStatus(status = item.status)
        }
    }
}

/* =======================================================
 *  LIST & CARD PENGAJUAN
 * ======================================================= */

@Composable
private fun PengajuanList(
    items: List<RiwayatPengajuanItem>,
    onPengajuanItemClick: ((StatusPengajuan) -> Unit)?
) {
    LazyColumn(
        contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
        verticalArrangement = Arrangement.spacedBy(10.dp),
        modifier = Modifier.fillMaxSize()
    ) {
        items(items) { item ->
            RiwayatPengajuanCard(
                item = item,
                onClick = { onPengajuanItemClick?.invoke(item.status) } // kirim status
            )
        }
    }
}

@Composable
private fun RiwayatPengajuanCard(
    item: RiwayatPengajuanItem,
    onClick: () -> Unit
) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() },
        shape = RoundedCornerShape(6.dp),
        border = BorderStroke(1.dp, CardLine),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Column(Modifier.padding(horizontal = 16.dp, vertical = 12.dp)) {
            RowField(label = "Nama Pelapor :", value = item.nama)

            Spacer(Modifier.height(6.dp))
            Text(
                text = "Ajuan : ${item.jenisAjuan}",
                fontFamily = PoppinsSemi,
                fontSize = 13.sp,
                color = BlueText,
                textDecoration = TextDecoration.Underline
            )

            Spacer(Modifier.height(6.dp))
            RowField(label = "Tanggal :", value = item.tanggal, valueColor = BlueText)

            Spacer(Modifier.height(6.dp))
            RowStatus(status = item.status)
        }
    }
}

/* =======================================================
 *  KOMONEN KECIL
 * ======================================================= */

@Composable
private fun RowItem(
    label: String,
    value: String,
    bold: Boolean = false,
    color: Color = TextDark
) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        verticalAlignment = Alignment.Top
    ) {
        Text(
            text = label,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = TextDark,
            modifier = Modifier.padding(end = 6.dp)
        )
        Text(
            text = value,
            fontFamily = if (bold) PoppinsSemi else PoppinsReg,
            fontWeight = if (bold) FontWeight.SemiBold else FontWeight.Normal,
            fontSize = 13.sp,
            color = color
        )
    }
}

@Composable
private fun RowField(
    label: String,
    value: String,
    valueColor: Color = TextDark
) {
    Row(verticalAlignment = Alignment.CenterVertically) {
        Text(
            text = label,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = TextDark
        )
        Spacer(Modifier.width(4.dp))
        Text(
            text = value,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = valueColor
        )
    }
}

@Composable
private fun RowStatus(status: StatusPengajuan) {
    Row(verticalAlignment = Alignment.CenterVertically) {
        Text(
            text = "Status :",
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = TextDark
        )
        Spacer(Modifier.width(4.dp))
        when (status) {
            StatusPengajuan.DITOLAK -> Text(
                text = "Di Tolak",
                fontFamily = PoppinsSemi,
                fontSize = 13.sp,
                color = Danger
            )
            StatusPengajuan.DITERIMA -> Text(
                text = "Di Terima",
                fontFamily = PoppinsSemi,
                fontSize = 13.sp,
                color = Success
            )
        }
    }
}

/* =======================================================
 *  DETAIL RIWAYAT PENGAJUAN (DITERIMA / DITOLAK)
 * ======================================================= */

@Composable
fun RiwayatDiterimaScreen(
    jenisPengajuan: String = "Peminjaman Fasilitas",
    namaPelapor: String = "Lily",
    tanggal: String = "1 Oktober 2025",
    tempat: String = "Masjid Perumahan Hawaii Garden",
    perihal: String = "Peminjaman fasilitas masjid untuk acara pengajian warga",
    status: StatusPengajuan = StatusPengajuan.DITERIMA,
    catatan: String =
        "Penggunaan fasilitas bangunan masjid untuk acara pengajian pada tanggal 1 Oktober 2025 akan diumumkan di dashboard Pengumuman.",
    onBack: () -> Unit = {}
) {
    val isDiterima = status == StatusPengajuan.DITERIMA

    Box(modifier = Modifier.fillMaxSize()) {

        // Background gambar biru melengkung (optional)
        Image(
            painter = painterResource(R.drawable.bg_dashboard),
            contentDescription = null,
            contentScale = ContentScale.Crop,
            modifier = Modifier.fillMaxSize()
        )

        // Icon back
        IconButton(
            onClick = { onBack() },
            modifier = Modifier
                .padding(top = 32.dp, start = 16.dp)
                .size(48.dp)
                .align(Alignment.TopStart)
        ) {
            Image(
                painter = painterResource(R.drawable.panahkembali),
                contentDescription = "Kembali",
                modifier = Modifier.size(28.dp)
            )
        }

        Column(
            modifier = Modifier
                .fillMaxSize()
                .statusBarsPadding()
                .navigationBarsPadding()
        ) {
            /* Header */
            Spacer(Modifier.height(8.dp))
            Text(
                text = if (isDiterima) "Pengajuan Diterima" else "Pengajuan Ditolak",
                color = Color.White,
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 20.dp)
            )

            Spacer(Modifier.height(6.dp))
            Text(
                text = if (isDiterima)
                    "Selamat, pengajuan Anda telah diterima."
                else
                    "Maaf, pengajuan Anda ditolak. Lihat detailnya di bawah.",
                color = Color.White.copy(alpha = 0.9f),
                fontFamily = PoppinsReg,
                fontSize = 12.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 20.dp)
            )

            /* Panel putih isi */
            Spacer(Modifier.height(28.dp))
            Surface(
                color = Color.White,
                shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
                tonalElevation = 0.dp,
                shadowElevation = 0.dp,
                modifier = Modifier.fillMaxSize()
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .verticalScroll(rememberScrollState())
                        .padding(horizontal = 20.dp, vertical = 18.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    // Jenis pengajuan
                    Text(
                        text = "Jenis Pengajuan:",
                        fontFamily = PoppinsReg,
                        fontSize = 14.sp,
                        color = LabelGray
                    )
                    Text(
                        text = jenisPengajuan,
                        fontFamily = PoppinsSemi,
                        fontSize = 16.sp,
                        color = if (isDiterima) AccentOrange else Danger
                    )

                    Spacer(modifier = Modifier.height(16.dp))

                    // Card detail
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .border(
                                width = 1.dp,
                                color = BorderBlue,
                                shape = RoundedCornerShape(10.dp)
                            )
                            .padding(horizontal = 16.dp, vertical = 14.dp)
                    ) {
                        ItemField(label = "Nama Pelapor", value = namaPelapor)
                        ItemField(label = "Tanggal", value = tanggal)
                        ItemField(label = "Tempat", value = tempat)
                        ItemField(label = "Perihal", value = perihal)
                        ItemField(
                            label = "Status",
                            value = if (status == StatusPengajuan.DITERIMA) "Diterima" else "Di Tolak",
                            valueColor = if (status == StatusPengajuan.DITERIMA)
                                Color(0xFF1BAE58) else Color(0xFFEF4444)
                        )
                        ItemField(
                            label = "Catatan",
                            value = catatan
                        )
                    }

                    Spacer(modifier = Modifier.height(32.dp))
                }
            }
        }
    }
}

/* ===== ItemField reusable untuk detail pengajuan ===== */
@Composable
private fun ItemField(label: String, value: String, valueColor: Color = Color.Black) {
    Column(modifier = Modifier.fillMaxWidth()) {
        Text(
            text = label,
            fontFamily = PoppinsSemi,
            fontSize = 12.sp,
            color = BlueMain
        )
        Text(
            text = value,
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = valueColor,
            lineHeight = 18.sp
        )
        Divider(
            color = Color(0xFFE5E7EB),
            thickness = 1.dp,
            modifier = Modifier.padding(vertical = 6.dp)
        )
    }
}

/* ====== Dummy data ====== */

private fun dummyPengaduanItems() = listOf(
    RiwayatPengaduanItem(
        nama = "Lily",
        tanggal = "1 Oktober 2025",
        tempat = "di depan lapangan voli",
        perihal = "Sampah berserakan di jalan",
        status = StatusPengajuan.DITERIMA
    ),
    RiwayatPengaduanItem(
        nama = "Lily",
        tanggal = "3 Oktober 2025",
        tempat = "di belakang Blok AA1",
        perihal = "Lampu jalan mati",
        status = StatusPengajuan.DITOLAK
    ),
    RiwayatPengaduanItem(
        nama = "Lily",
        tanggal = "5 Oktober 2025",
        tempat = "area taman bermain",
        perihal = "Selokan tersumbat",
        status = StatusPengajuan.DITERIMA
    )
)

private fun sampleRiwayatPengajuan() = listOf(
    RiwayatPengajuanItem("Lily", "Peminjaman Fasilitas", "1 Oktober 2025", StatusPengajuan.DITOLAK),
    RiwayatPengajuanItem("Lily", "Peminjaman Fasilitas", "2 Oktober 2025", StatusPengajuan.DITERIMA),
    RiwayatPengajuanItem("Lily", "Pengajuan Surat Domisili", "3 Oktober 2025", StatusPengajuan.DITOLAK),
    RiwayatPengajuanItem("Lily", "Izin Keramaian", "4 Oktober 2025", StatusPengajuan.DITERIMA),
)

/* Preview */
@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewRiwayatGabungan() {
    MaterialTheme { Riwayat1Screen() }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
fun PreviewRiwayatDiterimaScreen() {
    MaterialTheme {
        RiwayatDiterimaScreen()
    }
}
