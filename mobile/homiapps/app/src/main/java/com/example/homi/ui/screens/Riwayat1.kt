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
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

/* ====== Tokens ====== */
private val BlueMain   = Color(0xFF2F7FA3)
private val BlueText   = Color(0xFF2F7FA3)
private val CardLine   = Color(0xFFE0E0E0)
private val TextDark   = Color(0xFF0E0E0E)
/* ⬇️ Tambahan agar BorderStroke(LineGray) tidak error */
private val LineGray   = Color(0xFFDDDDDD)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg  = FontFamily(Font(R.font.poppins_regular))

data class RiwayatItem(
    val nama: String,
    val tanggal: String,
    val tempat: String,
    val perihal: String
)

@Composable
fun Riwayat1Screen(
    onTabPengajuan: (() -> Unit)? = null,
    onTabPengaduan: (() -> Unit)? = null,
    items: List<RiwayatItem> = defaultItems()
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
            text = "Riwayat Pengaduan",
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
            fontSize = 12.sp, // ✅ lebih besar agar terbaca seperti di gambar
            color = Color.White.copy(alpha = 0.95f), // ✅ putih lembut (bukan terlalu terang)
            textAlign = TextAlign.Center,
            lineHeight = 20.sp, // ✅ jarak antarbaris nyaman
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 8.dp) // ✅ spasi kiri-kanan & jarak vertikal proporsional
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
                    horizontalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    OutlinedButton(
                        onClick = { onTabPengajuan?.invoke() },
                        border = BorderStroke(2.dp, BlueText),
                        shape = RoundedCornerShape(16.dp),
                        modifier = Modifier
                            .weight(1f)
                            .height(48.dp),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = BlueText)
                    ) {
                        Text("Pengajuan", fontFamily = PoppinsSemi, fontSize = 14.sp)
                    }
                    Button(
                        onClick = { onTabPengaduan?.invoke() },
                        shape = RoundedCornerShape(16.dp),
                        modifier = Modifier
                            .weight(1f)
                            .height(48.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = BlueMain)
                    ) {
                        Text("Pengaduan", fontFamily = PoppinsSemi, fontSize = 14.sp, color = Color.White)
                    }
                }

                /* List */
                Spacer(Modifier.height(16.dp))
                LazyColumn(
                    contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                    modifier = Modifier.fillMaxSize()
                ) {
                    items(items.size) { i ->
                        RiwayatCard(items[i])
                    }
                }
            }
        }
    }
}

@Composable
private fun RiwayatCard(item: RiwayatItem) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(1.dp),
        border = BorderStroke(1.dp, LineGray),
        colors = CardDefaults.cardColors(
            containerColor = Color(0xFFF9F9F9) // ← abu muda, masih putih lembut
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
        }
    }
}

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

/* Dummy data */
private fun defaultItems() = listOf(
    RiwayatItem(
        nama = "Lily",
        tanggal = "1 Oktober 2025",
        tempat = "di depan lapangan voli",
        perihal = "Sampah Berserakan di Jalan"
    ),
    RiwayatItem(
        nama = "Lily",
        tanggal = "1 Oktober 2025",
        tempat = "di depan lapangan voli",
        perihal = "Sampah Berserakan di Jalan"
    ),
    RiwayatItem(
        nama = "Lily",
        tanggal = "1 Oktober 2025",
        tempat = "di depan lapangan voli",
        perihal = "Sampah Berserakan di Jalan"
    ),
    RiwayatItem(
        nama = "Lily",
        tanggal = "1 Oktober 2025",
        tempat = "di depan lapangan voli",
        perihal = "Sampah Berserakan di Jalan"
    ),
)

/* Preview */
@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewRiwayat1() {
    MaterialTheme { Riwayat1Screen() }
}
