@file:OptIn(ExperimentalMaterial3Api::class)

package com.example.homi.ui.screens


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
import androidx.compose.material.icons.rounded.SupportAgent
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
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

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DetailRiwayatPengaduan(
    complaintId: Long,
    complaintRepo: ComplaintRepository? = null,
    onBack: () -> Unit
) {
    // ===== PREMIUM COLORS =====
    val BlueMain = Color(0xFF2F7FA3)
    val BlueGradient = Brush.verticalGradient(
        colors = listOf(BlueMain, Color(0xFF1A5E7B))
    )
    val SurfaceBg = Color(0xFFF8FAFC)
    val PrimaryBlue = BlueMain
    val TextMuted = Color(0xFF64748B)

    // ===== Fonts =====
    val poppinsReg = try { FontFamily(Font(R.font.poppins_regular)) } catch (_: Exception) { FontFamily.Default }
    val poppinsSemi = try { FontFamily(Font(R.font.poppins_semibold)) } catch (_: Exception) { FontFamily.Default }

    var loading by remember { mutableStateOf(complaintRepo != null) }
    var refreshing by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }
    var data by remember { mutableStateOf<ComplaintDto?>(null) }
    val scope = rememberCoroutineScope()
    val ctx = LocalContext.current

    fun normalizeStatus(raw: String?): String {
        val s = raw?.trim()?.lowercase() ?: ""
        return when {
            s == "selesai" || s == "approved" || s == "done" -> "approved"
            s == "ditolak" || s == "rejected" -> "rejected"
            s == "proses" || s == "sedang ditangani" -> "processing"
            else -> "submitted"
        }
    }

    fun load(refresh: Boolean) {
        if (complaintRepo == null) return
        scope.launch {
            if (refresh) refreshing = true else loading = true
            error = null
            try {
                data = complaintRepo.detail(complaintId)
            } catch (e: Exception) {
                error = e.message ?: "Gagal memuat detail pengaduan"
            } finally {
                if (refresh) refreshing = false else loading = false
            }
        }
    }

    LaunchedEffect(complaintId) { load(refresh = false) }

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
                    text = "Detail Laporan",
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
                text = "Berikut adalah rincian pengaduan Anda\ndalam proses monitoring lingkungan.",
                fontFamily = poppinsReg,
                fontSize = 12.sp,
                color = Color.White.copy(alpha = 0.85f),
                textAlign = TextAlign.Center,
                lineHeight = 18.sp,
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
                            val item = data
                            if (item == null) {
                                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                                    Text("Data tidak ditemukan", fontFamily = poppinsReg)
                                }
                            } else {
                                val norm = normalizeStatus(item.status)
                                val nomorPengaduan = "REP-${complaintId.toString().padStart(4, '0')}"
                                val tanggal = item.tanggalLabel ?: item.tanggalPengaduan ?: "-"

                                Column(
                                    modifier = Modifier
                                        .fillMaxSize()
                                        .verticalScroll(rememberScrollState())
                                        .padding(horizontal = 20.dp)
                                        .animateContentSize(animationSpec = tween(300))
                                ) {
                                    // 📘 CARD 1: RINGKASAN DATA
                                    PremiumPengaduanCard {
                                        Column {
                                            PengaduanCardHeader(icon = Icons.Outlined.Description, title = "Ringkasan Laporan", color = PrimaryBlue, fontSemi = poppinsSemi)
                                            Spacer(Modifier.height(20.dp))
                                            PengaduanInfoRowModern("ID Tiket", nomorPengaduan, poppinsReg, poppinsSemi)
                                            HorizontalDivider(Modifier.padding(vertical = 12.dp), color = Color(0xFFE2E8F0))
                                            PengaduanInfoRowModern("Kategori", item.category ?: "Umum", poppinsReg, poppinsSemi)
                                            HorizontalDivider(Modifier.padding(vertical = 12.dp), color = Color(0xFFE2E8F0))
                                            PengaduanInfoRowModern("Tanggal", tanggal, poppinsReg, poppinsSemi)
                                        }
                                    }

                                    Spacer(Modifier.height(16.dp))

                                    // 🕒 CARD 2: TIMELINE STATUS
                                    PremiumPengaduanCard {
                                        Column {
                                            PengaduanCardHeader(icon = Icons.Default.History, title = "Lacak Progress", color = PrimaryBlue, fontSemi = poppinsSemi)
                                            Spacer(Modifier.height(24.dp))
                                            PengaduanStatusTimeline(norm, poppinsReg, poppinsSemi)
                                        }
                                    }

                                    Spacer(Modifier.height(16.dp))

                                    // 📄 CARD 3: DETAIL TAMBAHAN
                                    PremiumPengaduanCard {
                                        Column {
                                            PengaduanCardHeader(icon = Icons.Default.Info, title = "Informasi Tambahan", color = PrimaryBlue, fontSemi = poppinsSemi)
                                            Spacer(Modifier.height(20.dp))
                                            PengaduanInfoRowModern("Nama Pelapor", item.namaPelapor ?: "-", poppinsReg, poppinsSemi)
                                            HorizontalDivider(Modifier.padding(vertical = 12.dp), color = Color(0xFFE2E8F0))
                                            
                                            // Handle multi-line perihal correctly
                                            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.Top) {
                                                Text(text = "Rincian", fontFamily = poppinsReg, fontSize = 13.sp, color = Color(0xFF64748B))
                                                Text(text = item.perihal ?: "-", fontFamily = poppinsSemi, fontSize = 14.sp, color = Color(0xFF0F172A), textAlign = TextAlign.End, modifier = Modifier.weight(1f).padding(start = 16.dp))
                                            }
                                            
                                            HorizontalDivider(Modifier.padding(vertical = 12.dp), color = Color(0xFFE2E8F0))
                                            PengaduanInfoRowModern("Lokasi Kejadian", item.tempat ?: item.tempatKejadian ?: "-", poppinsReg, poppinsSemi)
                                        }
                                    }

                                    Spacer(Modifier.height(24.dp))

                                    // 🟢 WHATSAPP SUPPORT BUTTON
                                    Button(
                                        onClick = {
                                            val nomor = "REP-${complaintId.toString().padStart(4, '0')}"
                                            val msg = "Halo Admin HOMI, saya butuh bantuan untuk Pengaduan #$nomor (ID=$complaintId)."
                                            WhatsAppUtil.openChat(
                                                context = ctx,
                                                phoneInternational = "6281992440287",
                                                message = msg
                                            )
                                        },
                                        modifier = Modifier
                                            .fillMaxWidth()
                                            .height(54.dp),
                                        shape = RoundedCornerShape(16.dp),
                                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFE26A2C))
                                    ) {
                                        Icon(painter = painterResource(id = R.drawable.notif), contentDescription = null, tint = Color.White, modifier = Modifier.size(20.dp))
                                        Spacer(Modifier.width(12.dp))
                                        Text(
                                            text = "Bantuan via WhatsApp",
                                            fontFamily = poppinsSemi,
                                            fontSize = 15.sp,
                                            color = Color.White
                                        )
                                    }

                                    Spacer(Modifier.height(80.dp))
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
private fun PremiumPengaduanCard(content: @Composable () -> Unit) {
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
private fun PengaduanCardHeader(icon: androidx.compose.ui.graphics.vector.ImageVector, title: String, color: Color, fontSemi: FontFamily) {
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
private fun PengaduanInfoRowModern(label: String, value: String, fontReg: FontFamily, fontSemi: FontFamily) {
    Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
        Text(text = label, fontFamily = fontReg, fontSize = 13.sp, color = Color(0xFF64748B))
        Text(text = value, fontFamily = fontSemi, fontSize = 14.sp, color = Color(0xFF0F172A), textAlign = TextAlign.End, modifier = Modifier.weight(1f).padding(start = 16.dp), maxLines = 1)
    }
}

@Composable
private fun PengaduanStatusTimeline(status: String, fontReg: FontFamily, fontSemi: FontFamily) {
    val steps = listOf("Laporan Diterima", "Sedang Ditangani", "Selesai")
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
                    val displayLabel = if (status == "rejected" && index == 2) "Dibatalkan"
                    else if (status == "approved" && index == 2) "Terselesaikan"
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
                            0 -> "Laporan pengaduan berhasil dicatat oleh sistem."
                            1 -> "Tim terkait sedang menindaklanjuti laporan Anda."
                            else -> if (status == "rejected") "Pengaduan Anda tidak dapat ditindaklanjuti atau dibatalkan."
                            else "Penanganan laporan Anda telah selesai."
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
