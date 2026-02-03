// File: app/src/main/java/com/example/homi/ui/screens/ProsesPengajuanLayananScreen.kt
package com.example.homi.ui.screens

import android.os.Build
import androidx.annotation.RequiresApi
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Autorenew
import androidx.compose.material.icons.filled.Check
import androidx.compose.material.icons.filled.Description
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import com.example.homi.data.model.ComplaintDto
import com.example.homi.data.repository.ComplaintRepository
import com.example.homi.util.WhatsAppUtil
import com.google.accompanist.swiperefresh.SwipeRefresh
import com.google.accompanist.swiperefresh.rememberSwipeRefreshState
import kotlinx.coroutines.launch
import java.time.LocalDate
import java.time.format.DateTimeFormatter

/* ===== Tokens ===== */
private val BlueMain = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFE26A2C)
private val LineGray = Color(0xFFE5E7EB)
private val TextDark = Color(0xFF0E0E0E)
private val TextMute = Color(0xFF64748B)

private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

@RequiresApi(Build.VERSION_CODES.O)
@Composable
fun ProsesPengajuanLayananScreen(
    complaintId: Long,
    complaintRepo: ComplaintRepository,
    onBack: () -> Unit,
    onWhatsappClick: (() -> Unit)? = null,
) {
    val ctx = LocalContext.current
    val scope = rememberCoroutineScope()

    var loading by remember { mutableStateOf(true) }
    var refreshing by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }
    var dto by remember { mutableStateOf<ComplaintDto?>(null) }

    fun load(refresh: Boolean) {
        scope.launch {
            if (refresh) refreshing = true else loading = true
            error = null
            try {
                dto = complaintRepo.detail(complaintId)
            } catch (e: Exception) {
                error = e.message ?: "Gagal memuat detail pengaduan"
            } finally {
                if (refresh) refreshing = false else loading = false
            }
        }
    }

    LaunchedEffect(complaintId) { load(refresh = false) }

    val statusRaw = (dto?.status ?: "baru").trim().lowercase()
    val step = when {
        statusRaw.contains("selesai") -> 3
        statusRaw.contains("diproses") -> 2
        statusRaw.contains("tolak") || statusRaw.contains("ditolak") -> 3
        else -> 1
    }

    val nomor = complaintId.toInt().coerceAtLeast(0).toString().padStart(3, '0')
    val swipeState = rememberSwipeRefreshState(isRefreshing = refreshing)

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BlueMain)
            .statusBarsPadding()
    ) {
        // ===== HEADER =====
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 14.dp, vertical = 12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            IconButton(onClick = onBack) {
                Icon(
                    imageVector = Icons.Default.ArrowBack,
                    contentDescription = "Back",
                    tint = Color.White
                )
            }

            Spacer(Modifier.width(6.dp))

            Text(
                text = "Laporan Pengaduan",
                fontFamily = PoppinsSemi,
                fontSize = 18.sp,
                color = Color.White,
                modifier = Modifier.weight(1f)
            )

            // ✅ tombol refresh tetap ada
            IconButton(
                onClick = { load(refresh = true) },
                enabled = !refreshing && !loading
            ) {
                Icon(
                    imageVector = Icons.Default.Refresh,
                    contentDescription = "Refresh",
                    tint = Color.White
                )
            }
        }

        Spacer(Modifier.height(6.dp))

        // ===== STEPPER =====
        StepperHeader(step = step)

        Spacer(Modifier.height(10.dp))

        Text(
            text = "Nomor Pengaduan",
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = Color.White.copy(alpha = 0.95f),
            modifier = Modifier.fillMaxWidth(),
            textAlign = TextAlign.Center
        )
        Text(
            text = nomor,
            fontFamily = PoppinsSemi,
            fontSize = 32.sp,
            color = Color.White,
            modifier = Modifier.fillMaxWidth(),
            textAlign = TextAlign.Center
        )

        Spacer(Modifier.height(12.dp))

        Card(
            modifier = Modifier.fillMaxSize(),
            shape = RoundedCornerShape(topStart = 28.dp, topEnd = 28.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            SwipeRefresh(
                state = swipeState,
                onRefresh = { load(refresh = true) },
                modifier = Modifier.fillMaxSize()
            ) {
                // ✅ KUNCI: bikin konten scrollable biar pull-to-refresh kebaca stabil
                val scrollState = rememberScrollState()

                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .verticalScroll(scrollState)
                        .padding(16.dp)
                ) {
                    when {
                        loading -> {
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(520.dp),
                                contentAlignment = Alignment.Center
                            ) { CircularProgressIndicator() }
                        }

                        error != null -> {
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(520.dp),
                                contentAlignment = Alignment.Center
                            ) {
                                ErrorBlock(
                                    message = error!!,
                                    onRetry = { load(refresh = true) }
                                )
                            }
                        }

                        dto == null -> {
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(520.dp),
                                contentAlignment = Alignment.Center
                            ) {
                                Text("Data tidak ditemukan", fontFamily = PoppinsReg, color = TextMute)
                            }
                        }

                        else -> {
                            val nama = dto!!.namaPelapor.safeText("-")
                            val perihal = dto!!.perihal.safeText("Pengaduan")

                            val tanggal = dto!!.tanggalLabel?.trim()
                                ?.takeIf { it.isNotEmpty() }
                                ?: run {
                                    val tanggalRaw = dto!!.tanggalPengaduan
                                        .safeText("")
                                        .ifBlankFallback(dto!!.tanggalIso.takeDate10())
                                        .ifBlankFallback(dto!!.createdAt.takeDate10())
                                    formatIsoToDisplay(tanggalRaw)
                                }

                            val tempat = dto!!.tempat?.trim()
                                ?.takeIf { it.isNotEmpty() }
                                ?: dto!!.tempatKejadian.safeText("-")

                            StatusBox(statusRaw = statusRaw)

                            Spacer(Modifier.height(14.dp))

                            DetailBox(
                                nama = nama,
                                perihal = perihal,
                                tanggal = tanggal,
                                tempat = tempat
                            )

                            Spacer(Modifier.height(16.dp))

                            Button(
                                onClick = {
                                    if (onWhatsappClick != null) {
                                        onWhatsappClick.invoke()
                                    } else {
                                        val msg =
                                            "Halo Admin HOMI, saya butuh bantuan untuk Pengaduan #$nomor (ID=$complaintId)."
                                        WhatsAppUtil.openChat(
                                            context = ctx,
                                            phoneInternational = "6281992440287",
                                            message = msg
                                        )
                                    }
                                },
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(52.dp),
                                shape = RoundedCornerShape(14.dp),
                                colors = ButtonDefaults.buttonColors(containerColor = AccentOrange)
                            ) {
                                Text(
                                    text = "Bantuan via WhatsApp",
                                    fontFamily = PoppinsSemi,
                                    fontSize = 14.sp,
                                    color = Color.White
                                )
                            }

                            Spacer(Modifier.height(10.dp))

                            Text(
                                text = "*Tarik layar ke bawah untuk memperbarui status pengaduan.",
                                fontFamily = PoppinsReg,
                                fontSize = 11.sp,
                                color = Color(0xFFB45309),
                                lineHeight = 16.sp
                            )

                            Spacer(Modifier.height(30.dp)) // biar enak ditarik
                        }
                    }
                }
            }
        }
    }
}

