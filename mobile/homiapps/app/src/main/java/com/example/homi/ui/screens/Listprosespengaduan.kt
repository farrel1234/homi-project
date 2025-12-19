// File: listprosespengaduan.kt
package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
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

/* ====== Tokens (samakan dengan Riwayat1.kt) ====== */
private val BlueMain   = Color(0xFF2F7FA3)
private val BlueText   = Color(0xFF2F7FA3)
private val CardLine   = Color(0xFFE0E0E0)
private val TextDark   = Color(0xFF0E0E0E)
private val LineGray   = Color(0xFFDDDDDD)
private val Success    = Color(0xFF22C55E)
private val Danger     = Color(0xFFEF4444)
private val Warning    = Color(0xFF3B82F6) // untuk "di Selidiki" biar biru seperti contoh
private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

/* ====== Tab enum ====== */
private enum class ProsesTab { PENGAJUAN, PENGADUAN }

/* ====== Status proses (3 state) ====== */
private enum class StatusProses { DI_SELIDIKI, DI_TERIMA, DI_TOLAK }

/* ====== Model Pengaduan ====== */
private data class ProsesPengaduanItem(
    val nama: String,
    val tanggal: String,
    val perihal: String,
    val status: StatusProses
)

/* ====== Model Pengajuan ====== */
private data class ProsesPengajuanItem(
    val nama: String,
    val jenisAjuan: String,
    val tanggal: String,
    val status: StatusProses
)

@Composable
fun ListProsesPengaduanScreen(
    onBack: () -> Unit = {}
) {
    var selectedTab by rememberSaveable { mutableStateOf(ProsesTab.PENGAJUAN) }

    val pengaduanItems = remember { dummyProsesPengaduan() }
    val pengajuanItems = remember { dummyProsesPengajuan() }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        /* Header */
        Spacer(Modifier.height(12.dp))

        // ✅ Box untuk menaruh panah kiri tanpa menggeser judul (judul tetap center)
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp)
        ) {
            IconButton(
                onClick = onBack,
                modifier = Modifier
                    .align(Alignment.TopStart)
                    .size(44.dp)
            ) {
                Image(
                    painter = painterResource(R.drawable.panahkembali),
                    contentDescription = "Kembali",
                    modifier = Modifier.size(26.dp)
                )
            }

            Text(
                text = "Proses Pengajuan\n dan Pengaduan",
                fontFamily = PoppinsSemi,
                fontSize = 22.sp,
                color = Color.White,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 44.dp), // ruang biar tidak ketabrak tombol back
                textAlign = TextAlign.Center,
                lineHeight = 26.sp
            )
        }

        Spacer(Modifier.height(8.dp))
        Text(
            text = "Anda dapat melihat proses pengajuan dan pengaduan\n" +
                    "yang telah di ajukan oleh Anda",
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

                /* Tabs (pola sama seperti Riwayat1.kt) */
                Spacer(Modifier.height(14.dp))
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp),
                    horizontalArrangement = Arrangement.spacedBy(12.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Tombol Pengajuan
                    if (selectedTab == ProsesTab.PENGAJUAN) {
                        Button(
                            onClick = { selectedTab = ProsesTab.PENGAJUAN },
                            shape = RoundedCornerShape(16.dp),
                            colors = ButtonDefaults.buttonColors(containerColor = BlueMain),
                            modifier = Modifier
                                .weight(1f)
                                .height(48.dp)
                        ) {
                            Text("Pengajuan", fontFamily = PoppinsSemi, fontSize = 14.sp, color = Color.White)
                        }
                    } else {
                        OutlinedButton(
                            onClick = { selectedTab = ProsesTab.PENGAJUAN },
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
                    if (selectedTab == ProsesTab.PENGADUAN) {
                        Button(
                            onClick = { selectedTab = ProsesTab.PENGADUAN },
                            shape = RoundedCornerShape(16.dp),
                            colors = ButtonDefaults.buttonColors(containerColor = BlueMain),
                            modifier = Modifier
                                .weight(1f)
                                .height(48.dp)
                        ) {
                            Text("Pengaduan", fontFamily = PoppinsSemi, fontSize = 14.sp, color = Color.White)
                        }
                    } else {
                        OutlinedButton(
                            onClick = { selectedTab = ProsesTab.PENGADUAN },
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
                    ProsesTab.PENGADUAN -> ProsesPengaduanList(items = pengaduanItems)
                    ProsesTab.PENGAJUAN -> ProsesPengajuanList(items = pengajuanItems)
                }
            }
        }
    }
}

/* =======================================================
 *  LIST & CARD: PENGADUAN (TIDAK CLICKABLE)
 * ======================================================= */
