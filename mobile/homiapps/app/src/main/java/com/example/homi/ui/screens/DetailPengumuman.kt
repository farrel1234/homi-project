package com.example.homi.ui.screens

import android.text.method.LinkMovementMethod
import android.widget.TextView
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
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
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import androidx.core.text.HtmlCompat
import coil.compose.AsyncImage
import com.example.homi.R
import com.example.homi.data.model.AnnouncementDto
import java.time.Instant
import java.time.ZoneId
import java.time.format.DateTimeFormatter
import java.util.Locale

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

    val rawDate = announcement.publishedAt ?: announcement.createdAt ?: ""
    val displayDate = formatIsoToId(rawDate)

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFEFEFEF))
    ) {
        // ===== Header image =====
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
                            .clickable { onBack.invoke() }
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

                // tanggal/published
                Text(
                    text = displayDate,
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

                // ✅ render HTML content
                HtmlText(
                    html = announcement.content,
                    modifier = Modifier.fillMaxWidth()
                )

                Spacer(modifier = Modifier.height(24.dp))
            }
        }
    }
}

/**
 * Render HTML string menjadi TextView (via AndroidView)
 */
@Composable
private fun HtmlText(
    html: String,
    modifier: Modifier = Modifier
) {
    val ctx = LocalContext.current

    AndroidView(
        modifier = modifier,
        factory = {
            TextView(ctx).apply {
                // biar link <a href> bisa diklik kalau ada
                movementMethod = LinkMovementMethod.getInstance()

                // Styling dasar biar mirip Text Compose kamu
                textSize = 14f
                setTextColor(android.graphics.Color.BLACK)
                setLineSpacing(8f, 1.0f)
            }
        },
        update = { tv ->
            tv.text = HtmlCompat.fromHtml(html, HtmlCompat.FROM_HTML_MODE_COMPACT)
        }
    )
}

/**
 * Convert ISO date (termasuk yang ada microseconds "000000Z") ke format ID yang enak dibaca.
 * contoh: 2026-01-07T14:06:13.000000Z -> 07 Jan 2026, 21:06 (tergantung timezone device)
 */
private fun formatIsoToId(iso: String): String {
    if (iso.isBlank()) return ""

    // beberapa backend kasih format "2025-12-31 12:34:11" (non-ISO)
    // kita coba handle dua format: ISO (Instant.parse) & "yyyy-MM-dd HH:mm:ss"
    return try {
        val cleaned = iso
            .trim()
            .replace("000000Z", "Z") // handle microseconds panjang
        val instant = Instant.parse(cleaned)
        val formatter = DateTimeFormatter
            .ofPattern("dd MMM yyyy, HH:mm", Locale("id", "ID"))
            .withZone(ZoneId.systemDefault())
        formatter.format(instant)
    } catch (_: Exception) {
        // fallback untuk format "yyyy-MM-dd HH:mm:ss"
        try {
            val formatterIn = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss", Locale.US)
            val localDateTime = java.time.LocalDateTime.parse(iso.trim(), formatterIn)
            val formatterOut = DateTimeFormatter.ofPattern("dd MMM yyyy, HH:mm", Locale("id", "ID"))
            localDateTime.format(formatterOut)
        } catch (_: Exception) {
            iso
        }
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
private fun PreviewDetailPengumuman() {
    val dummy = AnnouncementDto(
        id = 1,
        title = "Kegiatan Gotong Royong Warga",
        content = """
            <p>Halo Warga Hawai Garden! 🌿<br>
            Dalam rangka menjaga kebersihan dan kenyamanan lingkungan, kami mengundang seluruh warga untuk ikut kegiatan
            <strong>Gotong Royong Bersih Lingkungan</strong>.</p>
            <ul>
              <li>Pembersihan selokan</li>
              <li>Pemangkasan rumput</li>
              <li>Pengangkutan sampah</li>
            </ul>
        """.trimIndent(),
        imageUrl = "http://10.0.2.2:8000/storage/announcements/dummy.png",
        publishedAt = "2026-01-07T14:06:13.000000Z"
    )

    MaterialTheme {
        DetailPengumumanScreen(
            announcement = dummy,
            onBack = {}
        )
    }
}