/* ===================== STEPPER UI (SIMETRIS) ===================== */

@Composable
private fun StepperHeader(step: Int) {
    val circleSize = 46.dp
    val lineStroke = 3.dp

    val doneCircle = AccentOrange
    val idleCircle = Color.White.copy(alpha = 0.65f)

    val lineDone = AccentOrange
    val lineIdle = Color.White.copy(alpha = 0.35f)

    val c1 = if (step >= 1) doneCircle else idleCircle
    val c2 = if (step >= 2) doneCircle else idleCircle
    val c3 = if (step >= 3) doneCircle else idleCircle

    val l12 = if (step >= 2) lineDone else lineIdle
    val l23 = if (step >= 3) lineDone else lineIdle

    Column(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 18.dp)
    ) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .height(circleSize),
            contentAlignment = Alignment.Center
        ) {
            Canvas(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(circleSize)
            ) {
                val y = size.height / 2f
                val x1 = size.width * (1f / 6f)
                val x2 = size.width * (3f / 6f)
                val x3 = size.width * (5f / 6f)

                drawLine(
                    color = l12,
                    start = Offset(x1, y),
                    end = Offset(x2, y),
                    strokeWidth = lineStroke.toPx()
                )
                drawLine(
                    color = l23,
                    start = Offset(x2, y),
                    end = Offset(x3, y),
                    strokeWidth = lineStroke.toPx()
                )
            }

            Row(
                modifier = Modifier.fillMaxWidth(),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Box(Modifier.weight(1f), contentAlignment = Alignment.Center) {
                    StepCircle(icon = Icons.Default.Autorenew, bg = c1)
                }
                Box(Modifier.weight(1f), contentAlignment = Alignment.Center) {
                    StepCircle(icon = Icons.Default.Description, bg = c2)
                }
                Box(Modifier.weight(1f), contentAlignment = Alignment.Center) {
                    StepCircle(icon = Icons.Default.Check, bg = c3)
                }
            }
        }

        Spacer(Modifier.height(6.dp))

        Row(modifier = Modifier.fillMaxWidth()) {
            StepLabel("Pengajuan\nLaporan", Modifier.weight(1f))
            StepLabel("Sedang\nDiproses", Modifier.weight(1f))
            StepLabel("Pengajuan\nSelesai", Modifier.weight(1f))
        }
    }
}

