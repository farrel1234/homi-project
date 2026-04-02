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
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
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
        ?.replace("http://127.0.0.1:8000", com.example.homi.data.remote.ApiConfig.HOST)
        ?.replace("http://localhost:8000", com.example.homi.data.remote.ApiConfig.HOST)
        ?.replace("http://127.0.0.1", com.example.homi.data.remote.ApiConfig.HOST)
        ?.replace("http://localhost", com.example.homi.data.remote.ApiConfig.HOST)

    val rawDate = announcement.publishedAt ?: announcement.createdAt ?: ""
    val displayDate = formatIsoToId(rawDate)

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
    ) {
        // ===== IMAGE TOPPER WITH OVERLAY =====
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .height(300.dp)
        ) {
            if (!imageUrl.isNullOrBlank()) {
                AsyncImage(
                    model = imageUrl,
                    contentDescription = "Header Image",
                    contentScale = ContentScale.Crop,
                    modifier = Modifier.fillMaxSize()
                )
            } else {
                Image(
                    painter = painterResource(id = R.drawable.img_pengumuman),
                    contentDescription = "Fallback Header",
                    contentScale = ContentScale.Crop,
                    modifier = Modifier.fillMaxSize()
                )
            }

            // Black Gradient Scrim (for text readability)
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(
                        androidx.compose.ui.graphics.Brush.verticalGradient(
                            colors = listOf(
                                Color.Black.copy(alpha = 0.6f),
                                Color.Transparent,
                                Color.Black.copy(alpha = 0.4f)
                            )
                        )
                    )
            )

            // Toolbar Content (Back + Title)
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .statusBarsPadding()
                    .padding(top = 16.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Box(modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp)) {
                    if (onBack != null) {
                        IconButton(
                            onClick = { onBack.invoke() },
                            modifier = Modifier.align(Alignment.CenterStart)
                        ) {
                            Image(
                                painter = painterResource(id = R.drawable.panahkembali),
                                contentDescription = "Kembali",
                                modifier = Modifier.size(24.dp)
                            )
                        }
                    }

                    Text(
                        text = "Pengumuman",
                        fontSize = 20.sp,
                        fontWeight = FontWeight.Bold,
                        fontFamily = poppins,
                        color = Color.White,
                        modifier = Modifier.align(Alignment.Center)
                    )
                }
            }
        }

        // ===== CONTENT CARD =====
        Card(
            modifier = Modifier
                .fillMaxSize()
                .padding(top = 220.dp), // Adjust this to overlap slightly
            shape = RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
                    .padding(horizontal = 15.dp, vertical = 12.dp),
                horizontalAlignment = Alignment.Start
            ) {

                // Kategori & Tanggal
                Row(
                    modifier = Modifier.fillMaxWidth().padding(bottom = 8.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    CategoryBadge(category = announcement.category ?: "Informasi")
                    
                    Text(
                        text = displayDate,
                        fontSize = 11.sp,
                        color = Color.Gray,
                        fontFamily = inter
                    )
                }

                Text(
                    text = announcement.title,
                    fontSize = 20.sp,
                    fontWeight = FontWeight.Bold,
                    fontFamily = poppins,
                    color = Color.Black,
                    lineHeight = 28.sp
                )

                Spacer(modifier = Modifier.height(12.dp))

                // Info Penulis (Author)
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(bottom = 20.dp)
                        .background(Color(0xFFF5F5F5), RoundedCornerShape(8.dp))
                        .padding(horizontal = 10.dp, vertical = 8.dp)
                ) {
                    Box(
                        modifier = Modifier
                            .size(24.dp)
                            .background(Color(0xFF2F7FA3), RoundedCornerShape(12.dp)),
                        contentAlignment = Alignment.Center
                    ) {
                        Text("H", color = Color.White, fontSize = 12.sp, fontWeight = FontWeight.Bold)
                    }
                    Spacer(Modifier.width(8.dp))
                    Column {
                        Text(
                            text = "Admin Homi",
                            fontSize = 12.sp,
                            fontWeight = FontWeight.SemiBold,
                            color = Color.Black
                        )
                        Text(
                            text = "Pengelola Kawasan Hawaii Garden",
                            fontSize = 10.sp,
                            color = Color.Gray
                        )
                    }
                }

                // ✅ render HTML content
                HtmlText(
                    html = announcement.content,
                    modifier = Modifier.fillMaxWidth()
                )

                Spacer(modifier = Modifier.height(32.dp))
            }
        }
    }
}

@Composable
fun CategoryBadge(category: String) {
    val bgColor = when (category) {
        "Keamanan" -> Color(0xFFFFEBEE)
        "Kegiatan" -> Color(0xFFE3F2FD)
        "Pembangunan" -> Color(0xFFF1F8E9)
        "Keuangan" -> Color(0xFFFFF3E0)
        else -> Color(0xFFF3E5F5)
    }
    val textColor = when (category) {
        "Keamanan" -> Color(0xFFC62828)
        "Kegiatan" -> Color(0xFF1565C0)
        "Pembangunan" -> Color(0xFF2E7D32)
        "Keuangan" -> Color(0xFFEF6C00)
        else -> Color(0xFF7B1FA2)
    }

    Box(
        modifier = Modifier
            .background(bgColor, RoundedCornerShape(4.dp))
            .padding(horizontal = 8.dp, vertical = 4.dp)
    ) {
        Row(verticalAlignment = Alignment.CenterVertically) {
            Box(Modifier.size(6.dp).background(textColor, RoundedCornerShape(3.dp)))
            Spacer(Modifier.width(6.dp))
            Text(
                text = category,
                color = textColor,
                fontSize = 10.sp,
                fontWeight = FontWeight.Bold,
                letterSpacing = 0.5.sp
            )
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
