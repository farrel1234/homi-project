// File: app/src/main/java/com/example/homi/ui/screens/PengajuanSuratStatusScreen.kt
package com.example.homi.ui.screens

import android.content.ContentValues
import android.content.Context
import android.content.Intent
import android.media.MediaScannerConnection
import android.net.Uri
import android.os.Build
import android.os.Environment
import android.provider.MediaStore
import android.widget.Toast
import androidx.compose.animation.animateContentSize
import androidx.compose.animation.core.tween
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.foundation.border
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.outlined.*
import androidx.compose.material.icons.rounded.FileDownload
import androidx.compose.material.icons.rounded.SupportAgent
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.model.ServiceRequestDto
import com.example.homi.data.repository.ServiceRequestRepository
import com.google.accompanist.swiperefresh.SwipeRefresh
import com.google.accompanist.swiperefresh.rememberSwipeRefreshState
import kotlinx.coroutines.launch
import java.io.File
import java.io.FileOutputStream

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PengajuanSuratStatusScreen(
    id: Long,
    repo: ServiceRequestRepository,
    onBack: () -> Unit
) {
    // ===== PREMIUM COLORS =====
    val BlueMain = Color(0xFF2F7FA3)
    val BlueGradient = Brush.verticalGradient(
        colors = listOf(BlueMain, Color(0xFF1A5E7B))
    )
    val SurfaceBg = Color(0xFFF8FAFC)
    val PrimaryBlue = BlueMain
    val AccentOrange = Brush.horizontalGradient(listOf(Color(0xFFF37335), Color(0xFFFDC830)))
    val TextMuted = Color(0xFF64748B)

    // ===== Fonts =====
    val poppinsReg = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }
    val poppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }

    val ctx = LocalContext.current
    val scope = rememberCoroutineScope()

    var loading by remember { mutableStateOf(true) }
    var refreshing by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }
    var item by remember { mutableStateOf<ServiceRequestDto?>(null) }
    var downloading by remember { mutableStateOf(false) }

    fun normalizeStatus(raw: String?): String {
        val s = raw?.trim()?.lowercase() ?: ""
        return when {
            s == "approved" || s == "disetujui" || s == "done" || s == "selesai" -> "approved"
            s == "rejected" || s == "ditolak" -> "rejected"
            else -> "processing"
        }
    }

    fun load(refresh: Boolean) {
        scope.launch {
            if (refresh) refreshing = true else loading = true
            error = null
            try {
                item = repo.detail(id)
            } catch (e: Exception) {
                error = e.message ?: "Gagal load detail"
            } finally {
                if (refresh) refreshing = false else loading = false
            }
        }
    }

    LaunchedEffect(id) { load(refresh = false) }

    val swipeState = rememberSwipeRefreshState(isRefreshing = refreshing)

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueGradient)
            .statusBarsPadding()
    ) {
        // ===== HEADER =====
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(bottom = 24.dp)
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 8.dp, vertical = 12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                IconButton(onClick = onBack) {
                    Icon(Icons.Default.ArrowBack, contentDescription = "Kembali", tint = Color.White)
                }

                Text(
                    text = "Detail Pengajuan",
                    fontFamily = poppinsSemi,
                    fontSize = 19.sp,
                    color = Color.White,
                    modifier = Modifier.weight(1f),
                    textAlign = TextAlign.Center
                )

                IconButton(onClick = { load(refresh = true) }, enabled = !refreshing && !loading) {
                    Icon(Icons.Default.Refresh, contentDescription = "Refresh", tint = Color.White)
                }
            }

            Text(
                text = "Pantau proses suratmu secara real-time.",
                fontFamily = poppinsReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.85f),
                textAlign = TextAlign.Center,
                modifier = Modifier.fillMaxWidth().padding(horizontal = 24.dp)
            )
        }

        // ===== MAIN CONTENT =====
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(SurfaceBg, RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp))
        ) {
            SwipeRefresh(
                state = swipeState,
                onRefresh = { load(refresh = true) },
                modifier = Modifier.fillMaxSize()
            ) {
                Box(modifier = Modifier.fillMaxSize()) {
                    when {
                        loading -> {
                            Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                                CircularProgressIndicator(color = PrimaryBlue)
                            }
                        }

                        error != null -> {
                            Column(
                                modifier = Modifier.fillMaxSize(),
                                horizontalAlignment = Alignment.CenterHorizontally,
                                verticalArrangement = Arrangement.Center
                            ) {
                                Text(text = error!!, color = Color.Red, fontFamily = poppinsReg)
                                Spacer(Modifier.height(12.dp))
                                Button(
                                    onClick = { load(refresh = false) },
                                    colors = ButtonDefaults.buttonColors(containerColor = PrimaryBlue)
                                ) {
                                    Text("Coba lagi", color = Color.White, fontFamily = poppinsSemi)
                                }
                            }
                        }

                        else -> {
                            val data = item
                            if (data == null) {
                                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                                    Text("Data tidak ditemukan", fontFamily = poppinsReg)
                                }
                            } else {
                                val norm = normalizeStatus(data.status)
                                val canDownload = norm == "approved" && !data.pdfUrl.isNullOrBlank()
                                val nomorPengajuan = "REQ-${id.toString().padStart(4, '0')}"
                                val tanggalLabel = formatIsoDate(data.requestDate ?: "-")

                                Column(
                                    modifier = Modifier
                                        .fillMaxSize()
                                        .verticalScroll(rememberScrollState())
                                        .padding(horizontal = 20.dp)
                                        .animateContentSize(animationSpec = tween(300))
                                ) {
                                    // 📘 CARD 1: RINGKASAN DATA
                                    PremiumCard {
                                        Column {
                                            CardHeader(icon = Icons.Outlined.Description, title = "Ringkasan Pengajuan", color = PrimaryBlue, fontSemi = poppinsSemi)
                                            Spacer(Modifier.height(20.dp))
                                            InfoRowModern("Jenis Surat", data.type?.name ?: "-", poppinsReg, poppinsSemi)
                                            HorizontalDivider(Modifier.padding(vertical = 12.dp), color = Color(0xFFE2E8F0))
                                            InfoRowModern("No. Pengajuan", nomorPengajuan, poppinsReg, poppinsSemi)
                                            HorizontalDivider(Modifier.padding(vertical = 12.dp), color = Color(0xFFE2E8F0))
                                            InfoRowModern("Tgl Diajukan", tanggalLabel, poppinsReg, poppinsSemi)
                                        }
                                    }

                                    Spacer(Modifier.height(16.dp))

                                    // 🕒 CARD 2: TIMELINE STATUS
                                    PremiumCard {
                                        Column {
                                            CardHeader(icon = Icons.Default.History, title = "Lacak Progress", color = PrimaryBlue, fontSemi = poppinsSemi)
                                            Spacer(Modifier.height(24.dp))
                                            StatusTimeline(norm, poppinsReg, poppinsSemi)
                                        }
                                    }

                                    Spacer(Modifier.height(16.dp))

                                    // 📄 CARD 3: DETAIL TAMBAHAN
                                    PremiumCard {
                                        Column {
                                            CardHeader(icon = Icons.Default.Info, title = "Informasi Tambahan", color = PrimaryBlue, fontSemi = poppinsSemi)
                                            Spacer(Modifier.height(20.dp))
                                            InfoRowModern("Nama Pelapor", data.reporterName ?: "-", poppinsReg, poppinsSemi)
                                            HorizontalDivider(Modifier.padding(vertical = 12.dp), color = Color(0xFFE2E8F0))
                                            InfoRowModern("Perihal", data.subject ?: "-", poppinsReg, poppinsSemi)
                                            HorizontalDivider(Modifier.padding(vertical = 12.dp), color = Color(0xFFE2E8F0))
                                            InfoRowModern("Lokasi", data.place ?: "-", poppinsReg, poppinsSemi)
                                        }
                                    }

                                    Spacer(Modifier.height(16.dp))

                                    // 📥 CARD 4: AKSI & DOWNLOAD
                                    PremiumCard {
                                        Column {
                                            if (canDownload) {
                                                Button(
                                                    onClick = {
                                                        scope.launch {
                                                            downloading = true
                                                            try {
                                                                val bytes = repo.downloadPdfBytes(id)
                                                                val uri = savePdfToDownloads(ctx, "surat_$id.pdf", bytes)
                                                                if (uri != null) {
                                                                    Toast.makeText(ctx, "PDF tersimpan!", Toast.LENGTH_SHORT).show()
                                                                    openPdfFile(ctx, uri)
                                                                }
                                                            } catch (e: Exception) {
                                                                Toast.makeText(ctx, e.message ?: "Gagal", Toast.LENGTH_LONG).show()
                                                            } finally {
                                                                downloading = false
                                                            }
                                                        }
                                                    },
                                                    enabled = !downloading,
                                                    shape = RoundedCornerShape(16.dp),
                                                    colors = ButtonDefaults.buttonColors(containerColor = Color.Transparent),
                                                    contentPadding = PaddingValues(),
                                                    modifier = Modifier
                                                        .fillMaxWidth()
                                                        .height(56.dp)
                                                        .shadow(4.dp, RoundedCornerShape(16.dp))
                                                ) {
                                                    Box(
                                                        modifier = Modifier
                                                            .fillMaxSize()
                                                            .background(AccentOrange, RoundedCornerShape(16.dp)),
                                                        contentAlignment = Alignment.Center
                                                    ) {
                                                        if (downloading) {
                                                            CircularProgressIndicator(Modifier.size(24.dp), color = Color.White, strokeWidth = 2.dp)
                                                        } else {
                                                            Row(verticalAlignment = Alignment.CenterVertically) {
                                                                Icon(Icons.Rounded.FileDownload, null, tint = Color.White)
                                                                Spacer(Modifier.width(8.dp))
                                                                Text("Download Surat Resmi", fontFamily = poppinsSemi, color = Color.White, fontSize = 15.sp)
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                Box(
                                                    modifier = Modifier
                                                        .fillMaxWidth()
                                                        .background(Color(0xFFF1F5F9), RoundedCornerShape(16.dp))
                                                        .padding(16.dp),
                                                    contentAlignment = Alignment.Center
                                                ) {
                                                    Text(
                                                        text = if (norm == "rejected") "Pengajuan Anda ditolak." else "Surat masih dalam proses verifikasi RT/RW.",
                                                        fontFamily = poppinsSemi,
                                                        fontSize = 13.sp,
                                                        color = TextMuted,
                                                        textAlign = TextAlign.Center
                                                    )
                                                }
                                            }

                                            if (!data.adminNote.isNullOrBlank()) {
                                                Spacer(Modifier.height(20.dp))
                                                Box(
                                                    modifier = Modifier
                                                        .fillMaxWidth()
                                                        .background(Color(0xFFFFF7ED), RoundedCornerShape(12.dp))
                                                        .border(1.dp, Color(0xFFFFEDD5), RoundedCornerShape(12.dp))
                                                        .padding(16.dp)
                                                ) {
                                                    Row(verticalAlignment = Alignment.Top) {
                                                        Icon(Icons.Outlined.HelpOutline, null, tint = Color(0xFFEA580C), modifier = Modifier.size(18.dp))
                                                        Spacer(Modifier.width(10.dp))
                                                        Column {
                                                            Text("Catatan Verifikator", fontFamily = poppinsSemi, fontSize = 12.sp, color = Color(0xFFC2410C))
                                                            Spacer(Modifier.height(4.dp))
                                                            Text(data.adminNote, fontFamily = poppinsReg, fontSize = 13.sp, color = Color(0xFF9A3412))
                                                        }
                                                    }
                                                }
                                            }

                                            Spacer(Modifier.height(20.dp))
                                            OutlinedButton(
                                                onClick = {
                                                    try {
                                                        val intent = Intent(Intent.ACTION_VIEW).apply {
                                                            setData(Uri.parse("https://wa.me/6281234567890?text=Halo%20Admin%20Homi,%20saya%20ingin%20tanya%20status%20pengajuan%20$nomorPengajuan"))
                                                        }
                                                        ctx.startActivity(intent)
                                                    } catch (e: Exception) {}
                                                },
                                                shape = RoundedCornerShape(16.dp),
                                                modifier = Modifier.fillMaxWidth().height(56.dp),
                                                border = BorderStroke(1.5.dp, Color(0xFF10B981)),
                                                colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFF10B981))
                                            ) {
                                                Icon(Icons.Rounded.SupportAgent, null)
                                                Spacer(Modifier.width(8.dp))
                                                Text("Bantuan Admin via WhatsApp", fontFamily = poppinsSemi, fontSize = 14.sp)
                                            }
                                        }
                                    }
                                    Spacer(Modifier.height(40.dp))
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun PremiumCard(content: @Composable () -> Unit) {
    Card(
        colors = CardDefaults.cardColors(containerColor = Color.White),
        shape = RoundedCornerShape(24.dp),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp),
        border = BorderStroke(1.dp, Color(0xFFE2E8F0)),
        modifier = Modifier.fillMaxWidth().shadow(6.dp, RoundedCornerShape(24.dp), spotColor = Color(0x1A000000))
    ) {
        Box(modifier = Modifier.padding(24.dp)) {
            content()
        }
    }
}

@Composable
fun CardHeader(icon: androidx.compose.ui.graphics.vector.ImageVector, title: String, color: Color, fontSemi: FontFamily) {
    Row(verticalAlignment = Alignment.CenterVertically) {
        Box(
            modifier = Modifier.size(36.dp).background(color.copy(alpha = 0.1f), CircleShape),
            contentAlignment = Alignment.Center
        ) {
            Icon(icon, contentDescription = null, tint = color, modifier = Modifier.size(20.dp))
        }
        Spacer(Modifier.width(12.dp))
        Text(title, fontFamily = fontSemi, fontSize = 16.sp, color = Color(0xFF1E293B))
    }
}

@Composable
private fun InfoRowModern(label: String, value: String, fontReg: FontFamily, fontSemi: FontFamily) {
    Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
        Text(text = label, fontFamily = fontReg, fontSize = 13.sp, color = Color(0xFF64748B))
        Text(text = value, fontFamily = fontSemi, fontSize = 14.sp, color = Color(0xFF0F172A), textAlign = TextAlign.End, modifier = Modifier.weight(1f).padding(start = 16.dp))
    }
}

@Composable
private fun StatusTimeline(status: String, fontReg: FontFamily, fontSemi: FontFamily) {
    val steps = listOf("Sedang Diajukan", "Proses Verifikasi", "Selesai")
    val currentIndex = when (status) {
        "approved", "rejected" -> 2
        "processing" -> 1
        else -> 0
    }

    Column(modifier = Modifier.fillMaxWidth()) {
        steps.forEachIndexed { index, label ->
            val isActive = index <= currentIndex
            val isLast = index == steps.size - 1
            val color = if (isActive) {
                if (status == "rejected" && index == 2) Color(0xFFEF4444) // Error Red
                else Color(0xFF0EA5E9) // Vibrant Blue
            } else Color(0xFFE2E8F0)

            Row(verticalAlignment = Alignment.Top) {
                Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.width(32.dp)) {
                    Box(
                        modifier = Modifier
                            .size(28.dp)
                            .background(
                                if (isActive) color.copy(alpha = 0.15f) else color.copy(alpha = 0.3f),
                                CircleShape
                            )
                            .padding(4.dp),
                        contentAlignment = Alignment.Center
                    ) {
                        Box(
                            modifier = Modifier
                                .size(if (isActive) 14.dp else 10.dp)
                                .background(color, CircleShape)
                        ) {
                            if (isActive) {
                                Icon(
                                    imageVector = if (status == "rejected" && index == 2) Icons.Default.Close else Icons.Default.Check,
                                    contentDescription = null,
                                    tint = Color.White,
                                    modifier = Modifier.padding(2.dp)
                                )
                            }
                        }
                    }
                    if (!isLast) {
                        Box(
                            modifier = Modifier
                                .width(2.5.dp)
                                .height(40.dp)
                                .background(if (index < currentIndex) color else Color(0xFFE2E8F0))
                        )
                    }
                }

                Spacer(Modifier.width(16.dp))

                Column {
                    val displayLabel = if (status == "rejected" && index == 2) "Ditolak"
                    else if (status == "approved" && index == 2) "Disetujui"
                    else label
                    
                    Text(
                        text = displayLabel,
                        fontFamily = fontSemi,
                        fontSize = 15.sp,
                        color = if (isActive) Color(0xFF0F172A) else Color(0xFF94A3B8)
                    )
                    Spacer(Modifier.height(2.dp))
                    Text(
                        text = when (index) {
                            0 -> "Pengajuan berhasil masuk ke sistem pengelola."
                            1 -> "Dokumen sedang divalidasi oleh RT/RW setempat."
                            else -> if (status == "rejected") "Pengajuan Anda tidak dapat diproses."
                            else "Surat resmi telah diterbitkan dan siap diunduh."
                        },
                        fontFamily = fontReg,
                        fontSize = 12.sp,
                        color = Color(0xFF64748B),
                        lineHeight = 16.sp
                    )
                    if (!isLast) Spacer(Modifier.height(24.dp))
                }
            }
        }
    }
}

fun savePdfToDownloads(context: Context, fileName: String, bytes: ByteArray): Uri? {
    return try {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            val resolver = context.contentResolver
            val values = ContentValues().apply {
                put(MediaStore.MediaColumns.DISPLAY_NAME, fileName)
                put(MediaStore.MediaColumns.MIME_TYPE, "application/pdf")
                put(MediaStore.MediaColumns.RELATIVE_PATH, Environment.DIRECTORY_DOWNLOADS + "/HOMI")
                put(MediaStore.MediaColumns.IS_PENDING, 1)
            }
            val uri = resolver.insert(MediaStore.Downloads.EXTERNAL_CONTENT_URI, values) ?: return null
            resolver.openOutputStream(uri)?.use { it.write(bytes) }
            values.clear()
            values.put(MediaStore.MediaColumns.IS_PENDING, 0)
            resolver.update(uri, values, null, null)
            uri
        } else {
            val downloadsDir = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS)
            val homiDir = File(downloadsDir, "HOMI")
            if (!homiDir.exists()) homiDir.mkdirs()
            val outFile = File(homiDir, fileName)
            FileOutputStream(outFile).use { it.write(bytes) }
            MediaScannerConnection.scanFile(context, arrayOf(outFile.absolutePath), arrayOf("application/pdf"), null)
            Uri.fromFile(outFile)
        }
    } catch (e: Exception) {
        null
    }
}

fun openPdfFile(context: Context, uri: Uri) {
    try {
        val intent = Intent(Intent.ACTION_VIEW).apply {
            setDataAndType(uri, "application/pdf")
            addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
            addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
        }
        context.startActivity(intent)
    } catch (e: Exception) {
        Toast.makeText(context, "Tidak ada penampil PDF", Toast.LENGTH_LONG).show()
    }
}

private fun formatIsoDate(t: String): String {
    if (t.isBlank() || t == "-") return "-"
    if (t.length >= 10 && t[4] == '-' && t[7] == '-') {
        return try {
            val y = t.substring(0, 4)
            val m = t.substring(5, 7)
            val d = t.substring(8, 10).toInt().toString()
            val bln = when (m) {
                "01" -> "Januari" "02" -> "Februari" "03" -> "Maret" "04" -> "April"
                "05" -> "Mei" "06" -> "Juni" "07" -> "Juli" "08" -> "Agustus"
                "09" -> "September" "10" -> "Oktober" "11" -> "November" "12" -> "Desember"
                else -> m
            }
            "$d $bln $y"
        } catch (e: Exception) {
            t
        }
    }
    return t
}