@Composable
private fun StepLabel(text: String, modifier: Modifier = Modifier) {
    Text(
        text = text,
        fontFamily = PoppinsReg,
        fontSize = 11.sp,
        color = Color.White,
        textAlign = TextAlign.Center,
        modifier = modifier
    )
}

@Composable
private fun StepCircle(
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    bg: Color
) {
    Box(
        modifier = Modifier
            .size(46.dp)
            .clip(CircleShape)
            .background(bg),
        contentAlignment = Alignment.Center
    ) {
        Icon(
            imageVector = icon,
            contentDescription = null,
            tint = Color.White,
            modifier = Modifier.size(22.dp)
        )
    }
}

/* ===================== CONTENT BOXES ===================== */

@Composable
private fun StatusBox(statusRaw: String) {
    val (title, desc) = when {
        statusRaw.contains("selesai") -> "Laporan pengaduan" to "Laporan selesai."
        statusRaw.contains("diproses") -> "Laporan pengaduan" to "Mohon ditunggu, laporan Anda sedang diproses."
        statusRaw.contains("tolak") || statusRaw.contains("ditolak") -> "Laporan pengaduan" to "Laporan Anda ditolak."
        else -> "Laporan pengaduan" to "Mohon ditunggu, laporan Anda sedang dalam antrian."
    }

    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(14.dp),
        colors = CardDefaults.cardColors(containerColor = Color(0xFFF8FAFC)),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Column(Modifier.padding(14.dp)) {
            Text(text = title, fontFamily = PoppinsSemi, fontSize = 13.sp, color = AccentOrange)
            Spacer(Modifier.height(6.dp))
            Text(text = desc, fontFamily = PoppinsReg, fontSize = 12.sp, color = TextMute)
        }
    }
}

@Composable
private fun DetailBox(nama: String, perihal: String, tanggal: String, tempat: String) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(14.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp)
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, LineGray, RoundedCornerShape(14.dp))
                .padding(14.dp)
        ) {
            Text(text = "Detail Pengaduan", fontFamily = PoppinsSemi, fontSize = 13.sp, color = AccentOrange)
            Spacer(Modifier.height(10.dp))

            DetailRow(label = "Nama", value = nama)
            Spacer(Modifier.height(8.dp))
            DetailRow(label = "Perihal", value = perihal)
            Spacer(Modifier.height(8.dp))
            DetailRow(label = "Tanggal Pengajuan", value = tanggal)
            Spacer(Modifier.height(8.dp))
            DetailRow(label = "Tempat Kejadian", value = tempat)
        }
    }
}

@Composable
private fun DetailRow(label: String, value: String) {
    Row(modifier = Modifier.fillMaxWidth(), verticalAlignment = Alignment.Top) {
        Text(
            text = label,
            fontFamily = PoppinsReg,
            fontSize = 12.sp,
            color = TextMute,
            modifier = Modifier.width(130.dp)
        )
        Text(
            text = value,
            fontFamily = PoppinsSemi,
            fontSize = 12.sp,
            color = TextDark
        )
    }
}

@Composable
private fun ErrorBlock(message: String, onRetry: () -> Unit) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp),
        verticalArrangement = Arrangement.Center,
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Text(text = message, fontFamily = PoppinsReg, color = Color(0xFFDC2626), textAlign = TextAlign.Center)
        Spacer(Modifier.height(10.dp))
        OutlinedButton(onClick = onRetry, shape = RoundedCornerShape(14.dp)) {
            Text("Coba Lagi", fontFamily = PoppinsSemi)
        }
    }
}

/* ===================== DATE FORMAT ===================== */

@RequiresApi(Build.VERSION_CODES.O)
private fun formatIsoToDisplay(iso: String): String {
    return try {
        if (iso.isBlank()) return "-"
        val safe = if (iso.length >= 10) iso.substring(0, 10) else iso
        val dt = LocalDate.parse(safe, DateTimeFormatter.ISO_DATE)
        dt.format(DateTimeFormatter.ofPattern("dd MMMM yyyy"))
    } catch (_: Exception) {
        iso.ifBlank { "-" }
    }
}

/* ================== helpers ================== */

private fun String?.safeText(fallback: String): String =
    this?.trim()?.takeIf { it.isNotEmpty() } ?: fallback

private fun String?.takeDate10(): String? =
    this?.trim()?.takeIf { it.length >= 10 }?.substring(0, 10)

private fun String.ifBlankFallback(fallback: String?): String =
    this.takeIf { it.isNotBlank() } ?: (fallback ?: this)