@Composable
private fun ProsesPengaduanList(items: List<ProsesPengaduanItem>) {
    LazyColumn(
        contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp),
        modifier = Modifier.fillMaxSize()
    ) {
        items(items) { item ->
            ProsesPengaduanCard(item = item)
        }
    }
}

@Composable
private fun ProsesPengaduanCard(item: ProsesPengaduanItem) {
    Card(
        modifier = Modifier.fillMaxWidth(), // ✅ no clickable
        shape = RoundedCornerShape(1.dp),
        border = BorderStroke(1.dp, LineGray),
        colors = CardDefaults.cardColors(containerColor = Color(0xFFF9F9F9)),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Column(Modifier.padding(horizontal = 16.dp, vertical = 14.dp)) {
            RowText(label = "Nama Pelapor :", value = item.nama)
            Spacer(Modifier.height(8.dp))
            RowText(label = "Tanggal :", value = item.tanggal)
            Spacer(Modifier.height(8.dp))
            RowText(
                label = "Perihal :",
                value = item.perihal,
                bold = true,
                color = BlueText
            )
            Spacer(Modifier.height(8.dp))
            RowStatusProses(status = item.status)
        }
    }
}

/* =======================================================
 *  LIST & CARD: PENGAJUAN (TIDAK CLICKABLE)
 * ======================================================= */
@Composable
private fun ProsesPengajuanList(items: List<ProsesPengajuanItem>) {
    LazyColumn(
        contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
        verticalArrangement = Arrangement.spacedBy(10.dp),
        modifier = Modifier.fillMaxSize()
    ) {
        items(items) { item ->
            ProsesPengajuanCard(item = item)
        }
    }
}

@Composable
private fun ProsesPengajuanCard(item: ProsesPengajuanItem) {
    Card(
        modifier = Modifier.fillMaxWidth(), // ✅ no clickable
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
            RowStatusProses(status = item.status)
        }
    }
}

/* =======================================================
 *  KOMPONEN KECIL (mirip Riwayat1.kt)
 * ======================================================= */
@Composable
private fun RowText(
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
private fun RowStatusProses(status: StatusProses) {
    Row(verticalAlignment = Alignment.CenterVertically) {
        Text(
            text = "Status :",
            fontFamily = PoppinsReg,
            fontSize = 13.sp,
            color = TextDark
        )
        Spacer(Modifier.width(4.dp))

        val (label, color) = when (status) {
            StatusProses.DI_SELIDIKI -> "di Selidiki" to Warning
            StatusProses.DI_TERIMA   -> "Di Terima"   to Success
            StatusProses.DI_TOLAK    -> "Di Tolak"    to Danger
        }

        Text(
            text = label,
            fontFamily = PoppinsSemi,
            fontSize = 13.sp,
            color = color
        )
    }
}

/* =======================================================
 *  Dummy data (sesuai contoh gambar)
 * ======================================================= */
private fun dummyProsesPengaduan() = listOf(
    ProsesPengaduanItem("Lily", "1 Oktober 2025", "Sampah Berserakan di Jalan", StatusProses.DI_SELIDIKI),
    ProsesPengaduanItem("Lily", "1 Oktober 2025", "Sampah Berserakan di Jalan", StatusProses.DI_TERIMA),
    ProsesPengaduanItem("Lily", "1 Oktober 2025", "Sampah Berserakan di Jalan", StatusProses.DI_TOLAK),
    ProsesPengaduanItem("Lily", "1 Oktober 2025", "Sampah Berserakan di Jalan", StatusProses.DI_SELIDIKI),
    ProsesPengaduanItem("Lily", "1 Oktober 2025", "Sampah Berserakan di Jalan", StatusProses.DI_SELIDIKI)
)

private fun dummyProsesPengajuan() = listOf(
    ProsesPengajuanItem("Lily", "Peminjaman Fasilitas", "1 Oktober 2025", StatusProses.DI_TOLAK),
    ProsesPengajuanItem("Lily", "Peminjaman Fasilitas", "1 Oktober 2025", StatusProses.DI_TERIMA),
    ProsesPengajuanItem("Lily", "Peminjaman Fasilitas", "1 Oktober 2025", StatusProses.DI_TOLAK),
    ProsesPengajuanItem("Lily", "Peminjaman Fasilitas", "1 Oktober 2025", StatusProses.DI_TERIMA),
    ProsesPengajuanItem("Lily", "Peminjaman Fasilitas", "1 Oktober 2025", StatusProses.DI_TERIMA)
)

/* =======================================================
 *  Preview
 * ======================================================= */
@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewListProsesPengaduanScreen() {
    MaterialTheme { ListProsesPengaduanScreen(onBack = {}) }
}
