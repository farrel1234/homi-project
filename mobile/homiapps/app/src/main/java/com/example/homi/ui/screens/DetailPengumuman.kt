package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
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
import com.example.homi.R

@Composable
fun DetailPengumumanScreen() {
    val poppins = FontFamily(Font(R.font.poppins_semibold))
    val inter = FontFamily(Font(R.font.inter_variablefont_opsz_wght))

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFEFEFEF))
    ) {
        // Header image
        Image(
            painter = painterResource(id = R.drawable.img_pengumuman),
            contentDescription = "Header Image",
            contentScale = ContentScale.Crop,
            modifier = Modifier
                .fillMaxWidth()
                .height(220.dp)
        )

        // Konten putih rounded di bawah gambar
        Card(
            modifier = Modifier
                .fillMaxSize()
                .offset(y = (-40).dp),
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 15.dp, vertical = 12.dp),
                horizontalAlignment = Alignment.Start
            ) {
                Text(
                    text = "Pengumuman",
                    fontSize = 20.sp,
                    fontWeight = FontWeight.Bold,
                    fontFamily = poppins,
                    color = Color.Black,
                    modifier = Modifier.fillMaxWidth(),
                    textAlign = TextAlign.Center
                )

                Text(
                    text = "Nomor: 15/PER-HWG/IX/2025",
                    fontSize = 12.sp,
                    color = Color.Gray,
                    textAlign = TextAlign.Center,
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(bottom = 16.dp)
                )

                Text(
                    text = "Perihal: Kegiatan Gotong Royong Warga\n",
                    fontSize = 14.sp,
                    fontWeight = FontWeight.Medium,
                    fontFamily = inter,
                    color = Color.Black
                )

                Text(
                    text = "Kepada Yth.\nSeluruh Warga Perumahan Hawaii Garden\ndi Tempat\n",
                    fontSize = 14.sp,
                    fontFamily = inter,
                    color = Color.Black
                )

                Text(
                    text = "Dengan hormat,\n" +
                            "Dalam rangka menjaga kebersihan dan kenyamanan lingkungan bersama, kami selaku pengurus lingkungan Perumahan Hawaii Garden mengundang seluruh warga untuk berpartisipasi dalam kegiatan Gotong Royong yang akan dilaksanakan pada:",
                    fontSize = 14.sp,
                    fontFamily = inter,
                    color = Color.Black,
                    lineHeight = 20.sp
                )

                Spacer(modifier = Modifier.height(8.dp))

                Text(
                    text = "• Hari/Tanggal : Jumat, 3 September 2025\n" +
                            "• Waktu : Pukul 07.00 WIB – selesai\n" +
                            "• Tempat : Area Masjid Perumahan Hawaii Garden\n" +
                            "• Peserta : Seluruh Warga Perumahan Hawaii Garden\n",
                    fontSize = 14.sp,
                    fontFamily = inter,
                    color = Color.Black
                )

                Text(
                    text = "Adapun kegiatan ini bertujuan untuk mempererat tali silaturahmi antarwarga serta menciptakan lingkungan yang bersih, asri, dan nyaman.\n",
                    fontSize = 14.sp,
                    fontFamily = inter,
                    color = Color.Black
                )

                Text(
                    text = "Diharapkan kepada seluruh warga untuk membawa perlengkapan kerja baik seperti sapu, cangkul, dan alat kebersihan lainnya.\n",
                    fontSize = 14.sp,
                    fontFamily = inter,
                    color = Color.Black
                )

                Text(
                    text = "Demikian pengumuman ini kami sampaikan. Atas perhatian dan partisipasinya, kami ucapkan terima kasih.\n",
                    fontSize = 14.sp,
                    fontFamily = inter,
                    color = Color.Black
                )

                Text(
                    text = "Hormat kami,\nPengurus Lingkungan Perumahan Hawaii Garden\n(ttd)\nKetua RW/RT",
                    fontSize = 14.sp,
                    fontFamily = inter,
                    color = Color.Black
                )
            }
        }
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewDetailPengumuman() {
    MaterialTheme { DetailPengumumanScreen() }
}
