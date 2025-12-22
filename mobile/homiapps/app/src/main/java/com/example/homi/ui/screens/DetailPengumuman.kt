package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.example.homi.R
import com.example.homi.data.model.AnnouncementDto

@Composable
fun DetailPengumumanScreen(
    announcement: AnnouncementDto,
    onBack: (() -> Unit)? = null
) {
    val poppins = FontFamily(Font(R.font.poppins_semibold))
    val inter = FontFamily(Font(R.font.inter_variablefont_opsz_wght))

    val imageUrl = announcement.imageUrl
        ?.replace("127.0.0.1", "10.0.2.2")
        ?.replace("localhost", "10.0.2.2")

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFEFEFEF))
    ) {
        // ===== Header image (PASTI di dalam Column, jangan di luar) =====
        if (!imageUrl.isNullOrBlank()) {
            AsyncImage(
                model = imageUrl,
                contentDescription = "Header Image",
                contentScale = ContentScale.Crop,
                modifier = Modifier
                    .fillMaxWidth()
                    .height(220.dp)
            )
        } else {
            Image(
                painter = painterResource(id = R.drawable.img_pengumuman),
                contentDescription = "Header Image (fallback)",
                contentScale = ContentScale.Crop,
                modifier = Modifier
                    .fillMaxWidth()
                    .height(220.dp)
            )
        }

        // ===== Konten putih rounded =====
        Card(
            modifier = Modifier
                .fillMaxSize()
                .offset(y = (-40).dp),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
                    .padding(horizontal = 15.dp, vertical = 12.dp),
                horizontalAlignment = Alignment.Start
            ) {
                // tombol back (simple)
                if (onBack != null) {
                    Text(
                        text = "Kembali",
                        fontSize = 12.sp,
                        color = Color(0xFF2F7FA3),
                        modifier = Modifier
                            .padding(bottom = 8.dp)
                    )
                }

                Text(
                    text = "Pengumuman",
                    fontSize = 20.sp,
                    fontWeight = FontWeight.Bold,
                    fontFamily = poppins,
                    color = Color.Black,
                    modifier = Modifier.fillMaxWidth(),
                    textAlign = TextAlign.Center
                )

                // tanggal/published (opsional)
                Text(
                    text = announcement.publishedAt ?: announcement.createdAt ?: "",
                    fontSize = 10.sp,
                    color = Color.Gray,
                    textAlign = TextAlign.Center,
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(bottom = 16.dp)
                )

                Text(
                    text = announcement.title,
                    fontSize = 14.sp,
                    fontWeight = FontWeight.SemiBold,
                    fontFamily = inter,
                    color = Color.Black
                )

                Spacer(modifier = Modifier.height(10.dp))

                Text(
                    text = announcement.content,
                    fontSize = 14.sp,
                    fontFamily = inter,
                    color = Color.Black,
                    lineHeight = 20.sp
                )

                Spacer(modifier = Modifier.height(24.dp))
            }
        }
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewDetailPengumuman() {
    val dummy = AnnouncementDto(
        id = 1,
        title = "Kegiatan Gotong Royong Warga",
        content = "Ini isi pengumuman dari API.\n\nSilakan warga hadir sesuai jadwal.",
        imageUrl = "http://10.0.2.2:8000/storage/announcements/dummy.png",
        publishedAt = "2025-12-17 14:35"
    )

    MaterialTheme {
        DetailPengumumanScreen(
            announcement = dummy,
            onBack = {}
        )
    }
}
