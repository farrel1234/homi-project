package com.example.homi.ui.screens

import androidx.compose.foundation.BorderStroke

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
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
private val Success    = Color(0xFF22C55E)
private val Danger     = Color(0xFFEF4444)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

/* ====== Model ====== */
enum class StatusPengajuan { DITERIMA, DITOLAK }

data class RiwayatPengajuanItem(
    val nama: String,
    val jenisAjuan: String,
    val tanggal: String,
    val status: StatusPengajuan
)

/* ====== Screen ====== */
@Composable
fun RiwayatPengajuanScreen(
    items: List<RiwayatPengajuanItem> = sampleRiwayatPengajuan(),
    onTabPengajuan: (() -> Unit)? = null,
    onTabPengaduan: (() -> Unit)? = null,
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        /* Header */
        Spacer(Modifier.height(12.dp))
        Text(
            text = "Riwayat Pengajuan",
            fontFamily = PoppinsSemi,
            fontSize = 22.sp,
            color = Color.White,
            modifier = Modifier.fillMaxWidth(),
            textAlign = TextAlign.Center
        )
        Spacer(Modifier.height(8.dp))
        Text(
            text = "Anda dapat melihat riwayat pengajuan dan pengaduan\n" +
                    "yang telah di ajukan oleh Anda",
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = Color.White.copy(alpha = 0.95f),
            textAlign = TextAlign.Center,
            lineHeight = 18.sp,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp)
        )

        /* Kontainer putih rounded */
        Spacer(Modifier.height(16.dp))
        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(Modifier.fillMaxSize()) {

                Spacer(Modifier.height(14.dp))
                /* Tabs */
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp),
                    horizontalArrangement = Arrangement.spacedBy(12.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Pengajuan (aktif/filled)
                    Button(
                        onClick = { onTabPengajuan?.invoke() },
                        shape = RoundedCornerShape(16.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = BlueMain),
                        modifier = Modifier
                            .weight(1f)
                            .height(48.dp)
                    ) {
                        Text("Pengajuan", fontFamily = PoppinsSemi, fontSize = 14.sp, color = Color.White)
                    }
                    // Pengaduan (outlined)
                    OutlinedButton(
                        onClick = { onTabPengaduan?.invoke() },
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

                /* List */
                Spacer(Modifier.height(16.dp))
                LazyColumn(
                    contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
                    verticalArrangement = Arrangement.spacedBy(10.dp),
                    modifier = Modifier.fillMaxSize()
                ) {
                    items(items.size) { i ->
                        RiwayatPengajuanCard(items[i])
                    }
                }
            }
        }
    }
}

/* ====== Card Item ====== */
@Composable
private fun RiwayatPengajuanCard(item: RiwayatPengajuanItem) {
    Card(
        modifier = Modifier.fillMaxWidth(),
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

/* ====== Sample data untuk preview ====== */
private fun sampleRiwayatPengajuan() = listOf(
    RiwayatPengajuanItem("Lily", "Peminjaman Fasilitas", "1 Oktober 2025", StatusPengajuan.DITOLAK),
    RiwayatPengajuanItem("Lily", "Peminjaman Fasilitas", "1 Oktober 2025", StatusPengajuan.DITERIMA),
    RiwayatPengajuanItem("Lily", "Peminjaman Fasilitas", "1 Oktober 2025", StatusPengajuan.DITOLAK),
    RiwayatPengajuanItem("Lily", "Peminjaman Fasilitas", "1 Oktober 2025", StatusPengajuan.DITERIMA),
)

/* ====== Preview ====== */
@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewRiwayatPengajuan() {
    MaterialTheme { RiwayatPengajuanScreen() }
}
